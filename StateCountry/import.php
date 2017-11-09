<?php

/*
  Import country and state data
  http://www.commondatahub.com/live/geography/state_province_region/iso_3166_2_state_codes
*/

require 'lib/dba-i.php';
require 'html/pb/lib/pb_item.class-i.php';

ob_end_flush();

/*
  0 COUNTRY NAME
  1 ISO 3166-2 SUB-DIVISION/STATE CODE
  2 ISO 3166-2 SUBDIVISION/STATE NAME
  3 ISO 3166-2 PRIMARY LEVEL NAME
  4 SUBDIVISION/STATE ALTERNATE NAMES
  5 ISO 3166-2 SUBDIVISION/STATE CODE (WITH *)
  6 SUBDIVISION CDH ID
  7 COUNTRY CDH ID
  8 COUNTRY ISO CHAR 2 CODE
  9 COUNTRY ISO CHAR 3 CODE
*/
$file = 'cdh_download_12_8_2012.txt';

$country = new pb_item($pdb, 'country');
$state = new pb_item($pdb, 'state');

if (($handle = fopen($file, "r")) !== FALSE) {
  $data = fgetcsv($handle, 2400, ",");
  while (($data = fgetcsv($handle, 2400, ",")) !== FALSE) {
    $num = count($data);
    //print_r($data);
    echo "<p> $num fields in line $row: <br /></p>\n";
    $row++;

    list($countryCode, $stateCode) = explode('-', $data[1]);

    // Load country if code chages
    if ($countryCode != $lastCode) {
      $country->reset();
      $country->load("pb_code = '$countryCode'");
      $country->name = $data[0];
      $country->code = $countryCode;
      $country->code_3 = $data[9];
      $lastCode = $countryCode;
    }

    $state->load("pb_code = '$stateCode' AND pb_country_code = '$countryCode'");
    $state->state_name = $data[2];
    $state->code = $stateCode;
    $state->country_code = $countryCode;
    $state->type = $data[3];
    $state->save();

    $country->states = $state->id;
    print_r($country->record);
    $country->save();

    $state->reset();
    for ($c=0; $c < $num; $c++) {
      echo $c . ' ' . $data[$c] . "<br />\n";
    }
  }
  fclose($handle);
}
