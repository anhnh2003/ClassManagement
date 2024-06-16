<?php
include('../includes/teacherVerify.php');
require '../includes/util.php';
$answers = ['A', 'B', 'C', 'D'];
$uid = $_COOKIE['uid'] ?? '';

if (isset($_POST['submit'])) {
  $qname = $_POST['qname'];
  $ansUpdate = [];
  for ($i = 0; $i < count($answers); $i++) {
    if (empty($_POST['correct' . $answers[$i]]) || empty($_POST['ans' . $answers[$i]])) {
      $ansUpdate[] = '';
    } else {
      $ansUpdate[] = $_POST['correct' . $answers[$i]];
    }
  }
  $correct_ans = implode('', $ansUpdate);
  if ($correct_ans == '') {
    echo '<script>alert("Please select a correct answer")</script>';
  } else {
    $ismul = $_POST['ismul'];
    if (strlen($correct_ans) > 1 && $ismul == 0) {
      $ismul = 1;
    }
    
    $sql = "INSERT INTO tblquestion (Question, AnsA, AnsB, AnsC, AnsD, CorrectAns, isMultipleChoice) VALUES (:qname, :ansA, :ansB, :ansC, :ansD, :correct_ans, :ismul)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':qname', $qname, PDO::PARAM_STR);
    $query->bindParam(':correct_ans', $correct_ans, PDO::PARAM_STR);
    $query->bindValue(':ansA', $_POST['ansA'] ?: "Untitled", PDO::PARAM_STR);
    $query->bindValue(':ansB', $_POST['ansB'] ?: null, PDO::PARAM_STR);
    $query->bindValue(':ansC', $_POST['ansC'] ?: null, PDO::PARAM_STR);
    $query->bindValue(':ansD', $_POST['ansD'] ?: null, PDO::PARAM_STR);
    $query->bindParam(':ismul', $ismul, PDO::PARAM_INT);
    $query->execute();

    echo '<script>alert("Question has been added.")</script>';
    writeLog("Question #" . $dbh->lastInsertId() . " - Added by Teacher #" . $uid . ".");
    echo "<script>window.location.href ='manage-question.php'</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Student Management System || Add Question</title>
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
            <h3 class="page-title"> Add Question </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="manage-question.php">Manage Questions</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Add Question</li>
              </ol>
            </nav>
          </div>
          <div class="row">
            <div class="col-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" style="text-align: center;">Create New Question</h4>
                  <form class="forms-sample" method="post">
                    <div class="form-group">
                      <label for="exampleInputName1">Question</label>
                      <textarea name="qname" class="form-control" rows="10" required='true' placeholder='Type your question:'></textarea>
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
                            $inputStyle = 'placeholder="Add answer (required)" required';
                            for ($i = 0; $i < count($answers); $i++) {
                              if ($i > 0) {
                                $inputStyle = 'placeholder="Add answer"';
                              }
                              echo '<tr>';
                              echo '<td>' . $answers[$i] . '</td>';
                              echo '<td>';
                              echo '<input type="text" name="ans' . $answers[$i] . '" value="" class="form-control" ' . $inputStyle . '>';
                              echo '</td>';
                              echo '<td>';
                              echo '<input type="checkbox" name="correct' . $answers[$i] . '" value="' . $answers[$i] . '">';
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
                      <select name="ismul" class="form-control" value="">
                        <option value="0" selected>No
                        </option>
                        <option value="1">Yes
                        </option>
                      </select>
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn btn-primary mr-2" name="submit">Save Change</button>
                    </div>
                  </form>
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