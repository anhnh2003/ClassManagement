<?php
session_start();
include('../includes/dbconnection.php');

$_SESSION['sturecmstuid'] = $_SESSION['sturecmsstuid'];
$uid = $_COOKIE['uid'] ?? '';
$tid = $_GET['testid'];
$answers = ['A', 'B', 'C', 'D'];
$redirectBack = '<script>window.location.replace("test-detail.php?editid=' . $tid. '")</script>';

function updateTestPoint($dbh, $uid, $tid) {
  $sql = "SELECT tq.ID, CorrectAns, Point, ChooseAns FROM (SELECT * FROM tblstudent_testquestion WHERE student_id=:uid and test_id=:tid) sq RIGHT JOIN (SELECT Point, q.* FROM tbltest_question, tblquestion q WHERE question_id=q.ID and test_id=:tid) tq ON tq.ID = sq.question_id";
  $query = $dbh->prepare($sql);
  $query->bindParam(':uid', $uid, PDO::PARAM_STR);
  $query->bindParam(':tid', $tid, PDO::PARAM_STR);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);

  $point = 0;
  foreach ($results as $row) {
    if ($row->CorrectAns == $row->ChooseAns) {
      $point += $row->Point;
    }
  }

  $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
  $submittime = $currentDateTime->format('Y-m-d H:i:s');

  $sql = "UPDATE tblstudent_test SET SubmitTime=:submittime, TotalPoint=:point WHERE student_id=:uid AND test_id=:tid";
  $query = $dbh->prepare($sql);
  $query->bindParam(':submittime', $submittime, PDO::PARAM_STR);
  $query->bindParam(':point', $point, PDO::PARAM_INT);
  $query->bindParam(':uid', $uid, PDO::PARAM_STR);
  $query->bindParam(':tid', $tid, PDO::PARAM_STR);
  $query->execute();
}

