#!/usr/bin/php -q

<?php

include "config.inc.php";
include "lib/LocalLibrary.php";
include "lib/phpagi-asmanager.php";

$name_camp = $argv[1];
if($name_camp == "") {
  echo "Set a valid Campaign Name.\n";
  exit;
}

$dblink = mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['pass']);
if(!$dblink) {
  $result = -1;
  mail($config['error_msg']['email'], "[$name_camp] " . $config['error_msg'][$result]['subject'], mysql_error() . "\n", $config['error_msg']['header']);
  exit -1;
}

if(!mysql_select_db($config['db']['name'])) {
  $result = -1;
  mail($config['error_msg']['email'], "[$name_camp] " . $config['error_msg'][$result]['subject'], mysql_error() . "\n", $config['error_msg']['header']);
  exit -1;
}

$sel_event = "SELECT * FROM filestate WHERE filename = 'EventHandler.php' AND state = 'running'";
$e_result = mysql_query($sel_event);

if(!$ehandler_running = mysql_fetch_object($e_result)) {
  echo "The Event Handler should be running\n";
  exit;
}

$scriptname = substr($argv[0], 2);

$sel_file = "SELECT * FROM filestate WHERE filename = '$scriptname' AND argument = '$name_camp' AND state = 'running'";
$f_result = mysql_query($sel_file);

if($campaign_running = mysql_fetch_object($f_result)) {
  $result = -2;
  mail($config['error_msg']['email'], "[$name_camp] " . $config['error_msg'][$result]['subject'], $config['error_msg'][$result]['message'], $config['error_msg']['header']);
  exit -2;
} else {
  $upd_file = "INSERT INTO filestate (filename, argument, state) VALUES ('$scriptname', '$name_camp', 'running')";
  mysql_query($upd_file);
}

$now = time('H:m:s');

$sel_camp = "SELECT * FROM campaign WHERE name = '$name_camp'";
$c_result = mysql_query($sel_camp);

if(!$campaign = mysql_fetch_object($c_result)) {
  $result = -3;
  mail($config['error_msg']['email'], "[$name_camp] " . $config['error_msg'][$result]['subject'], $config['error_msg'][$result]['message'], $config['error_msg']['header']);
  $del_file = "DELETE FROM filestate WHERE filename = '$scriptname' AND argument = '$name_camp'";
  mysql_query($del_file);
  exit -3;
} else if($campaign->state != "active") {
  $result = -4;
  mail($config['error_msg']['email'], "[$name_camp] " . $config['error_msg'][$result]['subject'], $config['error_msg'][$result]['message'], $config['error_msg']['header']);
  $del_file = "DELETE FROM filestate WHERE filename = '$scriptname' AND argument = '$name_camp'";
  mysql_query($del_file);
  exit -4;
} else if(compareDate($campaign->starttime, $now) > 0 && compareDate($now, $campaign->endtime) > 0) {
  $result = -5;
  mail($config['error_msg']['email'], "[$name_camp] " . $config['error_msg'][$result]['subject'], $config['error_msg'][$result]['message'], $config['error_msg']['header']);
  $del_file = "DELETE FROM filestate WHERE filename = '$scriptname' AND argument = '$name_camp'";
  mysql_query($del_file);
  exit -5;
}

$asm = new AGI_AsteriskManager();
$asr = $asm->connect($config['asterisk']['host'], $config['asterisk']['user'], $config['asterisk']['pass']);

if(!$asr) {
  $result = -6;
  mail($config['error_msg']['email'], "[$name_camp] " . $config['error_msg'][$result]['subject'], $config['error_msg'][$result]['message'], $config['error_msg']['header']);
  $del_file = "DELETE FROM filestate WHERE filename = '$scriptname' AND argument = '$name_camp'";
  mysql_query($del_file);
  exit -6;
}

$tr_ind = 0;
$trunks = array();

$sel_trunk = "SELECT * FROM trunk_per_campaign WHERE id_campaign =" . $campaign->id;
$t_result = mysql_query($sel_trunk);

while($trunk = mysql_fetch_object($t_result)) {
  $one_trunk = "SELECT * FROM trunk WHERE id = " . $trunk->id_trunk;
  $data_trunk = mysql_query($one_trunk);
  $row_trunk = mysql_fetch_object($data_trunk);

  $trunks[$tr_ind]['name'] = $row_trunk->name;
  $trunks[$tr_ind]['protocol'] = $row_trunk->protocol;

  $tr_ind++;
}

$ls_ind = 0;
$lists = array();

$sel_list = "SELECT * FROM list_per_campaign WHERE id_campaign =" . $campaign->id;
$l_result = mysql_query($sel_list);

while($list = mysql_fetch_object($l_result)) {
  $one_list = "SELECT * FROM list WHERE id = " . $list->id_list;
  $data_list = mysql_query($one_list);
  $row_list = mysql_fetch_object($data_list);

  $lists[$ls_ind++]['index'] = $row_list->id;
}

$n_lists = count($lists);
$n_trunks = count($trunks);

for($i = 0; $i < $n_lists; $i++) {
  $sel_phones = "SELECT * FROM phone WHERE id_list = " . $lists[$i]['index'];
  $p_result = mysql_query($sel_phones);
  //$n_phones = mysql_num_rows($p_result);

  for($i = 0; $i < $n_trunks; $i++) {
    if($no_channels) {
      // TODO: Verificar si hay canales disponibles.
      sleep(1);
    }

    if($phone = mysql_fetch_object($p_result)) {
      // TODO: Verificar y Realizar llamada.
      continue;
    } else {
      break;
    }
  }
}

$del_file = "DELETE FROM filestate WHERE filename = '$scriptname' AND argument = '$name_camp'";
mysql_query($del_file);

$asm->disconnect();
mysql_close($dblink);
?>
