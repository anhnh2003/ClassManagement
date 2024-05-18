<?php
session_start();
include('includes/dbconnection.php');



// function notify($status, $msg){
//   return die('<script type="text/javascript">Swal.fire("Error", "'.$msg.'", "'.$status.'"); setTimeout(function(){location.href="/classmanagement/student/login.php";},2000);</script>');
//   }
if(isset($_POST['login'])) 
  {
    $stuid=$_POST['stuid'];
    $password=($_POST['password']);
    $captcha = $_POST['g-recaptcha-response'];
    if (!$captcha){
      return;
      // notify('error', "Please check the captcha form");
    }
    else {
      $secret = '6LctYtwpAAAAAEP0w5UdNiqxoKbvdQo8WfQI-QtG';
      $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $captcha);
      $response_data = json_decode($verify_response);
      if (!$response_data->success) {
        return;
        // notify('error', "Captcha verification failed! Please try again.");
      }
    }
    $sql ="SELECT StuID,ID Password FROM tblstudent WHERE UserName=:stuid";
    $query=$dbh->prepare($sql);
    $query-> bindParam(':stuid', $stuid, PDO::PARAM_STR);
    $query-> execute();
    $result=$query->fetch(PDO::FETCH_OBJ);
    if($query->rowCount() > 0)
{
  if(password_verify($password, $result->Password)){
$_SESSION['sturecmsstuid']=$result->StuID;
$_SESSION['sturecmsuid']=$result->ID;
// Generate a random session token
$token = bin2hex(random_bytes(32));
// Store the token in the database
$insertTokenSQL = "INSERT INTO tbltoken (UserToken, UserID, role_id) VALUES (:token, :userid, 3)";
$tokenQuery = $dbh->prepare($insertTokenSQL);
$tokenQuery->bindParam(':token', $token, PDO::PARAM_STR);
$tokenQuery->bindParam(':userid', $result->ID, PDO::PARAM_INT);
$tokenQuery->execute();

// Send the token to the client to save it
setcookie("session_token", $token, time() + 7200); // 7200 seconds = 2 hours


if(!empty($_POST["remember"])) {
//COOKIES for username
setcookie ("uid",$result->ID,time()+7200);
} else {

setcookie ("uid",$result->ID,time()+9999);

  
}

$_SESSION['login']=$_POST['stuid'];
echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
} } else{
echo "<script>alert('Invalid Details');</script>";
}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
  
    <title>Student Management System || Student Login Page</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css">
   <script src="https://www.google.com/recaptcha/api.js" ></script>
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left p-5">
                <div class="brand-logo">
                  <img src="images/logo.svg"> SMS
                </div>
                <h4>Hello! let's get started</h4>
                <h6 class="font-weight-light">Sign in to continue.</h6>
                <form class="pt-3" id="login" method="post" name="login">
                  <div class="form-group">
                    <input type="text" class="form-control form-control-lg" placeholder="Enter Student id/Username" required="true" name="stuid" value="<?php if(isset($_COOKIE["user_login"])) { echo $_COOKIE["user_login"]; } ?>" >
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" placeholder="Enter Password" name="password" required="true" value="<?php if(isset($_COOKIE["userpassword"])) { echo $_COOKIE["userpassword"]; } ?>">
                  </div>
                  
                    <div class="g-recaptcha" data-sitekey="6LctYtwpAAAAAGqtbFtdwU1jq_hcUDl0rgjxmYSU"></div>
                  
                  <div class="mt-3">
                    <button class="btn btn-success btn-block loginbtn" name="login" type="submit">Login</button>
                  </div>
                  <div class="my-2 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" id="remember" class="form-check-input" name="remember" <?php if(isset($_COOKIE["user_login"])) { ?> checked <?php } ?> /> Keep me signed in </label>
                    </div>
                    <a href="forgot-password.php" class="auth-link text-black">Forgot password?</a>
                  </div>
                  <div class="mb-2">
                    <a href="../index.php" class="btn btn-block btn-facebook auth-form-btn">
                      <i class="icon-social-home mr-2"></i>Back Home </a>
                  </div>
                  
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <!-- endinject -->
  </body>
</html>