if (strlen($_SESSION['sturecmstuid']) == 0) {
  header('location:logout.php');
  exit();
} else {
  $sessionToken = $_COOKIE['session_token'] ?? '';

  $sql = "SELECT UserToken, role_id FROM tbltoken WHERE UserID = :uid AND UserToken = :sessionToken AND (CreationTime + INTERVAL 2 HOUR) >= NOW()";
  $query = $dbh->prepare($sql);
  $query->bindParam(':uid', $uid, PDO::PARAM_INT);
  $query->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
  $query->execute();
  $role_id = $query->fetch(PDO::FETCH_OBJ)->role_id;

  if (($query->rowCount() == 0) || ($role_id != 3)) {
    header('location:logout.php');
    exit();
  }

  if ((strlen($_SESSION['sturecmsuid']) == 0) || (strlen($_COOKIE['uid']) == 0) || (strlen($_COOKIE['session_token']) == 0)) {
    header('location:logout.php');
    exit();
  } else {

    $sql = "SELECT * from tbltest t, tblclass c, tblstudent_class sc, tbltoken where t.ID=:tid and sc.student_id=:uid and t.class_id=c.ID and c.ID=sc.class_id AND tbltoken.UserID=:uid AND tbltoken.UserToken=:sessionToken AND (tbltoken.CreationTime + INTERVAL 2 HOUR) >= NOW()";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->bindParam(':tid', $tid, PDO::PARAM_STR);
    $query->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() == 0) {
      echo $redirectBack;
      exit();
    }

    $sql = "SELECT * from tblstudent_test where student_id=:uid and test_id=:tid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->bindParam(':tid', $tid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() == 0) {
      echo $redirectBack;
      exit();
    } else {
      if ($results[0]->SubmitTime != Null || $results[0]->TotalPoint != Null) {
        echo '<script>alert("Test has been submitted.")</script>';
        echo $redirectBack;
        exit();
      } else {
        $sql = "SELECT StartTime, EndTime FROM tbltest WHERE ID=:tid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':tid', $tid, PDO::PARAM_STR);
        $query->execute();
        $results1 = $query->fetchAll(PDO::FETCH_OBJ);

        $startTime = $results1[0]->StartTime;
        $endTime = $results1[0]->EndTime;
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $currentDateTime = $currentDateTime->format('Y-m-d H:i:s');

        if ($currentDateTime < $startTime || $currentDateTime > $endTime) {
          if ($currentDateTime > $endTime) {
            updateTestPoint($dbh, $uid, $tid);
          }
          echo '<script>alert("Test has not started or ended.")</script>';
          echo $redirectBack;
          exit();
        }
      }
    }

    if (isset($_POST['submit'])) {
      updateTestPoint($dbh, $uid, $tid);
      echo '<script>alert("Test has been submitted.")</script>';
      echo $redirectBack;
      exit();
    }
  

    if (isset($_POST['choose'])) {
      if ($results[0]->TotalPoint != Null) {
        echo '<script>alert("Test has been submitted.")</script>';
        echo $redirectBack;
        exit();
      }

      $sql = "SELECT EndTime FROM tbltest WHERE ID=:tid";
      $query = $dbh->prepare($sql);
      $query->bindParam(':tid', $tid, PDO::PARAM_STR);
      $query->execute();
      $results = $query->fetchAll(PDO::FETCH_OBJ);

      $endTime = $results[0]->EndTime;
      $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
      $currentDateTime = $currentDateTime->format('Y-m-d H:i:s');
      if ($currentDateTime > $endTime) {
        updateTestPoint($dbh, $uid, $tid);
        echo '<script>alert("Test has ended and auto-submitted!")</script>';
        echo $redirectBack;
        exit();
      } else {
        $qid = $_POST['qid'];
        $chooseAns = '';
        if (isset($_POST['chooseOne'])) {
          $chooseAns = $_POST['chooseOne'];
        } else {
          for ($i = 0; $i < count($answers); $i++) {
            if (isset($_POST['choose'.$answers[$i]])) {
              $chooseAns = $chooseAns . $answers[$i];
            }
          }
        }

        $sql = "SELECT * from tblstudent_testquestion where student_id=:uid and question_id=:qid and test_id=:tid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
        $query->bindParam(':qid', $qid, PDO::PARAM_STR);
        $query->bindParam(':tid', $tid, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() == 0) {
          $sql = "INSERT INTO tblstudent_testquestion (student_id, test_id, question_id, ChooseAns) VALUES (:uid, :tid, :qid, :chooseAns)";
          $query = $dbh->prepare($sql);
          $query->bindParam(':uid', $uid, PDO::PARAM_STR);
          $query->bindParam(':tid', $tid, PDO::PARAM_STR);
          $query->bindParam(':qid', $qid, PDO::PARAM_STR);
          $query->bindParam(':chooseAns', $chooseAns, PDO::PARAM_STR);
          $query->execute();
        } else {
          $sql = "UPDATE tblstudent_testquestion SET ChooseAns=:chooseAns WHERE student_id=:uid AND question_id=:qid AND test_id=:tid";
          $query = $dbh->prepare($sql);
          $query->bindParam(':chooseAns', $chooseAns, PDO::PARAM_STR);
          $query->bindParam(':uid', $uid, PDO::PARAM_STR);
          $query->bindParam(':tid', $tid, PDO::PARAM_STR);
          $query->bindParam(':qid', $qid, PDO::PARAM_STR);
          $query->execute();
        }
        //header('location:test.php?testid=' . $tid);
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Student Management System || Testing</title>
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
    <?php
      $uid = $_SESSION['sturecmsuid'];
      $sql = "SELECT TestName, EndTime FROM tbltest t WHERE t.ID=:tid";
      $query = $dbh->prepare($sql);
      $query->bindParam(':tid', $tid, PDO::PARAM_STR);
      $query->execute();
      $results = $query->fetchAll(PDO::FETCH_OBJ);
    ?>
    <div class="main-panel">
      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title"> <?php echo htmlentities($results[0]->TestName);?> </h3>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">End Time: <b><?php echo htmlentities($results[0]->EndTime);?> </b></a></li>
            </ol>
          </nav>
        </div>
                <?php
                $sql = "SELECT tq.ID, Question, AnsA, AnsB, AnsC, AnsD, CorrectAns, Point, ChooseAns, isMultipleChoice FROM (SELECT * FROM tblstudent_testquestion WHERE student_id=:uid and test_id=:tid) sq RIGHT JOIN (SELECT Point, q.* FROM tbltest_question, tblquestion q WHERE question_id=q.ID and test_id=:tid) tq ON tq.ID = sq.question_id ORDER BY tq.ID ASC";
                $query = $dbh->prepare($sql);
                $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                $query->bindParam(':tid', $tid, PDO::PARAM_STR);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                
                $cnt = 1;
                if ($query->rowCount() > 0) {
                  foreach ($results as $row) {
                      $ansStyle = [];
                      $ansContent = [];
                      for ($i = 0; $i < count($answers); $i++) {
                        $ansContent[] = $row->{'Ans'. $answers[$i]};
                        if ($ansContent[$i] == Null) {
                          $ansStyle[] = 'display: none;';
                        } else if (strpos($row->ChooseAns, $answers[$i]) !== false) {
                          $ansStyle[] = 'checked';
                        } else {
                          $ansStyle[] = '';
                        }
                      }
                    ?>
                    <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                    <div class="card-body">
                    <h4 class="card-title" style="text-align: left;"> Question <?php echo htmlentities($cnt); ?>: </h4>
                    <form class="forms-sample" method="post">
                      <input type="hidden" name="qid" value="<?php echo htmlentities($row->ID); ?>">
                        <div class="form-group">
                        <textarea name="qname" rows="<?php echo ceil(strlen($row->Question) / 50); ?>" class="form-control" readonly style="background-color: white;"><?php echo htmlentities($row->Question); ?></textarea>
                        </div>
                          <?php
                          if ($row->isMultipleChoice == 0) {
                            for ($i = 0; $i < count($answers); $i++) {
                              echo '<div class="form-check">';
                              echo '<label class="form-check-label">';
                              echo '<input type="radio" class="form-check-input" name="chooseOne" value="'. $answers[$i] .'" ' . $ansStyle[$i] . '>'. $answers[$i] .'. ' . $ansContent[$i];
                              echo '</label>';
                              echo '</div>';
                            }
                          } else {
                            for ($i = 0; $i < count($answers); $i++) {
                              echo '<div class="form-check">';
                              echo '<label class="form-check-label">';
                              echo '<input type="checkbox" class="form-check-input" name="choose'. $answers[$i] .'" value="'. $answers[$i] .'" ' . $ansStyle[$i] . '>'. $answers[$i] .'. ' . $ansContent[$i];
                              echo '</label>';
                              echo '</div>';
                            }
                          }
                            ?>
                            <div class="text-center">
                            <button type="submit" class="btn btn-primary mr-2" name="choose">Save</button>
                            </div>
                    </form>
                    </div>
                    </div>
                    </div>
                    </div>
                    <?php
                  $cnt = $cnt + 1;
                  }
                } ?>
                  <form class="forms-sample" method="post">
                    <div class="text-center">
                      <button type="submit" class="btn btn-primary mr-2" style="background-color:red; border-color:red;" name="submit">Submit</button>
                      </div>
                  </form>
        
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