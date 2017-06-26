<?php
  require 'html/smc/autoload.php';
  use FiveTechnology\Core\App;

  $item = App::getItem('agenda_item', $_REQUEST['id']);

  // Make sure an item was loaded
  if ($item->newRecord) {
    exit(App::getJSON(false, 'Item not found'));
  }

  $item->delete();
  App::getJSON(true);
