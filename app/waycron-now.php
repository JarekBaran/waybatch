<?php

require_once('config.php');
require_once('WayCron.php');

$wayCronNow = new WayCron();
$wayCronNow->startArchive();

?>
