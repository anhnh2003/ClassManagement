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

  } else {
    // Token is valid, continue to the dashboard  
  // Code for deletion
  if (isset($_GET['delid'])) {
    $rid = intval($_GET['delid']);
    $sql = "delete from tblclass where ID=:rid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':rid', $rid, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('Data deleted');</script>";
    echo "<script>window.location.href = 'manage-class.php'</script>";
  }
}}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Student Management System || Manage Class</title>
  <title>Student Management System || My Class</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
@@ -52,11 +52,11 @@
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title"> Manage Class </h3>
            <h3 class="page-title"> My Class </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Manage Class</li>
                <li class="breadcrumb-item active" aria-current="page"> Manage My Class</li>
              </ol>
            </nav>
          </div>
@@ -65,7 +65,7 @@
              <div class="card">
                <div class="card-body">
                  <div class="d-sm-flex align-items-center mb-4">
                    <h4 class="card-title mb-sm-0">Manage Class</h4>
                    <h4 class="card-title mb-sm-0">My Class</h4>
                    <a href="#" class="text-dark ml-auto mb-3 mb-sm-0"> View all Classes</a>
                  </div>
                  <div class="table-responsive border rounded p-1">
@@ -75,6 +75,9 @@
                          <th class="font-weight-bold">No.</th>
                          <th class="font-weight-bold">Name</th>
                          <th class="font-weight-bold">Code</th>
                          <th class="font-weight-bold">Student</th>
                          <th class="font-weight-bold">Test</th>
                          <th class="font-weight-bold">Attendance</th>
                          <th class="font-weight-bold">Action</th>
                        </tr>
                      </thead>
@@ -109,6 +112,39 @@
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