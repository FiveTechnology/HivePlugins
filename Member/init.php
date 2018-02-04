<?php

  require 'html/smc/autoload.php';
  use FiveTechnology\Core\App;
  use FiveTechnology\Core\pb_plugin;
  use FiveTechnology\Core\SmcPage;
  use FiveTechnology\Core\SmcTemplate;

  $parentPage = new SmcPage(App::getDB(), 'smc_page', 0);
  $pageId = $parentPage->insert($asChild=true, 'Members');

  $pageTemplate = new SmcTemplate(App::getDb(), 'smc_template');
  $pageTemplate->content = file_get_contents('templates/page.tpl');
  $pageTemplate->newTemplate('Member Text');

  header("Content-type: text/plain");
  $plugin = new pb_plugin('Member', $verify=true);
  $plugin->processYaml(file_get_contents('schema.yml'));

  if (($handle = fopen("members.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $member = App::getItem('member', (int) $data[0]);
      $member->first_name = $data[1];
      $member->last_name = $data[2];
      $member->email = $data[3];
      $member->gender = $data[4];
      $member->ip_address = $data[4];
      $member->text = $data[1] . ' ' . $data[2];
      $member->save();
    }
    fclose($handle);
  }