<?php
session_start();
include('includes/dbconnection.php');

// Check if the user is logged in and the session variables are set
if (strlen($_SESSION['sturecmsstuid']) == 0) {
  header('location:logout.php');
  exit();
} else {
  // Retrieve the 'uid' and 'session_token' cookies
  $uid = $_COOKIE['uid'] ?? '';
  $sessionToken = $_COOKIE['session_token'] ?? '';

  // Prepare the SQL statement to select the token from the database
  $sql = "SELECT UserToken, role_id FROM tbltoken WHERE UserID = :uid AND UserToken = :sessionToken AND (CreationTime + INTERVAL 2 HOUR) >= NOW()";
  $query = $dbh->prepare($sql);
  $query->bindParam(':uid', $uid, PDO::PARAM_INT);
  $query->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
  $query->execute();
  $role_id = $query->fetch(PDO::FETCH_OBJ)->role_id;

  // Check if the token exists and is not expired
  if (($query->rowCount() == 0) || ($role_id != 2)) {
    // Token is invalid or expired, redirect to logout
    header('location:logout.php');
    exit();
  }
}

function getRandomStringShuffle($length = 43)
{
  $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $stringLength = strlen($stringSpace);
  $string = str_repeat($stringSpace, ceil($length / $stringLength));
  $shuffledString = str_shuffle($string);
  $randomString = substr($shuffledString, 1, $length);
  return $randomString;
}

