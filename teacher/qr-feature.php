<?php
include_once('../phpqrcode/qrlib.php');
$path = 'images/';
$qrcode = $path.time().".png";
QRcode::png("QR code test", $qrcode);
echo "<img src='".$qrcode."'>";
?>