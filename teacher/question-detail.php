<?php
session_start();
include('includes/dbconnection.php');

$_SESSION['sturecmstuid'] = $_SESSION['sturecmsstuid'];
$answers = ['A', 'B', 'C', 'D'];

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

    $sql = "SELECT * FROM tblquestion, tbltest_question, tbltest, tblclass, tblteacher, tbltoken WHERE tbltest.ID=tbltest_question.test_id and tblquestion.ID=:eid and teacher_id=:uid AND tblteacher.ID=:uid AND tblclass.ID=tbltest.class_id AND tbltoken.UserID=:uid AND tbltoken.UserToken=:sessionToken AND (tbltoken.CreationTime + INTERVAL 2 HOUR) >= NOW()";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->bindParam(':sessionToken', $sessionToken, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    $tid = $results[0]->test_id;

    if ($query->rowCount() == 0) {
      header('location:manage-test.php');
      exit();
    }
  }

  if (isset($_POST['edit'])) {
    $eid = $_GET['editid'];
    $qname = $_POST['qname'];
    for ($i = 0; $i < count($answers); $i++) {
      if (empty($_POST['correct' . $answers[$i]]) || empty($_POST['ans' . $answers[$i]])) {
        $answers[$i] = '';
      } else {
        $answers[$i] = $_POST['correct' . $answers[$i]];
      }
    }
    $correct_ans = implode('', $answers);
    if ($correct_ans == '') {
      echo '<script>alert("Please select a correct answer")</script>';
    } else {
      $ismul = $_POST['ismul'];
      if (strlen($correct_ans) > 1 && $ismul == 0) {
        $ismul = 1;
      }

      $sql = "UPDATE tblquestion SET Question=:qname, CorrectAns=:correct_ans, AnsA=:ansA, AnsB=:ansB, AnsC=:ansC, AnsD=:ansD, isMultipleChoice=:ismul WHERE ID=:eid";
      $query = $dbh->prepare($sql);
      $query->bindParam(':qname', $qname, PDO::PARAM_STR);
      $query->bindParam(':correct_ans', $correct_ans, PDO::PARAM_STR);
      $query->bindValue(':ansA', $_POST['ansA'] ?: "Untitled", PDO::PARAM_STR);
      $query->bindValue(':ansB', $_POST['ansB'] ?: null, PDO::PARAM_STR);
      $query->bindValue(':ansC', $_POST['ansC'] ?: null, PDO::PARAM_STR);
      $query->bindValue(':ansD', $_POST['ansD'] ?: null, PDO::PARAM_STR);
      $query->bindParam(':ismul', $ismul  , PDO::PARAM_INT);
      $query->bindParam(':eid', $eid, PDO::PARAM_STR);
      $query->execute();

      echo '<script>alert("Question has been updated")</script>';
    }
  }

  if (isset($_POST['delete'])) {
    $eid = $_GET['editid'];

    $sql = "DELETE FROM tbltest_question WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();

    echo '<script>alert("Question has been deleted")</script>';
    echo "<script>window.location.href ='test-detail.php?editid=$tid'</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Student Management System || Edit Question</title>
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
          <h3 class="page-title"> Edit Question </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
          <?php if (!empty($_GET['testid'])) { ?>
                <li class="breadcrumb-item"><a href="test-detail.php?editid=<?php echo htmlentities($_GET['testid']); ?>">Test Details</a></li>
          <?php } else { ?>
            <li class="breadcrumb-item"><a href="manage-question.php">Manage Questions</a></li>
          <?php } ?>
          <li class="breadcrumb-item active" aria-current="page"> Edit Test Question</li>
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
                $sql = "SELECT * FROM tblquestion q WHERE q.ID=:eid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                $cnt = 1;
                if ($query->rowCount() > 0) {
                  foreach ($results as $row) {
                    ?>
                    <h4 class="card-title" style="text-align: center;"> Question #<?php echo htmlentities($row->ID); ?> </h4>
                    <form class="forms-sample" method="post">
                        <div class="form-group">
                          <label for="exampleInputName1">Question</label>
                          <textarea name="qname" class="form-control" rows="10" required='true'><?php echo htmlentities($row->Question); ?></textarea>
                        </div>
                        <div class="form-group">
                        <div class="table-responsive ">
                          <table class="table table-striped table-bordered">
                          <thead>
                          <tr>
                          <th><b>Answer</b></th>
                          <th><b>Content</b></th>
                          <th><b>Correct Answer(s)</b></th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php
                          foreach ($answers as $answer) {
                          echo '<tr>';
                          echo '<td>' . $answer . '</td>';
                          echo '<td>';
                          echo '<input type="text" name="ans' . $answer . '" value="' . htmlentities($row->{'Ans' . $answer}) . '" class="form-control" placeholder="Add answer">';
                          echo '</td>';
                          echo '<td>';
                          echo '<input type="checkbox" name="correct' . $answer . '" value="' . $answer . '" ' . (strpos($row->CorrectAns, $answer) !== false ? 'checked' : '') . '>';
                          echo '</td>';
                          echo '</tr>';
                          }
                          ?>
                          </tbody>
                          </table>
                        </div>
                        </div>
                        <div class="form-group">
                        <label for="exampleInputName1">Allow Multiple choices</label>
                        <select name="ismul" class="form-control" value="<?php if($row->isMultipleChoice){echo "Yes";} else {echo "No";} ?>">
                          <option value="1" <?php if ($row->isMultipleChoice == 1) {
                            echo 'selected';
                          } ?>>Yes
                          </option>
                          <option value="0" <?php if ($row->isMultipleChoice == 0) {
                            echo 'selected';
                          } ?>>No
                          </option>
                        </select>
                      </div>
                      <?php $cnt = $cnt + 1;
                  }
                } ?>

                <button type="submit" class="btn btn-primary mr-2" name="edit">Edit Details
                </button>
                <button type="submit" class="btn btn-primary mr-2" name="delete" style="background-color: red; border-color: red; color: white;">Delete</button>
                </button>
              </form>
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