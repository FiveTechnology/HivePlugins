/**
 Check the home page, if find links with tid, pull those posts
 */

//var url = require('url');
var cheerio = require('cheerio');
var spider = require('./spider');
var pg = require('pg');

// TODO: Read host from current site or lib/config.json
var host = 'https://www.smumn.edu';

var conn = {};

console.log('Spider: ' + host);

// TODO: Read database configuration from lib/config.json
pg.connect('postgres://httpd:@127.0.0.1/smc_smumn2015', function main(err, client, done) {

  console.log('Connected');

  if (err) {
    console.log(err);
    return;
  }

  conn = client;

  // Each 5 seconds look at forum page and check for new posts (or stickies)
  conn.query('SELECT id, pb_url FROM pb.pb_spider WHERE pb_crawled IS NULL OR NOT pb_crawled', function(err, result) {

    if (err) {
      console.log(err);
      return;
    }

    for (var i=0; i<result.rows.length; i++) {
      console.log('Queuing: ' + result.rows[i].pb_url);
      var id = result.rows[i].id;
      var url = host + result.rows[i].pb_url;
      setTimeout(function(data) {
        console.log('Timer: ' + data);
        spider.getURL(data.url, scrapePage, {id:data.id});
      }, i * 5000, {id: id, url: url});
    }
  });

});

console.log('Connecting...');

function scrapePage(html, data) {
  console.log('Scraping: ' + data.url);

  var $ = cheerio.load(html);

  // We want all <a href="" for now
  $('a[href]').each(function() {
    var href = $(this).attr('href');
    //var parts = url.parse(href, true);
    conn.query('insert into pb.pb_spider (pb_url) SELECT $1 WHERE NOT EXISTS (SELECT id FROM pb.pb_spider WHERE pb_url=$2)', [href, href]);
    conn.query("update pb.pb_spider SET pb_crawled='t' WHERE id=$1", [data.id]);
  });

}
