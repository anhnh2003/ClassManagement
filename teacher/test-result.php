<?php
include('../includes/teacherVerify.php');
include('../includes/updateScore.php');
include('../includes/util.php');

$eid = $_GET['editid'];
$uid = $_COOKIE['uid'] ?? '';

// Check if teacher has access to this test
$sql = "SELECT * FROM tbltest, tblclass, tblteacher, tbltoken WHERE tbltest.ID=:eid and teacher_id=:uid AND tblteacher.ID=:uid AND tblclass.ID=tbltest.class_id AND tbltoken.UserID=:uid AND tbltoken.UserToken=:sessionToken AND (tbltoken.CreationTime + INTERVAL 2 HOUR) >= NOW()";
$query = $dbh->prepare($sql);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->bindParam(':eid', $eid, PDO::PARAM_STR);
$query->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

if ($query->rowCount() == 0) {
  header('location:manage-test.php');
  exit();
}

if (isset($_POST['rescore'])) {
  $sql = "SELECT student_id FROM tblstudent_test WHERE test_id=:eid AND TotalPoint is not null";
  $query = $dbh->prepare($sql);
  $query->bindParam(':eid', $eid, PDO::PARAM_STR);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);
  foreach ($results as $row) {
    updateTestPoint($dbh, $row->student_id, $eid, $updateSubmitTime = false);
  }
  writeLog("Test #" . $eid ." - Score recalculated by Teacher #" . $uid);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Student Management System || Test Result</title>
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
            <h3 class="page-title"> Manage Test </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="test-detail.php?editid=<?php echo htmlentities($eid); ?>">Test Details</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Test Result</li>
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
                  $sql = "SELECT tbltest.*, ClassName, Room FROM tblclass, tbltest WHERE class_id=tblclass.ID AND tbltest.ID=:eid";
                  $query = $dbh->prepare($sql);
                  $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  $cnt = 1;
                  if ($query->rowCount() > 0) {
                    foreach ($results as $row) {
                  ?>
                      <h4 class="card-title" style="text-align: center;"> <?php echo htmlentities($row->TestName); ?> | Result </h4>
                  <?php
                      $cnt = $cnt + 1;
                    }
                  }
                  ?>
                  <?php
                  $eid = $_GET['editid'];
                  $sql = "SELECT TotalPoint, StartTime, SubmitTime, s.StudentName, s.StuID FROM tblstudent_test st, tblstudent s WHERE student_id=s.ID AND test_id=:eid";
                  $query = $dbh->prepare($sql);
                  $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  ?>
                  <div class="form-group"><label for="text">Total Submitted: <b><?php
                                                $sql1 = "SELECT COUNT(*) as total FROM tblstudent_test WHERE test_id=:eid and SubmitTime is not null";
                                                $query1 = $dbh->prepare($sql1);
                                                $query1->bindParam(':eid', $eid, PDO::PARAM_STR);
                                                $query1->execute();
                                                $results = $query1->fetchAll(PDO::FETCH_OBJ);
                                                echo htmlentities($results[0]->total);
                                                ?></b></label></div>
                  <div class="form-group"><label for="text2">Average Score: <b><?php
                                                $sql1 = "SELECT ROUND(AVG(TotalPoint),2) as avg FROM tblstudent_test WHERE test_id=:eid and SubmitTime is not null";
                                                $query1 = $dbh->prepare($sql1);
                                                $query1->bindParam(':eid', $eid, PDO::PARAM_STR);
                                                $query1->execute();
                                                $results = $query1->fetchAll(PDO::FETCH_OBJ);
                                                $avg = $results[0]->avg;
                                                if ($avg == Null) {
                                                  echo htmlentities('N/A');
                                                } else {
                                                  echo htmlentities($avg);
                                                }
                                                ?></b></label></div>
                  <div class="table-responsive border rounded p-1">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="font-weight-bold">No.</th>
                          <th class="font-weight-bold">Name</th>
                          <th class="font-weight-bold">ID</th>
                          <th class="font-weight-bold">Score</th>
                          <th class="font-weight-bold">Start Time</th>
                          <th class="font-weight-bold">Submit Time</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $eid = $_GET['editid'];
                        $sql = "SELECT TotalPoint, StartTime, SubmitTime, s.StudentName, s.StuID FROM tblstudent_test st, tblstudent s WHERE student_id=s.ID AND test_id=:eid ORDER BY StartTime ASC";
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
                              <td><?php
                                if ($row->SubmitTime != Null) {
                                  echo htmlentities($row->TotalPoint);
                                } else {
                                  echo '<label class="badge badge-secondary">N/A</label>';
                                } ?></td>
                              <td><?php echo htmlentities($row->StartTime); ?></td>
                              <td><?php
                                if ($row->SubmitTime != Null) {
                                  echo htmlentities($row->SubmitTime);
                                } else {
                                  echo '<label class="badge badge-danger">On Going</label>';
                                } ?></td>
                            </tr>
                        <?php
                            $cnt = $cnt + 1;
                          }
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                      </form>
                    <form class="forms-sample" method="post">
                    <div class="form-group">
                    </div>
                    <button type="submit" class="btn btn-primary mr-2" name="rescore">Recalculate Scores</button>
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
                  <h4 class="card-title mb-sm-0">Questions</h4>
                </div>
                <div class="table-responsive border rounded p-1">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="font-weight-bold">ID</th>
                        <th class="font-weight-bold">Point</th>
                        <th class="font-weight-bold">Correct %</th>
                        <th class="font-weight-bold">Submitted</th>
                        <th class="font-weight-bold">Question</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT tq.*, q.Question, q.CorrectAns FROM tbltest_question tq, tblquestion q WHERE tq.test_id=:eid AND q.ID=tq.question_id ORDER BY tq.ID DESC";
                      $query = $dbh->prepare($sql);
                      $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                      $query->execute();
                      $results = $query->fetchAll(PDO::FETCH_OBJ);
                      $sql1 = "SELECT tq.*, sq.student_id, sq.ChooseAns, q.Question, q.CorrectAns FROM tbltest_question tq, tblstudent_testquestion sq, tblquestion q WHERE tq.test_id=:eid AND sq.test_id=:eid AND tq.question_id=sq.question_id AND q.ID=sq.question_id ORDER BY tq.ID DESC";
                      $query1 = $dbh->prepare($sql1);
                      $query1->bindParam(':eid', $eid, PDO::PARAM_STR);
                      $query1->execute();
                      $results1 = $query1->fetchAll(PDO::FETCH_OBJ);
                      if ($query->rowCount() > 0) {
                        foreach ($results as $row) {
                      ?>
                          <tr>
                            <td><?php echo htmlentities($row->question_id); ?></td>
                            <td><?php echo htmlentities($row->Point); ?></td>
                            <td>
                              <?php
                              $correct = 0;
                              $total = 0;
                              if ($query1->rowCount() > 0) {
                                foreach ($results1 as $row1) {
                                  if ($row1->question_id == $row->question_id) {
                                    if ($row1->ChooseAns == $row1->CorrectAns) {
                                      $correct = $correct + 1;
                                    }
                                    $total = $total + 1;
                                  }
                                }
                              }
                              if ($total == 0) {
                                echo htmlentities('N/A');
                              } else {
                                echo htmlentities(round($correct / $total * 100, 2) . '%');
                              }
                              ?></td>
                            <td><?php echo htmlentities($total); ?></td>
                            <td>
                              <a href="#" data-toggle="modal" data-target="#myModal<?php echo htmlentities( $row->question_id); ?>"><?php echo htmlentities($row->Question); ?></a>
                              <div class="modal fade" id="myModal<?php echo htmlentities( $row->question_id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h4 class="modal-title" id="myModalLabel">Student's Answers</h4>
                                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    </div>
                                    <?php
                                    $question_id = $row->question_id;
                                    $sql2 = "SELECT s.StudentName, sq.ChooseAns,s.StuID FROM tblstudent_testquestion sq, tblstudent s WHERE sq.test_id=:eid AND sq.question_id=:question_id AND sq.student_id=s.ID LIMIT 10";
                                    $query2 = $dbh->prepare($sql2);
                                    $query2->bindParam(':eid', $eid, PDO::PARAM_STR);
                                    $query2->bindParam(':question_id', $question_id, PDO::PARAM_STR);
                                    $query2->execute();
                                    $results2 = $query2->fetchAll(PDO::FETCH_OBJ);
                                    if ($query2->rowCount() > 0) {
                                      echo '<div class="modal-body" style="max-height: 300px; overflow-y: scroll;">';
                                      echo '<table class="table table-striped table-bordered">';
                                      foreach ($results2 as $row2) {
                                        echo "<tr>";
                                        echo "<td>" . htmlentities($row2->StuID) . "</td>";
                                        echo "<td>" . htmlentities($row2->StudentName) . "</td>";
                                        echo "<td>" . htmlentities($row2->ChooseAns) . "</td>";
                                        echo "</tr>";
                                      }
                                      echo "</table>";
                                    } else {
                                      echo '<div class="modal-body">';
                                      echo "No student has answered this question.";
                                    }
                                    ?>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-gray" data-dismiss="modal">Close</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </td>
                          </tr>
                      <?php
                        }
                      }
                      ?>
                    </tbody>
                  </table>
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