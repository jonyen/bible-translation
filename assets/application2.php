<?php
//$_GET['passages'] = "{%22John.3.16%22:[%22John%203%22],%22Ps.46.10%22:[%22Ps%2046%22],%221John.1.9-1John.1.10%22:[%221John%201%22]}";
//$_GET['verses'] = "{%22JHN.3.16%22:1,%22PSA.46.10%22:1,%221JN.1.9%22:1,%221JN.1.10%22:1}";

//$_GET['passages'] = "{%22John.3.16%22:[%22John%203%22]}";
//$_GET['verses'] = "{%22JHN.3.16%22}";

// ********************* //
// Constants
// ********************* //

$bcv_books = ["Gen", "Exod", "Lev", "Num", "Deut", "Josh", "Judg", "Ruth", "1Sam", "2Sam", "1Kgs", "2Kgs", "1Chr", "2Chr", "Ezra", "Neh", "Esth", "Job", "Ps", "Prov", "Eccl", "Song", "Isa", "Jer", "Lam", "Ezek", "Dan", "Hos", "Joel", "Amos", "Obad", "Jonah", "Mic", "Nah", "Hab", "Zeph", "Hag", "Zech", "Mal", "Matt", "Mark", "Luke", "John", "Acts", "Rom", "1Cor", "2Cor", "Gal", "Eph", "Phil", "Col", "1Thess", "2Thess", "1Tim", "2Tim", "Titus", "Phlm", "Heb", "Jas", "1Pet", "2Pet", "1John", "2John", "3John", "Judge", "Rev"];

$bible_com_books = ["GEN", "EXO", "LEV", "NUM", "DEU", "JOS", "JDG", "RUT", "1SA", "2SA", "1KI", "2KI", "1CH", "2CH", "EZR", "NEH", "EST", "JOB", "PSA", "PRO", "ECC", "SNG", "ISA", "JER", "LAM", "EZK", "DAN", "HOS", "JOL", "AMO", "OBA", "JON", "MIC", "NAM", "HAB", "ZEP", "HAG", "ZEC", "MAL", "MAT", "MRK", "LUK", "JHN", "ACT", "ROM", "1CO", "2CO", "GAL", "EPH", "PHP", "COL", "1TH", "2TH", "1TI", "2TI", "TIT", "PHM", "HEB", "JAS", "1PE", "2PE", "1JN", "2JN", "3JN", "JUD", "REV"];

