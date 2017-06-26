<?php
  /*
    Make a copy of the agenda, mark it as hidden and goto the mange interface
  */
  require $_SERVER["DOCUMENT_ROOT"] . "/pb/lib/pb_xml.class-i.php";
  require 'lib/dba-i.php';

  $agendaId = $_GET['id'];

  $dump = new pb_xml_dump($pdb);
  $dump->process_table('pb_agenda', $agendaId);

  // To view the XML
  #header("Content-Type: text/xml");
  #echo $dump->output_xml();
  #exit;

  $restore = new pb_xml_restore($pdb, $dump->output_xml());
  $restore->cleanFiles = true;
  $restore->pass_one();
  $restore->pass_two();

  // We now have a new id
  $agendaId = $restore->plugins['pb_agenda'][$agendaId];


  /* Set Agenda to not be a template when copied, make it hidden, and make sure
  it does not associate to a live page */
  $record = new pb_item($pdb, 'agenda', "id=$agendaId");
  $record->template = 0;
  $record->show = 0;
  $record->minutes = 0;
  /*$record->smc_associated_pages = 46;*/
  $record->save();

  // Got the manage page
  header("Location: /pb/plugin/plugin.php?template=default_add_edit&lcommtypeid=11&item_id=$agendaId&copy=true");
