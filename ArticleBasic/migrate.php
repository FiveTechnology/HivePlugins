<?php

  require 'html/smc/autoload.php';
  use FiveTechnology\Core\App;
  use FiveTechnology\Core\pb_plugin;

  header("Content-type: text/plain");

  $plugin = new pb_plugin('Article', $verify=true);
  $plugin->processYaml(file_get_contents('schema.yml'));

  $db = App::getDb();
  $sql = "SELECT * FROM tblarticles";
  $articles = $db->getAssoc($sql);

  foreach($articles as $article) {
    $item = App::getItem('article', $article['articleid']);
    $item->id = $article['articleid'];
    $item->title = $article['arttitle'];
    $item->body = $article['artintro'];
    $item->created_on = $article['artdate'];
    $item->show = $article['artshow'] == 't' ? 1 : 0;
    $item->show_order = $article['artshoworder'];
    $sql = "SELECT pageid FROM tblartpage WHERE articleid=?";
    $pages = $db->getCol($sql, $item->id);
    $item->smc_associated_pages = $pages;
    $item->save();
    echo $article['arttitle'] . "\n";
  }

  $sql = "SELECT setval('pb.pb_article_id_seq', (SELECT max(id) FROM pb.pb_article));";
  $db->execute($sql);
