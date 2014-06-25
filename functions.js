var bcv = new bcv_parser; 

function getJSON(seq) {
  var json = {};
  passages = bcv.parse(seq).osis().split(',');
  passages.forEach(function(passage) {
    json[passage] = getChapters(passage);
  });
  return json;
}

function getChapters(ref) {
  passage = bcv.parse(ref);
    switch(passage.entities[0].type) {
      case 'bcv':
      case 'bc':
        return [passage.entities[0].passages[0].start.b + " " + passage.entities[0].passages[0].start.c];
      case 'b':
      case 'range':
        chapters = [];
        start = passage.entities[0].passages[0].start;
        end = passage.entities[0].passages[0].end;
        book = start.b;
        chapter = start.c || 1;
        while (true) {
	  // force a "1" verse so that it gets detected in special cases such as Philemon or 2 John
          if (bcv.parse(book + " " + chapter + ":1").osis() == "" || chapter > end.c) {  
            return chapters;
          }
        chapters.push(book + " " + chapter);
        chapter++;
        }
      case 'sequence':
        return passage.osis()
		      .split(",")
		      .map(function(p) { return getChapters(p) });
      default:
        return [];
    }
}

function getVerses(str) {
  passage = bcv.parse(str);
  switch (passage.entities[0].type) {
    case 'bcv':
      return [passage.s];
    case 'bc':
      start = passage.entities[0].passages[0].start;
      book = start.b;
      chapter = start.c;
      verse = 1;
      var verses = [];
      while (true) {
        ref = book + " " + chapter + ":" + verse;
        curParse = bcv.parse(ref);
        if (curParse.osis() == "") {
          return verses; 
	}
        verses.push(ref);
        verse++;
      }
    case 'b':
      start = passage.entities[0].passages[0].start;
      book = start.b;
      chapter = 1;
      verse = 1;
      var verses = [];
      while (true) {
        ref = book + " " + chapter + ":" + verse;
        curParse = bcv.parse(ref);
        if (curParse.osis() == "") {
          verse = 1;
          chapter++;
          ref = book + " " + chapter + ":" + verse;
          if (bcv.parse(ref).osis() == "") {
            return verses;
	  }
       }
        verses.push(ref);
        verse++;
      }
    case 'range':
      start = passage.entities[0].passages[0].start;
      end = passage.entities[0].passages[0].end;
      endRef = end.b + " " + end.c + ":" + end.v;
      book = start.b;
      chapter = start.c;
      verse = start.v || 1;
      var verses = [];
      while (true) {
	ref = book + " " + chapter + ":" + verse;
	curParse = bcv.parse(ref); 
        if (ref == endRef) {
	  verses.push(ref);
          return verses;
	}
        if (curParse.osis() == "") {
	  verse = 1;
          chapter++;
	  ref = book + " " + chapter + ":" + verse;
	  if (chapter > end.c || bcv.parse(ref).osis() == "") {
            return verses;
	  }
	}
	verses.push(ref);
        verse++;
      }
      return verses;
    case 'sequence':
      return passage.osis()
                    .split(",")
                    .map(function(p) { return getVerses(p); } )
                    .reduce(function(a,b) { return a.concat(b); })
                    .map(function(v) { return bcv.parse(v).osis(); });
    default:
      return [];
  }
}

function parse() {
      text = document.getElementById("input").value;
      passages = bcv.parse(text).osis();

      xmlHttp = new XMLHttpRequest();
      xmlHttp.open("GET", "application.php?passages=" + passages, false);
      xmlHttp.send(null);
      document.getElementById("output").innerHTML = xmlHttp.responseText;     

      parser = new DOMParser(); 
      versesJSON = {};
      verses = getVerses(passages).forEach( function(v) { v = v.split("."); v[0] = osis2bible[v[0]]; versesJSON[v.join(".")] = 1; }); 

      passagesJSON = getJSON(passages);

      xmlHttp2 = new XMLHttpRequest();
      xmlHttp2.open("GET", "application2.php?passages=" + JSON.stringify(passagesJSON) + "&verses=" + JSON.stringify(versesJSON), false);
      xmlHttp2.send(null); 

      xmlHttp2.addEventListener("progress", function() { document.getElementById("progress").style.display = ""; }, false); 
      xmlHttp2.addEventListener("load", function() { document.getElementById("progress").style.display = "none"; }, false); 
 
      document.getElementById("output").innerHTML += xmlHttp2.responseText;     
}

function selectText(containerid) { 
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));            
	    range.select();        
	} else if (window.getSelection) {            
	    window.getSelection().removeAllRanges();            
	    var range = document.createRange();            
	    range.selectNode(document.getElementById(containerid));            
	    window.getSelection().addRange(range);        
	}    
}

function toggleVerses(translation) {
      verseNums = document.getElementById(translation).getElementsByClassName('versenum');
      Array.prototype.filter.call(verseNums, function(verseNum) { 
        verseNum.style.display = (verseNum.style.display == '' ? 'none' : '');
      });
      verseNums = document.getElementById(translation).getElementsByClassName('label');
      Array.prototype.filter.call(verseNums, function(verseNum) { 
        verseNum.style.display = (verseNum.style.display == '' ? 'none' : '');
      });
}