$KRV_books = [ "창세기", "출애굽기", "레위기", "민수기", "신명기", "여호수아", "사사기", "룻기", "사무엘상", "사무엘하", "열왕기상", "열왕기하", "역대상", "역대하", "에스라", "느 헤미야", "에스더", "욥기", "시편", "잠언", "전도서", "아가", "이사야", "예레미야", "예레미야애가", "에스겔", "다니엘", "호세아", "요엘", "아모스", "오바댜", "요나", "미가", "나훔 ", "하박국", "스바냐", "학개", "스가랴", "말라기", "마태복음", "마가복음", "누가복음", "요한복음", "사도행전", "로마서", "고린도전서", "고린도후서", "갈라디아서", "에베소서", "빌 립보서", "골로새서", "데살로니가전서", "데살로니가후서", "디모데전서", "디모데후서", "디도서", "빌레몬서", "히브리서", "야고보서", "베드로전서", "베드로후서", "요한일서", "요한2>서", "요한3서", "유다서", "요한계시록" ];
$KHSV_books = [
"លោកុ&zwnj;ប្បត្តិ",
"និក្ខមនំ",
"លេវី&zwnj;វិន័យ",
"ជន&zwnj;គណនា",
"ទុតិយ&zwnj;កថា",
"យ៉ូស្វេ",
"ចៅហ្វាយ",
"នាង&#8203;រស់",
"១ សាំយូ&zwnj;អែល",
"២ សាំយូ&zwnj;អែល",
"១ ពង្សា&zwnj;វតារ&zwnj;ក្សត្រ",
"២ ពង្សា&zwnj;វតារ&zwnj;ក្សត្រ",
"១ របាក្សត្រ",
"២ របាក្សត្រ",
"អែសរ៉ា",
"នេហេ&zwnj;មា",
"នាង&#8203;អេសធើរ",
"យ៉ូប",
"ទំនុក&zwnj;តម្កើង",
"សុភាសិត",
"សាស្ដា",
"បទ&#8203;ចម្រៀង",
"អេសាយ",
"យេរេមា",
"បរិទេវ",
"អេសេ&zwnj;គាល",
"ដានី&zwnj;យ៉ែល",
"ហូសេ",
"យ៉ូអែល",
"អេម៉ុស",
"អូបា&zwnj;ឌា",
"យ៉ូណាស",
"មីកា",
"ណាហ៊ូម",
"ហាបា&zwnj;គូក",
"សេផា&zwnj;នី",
"ហាកាយ",
"សាការី",
"ម៉ាឡា&zwnj;គី",
"ម៉ាថាយ",
"ម៉ាកុស",
"លូកា",
"យ៉ូហាន",
"កិច្ចការ",
"រ៉ូម",
"១ កូរិន&zwnj;ថូស",
"២ កូរិន&zwnj;ថូស",
"កាឡាទី",
"អេភេសូ",
"ភីលីព",
"កូឡូស",
"១ ថេស្សា&zwnj;ឡូនិក",
"២ ថេស្សា&zwnj;ឡូនិក",
"១ ធីម៉ូថេ",
"២ ធីម៉ូថេ",
"ទីតុស",
"ភីលេ&zwnj;ម៉ូន",
"ហេប្រឺ",
"យ៉ាកុប",
"១ ពេត្រុស",
"២ ពេត្រុស",
"១ យ៉ូហាន",
"២ យ៉ូហាន",
"$៣ យ៉ូហាន",
"យូដាស",
"វិវរណៈ"];
$JLB_books = [ "創世記", "出エジプト記", "レビ記", "民数記", "申命記", "ヨシュア記", "士師記", "ルツ記", "サムエル記Ⅰ", "サムエル記Ⅱ", "列王記Ⅰ", "列王記Ⅱ", "歴代誌Ⅰ", "歴代誌Ⅱ", "エズラ記", "ネヘミヤ 記", "エステル 記", "ヨブ 記", "詩篇", "箴言 知恵の泉", "伝道者の書", "雅歌", "イザヤ書", "エレミヤ書", "哀歌", "エゼキエル書", "ダニエル書", "ホセア書", "ヨエル書", "アモス書", "オバデヤ書", "ヨナ書", "ミカ書", "ナホム書", "ハバクク書", "ゼパニヤ書", "ハガイ書", "ゼカリヤ書", "マラキ書", "マタイの福音書", "マルコの福音書", "ルカの 福音書", "ヨハネの福音書", "使徒の働き", "ローマ人への手紙", "コリント人への手紙Ⅰ", "コリント人への手紙Ⅱ", "ガラテヤ人への手紙", "エペソ人への手紙", "ピリピ人への手紙", "コロサイ 人への手紙", "テサロニケ人への手紙Ⅰ", "テサロニケ人への手紙Ⅱ", "テモテへの手紙Ⅰ", "テモテへの手紙Ⅱ", "テトスへの手紙", "ピレモンへの手紙", "へブル人への手紙", "ヤコブの手紙", "ペ テロの手紙Ⅰ", "ペテロの手紙Ⅱ", "ヨハネの手紙Ⅰ", "ヨハネの手紙Ⅱ", "ヨハネの手紙Ⅲ", "ユダの手紙", "ヨハネの黙示録" ];

$book_translations = ["KRV" => $KRV_books, "KHSV" => $KHSV_books, "JLB" => $JLB_books, "FCB" => $bcv_books];

$passages = json_decode(urldecode($_GET['passages']));
$verses = json_decode(urldecode($_GET['verses']), true);

//$translations = ["ESV" => "1"]; // mappings for Korean, Khmer, & Japanese on Bible.com
$translations = ["KRV" => "86", "KHSV" => "85", "JLB" => "83", "FCB" => "1619"]; // mappings for Korean, Khmer, & Japanese on Bible.com
$languages = ["Korean", "Khmer", "Japanese", "Farsi"];

$khmer_nums = array("០","១","២","៣","៤","៥","៦","៧","៨","៩");

