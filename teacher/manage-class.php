<?php
include('../includes/teacherVerify.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Student Management System || My Class</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="./css/style.css">
  <!-- End layout styles -->
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
            <h3 class="page-title"> My Class </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Manage My Class</li>
              </ol>
            </nav>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-sm-flex align-items-center mb-4">
                    <h4 class="card-title mb-sm-0">My Class</h4>
                    <a href="#" class="text-dark ml-auto mb-3 mb-sm-0"> View all Classes</a>
                  </div>
                  <div class="table-responsive border rounded p-1">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="font-weight-bold">No.</th>
                          <th class="font-weight-bold">Name</th>
                          <th class="font-weight-bold">Code</th>
                          <th class="font-weight-bold">Student</th>
                          <th class="font-weight-bold">Test</th>
                          <th class="font-weight-bold">Attendance</th>
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
                        if ((strlen($_SESSION['sturecmsuid']) == 0) || (strlen($_COOKIE['uid']) == 0) || (strlen($_COOKIE['session_token']) == 0)){
                            header('location:logout.php');
                            exit();
                          } else {
                            $uid = $_COOKIE['uid'] ?? '';
                            $sessionToken = $_COOKIE['session_token'] ?? '';
                        $no_of_records_per_page = 15;
                        $offset = ($pageno - 1) * $no_of_records_per_page;
                        $ret = "SELECT ID FROM tblclass";
                        $query1 = $dbh->prepare($ret);
                        $query1->execute();
                        $results1 = $query1->fetchAll(PDO::FETCH_OBJ);
                        $total_rows = $query1->rowCount();
                        $total_pages = ceil($total_rows / $no_of_records_per_page);
                      
                        #query all classes belongs to the teacher in tblclass and check the teacher has a valid token in tbltoken
                        $sql = "SELECT tblclass.* from tblclass, tbltoken where tblclass.teacher_id=:uid AND tbltoken.UserID = tblclass.teacher_id AND tbltoken.UserToken = :sessionToken AND (tbltoken.CreationTime + INTERVAL 2 HOUR) >= NOW() ORDER BY tblclass.CreationTime DESC LIMIT $offset, $no_of_records_per_page";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':uid',$uid,PDO::PARAM_STR);
                        $query->bindParam(':sessionToken',$sessionToken,PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $cnt = 1;
                        if ($query->rowCount() > 0) {
                          foreach ($results as $row) {
                        ?>
                            <tr>
                              <td><?php echo htmlentities($cnt); ?></td>
                              <td><?php echo htmlentities($row->ClassName); ?></td>
                              <td><?php echo htmlentities($row->JoinCode); ?></td>
                              <td>
                                <?php
                                $cid = $row->ID;
                                $sql1 = "SELECT * from tblstudent_class where class_id=:cid";
                                $query1 = $dbh->prepare($sql1);
                                $query1->bindParam(':cid', $cid, PDO::PARAM_STR);
                                $query1->execute();
                                $results1 = $query1->fetchAll(PDO::FETCH_OBJ);
                                $totalstudent = $query1->rowCount();
                                echo htmlentities($totalstudent);
                                ?>
                              </td>
                              <td>
                                <?php
                                $sql1 = "SELECT * from tbltest where class_id=:cid";
                                $query1 = $dbh->prepare($sql1);
                                $query1->bindParam(':cid', $cid, PDO::PARAM_STR);
                                $query1->execute();
                                $results1 = $query1->fetchAll(PDO::FETCH_OBJ);
                                $totaltest = $query1->rowCount();
                                echo htmlentities($totaltest);
                                ?>
                              </td>
                              <td>
                                <?php
                                $sql1 = "SELECT * from tblattendance where class_id=:cid";
                                $query1 = $dbh->prepare($sql1);
                                $query1->bindParam(':cid', $cid, PDO::PARAM_STR);
                                $query1->execute();
                                $results1 = $query1->fetchAll(PDO::FETCH_OBJ);
                                $totalattendance = $query1->rowCount();
                                echo htmlentities($totalattendance);
                                ?>
                              <td>
                                <div><a href="class-detail.php?editid=<?php echo htmlentities($row->ID); ?>"><i class="icon-eye"></i></a></div>
                              </td>
                            </tr>
                        <?php $cnt = $cnt + 1;
                          } 
                        } else {
                            echo "<b>No Class Found</b>";
                            
                        }} ?>
                      </tbody>
                    </table>
                  </div>
                  <div align="left">
                    <ul class="pagination">
                      <li><a href="?pageno=1"><strong>First></strong></a></li>
                      <li class="<?php if ($pageno <= 1) {
                              echo 'disabled';
                            } ?>">
                        <a href="<?php if ($pageno <= 1) {
                                echo '#';
                              } else {
                                echo "?pageno=" . ($pageno - 1);
                              } ?>"><strong style="padding-left: 10px">Prev></strong></a>
                      </li>
                      <li class="<?php if ($pageno >= $total_pages) {
                              echo 'disabled';
                            } ?>">
                        <a href="<?php if ($pageno >= $total_pages) {
                                echo '#';
                              } else {
                                echo "?pageno=" . ($pageno + 1);
                              } ?>"><strong style="padding-left: 10px">Next></strong></a>
                      </li>
                      <li><a href="?pageno=<?php echo htmlentities( $total_pages); ?>"><strong style="padding-left: 10px">Last</strong></a></li>
                    </ul>
                  </div>
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
  <script src="./vendors/chart.js/Chart.min.js"></script>
  <script src="./vendors/moment/moment.min.js"></script>
  <script src="./vendors/daterangepicker/daterangepicker.js"></script>
  <script src="./vendors/chartist/chartist.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/misc.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <script src="./js/dashboard.js"></script>
  <!-- End custom js for this page -->
</body>
</html>