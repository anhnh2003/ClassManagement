<?php
session_start();
//error_reporting(0);
include('../includes/dbconnection.php');
include('../includes/sendEmail.php');
include('../includes/util.php');

$btnSubmit = "";
$btnConfirm = "display: none;";
$hideOTP = "display: none;";
$readonlyEmail = "";
$valueEmail = "";
$readonlyNewPassword = "";
$valueNewPassword = "";
$readonlyConfirmPassword = "";
$valueConfirmPassword = "";

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $sql = "SELECT ContactNumber, Email, StudentName FROM tblstudent WHERE Email=:email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        $newpassword = $_POST['newpassword'];
        $confirmpassword = $_POST['confirmpassword'];

        if(!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,}$/', $newpassword)) {
            $_SESSION['error'] = "Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one symbol.";
            header('Location: forgot-password.php');
        exit();
        }

        if ($newpassword != $confirmpassword) {
            echo "<script>alert('New Password and Confirm Password do not match');</script>";
        } else {
            // Update fields
            $valueEmail = $email;
            $readonlyEmail = "readonly";
            $valueNewPassword = $newpassword;
            $readonlyNewPassword = "readonly";
            $valueConfirmPassword = $confirmpassword;
            $readonlyConfirmPassword = "readonly";
            $btnSubmit = "display: none;";
            $btnConfirm = "";
            $hideOTP = "";

            // Save OTP
            $genotp = randomGen(6);
            $_SESSION['otp'] = $genotp;
            $_SESSION['newpassword'] = $newpassword;
            $_SESSION['email'] = $email;

            // Send OTP
            sendEmail($results[0]->StudentName, $genotp, $email, 'reset');
        }
    } else {
        echo "<script>alert('Email is invalid');</script>";
    }
}

if (isset($_POST['confirm'])) {
    $otp = $_POST['otp'];
    if ($otp == $_SESSION['otp'] && $otp != null) {
        $_SESSION['otp'] = null;
        $newpassword = password_hash($_SESSION['newpassword'], PASSWORD_DEFAULT);
        $con = "update tblstudent set Password=:newpassword where Email=:email";
        $chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        $chngpwd1->execute();
        echo "<script>alert('Your Password has been successfully changed');</script>";
        echo "<script>window.location.href ='../index.php'</script>";
    } else {
        $_SESSION['otp'] = null;
        echo "<script>alert('OTP is incorrect');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Management System || Forgot Password</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript"></script>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo">
                                <img src="images/logo.svg">
                            </div>
                            <h4>RECOVER PASSWORD</h4>
                            <h6 class="font-weight-light">Enter your Email address to reset password!</h6>
                            <form class="pt-3" id="login" method="post" name="login">
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-lg" placeholder="Email Address" required="true" name="email" value="<?php echo htmlentities($valueEmail); ?>" <?php echo htmlentities($readonlyEmail); ?>>
                                </div>
                                <div class="form-group">
                                    <input class="form-control form-control-lg" type="password" name="newpassword" value="<?php echo htmlentities($valueNewPassword); ?>" placeholder="New Password" required="true" <?php echo htmlentities($readonlyNewPassword); ?> />
                                </div>
                                <div class="form-group">
                                    <input class="form-control form-control-lg" type="password" name="confirmpassword" value="<?php echo htmlentities($valueConfirmPassword); ?>" placeholder="Confirm Password" required="true" <?php echo htmlentities($readonlyConfirmPassword); ?> />
                                </div>
                                <?php
                                if (isset($_SESSION['error'])) {
                                echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
                                unset($_SESSION['error']);}
                                ?>
                                <div class="mt-3" style="<?php echo htmlentities($btnSubmit); ?>">
                                    <button class="btn btn-success btn-block loginbtn" name="submit" type="submit">Reset</button>
                                </div>
                            </form>
                            <form class="pt-3" id="sendotp" method="post" name="sendotp">
                                <div class="form-group" style="<?php echo htmlentities($hideOTP); ?>">
                                    <input class="form-control form-control-lg" type="text" name="otp" placeholder="Enter OTP sent to Email" maxlength='6' required='true' />
                                </div>
                                <div class="mt-3" style="<?php echo htmlentities($btnConfirm); ?>">
                                    <button class="btn btn-success btn-block loginbtn" name="confirm" type="submit">Confirm OTP</button>
                                </div>
                                <div class="mt-2">
                                    <a href="login.php" class="btn btn-block btn-facebook auth-form-btn">
                                        <i class="icon-social-home mr-2"></i>Back </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>

</html>
