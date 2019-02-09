<?php require_once('config.php');

if(isset($_GET['url'])) {
  $url = $_GET['url'];

  require_once('WebArchive.php');

  $webArchive = new WebArchive();

  try {
    $saved = $webArchive->save($url);

    header('Content-type: application/json');
    echo json_encode($saved);

    @file_put_contents(PATH.REPORT_FILE, $saved.PHP_EOL , FILE_APPEND | LOCK_EX);
  }

  catch(Exception $e) {
    @file_put_contents(PATH.REPORT_FILE_ERROR, $url.PHP_EOL, FILE_APPEND | LOCK_EX);
  }

} elseif(isset($_GET['reports'])) {
    $reports = array();

    foreach(glob('..'.REPORT_FOLDER.'{*.txt}', GLOB_BRACE) as $reportPath){
      $reportFile = basename($reportPath);
      $reports[$reportFile] = HOST.REPORT_FOLDER.$reportFile;
    }

    header('Content-type: application/json');
    echo json_encode($reports);

} elseif(isset($_POST['cron']) && isset($_POST['token'])) {
    if($_POST['token'] == date('dmY')) {
      try {
        @file_put_contents(PATH.DOMAINS_FILE, $_POST['cron'].PHP_EOL , LOCK_EX);
        echo 'Domains added to CRON -- '.count(file(PATH.DOMAINS_FILE));
      }

      catch(Exception $e) {
        echo 'FAIL: '.$e;
      }
    }

} else exit();

?>
