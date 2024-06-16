<?php
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function sendEmail($Name, $OTP, $Email, $type = 'login') {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.gmail.com';
    $mail->Post = 587;
    $mail->isHTML(true);
    $mail->Username = $_ENV['GMAIL_SENDER'];
    $mail->Password = $_ENV['GMAIL_PASSWORD'];

    $mail->setFrom($_ENV['GMAIL_SENDER']);
    $mail->addAddress($Email, 'User');

    if ($type == 'login') {
        $subject = 'Two Factor Login OTP - Student Management System';
        $proc = 'Login';
    } else {
        $subject = 'Reset Password OTP - Student Management System';
        $proc = 'Reset Password';
    }

    $mail->Subject = $subject;
    $mail->Body = '<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2"> <div style="margin:50px auto;width:70%;padding:20px 0"> <div style="border-bottom:1px solid #eee"> <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">Student Management System</a> </div> <p style="font-size:1.1em">Hi, '. $Name .'</p> <p>Use the following OTP to complete your ' . $proc. ' procedures.</p> <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">'. $OTP .'</h2> <hr style="border:none;border-top:1px solid #eee" /> <p style="font-size:0.9em;"><em>If this is not you, please do not share this OTP.</em></p> </div> </div>';
    if (!$mail->send()) {
        echo "<script>alert('" . $mail->ErrorInfo ."');</script>";
    }
    $mail->smtpClose();
}
?>