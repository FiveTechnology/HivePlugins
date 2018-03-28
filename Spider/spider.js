
var request = require('request');
var jar = request.jar();

/*
 var cookie = request.cookie('password=C6E5E278ECDCE42F8BC3E08E029C37A0EF2C7F9A');
 jar.setCookie(cookie, 'http://www.yamaha-triples.org');

 var cookie = request.cookie('bbsmid=5042');
 jar.setCookie(cookie, 'http://www.yamaha-triples.org');
 */

module.exports = {

  jar: jar,

  getURL: function(url, done, data) {
    console.log('getURL: ' + url);
    data.url = url;

    request({url: url, jar: jar, followRedirect: false}, function (error, response, html) {
      if (error) {
        console.log(error);
        return;
      }

      if (response.statusCode != 200) {
        console.log('Error Code: ' + response.statusCode);
        return;
      }

      done(html, data);
    });
  }

};
