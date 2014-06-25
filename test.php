<?php
define("MEDIAWIKI_PATH", "./mediawiki-1.23.0");
require_once "mediawiki-zhconverter.inc.php";

echo MediaWikiZhConverter::convert("面包", "zh-tw");
?>
