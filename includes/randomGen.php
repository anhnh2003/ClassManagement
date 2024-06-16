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
?>