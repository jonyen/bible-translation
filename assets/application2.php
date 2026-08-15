<?php
// Translations that BibleGateway does not carry (Korean, Japanese, Farsi) come
// from Bible.com instead. Bible.com only serves whole chapters, so we fetch the
// chapter and hide the verses that weren't asked for.
require_once __DIR__ . '/fetcher.php';

$bcv_books = ["Gen", "Exod", "Lev", "Num", "Deut", "Josh", "Judg", "Ruth", "1Sam", "2Sam", "1Kgs", "2Kgs", "1Chr", "2Chr", "Ezra", "Neh", "Esth", "Job", "Ps", "Prov", "Eccl", "Song", "Isa", "Jer", "Lam", "Ezek", "Dan", "Hos", "Joel", "Amos", "Obad", "Jonah", "Mic", "Nah", "Hab", "Zeph", "Hag", "Zech", "Mal", "Matt", "Mark", "Luke", "John", "Acts", "Rom", "1Cor", "2Cor", "Gal", "Eph", "Phil", "Col", "1Thess", "2Thess", "1Tim", "2Tim", "Titus", "Phlm", "Heb", "Jas", "1Pet", "2Pet", "1John", "2John", "3John", "Jude", "Rev"];
$bible_com_books = ["GEN", "EXO", "LEV", "NUM", "DEU", "JOS", "JDG", "RUT", "1SA", "2SA", "1KI", "2KI", "1CH", "2CH", "EZR", "NEH", "EST", "JOB", "PSA", "PRO", "ECC", "SNG", "ISA", "JER", "LAM", "EZK", "DAN", "HOS", "JOL", "AMO", "OBA", "JON", "MIC", "NAM", "HAB", "ZEP", "HAG", "ZEC", "MAL", "MAT", "MRK", "LUK", "JHN", "ACT", "ROM", "1CO", "2CO", "GAL", "EPH", "PHP", "COL", "1TH", "2TH", "1TI", "2TI", "TIT", "PHM", "HEB", "JAS", "1PE", "2PE", "1JN", "2JN", "3JN", "JUD", "REV"];

$translations = ["KRV" => "86", "JLB" => "83", "FCB" => "1619"];
$languages    = ["KRV" => "Korean", "JLB" => "Japanese", "FCB" => "Farsi"];

$biblegateway_url = "https://www.biblegateway.com/passage/";
$bible_com_url    = "https://www.bible.com/bible";

$passages = json_decode($_GET['passages'], true);
$verses   = json_decode($_GET['verses'], true);
if (!is_array($passages)) { $passages = []; }
if (!is_array($verses))   { $verses = []; }

// getChapters() nests arrays when the reference is a sequence.
function flatten_chapters($value) {
	$out = [];
	foreach ((array) $value as $item) {
		if (is_array($item)) {
			$out = array_merge($out, flatten_chapters($item));
		} elseif ($item !== null && $item !== '') {
			$out[] = $item;
		}
	}
	return $out;
}

// Issue #3: "Mark 1:1-9, 15" parses into two references that both live in Mark 1.
// The old code looped reference-by-reference and so fetched and rendered Mark 1
// once per reference, printing the chapter twice. Collapse to one entry per
// chapter first, remembering which references asked for it so we can still
// build an accurate heading.
$chapter_refs = [];
foreach ($passages as $ref => $chapters) {
	foreach (flatten_chapters($chapters) as $chapter) {
		if (!isset($chapter_refs[$chapter])) {
			$chapter_refs[$chapter] = [];
		}
		if (!in_array($ref, $chapter_refs[$chapter], true)) {
			$chapter_refs[$chapter][] = $ref;
		}
	}
}

// BibleGateway renders the canonical English heading ("Mark 1:1-9"); Bible.com
// has no equivalent, so we borrow it the way the app already does elsewhere.
function heading_for($refs) {
	global $biblegateway_url;
	$titles = [];
	foreach ($refs as $ref) {
		$html = fetch_url("$biblegateway_url?search=" . rawurlencode($ref) . "&version=ESV");
		if ($html !== false && preg_match("/<div class=\"dropdown-display-text\">(.+?)<\/div>/s", $html, $m)) {
			$title = trim($m[1]);
			if (!in_array($title, $titles, true)) {
				$titles[] = $title;
			}
		}
	}
	return $titles ? implode(", ", $titles) : null;
}

// Bible.com ships CSS-module class names ("ChapterContent-module__cat7xG__verse")
// whose hash changes between deploys, so match on the stable suffix.
function has_part($node, $part) {
	return $node instanceof DOMElement && strpos($node->getAttribute('class'), '__' . $part) !== false;
}

