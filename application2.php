<?php 
$passages = json_decode($_GET['passages']);
$verses = json_decode($_GET['verses'], true);
$translations = ["KRV" => "88", "KHSV" => "85"]; // mappings for Korean & Khmer on Bible.com

$biblegateway_url="https://new.biblegateway.com/passage/";
// Since Korean and Khmer are not available on Biblegateway, we use Bible.com
$bible_com_url="https://www.bible.com/bible";

foreach(array_keys($translations) as $translation) {
  echo "<div class='block'>";
  echo "<hr />";
  echo "<span class='simptip-position-right simptip-multiline simptip-smooth simptip-info simptip-fade' data-tooltip=\"Press Ctrl+C or Cmd+C to copy to clipboard after clicking on 'Select all'\"><a href='javascript:void(0);' id='$translation-select' onclick='selectText(\"$translation\")' class='button white noprint'>Select text</a></span>";
  echo "<div class='info noprint'>Show verse references <input type='checkbox' onchange='javascript:toggleVerses(\"$translation\")' checked /></div>";
  echo "<div id='$translation' class='translation'>";
  $bibleID = $translations[$translation]; 
  foreach($passages as $ref => $chapters) { 
    foreach($chapters as $chapter) {    
      $verseNums = curl_init();
      curl_setopt($verseNums, CURLOPT_URL, $biblegateway_url);
      curl_setopt($verseNums, CURLOPT_POSTFIELDS, "search=$ref&version=ESV");
      curl_setopt($verseNums, CURLOPT_RETURNTRANSFER, 1);

      $verseNums = curl_exec($verseNums);
      preg_match("/<h1 class=\"bcv\">(.+?)<\/h1>/", $verseNums, $matches);

      $verseNums = array_pop(explode(" ", trim($matches[1]))); 

      echo "<div class='passages'>";
      $passage = rawurlencode($chapter);
      $curl_handle = curl_init();

      curl_setopt($curl_handle, CURLOPT_URL, "$bible_com_url/$bibleID/$passage");
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

      $result = curl_exec($curl_handle); 

      preg_match("/<article class='reader'.+?data-book-human='(.+?)'.+?data-chapter='(.+?)'.+?id='reader'>/", $result, $matches); 
      $title = $matches[1];

      echo  "<div style='text-align: center'><span class='fleuron'>d</span>  $title $verseNums  <span class='fleuron'>c</span></div>";

      preg_match("/<!-- \/ Primary version content -->(.+?)<!-- \/ Secondary version content -->/s", $result, $matches);
      $content = $matches[1];
      $dom = new DOMDocument();
      $dom->loadHTML($content);
 
      $nodes = $dom->getElementsByTagName("span"); 

      foreach($nodes as $node) { 
        if ($node->hasAttribute("class")) {
          if ($node->getAttribute("class") == "heading") {
            // set display:none for verses that aren't relevant, since removing the node runs into complications
            $node->setAttribute("style", "display:none");
          } 
          if ($node->getAttribute("class") == "label") {
            $val = $node->nodeValue;
            $newNode = $dom->createElement("sup", "$val&nbsp;"); 
            $newNode->setAttribute("class", "versenum");
            $node->parentNode->replaceChild($newNode, $node);
          }
        }

        if ($node->hasAttribute("data-usfm")) {
          if (!$verses[$node->getAttribute("data-usfm")]) {
            // set display:none for verses that aren't relevant, since removing the node runs into complications
            $node->setAttribute("style", "display:none");
          }    
        }
      }

      $nodes = $dom->getElementsByTagName("div");

      foreach($nodes as $node) {
        if ($node->hasAttribute("class")) {
          if(preg_match("/^chapter/", $node->getAttribute("class"))) {
            $chapter_node = ($node->firstChild->nextSibling);
            $chapter_node->setAttribute("style", "visibility:hidden");
          }
        }
      }


      echo $dom->saveHTML();
      echo "</div>";
    }
  }
  echo "</div>";
  echo "</div>";
}
?>
