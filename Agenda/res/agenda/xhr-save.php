<?php

  /*
    Save data passed from ckeditor agenda template
  */
  require 'html/smc/autoload.php';
  use FiveTechnology\Core\App;

  $item = App::getItem('agenda_item', $_REQUEST['id']);

  /*
    New item is based on insert being sibling or child.  Depending on that
    value we use sortid (00000001,00000081,00000085) to identify agenda
    or parent item.

    Then after insert we update left/right values
  */
  if ($item->newRecord) {
    if ($_REQUEST['parent'] == 'undefined') {
      $item->agenda = $_REQUEST['agenda'];
    } else {
      $item->parent_item = $_REQUEST['parent'];
    }

    $item->sort_order = $_REQUEST['order'];
    $item->save();

    // re-order the sort values for all siblings
    $sql = "update pb.pb_agenda_item set pb_sort_order = t.rn from 
      (select id, pb_parent_item, row_number() over (order by pb_sort_order) as rn from pb.pb_agenda_item WHERE pb_parent_item = '{" . $item->parent_item->value . "}') t 
      where t.id = pb_agenda_item.id";
    App::getDb()->execute($sql);
  }

  /**
   * nodeType is one of title, note, or motion
   */
  if ($_REQUEST['nodeType'] == 'title') {
    $item->item_text = $_REQUEST['editabledata'];
    $item->save();
  }

  /*
    If $nodeType is note we are just updating the items' note field
  */
  if ($_REQUEST['nodeType'] == 'note' or $_REQUEST['nodeType'] == 'motion') {
    $item->{$_REQUEST['nodeType']} = $_REQUEST['editabledata'];
    $item->save();
  }

  echo json_encode($item->toJSON());
