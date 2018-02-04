<?php

  require 'html/smc/autoload.php';
  use FiveTechnology\Core\App;

  $q = App::getQuery('member');
  $q->orderBy('random()');
  $q->limit(rand(3, 20));

  $json = new stdClass();
  foreach($q->find() as $member) {
    $item = $member->json();
    //$item = new stdClass();
    //$item->id = $member->id;
    $item->text = $member->first_name . ' ' . $member->last_name;
    $json->results[] = $item;
  }
  echo json_encode($json);