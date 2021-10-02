<?php 
$passages = explode(",", $_GET['passages']);
$translations = ["ESV", "CNVT", "NVI", "RUSV", "BPT", "FSV", "NVI-PT"];
// $translations = ["ESV"];
$languages = ["English", "Chinese (Traditional)", "Spanish", "Russian", "Vietnamese", "Tagalog", "Portuguese"];

$biblegateway_url="https://www.biblegateway.com/passage/";

foreach ($translations as $translation) {
        $language = $languages[array_search($translation, $translations)];
        echo "<div class='block'>";
        echo "<hr />";
	echo "<span class='simptip-position-right simptip-multiline simptip-smooth simptip-info simptip-fade' data-tooltip=\"Press Ctrl+C or Cmd+C to copy to clipboard after clicking on 'Select all'\"><a href='javascript:void(0);' id='$translation-select' onclick='selectText(\"$translation\")' class='button white noprint'>Select text</a> <span style='font-size: 14px'>$language</span></span>";
	echo "<div class='info noprint'>Show verse references <input type='checkbox' onchange='javascript:toggleVerses(\"$translation\")' checked /></div>";
        echo "<div id='$translation' class='translation'>";
	foreach ($passages as $passage) {
		$passage = rawurlencode($passage);
		$curl_handle = curl_init();

		curl_setopt($curl_handle, CURLOPT_URL, "$biblegateway_url/?search=$passage&version=$translation");
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl_handle);
		echo "<div class='passages'>";

		$dom = new DOMDocument();

		libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="UTF-8">' . $result);
		libxml_clear_errors();

		$finder = new DomXPath($dom);
		// Since CNVT has a glitch and shows simplified characters for the verse reference, we grab the traditional characters from CUVMPT instead
		if ($translation == "CNVT") {
			$traditional_title = curl_init();
			curl_setopt($traditional_title, CURLOPT_URL, "$biblegateway_url/?search=$passage&version=CUVMPT");
			curl_setopt($traditional_title, CURLOPT_RETURNTRANSFER, 1);

			$traditional = curl_exec($traditional_title);
			// preg_match("/<h1 class=\"bcv.+\">(.+?)<\/h1>/", $traditional, $matches);
		    $title = $finder->query("//div[contains(@class, 'bcv')]")->item(0)->textContent;
			// $titleText = $dom->saveHTML($title);
			echo  "<div style='text-align: center'><span class='fleuron'>d</span>  $title  <span class='fleuron'>c</span></div>";
		} else {
			preg_match("/<div class=\"dropdown\-display\-text\">(.+?)<\/div>/", $result, $matches);
			if (!isset($matches[1])) {
				echo "<div>Sorry, $passage could not be found for this translation.</div>";
			} else {
				echo  "<div style='text-align: center'><span class='fleuron'>d</span>  $matches[1]  <span class='fleuron'>c</span></div>";
			}
		}

		$text = "";
		$context = $finder->query("//div[@class='passage-text']")->item(0);
		$paragraphs = $finder->query("//div[@class='passage-text']//p");
		$verses = $finder->query("//div[@class='passage-text']//span[contains(@class, 'text')]");
		foreach ($paragraphs as $paragraph) {
			if($finder->query('.//span[contains(@class, "text")]', $paragraph)->length) {
				$results = $finder->query("//sup[contains(@class, 'crossreference') or contains(@class, 'footnote')] | //div[contains(@class, 'crossrefs') or contains(@class, 'footnotes')]", $paragraph);
				foreach($results as $result)
				{
					$result->parentNode->removeChild($result);
				}
				$text .= $dom->saveHTML($paragraph);
			}

		}
		echo $text;

		// print_r(get_class($node));
		// for ($i = 0; $i < $node->count(); $i++) {
			// print_r($node->item($i));
		// print_r($node->item(0));
		// foreach($node->childNodes as $childNode) {
		// 	// print_r(get_class($childNode));
		// 	if (get_class($childNode) == "DOMNode" && $childNode->hasAttribute("class")) {
		// 		if ($childNode->getAttribute("class") == "crossreference") {
		// 			$childNode->setAttribute("style", "display:none");
		// 		}
		// 	}
		// }
		// echo $dom->saveHTML($node);

                //preg_match("/(text-html\">.+?)/s", $result, $matches);
	//	preg_match("/<div class=\"passage-text\">(.+?)<\/div>/", $result, $matches);
		// $result = $matches[1];

		// remove passage display
/*		$result = preg_replace("/<h1 class=\"passage-display\">.+?<\/h1>/s", "", $result);

	        // change chapter number to verse number 1
                $result = preg_replace("/<span class=\"chapternum\">.+?<\/span>/", "<sup class=\"versenum\">1&nbsp;</sup>", $result);

		// remove titles
		$result = preg_replace("/<h3>.+?<\/h3>/s", "", $result);

		// remove crossrefs and footnotes
		$result = preg_replace("/<sup data-(cr|fn)=['\"].+?['\"] class=['\"](crossref|crossreference|footnote)['\"].+?>.+?<\/sup>/s", "", $result);
		$result = preg_replace("/<div class=\"(crossrefs hidden|footnotes)\">.+?<\/div>/s", "", $result);
		$result = preg_replace("/(<sup class=\"versenum\">)/", "&#8203;$1", $result);  // adds zero-width space so that the verse num will break from previous word
		$result = preg_replace("/[\n\r\f]+/m","", $result); */
		// echo $result;
		echo "</div>";

		curl_close($curl_handle);
	}
        echo "</div>";
        echo "</div>";
}
?>