function query_all($finder, $expr, $context) {
	// Copy into a plain array: removing nodes while walking a DOMNodeList skips entries.
	$nodes = [];
	foreach ($finder->query($expr, $context) as $node) {
		$nodes[] = $node;
	}
	return $nodes;
}

// Turns one fetched Bible.com chapter into printable HTML containing only the
// requested verses. Returns null if the chapter could not be parsed.
function render_chapter($html, $chapter_usfm, $wanted_verses) {
	list($dom, $finder) = xpath_for($html);

	$chapters = query_all($finder, "//div[contains(@class, '__chapter')][@data-usfm='$chapter_usfm']", null);
	if (!$chapters) {
		return null;
	}
	$chapter = $chapters[0];

	// Section headings, footnotes and cross-references are not wanted in a bulletin.
	foreach (query_all($finder, ".//span[contains(@class, '__note')] | .//div[contains(@class, '__s1')] | .//span[contains(@class, '__heading')]", $chapter) as $node) {
		if ($node->parentNode) {
			$node->parentNode->removeChild($node);
		}
	}

	// The big drop-cap chapter number is a <div class="...__label">; verse numbers
	// are <span class="...__label"> inside a verse. Drop the former.
	foreach (query_all($finder, ".//div[contains(@class, '__label')]", $chapter) as $node) {
		if ($node->parentNode) {
			$node->parentNode->removeChild($node);
		}
	}

	foreach (query_all($finder, ".//span[contains(@class, '__verse')]", $chapter) as $verse) {
		$usfm = $verse->getAttribute('data-usfm');
		if (!isset($wanted_verses[$usfm])) {
			if ($verse->parentNode) {
				$verse->parentNode->removeChild($verse);
			}
			continue;
		}
		// Rewrite the verse number so the "Show verse references" checkbox,
		// which looks for .versenum, can toggle it.
		foreach (query_all($finder, ".//span[contains(@class, '__label')]", $verse) as $label) {
			$sup = $dom->createElement('sup');
			$sup->setAttribute('class', 'versenum');
			$sup->appendChild($dom->createTextNode($label->textContent . "\xc2\xa0"));
			$label->parentNode->replaceChild($sup, $label);
		}
	}

	// Paragraphs left empty by the removals above would print as blank lines.
	foreach (query_all($finder, ".//div[contains(@class, '__p')]", $chapter) as $p) {
		if (trim($p->textContent) === '') {
			if ($p->parentNode) {
				$p->parentNode->removeChild($p);
			}
		}
	}

	$out = "";
	foreach ($chapter->childNodes as $child) {
		$out .= $dom->saveHTML($child);
	}
	return trim($out) === '' ? null : $out;
}

$body = "";
foreach ($translations as $translation => $bibleID) {
	$language = $languages[$translation];
	$body .= "<div class='block'>";
	$body .= "<hr />";
	$body .= "<span class='simptip-position-right simptip-multiline simptip-smooth simptip-info simptip-fade' data-tooltip=\"Press Ctrl+C or Cmd+C to copy to clipboard after clicking on 'Select all'\"><a href='javascript:void(0);' id='$translation-select' onclick='selectText(\"$translation\")' class='button white noprint'>Select text</a> <span style='font-size: 14px'>$language</span></span>";
	$body .= "<div class='info noprint'>Show verse references <input type='checkbox' onchange='javascript:toggleVerses(\"$translation\")' checked /></div>";
	$body .= "<div id='$translation' class='translation'>";

	foreach ($chapter_refs as $chapter => $refs) {
		// "Mark 1" -> "MRK.1"
		$parts = explode(" ", $chapter);
		$book_index = array_search($parts[0], $bcv_books);
		if ($book_index === false) {
			continue;
		}
		$parts[0] = $bible_com_books[$book_index];
		$chapter_usfm = implode(".", $parts);

		$body .= "<div class='passages'>";
		$title = heading_for($refs);
		if ($title !== null) {
			$body .= "<div style='text-align: center'><span class='fleuron'>d</span>  $title  <span class='fleuron'>c</span></div>";
		}

		$html = fetch_url("$bible_com_url/$bibleID/" . rawurlencode($chapter_usfm));
		$rendered = $html === false ? null : render_chapter($html, $chapter_usfm, $verses);
		if ($rendered === null) {
			$safe = htmlspecialchars($chapter, ENT_QUOTES, 'UTF-8');
			$body .= "<div>Sorry, $safe could not be retrieved for this translation.</div>";
		} else {
			$body .= $rendered;
		}
		$body .= "</div>";
	}

	$body .= "</div>";
	$body .= "</div>";
}

echo cache_notice() . $body;
