<?php

define("RECHECK", date("Y-m-d", time()-86400));
define("FAILED_LOOPS", 13);
define("FAILED_DELAY", 9);

require_once('config.php');
require_once('WayCron.php');

$wayCronReCheck = new WayCron();
$wayCronReCheck->startArchive();

?>
