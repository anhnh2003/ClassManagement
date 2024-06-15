<?php
session_start();
include('../includes/dbconnection.php');
$uid = $_COOKIE['uid'] ?? '';

if (strlen($_SESSION['sturecmsuid']) == 0) {
  header('location:logout.php');
} else {
  if (isset($_POST['leave'])) {
    $sql = "DELETE FROM tblstudent_class WHERE student_id=:uid AND class_id=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('You have left the class');</script>";
    echo "<script>window.location.href = 'manage-class.php'</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Student Management System || Check Attendance</title>
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
            <h3 class="page-title"> Check Attendance </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page"> Check Attendance</li>
              </ol>
            </nav>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-sm-flex align-items-center mb-4">
                    <h4 class="card-title mb-sm-0">Scan Attendance QR</h4>
                    <a href="#" class="text-dark ml-auto mb-3 mb-sm-0"> </a>
                  </div>
                  <div class="text-center">
                    <button class="btn btn-primary mr-2" id="startStop">Start Camera</button>
                    <button class="btn btn-secondary mr-2" id="snap" disabled>Snap Photo</button>
                  </div>
                  <div class="text-center">
                    <video id="video" width="800" height="600" autoplay></video>
                    <canvas id="canvas" width="800" height="600"></canvas>
                  </div>
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
  <script src="https://unpkg.com/jsqr"></script>
  <script>
    var video = document.getElementById('video');
    var canvas = document.getElementById('canvas');
    var context = canvas.getContext('2d');
    var snap = document.getElementById('snap');
    var startStop = document.getElementById('startStop');
    var stream;

    // Get access to the camera
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
      startStop.addEventListener("click", function() {
        if (stream) {
          stream.getTracks().forEach(track => track.stop());
          stream = null;
          startStop.className = 'btn btn-primary mr-2';
          startStop.textContent = 'Start Camera';
          snap.className = 'btn btn-secondary mr-2';
          snap.disabled = true;
        } else {
          navigator.mediaDevices.getUserMedia({ video: true }).then(function(mediaStream) {
            stream = mediaStream;
            video.srcObject = stream;
            video.play();
            startStop.className = 'btn btn-danger mr-2';
            startStop.textContent = 'Stop Camera';
            snap.className = 'btn btn-primary mr-2';
            snap.disabled = false;
          });
        }
      });
    }

    // Trigger photo take
    snap.addEventListener("click", function() {
      var videoWidth = video.videoWidth;
      var videoHeight = video.videoHeight;
      var canvasWidth = videoWidth;
      var canvasHeight = videoHeight;
      if (videoWidth > 800 || videoHeight > 600) {
        var aspectRatio = videoWidth / videoHeight;
        if (aspectRatio > 1) {
          canvasWidth = 800;
          canvasHeight = 800 / aspectRatio;
        } else {
          canvasWidth = 600 * aspectRatio;
          canvasHeight = 600;
        }
      } else {
        canvasWidth = videoWidth;
        canvasHeight = videoHeight;
      }
      var offsetX = (800 - canvasWidth) / 2;
      var offsetY = (600 - canvasHeight) / 2;
      context.drawImage(video, offsetX, offsetY, canvasWidth, canvasHeight);
      var dataUrl = canvas.toDataURL('image/png');
      // Send the data URL to the server-side script
      $.ajax({
        url: 'scanQR.php',
        type: 'post',
        data: { imgData: dataUrl, uid: '<?php echo $uid; ?>'},
        success: function(response) {
          if (response == 1) {
            alert('Attendance recorded!');
            window.location.href = 'manage-class.php';
          } else if (response == -1) {
            alert('Invalid or expired QR code');
          } else if (response == -2) {
            alert('Class already attended!');
            window.location.href = 'manage-class.php';
          }
        }
      });
    });
    canvas.style.display = 'none'; // Hide the canvas
  </script>
  <!-- End custom js for this page -->
</body>

</html>