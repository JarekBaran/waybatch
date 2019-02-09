<?php

defined("FAILED_LOOPS") or define("FAILED_LOOPS", 2);
defined("FAILED_DELAY") or define("FAILED_DELAY", 3);
define("TODAY", date("Y-m-d"));
define("HOST", '//'.$_SERVER['HTTP_HOST']);
define("PATH", $_SERVER['DOCUMENT_ROOT']);
define("REPORT_EMAIL", 'report@'.$_SERVER['HTTP_HOST']);
define("REPORT_FOLDER", '/reports/');
define("REPORT_FILE", REPORT_FOLDER.'report-'.TODAY.'.txt');
define("REPORT_FILE_ERROR", REPORT_FOLDER.'report-'.TODAY.'-error.txt');
define("REPORT_CRON_FILE", REPORT_FOLDER.'report-cron-'.TODAY.'.txt');
define("REPORT_CRON_FILE_ERROR", REPORT_FOLDER.'report-cron-'.TODAY.'-error.txt');
define("UPLOAD_FOLDER", '/uploads/');
if(defined('RECHECK')){
  define("DOMAINS_FILE", REPORT_FOLDER.'report-cron-'.RECHECK.'-error.txt');
} else {
  define("DOMAINS_FILE", UPLOAD_FOLDER.'domains.txt');
}

?>
