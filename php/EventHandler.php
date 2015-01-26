#!/usr/bin/php -q

<?php

include "config.inc.php";
include "lib/AMILibrary.php";
include "lib/phpagi-asmanager.php";

$issue = "Event Handler";

$dblink = mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['pass']);
if(!$dblink) {
  $result = -1;
  mail($config['error_msg']['email'], "[$issue] " . $config['error_msg'][$result]['subject'], mysql_error() . "\n", $config['error_msg']['header']);
  exit -1;
}

if(!mysql_select_db($config['db']['name'])) {
  $result = -1;
  mail($config['error_msg']['email'], "[$issue] " . $config['error_msg'][$result]['subject'], mysql_error() . "\n", $config['error_msg']['header']);
  exit -1;
}

$scriptname = substr($argv[0], 2);

$sql = "SELECT * FROM filestate WHERE filename = '$scriptname'";
$f_res = mysql_query($sql);

if($row = mysql_fetch_object($f_res)) {
  if($row->state == "running") {
    exit -1;
  } else {
    $upd = "UPDATE filestate SET state = 'running'";
    mysql_query($upd);
  }
} else {
  $result = -1;
  mail($config['error_msg']['email'], "[$issue] " . $config['error_msg'][$result]['subject'], mysql_error() . "\n", $config['error_msg']['header']);
  exit -1;
}

$asm = new AGI_AsteriskManager();
$asr = $asm->connect($config['asterisk']['host'], $config['asterisk']['user'], $config['asterisk']['pass']);

if(!$asr) {
  $result = -6;
  mail($config['error_msg']['email'], "[$issue] " . $config['error_msg'][$result]['subject'], $config['error_msg'][$result]['message']);
  exit -6;
}

$evr = $asm->send_request("Events");

$asm->add_event_handler("*", "EventPrint");
$asm->wait_response(true);

$upd = "UPDATE filestate SET state = 'stopped'";
mysql_query($upd);

$asm->disconnect();
mysql_close($dblink);
?>
