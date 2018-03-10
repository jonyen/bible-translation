<?php
$passages = explode(",", $_GET['passages']);
$translations = ["ESV", "CNVT", "NVI", "RUSV", "BPT", "FSV", "KLB"];

$biblegateway_url="https://www.biblegateway.com/passage/";

foreach ($translations as $translation) {
        echo "<div class='block'>";
        echo "<hr />";
	echo "<span class='simptip-position-right simptip-multiline simptip-smooth simptip-info simptip-fade' data-tooltip=\"Press Ctrl+C or Cmd+C to copy to clipboard after clicking on 'Select all'\"><a href='javascript:void(0);' id='$translation-select' onclick='selectText(\"$translation\")' class='button white noprint'>Select text</a></span>";
	echo "<div class='info noprint'>Show verse references <input type='checkbox' onchange='javascript:toggleVerses(\"$translation\")' checked /></div>";
        echo "<div id='$translation' class='translation'>";
	foreach ($passages as $passage) {
		$passage = rawurlencode($passage);
		$curl_handle = curl_init();

		curl_setopt($curl_handle, CURLOPT_URL, $biblegateway_url);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "search=$passage&version=$translation");
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl_handle);

		echo "<div class='passages'>";

		// Since CNVT has a glitch and shows simplified characters for the verse reference, we grab the traditional characters from CUVMPT instead
		if ($translation == "CNVT") {
			$traditional_title = curl_init();
			curl_setopt($traditional_title, CURLOPT_URL, $biblegateway_url);
			curl_setopt($traditional_title, CURLOPT_POSTFIELDS, "search=$passage&version=CUVMPT");
			curl_setopt($traditional_title, CURLOPT_RETURNTRANSFER, 1);

			$traditional = curl_exec($traditional_title);
			preg_match("/<h1 class=\"bcv\">(.+?)<\/h1>/", $traditional, $matches);
		} else {
			preg_match("/<h1 class=\"bcv\">(.+?)<\/h1>/", $result, $matches);
		}

		echo  "<div style='text-align: center'><span class='fleuron'>d</span>  $matches[1]  <span class='fleuron'>c</span></div>";
		preg_match("/(<div class=\"version-$translation result-text-style-normal text-html \">.+?)<div class=\"publisher-info-bottom.+?\">/s", $result, $matches);
		$result = $matches[1];

 //		echo "\n\n*****" . $result . "\n\n\n";

		// remove passage display
		$result = preg_replace("/<h1 class=\"passage-display\">.+?<\/h1>/s", "", $result);

	        // change chapter number to verse number 1
                $result = preg_replace("/<span class=\"chapternum\">.+?<\/span>/", "<sup class=\"versenum\">1&nbsp;</sup>", $result);

		// remove titles
		$result = preg_replace("/<h3>.+?<\/h3>/s", "", $result);

		// remove crossrefs and footnotes
		$result = preg_replace("/<sup data-(cr|fn)=['\"].+?['\"] class=['\"](crossref|crossreference|footnote)['\"].+?>.+?<\/sup>/s", "", $result);
		$result = preg_replace("/<div class=\"(crossrefs hidden|footnotes)\">.+?<\/div>/s", "", $result);
//		$result = preg_replace("/<h4>(Cross references|Footnotes):<\/h4>/", "", $result);
/*		$result = preg_replace("/<ol .+?>.+?<\/ol>/s", "", $result);   */
		$result = preg_replace("/(<sup class=\"versenum\">)/", "&#8203;$1", $result);  // adds zero-width space so that the verse num will break from previous word
//		$result = preg_replace("/<span.+?class=\"text.+?\">/", "", $result);
//		$result = preg_replace("/<\/span>/", "", $result);
		$result = preg_replace("/[\n\r\f]+/m","", $result);
		echo $result;
		echo "</div>";

		curl_close($curl_handle);
	}
        echo "</div>";
        echo "</div>";
}
?>
