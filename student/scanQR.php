<?php
include('../includes/dbconnection.php');
include('../includes/studentVerify.php');
if (isset($_POST['imgData'])) {
    $imgData = $_POST['imgData'];
    $uid = $_POST['uid'];

    // Remove the header of the data URL
    $imgData = str_replace('data:image/png;base64,', '', $imgData);
    $imgData = str_replace(' ', '+', $imgData);

    // Decode the base64 data
    $imgData = base64_decode($imgData);

    // Write the data to a file in the student/temp directory
    if (!is_dir("temp")) {
        mkdir("temp");
    }
    $filePath = "temp/photo".rand(100000,999999).".png";
    file_put_contents($filePath, $imgData);
    
    // Decode to text
    require "../vendor/autoload.php";
    $qrcode = new Zxing\QrReader($filePath);
    $text = $qrcode->text();
    unlink($filePath);

    // Check if the QR code is valid
    if ($text) {
        $sql = "SELECT ID, LastGeneratedTime, TimeToLive FROM tblattendance WHERE Secret=:text";
        $query = $dbh->prepare($sql);
        $query->bindParam(':text', $text, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($query->rowCount() == 1) {
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $current_time = date('Y-m-d H:i:s');
            $last_generated_time = strtotime($result->LastGeneratedTime);
            $time_difference = strtotime($current_time) - $last_generated_time;

            if ($time_difference > $result->TimeToLive) {
                echo -1;
            } else {
                $aid = $result->ID;
                $sql = "SELECT * FROM tblstudent_attendance WHERE student_id = :uid AND attendance_id = :aid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                $query->bindParam(':aid', $aid, PDO::PARAM_STR);
                $query->execute();

                if ($query->rowCount() > 0) {
                    echo -2;
                } else {
                    $sql = "INSERT INTO tblstudent_attendance(student_id, attendance_id) VALUES(:uid, :aid)";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                    $query->bindParam(':aid', $aid, PDO::PARAM_STR);
                    $query->execute();
                    echo 1;
                }
            }
        } else {
            echo -1;
        }
    } else {
        echo 0;
    }
}
?>