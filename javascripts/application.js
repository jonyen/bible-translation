onmessage = function(oEvent) {
  var message = "";
  xmlHttp = new XMLHttpRequest();
  xmlHttp.open("GET", "application.php?passages=" + oEvent.data.passages, false);
  xmlHttp.send(null);
  message = xmlHttp.responseText;     

  xmlHttp.open("GET", "application2.php?passages=" + JSON.stringify(oEvent.data.passagesJSON) + "&verses=" + JSON.stringify(oEvent.data.versesJSON), false);
  xmlHttp.send(null); 
 
  message += xmlHttp.responseText;    
  postMessage(message);
}

