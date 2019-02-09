<?php require_once('WebArchive.php');

class WayCron extends WebArchive {

  private $domains = array();
  private $failed = array();
  private $failedCounter = 0;
  private $savedCounter = 0;

  function __construct(){
    echo "\n Loading domains from a file: ".DOMAINS_FILE."\n";
    foreach(file(PATH.DOMAINS_FILE, FILE_IGNORE_NEW_LINES) as $key => $line) {
      $domain = idn_to_ascii($line);
      if(!in_array($domain, $this->domains)) {
         $this->domains[] = $domain;
      } else {
        echo "\n Duplicate found: [".$key."] ".$line;
      }
    }
    echo "\n\n Domains for archiving: ".count($this->domains)."\n "; print_r($this->domains); echo "\n";
  }

  public function startArchive(){
    foreach($this->domains as $domain){
      $this->saveUrl($domain);
      usleep(100000);
    }
    $this->checkFailed();
  }

  private function saveUrl($url){
    try {
      $saved = $this->save($url);
      file_put_contents(PATH.REPORT_CRON_FILE, $saved.PHP_EOL , FILE_APPEND | LOCK_EX);
      echo "\n -- Domain saved: [".(++$this->savedCounter)."] -> ".$saved;
    }
    catch(Exception $e) {
      $this->failed[] = $url;
      echo "\n -- Saving error -> ".$url;
    }
  }

  private function checkFailed(){
    if(!empty($this->failed)) {
      for($x=0; $x<=FAILED_LOOPS; $x++) {
        echo "\n\n I repeat unsaved domains - approach ".($x+1)."\n";
        foreach($this->failed as $fail){
          array_shift($this->failed);
          $this->saveUrl($fail);
          sleep(FAILED_DELAY);
        }
        sleep(FAILED_DELAY*10);
      }
      file_put_contents(PATH.REPORT_CRON_FILE_ERROR, implode(PHP_EOL, $this->failed).PHP_EOL, FILE_APPEND | LOCK_EX);
      $this->failedCounter = count($this->failed);
      echo "\n\n Domains that could not be archived - ".$this->failedCounter."\n"; print_r($this->failed); echo "\n";
    }
    $this->sendReport();
  }

  private function sendReport(){
    $to      = REPORT_EMAIL;
    $headers = "From: ".REPORT_EMAIL."\r\n"."Reply-To: ".REPORT_EMAIL."\r\n";
    $subject = "The report was generated ".TODAY." - ".$this->savedCounter."/".$this->failedCounter;
    $message = $subject."\r\n";
    if($this->savedCounter){$message .= "Saved ".$this->savedCounter." - ".HOST.REPORT_CRON_FILE."\r\n";}
    if($this->failedCounter){$message .= "Failed ".$this->failedCounter." - ".HOST.REPORT_CRON_FILE_ERROR;}
    mail($to, $subject, $message, $headers);
  }
}

?>