if ((strlen($_SESSION['sturecmsuid']) == 0) || (strlen($_COOKIE['uid']) == 0) || (strlen($_COOKIE['session_token']) == 0)) {
  header('location:logout.php');
  exit();
} else {
  $uid = $_COOKIE['uid'] ?? '';
  $eid = $_GET['editid'];

  // Check if attendance from a class belongs to the teacher in tblclass and check the teacher has a valid token in tbltoken
  $sql = "SELECT * FROM tblattendance, tblclass, tblteacher, tbltoken WHERE tblattendance.ID=:eid and teacher_id=:uid AND tblteacher.ID=:uid AND class_id=tblclass.ID AND tbltoken.UserID=:uid AND tbltoken.UserToken=:sessionToken AND (tbltoken.CreationTime + INTERVAL 2 HOUR) >= NOW()";
  $query = $dbh->prepare($sql);
  $query->bindParam(':uid', $uid, PDO::PARAM_STR);
  $query->bindParam(':eid', $eid, PDO::PARAM_STR);
  $query->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);

  if ($query->rowCount() == 0) {
    header('location:manage-class.php');
    exit();
  }

  $pngAbsoluteFilePath = '';

  if (isset($_POST['genqr'])) {
    // Generate QR code
    include_once('../lib/phpqrcode/qrlib.php');
    $tempDir = 'temp/';

    // Clean /temp folder
    $tempDir = 'temp/';
    $files = glob($tempDir . '*.png');
    if (count($files) > 5) {
      foreach ($files as $file) {
        if (is_file($file)) {
          unlink($file);
        }
      }
    }

    // Generate QR code
    $qrContent = getRandomStringShuffle();
    $qrImgName = "qr" . getRandomStringShuffle(10) . ".png";
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $gentime = date('Y-m-d H:i:s');
    $pngAbsoluteFilePath = $tempDir . $qrImgName;
    QRcode::png($qrContent, $pngAbsoluteFilePath, QR_ECLEVEL_L, 15, 2);

    // Update the QR code and the last generated time in the database
    $sql = "UPDATE tblattendance SET Secret=:qrContent, LastGeneratedTime=:gentime WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->bindParam(':qrContent', $qrContent, PDO::PARAM_STR);
    $query->bindParam(':gentime', $gentime, PDO::PARAM_STR);
    $query->execute();
  }

  if (isset($_POST['update_ttl'])) {
    // Update the TimeToLive of QR
    $eid = $_GET['editid'];
    $ttl = $_POST['ttl'];
    $sql = "UPDATE tblattendance SET TimeToLive=:ttl WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->bindParam(':ttl', $ttl, PDO::PARAM_STR);
    $query->execute();
  }

  if (isset($_POST['toggle_attendance'])) {
    $sid = $_POST['student_id'];
    $isAttended = $_POST['isAttended'];
    $aid = $_GET['editid'];

    if ($isAttended == null) {
      // Insert the attendance
      $sql = "INSERT INTO tblstudent_attendance (student_id, attendance_id) VALUES (:sid, :aid)";
      $query = $dbh->prepare($sql);
      $query->bindParam(':sid', $sid, PDO::PARAM_STR);
      $query->bindParam(':aid', $aid, PDO::PARAM_STR);
      $query->execute();
    } else {
      // Delete the attendance
      $sql = "DELETE FROM tblstudent_attendance WHERE student_id=:sid AND attendance_id=:aid";
      $query = $dbh->prepare($sql);
      $query->bindParam(':sid', $sid, PDO::PARAM_STR);
      $query->bindParam(':aid', $aid, PDO::PARAM_STR);
      $query->execute();
    }
  }

  if (isset($_POST['stopqr'])) {
    // Stop the QR code
    $sql = "UPDATE tblattendance SET Secret=NULL, LastGeneratedTime=NULL WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Student Management System || Manage Attendance</title>
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
  <link rel="stylesheet" href="css/style.css" />
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
            <h3 class="page-title"> Manage Attendance </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="class-detail.php?editid=<?php echo $_GET['classid']; ?>">Class Details</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Attendance Details</li>
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
                  $sql = "SELECT * FROM tblattendance, tblclass WHERE tblattendance.ID=:eid and tblattendance.class_id=tblclass.ID";
                  $query = $dbh->prepare($sql);
                  $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  $cnt = 1;
                  if ($query->rowCount() > 0) {
                    foreach ($results as $row) {
                  ?>
                      <h4 class="card-title" style="text-align: center;"> Create Attendance QR </h4>
                      <form class="forms-sample" method="post">
                        <div class="form-group">
                          <label for="exampleInputName1">Class</label>
                          <input type="text" name="class" value="<?php echo htmlentities($row->ClassName); ?>" class="form-control" required='true' readonly>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputName1">Creation Time</label>
                          <input type="text" name="ctime" value="<?php echo htmlentities($row->CreationTime); ?>" class="form-control" required='true' readonly>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputName1">QR Valid Time <i>(seconds)</i></label>
                          <input type="text" name="ttl" value="<?php echo htmlentities($row->TimeToLive); ?>" class="form-control" required='true' pattern="[0-9]+">
                        </div>
                        <?php $cnt = $cnt + 1;
                      }
                    } ?>
                    <button type="submit" class="btn btn-primary mr-2" name="update_ttl">Update</button>
                    <button type="submit" class="btn btn-primary mr-2" name="genqr">Generate QR</button>
                    <?php
                    if (file_exists($pngAbsoluteFilePath)) {
                      echo '<button type="stop" class="btn btn-danger mr-2" name="stopqr">Stop Now</button>';
                    }
                    ?>
                  </form>
                  <?php
                  // Check if the QR image exists
                  if (file_exists($pngAbsoluteFilePath)) {
                    echo '<img src="' . $pngAbsoluteFilePath . '" alt="QR Code">';
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-sm-flex align-items-center mb-4">
                    <h4 class="card-title mb-sm-0">Students</h4>
                    <a href="#" class="text-dark ml-auto mb-3 mb-sm-0"> </a>
                  </div>
                  <div class="table-responsive border rounded p-1">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="font-weight-bold">No.</th>
                          <th class="font-weight-bold">Name</th>
                          <th class="font-weight-bold">ID</th>
                          <th class="font-weight-bold">Attended</th>
                          <th class="font-weight-bold">Time</th>
                          <th class="font-weight-bold">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if (isset($_GET['pageno'])) {
                          $pageno = $_GET['pageno'];
                        } else {
                          $pageno = 1;
                        }

                        // Formula for pagination
                        $sql = "SELECT tb.*, sa.AttendanceTime FROM (SELECT s.ID, s.StudentName, s.StuID, a.ID aid FROM tblattendance a, tblstudent_class sc, tblstudent s WHERE a.ID=:eid and a.class_id=sc.class_id and sc.student_id=s.ID) tb LEFT JOIN tblstudent_attendance sa ON sa.student_id = tb.ID and sa.attendance_id = tb.aid ORDER BY StudentName";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                        $cnt = 1;
                        if ($query->rowCount() > 0) {
                          foreach ($results as $row) {
                        ?>
                            <tr>
                              <td><?php echo htmlentities($cnt); ?></td>
                              <td><?php echo htmlentities($row->StudentName); ?></td>
                              <td><?php echo htmlentities($row->StuID); ?></td>
                              <td>
                                <?php
                                if ($row->AttendanceTime != null) {
                                  echo '<label class="badge badge-success"style="width: 30pt; height: 15pt; font-size: 12px;">Yes</label>';
                                } else {
                                  echo '<label class="badge badge-danger" style="width: 30pt; height: 15pt; font-size: 12px;">No</label>';
                                }
                                ?>
                              </td>
                              <td>
                                <?php
                                if ($row->AttendanceTime != null) {
                                  echo htmlentities($row->AttendanceTime);
                                } else {
                                  echo 'N/A';
                                }
                                ?>
                              </td>
                              <td>
                                <form class="forms-sample" method="post">
                                  <input type="hidden" name="student_id" value="<?php echo htmlentities($row->ID); ?>">
                                  <input type="hidden" name="isAttended" value="<?php echo htmlentities($row->AttendanceTime); ?>">
                                  <button type="submit" class="btn btn-sm btn-primary mr-2" name="toggle_attendance" style="width: 70pt;">Toggle</button>
                                </form>
                              </td>
                            </tr>
                        <?php $cnt = $cnt + 1;
                          }
                        } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->

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