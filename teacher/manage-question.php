<?php
include('../includes/teacherVerify.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Student Management System || Question Bank</title>
  <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>
  <div class="container-scroller">
    <?php include_once('includes/header.php'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php include_once('includes/sidebar.php'); ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title"> Questions Bank </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Manage Created Questions</li>
              </ol>
            </nav>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-sm-flex align-items-center mb-4">
                    <h4 class="card-title mb-sm-0">View Questions</h4>
                  </div>
                  <div class="table-responsive border rounded p-1">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="font-weight-bold">ID</th>
                          <th class="font-weight-bold">Type</th>
                          <th class="font-weight-bold">Content</th>
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

                        if ((strlen($_SESSION['sturecmsuid']) == 0) || (strlen($_COOKIE['uid']) == 0) || (strlen($_COOKIE['session_token']) == 0)) {
                          header('location:logout.php');
                          exit();
                        } else {
                          $uid = $_COOKIE['uid'] ?? '';
                          $sessionToken = $_COOKIE['session_token'] ?? '';
                          $no_of_records_per_page = 15;
                          $offset = ($pageno - 1) * $no_of_records_per_page;
                          $ret = "SELECT ID, Question, isMultipleChoice from tblquestion ORDER BY ID DESC";
                          $query = $dbh->prepare($ret);
                          $query->execute();
                          $results = $query->fetchAll(PDO::FETCH_OBJ);
                          $total_rows = $query->rowCount();
                          $total_pages = ceil($total_rows / $no_of_records_per_page);
                          $cnt = 1;

                          if ($query->rowCount() > 0) {
                            foreach ($results as $row) {
                        ?>
                              <tr>
                                <td><b><?php echo htmlentities($row->ID); ?></b></td>
                                <td><?php if ($row->isMultipleChoice == 1) {
                                      echo '<label class="badge badge-info">Multiple</label>';
                                    } else {
                                      echo '<label class="badge badge-primary">Single</label>';
                                    } ?></td>
                                <td><?php echo htmlentities($row->Question); ?></td>
                                <td>
                                  <div><a href="question-detail.php?editid=<?php echo htmlentities($row->ID); ?>"><i class="icon-eye"></i></a></div>
                                </td>
                              </tr>
                        <?php
                              $cnt = $cnt + 1;
                            }
                          }
                        }
                        ?>
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
                      <li><a href="?pageno=<?php echo $total_pages; ?>"><strong style="padding-left: 10px">Last</strong></a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php include_once('includes/footer.php'); ?>
      </div>
    </div>
  </div>
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="./vendors/chart.js/Chart.min.js"></script>
  <script src="./vendors/moment/moment.min.js"></script>
  <script src="./vendors/daterangepicker/daterangepicker.js"></script>
  <script src="./vendors/chartist/chartist.min.js"></script>
  <script src="js/off-canvas.js"></script>
  <script src="js/misc.js"></script>
  <script src="./js/dashboard.js"></script>
</body>

</html>
