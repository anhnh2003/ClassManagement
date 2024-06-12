<?php
session_start();
include('includes/dbconnection.php');

$_SESSION['sturecmstuid'] = $_SESSION['sturecmsstuid'];

if (strlen($_SESSION['sturecmstuid']) == 0) {
  header('location:logout.php');
  exit();
} else {
  $uid = $_COOKIE['uid'] ?? '';
  $sessionToken = $_COOKIE['session_token'] ?? '';

  $sql = "SELECT UserToken, role_id FROM tbltoken WHERE UserID = :uid AND UserToken = :sessionToken AND (CreationTime + INTERVAL 2 HOUR) >= NOW()";
  $query = $dbh->prepare($sql);
  $query->bindParam(':uid', $uid, PDO::PARAM_INT);
  $query->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
  $query->execute();
  $role_id = $query->fetch(PDO::FETCH_OBJ)->role_id;

  if (($query->rowCount() == 0) || ($role_id != 2)) {
    header('location:logout.php');
    exit();
  }

  if ((strlen($_SESSION['sturecmsuid']) == 0) || (strlen($_COOKIE['uid']) == 0) || (strlen($_COOKIE['session_token']) == 0)) {
    header('location:logout.php');
    exit();
  } else {
    $uid = $_COOKIE['uid'] ?? '';
    $eid = $_GET['editid'];

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
  }

  if (isset($_POST['edit'])) {
    $eid = $_GET['editid'];
    $tname = $_POST['tname'];
    $stime = $_POST['stime'];
    $etime = $_POST['etime'];

    $sql = "update tbltest set TestName=:tname, StartTime=:stime, EndTime=:etime where ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':tname', $tname, PDO::PARAM_STR);
    $query->bindParam(':stime', $stime, PDO::PARAM_STR);
    $query->bindParam(':etime', $etime, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();
    echo '<script>alert("Test details have been updated")</script>';
  }

  if (isset($_POST['add_ques'])) {
    $eid = $_GET['editid'];
    $qid = $_POST['qid'];
    if (strlen($qid) > 0) {
      $sql_check = "SELECT * FROM tbltest_question WHERE test_id=:eid AND question_id=:qid";
      $query_check = $dbh->prepare($sql_check);
      $query_check->bindParam(':eid', $eid, PDO::PARAM_STR);
      $query_check->bindParam(':qid', $qid, PDO::PARAM_STR);
      $query_check->execute();

      if ($query_check->rowCount() == 0) {
        $sql = "INSERT INTO tbltest_question(test_id, question_id, Point) VALUES(:eid, :qid , 1)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
        $query->bindParam(':qid', $qid, PDO::PARAM_STR);
        $query->execute();
      }
    }
  }

  if (isset($_POST['del_ques'])) {
    $qid = $_POST['qid'];
    $eid = $_GET['editid'];
    $sql = "DELETE FROM tbltest_question WHERE test_id=:eid AND question_id=:qid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->bindParam(':qid', $qid, PDO::PARAM_STR);
    $query->execute();
  }

  if (isset($_POST['edit_ques'])) {
    $eid = $_GET['editid'];
    $sql = "SELECT * FROM tbltest_question WHERE test_id=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
      foreach ($results as $row) {
        $qid = $row->question_id;
        $new_point = $_POST["point_q".$qid];
        $sql1 = "UPDATE tbltest_question SET Point=:point WHERE test_id=:eid AND question_id=:qid";
        $query1 = $dbh->prepare($sql1);
        $query1->bindParam(':point', $new_point, PDO::PARAM_INT);
        $query1->bindParam(':eid', $eid, PDO::PARAM_STR);
        $query1->bindParam(':qid', $qid, PDO::PARAM_STR);
        $query1->execute();
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Student Management System || Manage Test</title>
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
                <li class="breadcrumb-item"><a href="manage-test.php">Manage Tests</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Test Details</li>
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
                      <h4 class="card-title" style="text-align: center;"> <?php echo htmlentities($row->TestName); ?> </h4>
                      <form class="forms-sample" method="post">
                        <div class="form-group">
                          <label for="exampleInputName1">Title</label>
                          <input type="text" name="tname" value="<?php echo htmlentities($row->TestName); ?>" class="form-control" required='true'>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputName1">Class</label>
                          <input type="text" name="cname" value="<?php echo htmlentities($row->ClassName); ?>" class="form-control" required='true' readonly>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputName1">Room</label>
                          <input type="text" name="room" value="<?php echo htmlentities($row->Room); ?>" class="form-control" required='true' readonly>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputName1">Start Time</label>
                          <input type="datetime-local" name="stime" value="<?php echo htmlentities($row->StartTime); ?>" class="form-control" required='true'>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputName1">End Time</label>
                          <input type="datetime-local" name="etime" value="<?php echo htmlentities($row->EndTime); ?>" class="form-control" required='true'>
                        </div>
                  <?php
                        $cnt = $cnt + 1;
                      }
                    } ?>

                    <button type="submit" class="btn btn-primary mr-2" name="edit">Save Changes</button>
                    <a href="test-result.php?editid=<?php echo $eid; ?>" class="btn btn-info">View Results</a>
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
                  <form class="forms-sample" method="post">
                    <div class="form-group">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                          <thead>
                            <tr>
                              <th class="font-weight-bold">ID</th>
                              <th class="font-weight-bold">Point</th>
                              <th class="font-weight-bold">Question</th>
                              <th class="font-weight-bold"></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $sql = "SELECT q.Question, q.ID, tq.Point FROM tbltest_question tq, tblquestion q WHERE tq.test_id=:eid and tq.question_id=q.ID ORDER BY q.ID ASC";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                            $query->execute();
                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                            if ($query->rowCount() > 0) {
                              foreach ($results as $row) {
                            ?>
                                <tr>
                                  <td><b><?php echo htmlentities($row->ID); ?></b></td>
                                  <td><input type="text" style="width: 50pt;" name="point_q<?php echo htmlentities($row->ID);?>" value="<?php echo htmlentities($row->Point); ?>" class="form-control" pattern="[0-9]+" required='true'></td>
                                  <td>
                                    <a href="question-detail.php?editid=<?php echo htmlentities($row->ID); ?>&testid=<?php echo htmlentities($eid); ?>">
                                      <i class="icon-pencil"></i>
                                      <?php echo htmlentities($row->Question); ?>
                                    </a>
                                  </td>
                                  <td>
                                    <form class="forms-sample" method="post">
                                      <button type="submit" class="btn btn-sm btn-danger" name="del_ques" style="width: 70pt;">Delete</button>
                                      <input type="hidden" name="qid" value="<?php echo htmlentities($row->ID); ?>">
                                    </form>
                                  </td>
                                </tr>
                            <?php
                              }
                            } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2" name="edit_ques">Save Changes</button>
                    <button type="button" class="btn btn-primary mr-2" name="new_ques" data-toggle="modal" data-target="#questionModal" >Add Question</button>
                  </form>
                  <div class="mt-4"></div>

                  <!-- Question Modal -->
                  <div class="modal fade" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="questionModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="questionModalLabel">Select a Question</h5>
                        </div>
                        <form class="forms-sample" method="post">
                          <div class="modal-body">
                            <?php
                            // Fetch all available questions from the database (not added)
                            $sql = "SELECT * FROM tblquestion WHERE ID NOT IN (SELECT question_id FROM tbltest_question WHERE test_id=:eid)";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                            $query->execute();
                            $questions = $query->fetchAll(PDO::FETCH_OBJ);

                            // Display each question as an option in the dropdown
                            if (count($questions) > 0) {
                              echo '<select class="form-control" id="qid" name="qid">';
                              foreach ($questions as $question) {
                                echo '<option value="' . $question->ID . '">'. $question->ID . " - " . $question->Question . '</option>';
                              }
                              echo '</select>';
                              $btnAddStyle = 'class="btn btn-primary"';
                            } else {
                              echo 'No questions available...';
                              $btnAddStyle = 'hidden';
                            }
                            ?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" <?php echo $btnAddStyle ?> name="add_ques">Add</button>
                          </div>
                        </form>
                      </div>
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
