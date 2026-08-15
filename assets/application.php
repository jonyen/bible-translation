<?php
require_once __DIR__ . '/fetcher.php';

$passages = explode(",", $_GET['passages']);
$translations = ["ESV", "CNVT", "NVI", "RUSV", "BPT", "FSV", "NVI-PT"];
$languages = ["English", "Chinese (Traditional)", "Spanish", "Russian", "Vietnamese", "Tagalog", "Portuguese"];

$biblegateway_url = "https://www.biblegateway.com/passage/";

// BibleGateway shows the resolved reference ("John 3:16") in the first
// .dropdown-display-text on the page; the second one is the version name.
function biblegateway_title($html) {
	if (preg_match("/<div class=\"dropdown-display-text\">(.+?)<\/div>/s", $html, $matches)) {
		return trim($matches[1]);
	}
	return null;
}

$body = "";
foreach ($translations as $translation) {
	$language = $languages[array_search($translation, $translations)];
	$body .= "<div class='block'>";
	$body .= "<hr />";
	$body .= "<span class='simptip-position-right simptip-multiline simptip-smooth simptip-info simptip-fade' data-tooltip=\"Press Ctrl+C or Cmd+C to copy to clipboard after clicking on 'Select all'\"><a href='javascript:void(0);' id='$translation-select' onclick='selectText(\"$translation\")' class='button white noprint'>Select text</a> <span style='font-size: 14px'>$language</span></span>";
	$body .= "<div class='info noprint'>Show verse references <input type='checkbox' onchange='javascript:toggleVerses(\"$translation\")' checked /></div>";
	$body .= "<div id='$translation' class='translation'>";
	foreach ($passages as $passage) {
		$passage = rawurlencode($passage);
		$result = fetch_url("$biblegateway_url?search=$passage&version=$translation");
		$body .= "<div class='passages'>";

		if ($result === false) {
			$body .= "<div>Sorry, $passage could not be retrieved for this translation.</div></div>";
			continue;
		}

		list($dom, $finder) = xpath_for($result);

		// CNVT's own reference heading renders in simplified characters, so we
		// take the heading from CUVMPT, which is correctly traditional.
		if ($translation == "CNVT") {
			$traditional = fetch_url("$biblegateway_url?search=$passage&version=CUVMPT");
			$title = $traditional === false ? null : biblegateway_title($traditional);
		} else {
			$title = biblegateway_title($result);
		}

		if ($title === null) {
			$body .= "<div>Sorry, $passage could not be found for this translation.</div>";
		} else {
			$body .= "<div style='text-align: center'><span class='fleuron'>d</span>  $title  <span class='fleuron'>c</span></div>";
		}

		$text = "";
		$paragraphs = $finder->query("//div[contains(@class, 'passage-text')]//p");
		foreach ($paragraphs as $paragraph) {
			if (!$finder->query('.//span[contains(@class, "text")]', $paragraph)->length) {
				continue;
			}
			// Scope the removal to this paragraph. The leading "//" here used to
			// search the whole document, so the first paragraph stripped every
			// footnote on the page and later paragraphs found nothing to strip.
			$notes = $finder->query(".//sup[contains(@class, 'crossreference') or contains(@class, 'footnote')] | .//div[contains(@class, 'crossrefs') or contains(@class, 'footnotes')]", $paragraph);
			foreach ($notes as $note) {
				$note->parentNode->removeChild($note);
			}
			$text .= $dom->saveHTML($paragraph);
		}
		$body .= $text;
		$body .= "</div>";
	}
	$body .= "</div>";
	$body .= "</div>";
}

echo cache_notice() . $body;
