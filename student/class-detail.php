<?php
include('../includes/studentVerify.php');
require '../includes/util.php';
// Check if the student is in the class
$eid = $_GET['editid'];
$uid = $_SESSION['sturecmsuid'];
$sql = "SELECT * FROM tblstudent_class WHERE student_id=:uid AND class_id=:eid";
$query = $dbh->prepare($sql);
$query->bindParam(':eid', $eid, PDO::PARAM_STR);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->execute();
if ($query->rowCount() == 0) {
  header('location:manage-class.php');
  exit();
}

if (isset($_POST['leave'])) {
  $sql = "DELETE FROM tblstudent_class WHERE student_id=:uid AND class_id=:eid";
  $query = $dbh->prepare($sql);
  $query->bindParam(':eid', $eid, PDO::PARAM_STR);
  $query->bindParam(':uid', $uid, PDO::PARAM_STR);
  $query->execute();
  echo "<script>alert('You have left the class');</script>";
  writeLog("Student #" . $uid . " - Left class #" . $eid . ".");
  echo "<script>window.location.href = 'manage-class.php'</script>";
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
                  $sql = "SELECT * FROM tblclass, tblteacher WHERE tblclass.ID=:eid AND teacher_id=tblteacher.ID";
                  $query = $dbh->prepare($sql);
                  $query->bindParam(':eid', $eid, PDO::PARAM_STR);
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
                          <input type="text" name="room" value="<?php echo htmlentities($row->Room); ?>" class="form-control" required='true' readonly>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputName1">Teacher</label>
                          <input type="text" name="teacher" value="<?php echo htmlentities($row->TeacherName); ?>" class="form-control" required='true' readonly>
                        </div>
                        <div class="text-right">
                          <button type="submit" class="btn btn-primary mr-2" name="leave" style="background-color: red; border-color: red;">Leave Class</button>
                        </div>
                        <?php $cnt = $cnt + 1;
                    }
                  } ?>
                      </form>
                </div>
              </div>

            </div>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-sm-flex align-items-center mb-4">
                    <h4 class="card-title mb-sm-0">Attendances</h4>
                    <a href="#" class="text-dark ml-auto mb-3 mb-sm-0"> </a>
                  </div>
                  <div class="table-responsive border rounded p-1">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="font-weight-bold">No.</th>
                          <th class="font-weight-bold">Time</th>
                          <th class="font-weight-bold">Status</th>
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
                        $eid = $_GET['editid'];
                        $uid = $_SESSION['sturecmsuid'];
                        $sql = "SELECT ID, CreationTime, sa.student_id FROM tblattendance LEFT JOIN (SELECT student_id, attendance_id  FROM tblstudent_attendance WHERE student_id=:uid) sa ON sa.attendance_id=ID WHERE class_id=:eid ORDER BY CreationTime ASC;";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                        $cnt = 1;
                        if ($query->rowCount() > 0) {
                          foreach ($results as $row) {
                        ?>
                            <tr>
                              <td><?php echo htmlentities($cnt); ?></td>
                              <td><?php echo htmlentities($row->CreationTime); ?></td>
                              <td>
                                <?php
                                if ($row->student_id != Null) {
                                  echo '<label class="badge badge-success"style="font-size: 13px;">Attended</label>';
                                } else {
                                  echo '<label class="badge badge-danger" style="font-size: 13px;">Absent</label>';
                                }
                                ?>
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
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-sm-flex align-items-center mb-4">
                    <h4 class="card-title mb-sm-0">Tests</h4>
                    <a href="#" class="text-dark ml-auto mb-3 mb-sm-0"> </a>
                  </div>
                  <div class="table-responsive border rounded p-1">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="font-weight-bold">No.</th>
                          <th class="font-weight-bold">Title</th>
                          <th class="font-weight-bold">Start Time</th>
                          <th class="font-weight-bold">Submitted</th>
                          <th class="font-weight-bold">Point</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php

                        $eid = $_GET['editid'];
                        $uid = $_SESSION['sturecmsuid'];
                        $sql = "SELECT t.*, st.SubmitTime, st.TotalPoint from tbltest t LEFT JOIN (SELECT * FROM tblstudent_test WHERE student_id=:uid) st ON test_id=ID WHERE class_id=:eid ORDER BY StartTime ASC;";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                        $cnt = 1;
                        if ($query->rowCount() > 0) {
                          foreach ($results as $row) {
                        ?>
                            <tr>
                              <td><?php echo htmlentities($cnt); ?></td>
                              <td><a href="test-detail.php?editid=<?php echo htmlentities($row->ID); ?>"><?php echo htmlentities($row->TestName); ?></a></td>
                              <td><?php echo htmlentities($row->StartTime); ?></td>
                              <td><?php if ($row->SubmitTime != Null) {
                                    echo htmlentities($row->SubmitTime);
                                  } else {
                                    echo "N/A";
                                  } ?></td>
                              <td><?php if ($row->TotalPoint != Null) {
                                    echo htmlentities($row->TotalPoint);
                                  } else {
                                    echo "N/A";
                                  } ?></td>
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