<?php
include('../includes/adminVerify.php');
require '../includes/util.php';

    // Token is valid, continue
if(isset($_POST['submit'])) {
  $name=$_POST['name'];
  $teaid=
  $teaid = "";
  $maxIdQuery = "SELECT MAX(ID) AS maxId FROM tblteacher";
  $maxIdResult = $dbh->query($maxIdQuery);
  $maxIdRow = $maxIdResult->fetch(PDO::FETCH_ASSOC);
  if ($maxIdRow['maxId']) {
    $teaid = $maxIdRow['maxId'] + 1;
  } else {
    $teaid = RAND(20000, 29999);
  }
  $email=$_POST['email'];
  $gender=$_POST['gender'];
  $uname=$_POST['uname'];
  $connum=$_POST['connum'];
  $password=$_POST['password'];
  if(!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,}$/', $password)) {
    $_SESSION['error'] = "Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one symbol.";
    header('Location: add-teacher.php');
    exit();
}
  $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
  
  $ret="select UserName from tblteacher where UserName=:uname || TeaID=:teaid";
  $query= $dbh -> prepare($ret);
$query->bindParam(':uname',$uname,PDO::PARAM_STR);
$query->bindParam(':teaid',$teaid,PDO::PARAM_STR);
$query-> execute();
     $results = $query -> fetchAll(PDO::FETCH_OBJ);
// If there is no result, insert the new teacher
if($query -> rowCount() == 0)
{

$sql="insert into tblteacher(TeaID,TeacherName,Email,Gender,UserName,Password,ContactNumber) values(:teaid,:name,:email,:gender,:uname,:password,:connum)";
$query=$dbh->prepare($sql);
$query->bindParam(':name',$name,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':gender',$gender,PDO::PARAM_STR);
$query->bindParam(':teaid',$teaid,PDO::PARAM_STR);
$query->bindParam(':connum',$connum,PDO::PARAM_STR);
$query->bindParam(':uname',$uname,PDO::PARAM_STR);
$query->bindParam(':password',$password,PDO::PARAM_STR);
 $query->execute();
   $LastInsertId=$dbh->lastInsertId();
   if ($LastInsertId>0) {
    writeLog("Teacher #" . $teaid . " - Added teacher");
    echo '<script>alert("Teacher has been added.")</script>';
echo "<script>window.location.href ='add-teacher.php'</script>";
  }
  else
    {
         echo '<script>alert("Something Went Wrong. Please try again")</script>';
    }
}

else
{

echo "<script>alert('Username or Teacher ID already exist. Please try again');</script>";
}
}
  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
   
    <title>Student  Management System || Add Teacher</title>
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
     <?php include_once('includes/header.php');?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
      <?php include_once('includes/sidebar.php');?>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title"> Add Teacher </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Add Teacher</li>
                </ol>
              </nav>
            </div>
            <div class="row">
          
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title" style="text-align: center;">Teacher Info</h4>
                   
                    <form class="forms-sample" method="post" enctype="multipart/form-data">
                      
                      <div class="form-group">
                        <label for="exampleInputName1">Name</label>
                        <input type="text" name="name" value="" class="form-control" required='true'>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputName1">Email</label>
                        <input type="text" name="email" value="" class="form-control" required='true'>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputName1">Gender</label>
                        <select name="gender" value="" class="form-control" required='true'>
                          <option value="">Choose Gender</option>
                          <option value="Male">Male</option>
                          <option value="Female">Female</option>
                          <option value="Other">Other</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputName1">Contact Number</label>
                        <input type="text" name="connum" value="" class="form-control" required='true' maxlength="15" pattern="[0-9]+">
                      </div>
                      <h4 class="card-title" style="text-align: center;">Login Details</h4>
<div class="form-group">
                        <label for="exampleInputName1">User Name</label>
                        <input type="text" name="uname" value="" class="form-control" required='true'>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputName1">Password</label>
                        <input type="Password" name="password" value="" class="form-control" required='true'>
                      </div>
                      <?php
                      if (isset($_SESSION['error'])) {
                      echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
                      unset($_SESSION['error']);}
                      ?>
                      <button type="submit" class="btn btn-primary mr-2" name="submit">Add</button>
                     
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
         <?php include_once('includes/footer.php');?>
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
</html><?php  ?>