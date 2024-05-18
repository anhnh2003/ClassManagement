<?php
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
    // Token is valid, continue

$uid = $_COOKIE['uid'];

$sql = "SELECT tblclass.* from tblclass where teacher_id=:uid ORDER BY tblclass.CreationTime DESC";
$query = $dbh->prepare($sql);
$query->bindParam(':uid',$uid,PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

if ($query->rowCount() > 0) {
    echo json_encode($results);
} else {
    echo json_encode([]);
}}}
?>