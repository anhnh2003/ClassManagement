<?php
session_start();
//error_reporting(0);
include('includes/dbconnection.php');

function getRandomStringShuffle($length = 16)
{
    $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $stringLength = strlen($stringSpace);
    $string = str_repeat($stringSpace, ceil($length / $stringLength));
    $shuffledString = str_shuffle($string);
    $randomString = substr($shuffledString, 1, $length);
    return $randomString;
}

if (strlen($_SESSION['sturecmsuid']) == 0) {
  header('location:logout.php');
} else {
  if (isset($_POST['submit'])) {
    // $teaid = $_POST['teaid'];
    $room = $_POST['room'];
    $eid = $_GET['editid'];

    // Generate QR code
    include_once('../phpqrcode/qrlib.php');
    $tempDir = 'temp/';
    // $qrContent = "QR Code for attendance in class: " . $cname . " in room: " . $room . " with join code: " . $joincode . " by teacher: " . $row->TeacherName . " at " . date('Y-m-d H:i:s');
    $qrContent = getRandomStringShuffle();
    $qrImgName = "qrImg.png";
    $pngAbsoluteFilePath = $tempDir.$qrImgName;
    QRcode::png($qrContent, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 10);
    // echo "<div style='display: flex; justify-content: center; align-items: center; height: 100vh;'>";
    // echo "<img src='".$pngAbsoluteFilePath."'>";
    // echo "</div>";
    // echo "<div style='display: flex; justify-content: center; align-items: center; height: 100vh;'>";
    // echo "<br>Using shuffle(): " . getRandomStringShuffle();
    // echo "</div>";
    echo "<script>window.open('".$pngAbsoluteFilePath."');</script>";
  }

  if (isset($_POST['regencode'])) {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $joincode = '';
    for ($i = 0; $i < 6; $i++) {
      $index = rand(0, strlen($characters) - 1);
      $joincode .= $characters[$index];
    }
    $eid = $_GET['editid'];
    $sql = "UPDATE tblclass SET JoinCode=:joincode WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':joincode', $joincode, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();
    echo '<script>alert("Join Code has been changed")</script>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Student Management System || Manage Class</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="vendors/select2/select2.min.css">
  <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<div class="container-scroller">
  <!-- partial:partials/_navbar.html -->
  <?php include_once('includes/header.php'); ?>
  <!-- partial -->
  <div class="container-fluid page-body-wrapper">
    <!-- partial:partials/_sidebar.html -->
    <?php include_once('includes/sidebar.php'); ?>
    <!-- partial -->
    <div class="main-panel">
      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title"> Manage Class </h3>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page"> Manage Class</li>
            </ol>
          </nav>
        </div>
        <div class="row">
          <div class="col-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
              <?php
                  $uid = $_SESSION['sturecmsuid'];
                  $eid = $_GET['editid'];
                  $sql = "SELECT * FROM tblclass, tblteacher WHERE teacher_id=:uid AND tblteacher.ID=:uid AND tblclass.ID=$eid";
                  $query = $dbh->prepare($sql);
                  $query->bindParam(':uid',$uid,PDO::PARAM_STR);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  $cnt = 1; 
                  if ($query->rowCount() > 0) {
                    foreach ($results as $row) {
                      ?>
                <h4 class="card-title" style="text-align: center;"> <?php echo htmlentities($row->ClassName); ?> </h4>
                <form class="forms-sample" method="post">
                      <div class="form-group">
                        <label for="exampleInputName1">Room</label>
                        <input type="text" name="room"
                             value="<?php echo htmlentities($row->Room); ?>"
                             class="form-control" required='true', readonly>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputName1">Teacher</label>
                        <input type="text" name="teacher"
                             value="<?php echo htmlentities($row->TeacherName); ?>"
                             class="form-control" required='true', readonly>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputName1">Join Code</label>
                        <input type="text" name="joincode"
                             value="<?php echo htmlentities($row->JoinCode); ?>"
                             class="form-control" required='true' readonly="">
                      </div>
                      <?php $cnt = $cnt + 1;
                    }
                  } ?>
                  <button type="submit" class="btn btn-primary mr-2" name="submit">Create QR Code</button>
                  <button type="submit" class="btn btn-primary mr-2" name="regencode">Change Code
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
      <!-- partial:partials/_footer.html -->
      <?php include_once('includes/footer.php'); ?>
      <!-- partial -->
    </div>
    <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- plugins:js -->
<script src="vendors/js/vendor.bundle.base.js"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="vendors/select2/select2.min.js"></script>
<script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="js/off-canvas.js"></script>
<script src="js/misc.js"></script>
<!-- endinject -->
<!-- Custom js for this page -->
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
<!-- End custom js for this page -->
</body>
</html>