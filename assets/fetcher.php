<?php
// Shared HTTP fetch with an on-disk cache.
//
// Issue #6: BibleGateway and Bible.com are the only sources of passage text, so
// if either one is down (or changes its markup) the app has nothing to show.
// Every successful fetch is written to assets/cache/, and a failed fetch falls
// back to the last good copy instead of rendering an empty passage.

define('CACHE_DIR', __DIR__ . '/cache');
define('CACHE_TTL', 60 * 60 * 24 * 30); // refetch after 30 days
define('MAX_ATTEMPTS', 2);
define('RETRY_DELAY', 2); // seconds, multiplied by the attempt number
define('GIVE_UP_AFTER', 2); // consecutive challenges before a host is left alone

// Set by fetch_url() so callers can tell the user the text is stale.
$GLOBALS['fetch_served_from_cache'] = false;
$GLOBALS['fetch_cache_date'] = null;
// Consecutive bot-check responses per host, so one blocked source doesn't make
// the whole page wait through a retry backoff for every passage.
$GLOBALS['fetch_blocked_hosts'] = [];

// Bible.com answers a rate-limited request with a bot-check page rather than an
// error status, so a 200 is not on its own proof that we got the chapter.
function is_challenge_page($html) {
	return $html !== false && strpos($html, '<title>Client Challenge') !== false;
}

function cache_path($url) {
	return CACHE_DIR . '/' . sha1($url) . '.html';
}

// Returns the page body, or false when the network failed and nothing is cached.
function fetch_url($url) {
	$path = cache_path($url);
	$cached = is_readable($path) ? file_get_contents($path) : false;

	// A fresh cache entry short-circuits the network entirely.
	if ($cached !== false && (time() - filemtime($path)) < CACHE_TTL) {
		return $cached;
	}

	// Bible.com rate-limits bursts by answering with a bot-check page (HTTP 200,
	// title "Client Challenge") instead of the chapter, so retry with a backoff.
	// Once a host has turned us away GIVE_UP_AFTER times in a row, stop asking for
	// the rest of this request and fall straight through to the cache.
	$host = parse_url($url, PHP_URL_HOST);
	$blocked = isset($GLOBALS['fetch_blocked_hosts'][$host]) && $GLOBALS['fetch_blocked_hosts'][$host] >= GIVE_UP_AFTER;
	$result = false;
	$status = 0;
	for ($attempt = 0; !$blocked && $attempt < MAX_ATTEMPTS; $attempt++) {
		if ($attempt > 0) {
			sleep($attempt * RETRY_DELAY);
		}
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 30);
		// Both sources serve an error page to clients without a browser user agent.
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36');
		$result = curl_exec($curl_handle);
		$status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if (!is_challenge_page($result)) {
			$GLOBALS['fetch_blocked_hosts'][$host] = 0;
			break;
		}
		$result = false;
	}
	if ($result === false && !$blocked) {
		$GLOBALS['fetch_blocked_hosts'][$host] = ($GLOBALS['fetch_blocked_hosts'][$host] ?? 0) + 1;
	}

	if ($result !== false && $result !== '' && $status >= 200 && $status < 300) {
		if (!is_dir(CACHE_DIR)) {
			mkdir(CACHE_DIR, 0755, true);
		}
		// Write to a temp file first so a crash mid-write can't corrupt the cache.
		$tmp = $path . '.' . getmypid() . '.tmp';
		if (file_put_contents($tmp, $result) !== false) {
			rename($tmp, $path);
		}
		return $result;
	}

	if ($cached !== false) {
		$GLOBALS['fetch_served_from_cache'] = true;
		$GLOBALS['fetch_cache_date'] = date('Y-m-d', filemtime($path));
		return $cached;
	}

	return false;
}

// Renders the "this text is stale" notice, once per request, if any fetch fell
// back to the cache.
function cache_notice() {
	if (!$GLOBALS['fetch_served_from_cache']) {
		return '';
	}
	$date = htmlspecialchars($GLOBALS['fetch_cache_date'], ENT_QUOTES, 'UTF-8');
	return "<div class='cache-notice noprint'>Some sources could not be reached, so passages below are served from a cached copy (last retrieved $date).</div>";
}

// Parses an HTML string into a DOMXPath, suppressing the parse warnings that
// real-world markup always produces.
function xpath_for($html) {
	$dom = new DOMDocument();
	libxml_use_internal_errors(true);
	$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
	libxml_clear_errors();
	return [$dom, new DOMXPath($dom)];
}
