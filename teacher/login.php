<?php
// Set the error display level
ini_set('display_errors', '0');
// report all errors
error_reporting(E_ALL);

// Set secure session cookie flags
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');

// Use strict mode
ini_set('session.use_strict_mode', '1');
// start session
session_start();
// Regenerate session ID upon login
if (!isset($_SESSION['initialized'])) {
    session_regenerate_id();
    $_SESSION['initialized'] = true;
}

// Set session expiration time
ini_set('session.gc_maxlifetime', 3600); // 1 hour
// Implement HTTPS enforcement in .htaccess or web server configuration

// Validate session ID (example pattern)
if (isset($_SESSION['user_id']) && !preg_match('/^[a-zA-Z0-9,-]{26,40}$/', session_id())) {
    // Invalid session ID, handle accordingly
}
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['login'])) 
  {
    $username=$_POST['username'];
    $password=$_POST['password'];
    $captcha = $_POST['g-recaptcha-response'];
    if (!$captcha){
      // notify('error', "Please check the captcha form");
      return;
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
    $sql ="SELECT ID, Password FROM tblteacher WHERE UserName=:username";
    $query=$dbh->prepare($sql);
    $query-> bindParam(':username', $username, PDO::PARAM_STR);
    $query-> execute();
    $result=$query->fetch(PDO::FETCH_OBJ);
    if($query->rowCount()>0)
{
if(password_verify($password, $result->Password)){
$_SESSION['sturecmsuid']=$result->ID;


  if(!empty($_POST["remember"])) {
//COOKIES for username
setcookie ("user_login",$_POST["username"],time()+ (10 * 365 * 24 * 60 * 60));
} else {
if(isset($_COOKIE["user_login"])) {
setcookie ("user_login","");

      }
}
$_SESSION['user_login']=$_POST['username'];

echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
} else { 
  // echo $result->ID;
  echo "<script>alert('Invalid Details');</script>";

} }else{
echo "<script>alert('Invalid Details');</script>";
}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
  
    <title>Student  Management System|| Student Login Page</title>
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
    <script src="https://www.google.com/recaptcha/api.js"></script>
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
                <form class="pt-3" id="user_login" method="post" name="user_login">
                  <div class="form-group">
                    <input type="text" class="form-control form-control-lg" placeholder="enter your username" required="true" name="username" value="<?php if(isset($_COOKIE["user_login"])) { echo $_COOKIE["user_login"]; } ?>" >
                  </div>
                  <div class="form-group">
                    
                    <input type="password" class="form-control form-control-lg" placeholder="enter your password" name="password" required="true" value="<?php if(isset($_COOKIE["userpassword"])) { echo $_COOKIE["userpassword"]; } ?>">
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