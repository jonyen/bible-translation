onmessage = function(oEvent) {
  var message = "";

  // BibleGateway-backed translations.
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open("GET", "../assets/application.php?passages=" + oEvent.data.passages, false);
  xmlHttp.send(null);
  message = xmlHttp.responseText;

  // Korean, Japanese and Farsi come from Bible.com, which needs the chapter and
  // verse breakdown rather than the raw reference.
  xmlHttp = new XMLHttpRequest();
  xmlHttp.open("GET", "../assets/application2.php?passages=" + encodeURIComponent(JSON.stringify(oEvent.data.passagesJSON)) + "&verses=" + encodeURIComponent(JSON.stringify(oEvent.data.versesJSON)), false);
  xmlHttp.send(null);
  message += xmlHttp.responseText;

  postMessage(message);
}