$biblegateway_url = "https://www.biblegateway.com/passage/";
// Since Korean and Khmer are not available on Biblegateway, we use Bible.com
$bible_com_url = "https://www.bible.com/bible";

// ********************* //
// Main Application Code
// ********************* //

foreach(array_keys($translations) as $translation) {
  $language = $languages[array_search($translation, array_keys($translations))];
  echo "<div class='block'>";
  echo "<hr />";
  echo "<span class='simptip-position-right simptip-multiline simptip-smooth simptip-info simptip-fade' data-tooltip=\"Press Ctrl+C or Cmd+C to copy to clipboard after clicking on 'Select all'\"><a href='javascript:void(0);' id='$translation-select' onclick='selectText(\"$translation\")' class='button white noprint'>Select text</a> <span style='font-size: 14px'>$language</span></span>";
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
      if ($translation == "KHSV") {
        $verseNums = strtr($verseNums, $khmer_nums);
      }

      echo "<div class='passages'>";
      $passage_array = explode(" ", $chapter);
      $passage_array[0] = $bible_com_books[array_search($passage_array[0], $bcv_books)];
      $passage = rawurlencode(join(".", $passage_array));
      $curl_handle = curl_init();

      curl_setopt($curl_handle, CURLOPT_URL, "$bible_com_url/$bibleID/$passage");
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

      $result = curl_exec($curl_handle);

      $passage_array = explode(" ", $chapter);
      $book = $book_translations[$translation][array_search($passage_array[0], $bcv_books)];
      echo "<div style='text-align: center'><span class='fleuron'>d</span>  $book $verseNums  <span class='fleuron'>c</span></div>";

      // enable user error handling
      libxml_use_internal_errors(true);

      $dom = new DOMDocument();

      if (!$dom->loadHTML($result)) {
        foreach (libxml_get_errors() as $error) {
          // handle errors here
        }

        libxml_clear_errors();
      }

      $finder = new DomXPath($dom);
      $classname = "verse";
      $nodes = $finder->query("//span[contains(@class, '$classname')]");

//      print_r($nodes);

      $dom = new DOMDocument();
//      print_r($nodes[0]);

//      $nodes = $dom->getElementsByTagName("span");

      foreach($nodes as $node) {
        foreach($node->childNodes as $childNode) {
          if ($childNode->hasAttribute("class")) {
            if ($childNode->getAttribute("class") == "heading" || $childNode->getAttribute("class") == "note x" || $childNode->getAttribute("class") == "note f") {
              $childNode->setAttribute("style", "display:none");
            }
          }
        }

        if ($node->hasAttribute("class")) {
          if ($node->getAttribute("class") == "heading" || $node->getAttribute("class") == "note x" || $node->getAttribute("class") == "note f") {
            // set display:none for verses that aren't relevant, since removing the node runs into complications
            $node->setAttribute("style", "display:none");
          }
          if ($node->getAttribute("class") == "label") {
            $val = $node->nodeValue;
            if ($translation == "KHSV") {
              $val = strtr($val, $khmer_nums);
            }
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
        $node = $dom->importNode($node, true);
        echo $dom->saveHTML($node);
      }

  /*    $nodes = $finder->query("//div[contains(@class, '$classname')]");
      //$nodes = $dom->getElementsByTagName("div");

      foreach($nodes as $node) {
        if ($node->hasAttribute("class")) {
          if(preg_match("/^chapter/", $node->getAttribute("class"))) {
            $chapter_node = ($node->firstChild->nextSibling);
            $chapter_node->setAttribute("style", "display:none");
          }
          if ($node->getAttribute("class") == "d") {
            $node->setAttribute("style", "display:none");
          }
          if ($node->getAttribute("class") == "copyright") {
            $node->setAttribute("style", "display:none");
          }
          if ($node->getAttribute("class") == "ng-hide") {
            $node->setAttribute("style", "display:none");
          }
        }
        if ($node->hasAttribute("id")) {
          if ($node->getAttribute("id") == "site-footer") {
            $node->setAttribute("style", "display:none");
          }
	}
      } */


//      echo $dom->saveHTML();
      echo "</div>";
    }
  }
  echo "</div>";
  echo "</div>";
}
?>
