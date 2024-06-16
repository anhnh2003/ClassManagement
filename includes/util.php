<?php
function randomGen($length = 6) {
  $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';

  for ($i = 0; $i < $length; $i++) {
    $randomByte = openssl_random_pseudo_bytes(1);
    $randomIndex = ord($randomByte) % $charactersLength;
    $randomString .= $characters[$randomIndex];
  }

  return $randomString;
}

function writeLog($logMessage) {
  $logFile = "../sms.log";
  if (!file_exists($logFile)) {
      touch($logFile);
  }
  date_default_timezone_set('Asia/Ho_Chi_Minh');
  $logContent = date("Y-m-d H:i:s") . " UTC+7 - " . $logMessage . "\n";
  file_put_contents($logFile, $logContent, FILE_APPEND);
}
?>