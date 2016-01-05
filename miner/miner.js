var bcv_parser = require("bible-passage-reference-parser/js/en_bcv_parser").bcv_parser;
var bcv = new bcv_parser;

var request = require('request');
var cheerio = require('cheerio');

for (book_id = 0; book_id < 1; book_id++) {
	book = bcv.translation_info().books[book_id];
	for (chapter_id = 0; chapter_id < bcv.translation_info().chapters[book].length; chapter_id++) {
		for (verse_id = 0; verse_id < bcv.translation_info().chapters[book][chapter_id]; verse_id++) {
			// todo:
			// 1. fetch verse
			// 2. insert into database
			var verse_ref = book + " " + (chapter_id+1) + ":" + (verse_id+1);
//			console.log(verse_ref);
			//    console.log(doc.getElementsByClassName("version-ESV"));
	//		console.log(verse_ref);
			request('https://www.biblegateway.com/passage/?search=' + encodeURIComponent(verse_ref) + '&version=ESV', function (error, response, body) {
			  if (!error && response.statusCode == 200) {
			    //var rePattern = new RegExp("<div class=\"version-ESV result-text-style-normal text-html \">(.+?)<div class=\"publisher-info-bottom.+?\">", "m");
			//    var rePattern = /<div class=\"version-ESV result-text-style-normal text-html \">(.*)<div class=\"publisher/gm;
			    //var rePattern = new RegExp("div");
			 //   console.log(body.match(rePattern));
			    $ = cheerio.load(body);
			    console.log($('.version-ESV .passage-display-bcv').text() + "\n");
			    //console.log($('.version-ESV').html());
			    //console.log('---------');
			    $('.version-ESV .passage-display').remove();
			    $('.version-ESV .versenum').remove();
			    $('.version-ESV .chapternum').remove();
			    $('.version-ESV .crossreference').remove();
			    $('.version-ESV .crossrefs').remove();
			    $('.version-ESV .footnote').remove();
			    $('.version-ESV .footnotes').remove();
			    $('.version-ESV h3').remove();
			    $('.version-ESV h4').remove();
			    console.log($('.version-ESV').html());
//			    console.log(body);
			  }
			});



		}
	}
}


// esvapi key: 3f1bc93eaaabb19b

/*
var pg = require("pg");
var conString = "postgres://rasgflaghhhbnk:AA1EJY0zwj6hVSVt6kUXAsEXS2@ec2-54-83-203-50.compute-1.amazonaws.com:5432/d6aaju7su2bscd?ssl=true";

var client = new pg.Client(conString);
client.connect(function(err) {
  if(err) {
    return console.error('could not connect to postgres', err);
  }
  //client.query('SELECT NOW() AS "theTime"', function(err, result) {
  client.query('SELECT * FROM pg_catalog.pg_tables', function(err, result) {
    if(err) {
      return console.error('error running query', err);
    }
    console.log(result.rows[0].theTime);
    //output: Tue Jan 15 2013 19:12:47 GMT-600 (CST)
    client.end();
  });
});
*/
