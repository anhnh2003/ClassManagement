<?php
function updateTestPoint($dbh, $uid, $tid, $updateSubmitTime = true) {
    $sql = "SELECT tq.ID, CorrectAns, Point, ChooseAns FROM (SELECT * FROM tblstudent_testquestion WHERE student_id=:uid and test_id=:tid) sq RIGHT JOIN (SELECT Point, q.* FROM tbltest_question, tblquestion q WHERE question_id=q.ID and test_id=:tid) tq ON tq.ID = sq.question_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->bindParam(':tid', $tid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
  
    $point = 0;
    foreach ($results as $row) {
      if ($row->CorrectAns == $row->ChooseAns) {
        $point += $row->Point;
      }
    }
  
    if ($updateSubmitTime) {
      $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
      $submittime = $currentDateTime->format('Y-m-d H:i:s');

      $sql = "UPDATE tblstudent_test SET SubmitTime=:submittime, TotalPoint=:point WHERE student_id=:uid AND test_id=:tid";
      $query = $dbh->prepare($sql);
      $query->bindParam(':submittime', $submittime, PDO::PARAM_STR);
      $query->bindParam(':point', $point, PDO::PARAM_INT);
      $query->bindParam(':uid', $uid, PDO::PARAM_STR);
      $query->bindParam(':tid', $tid, PDO::PARAM_STR);
      $query->execute();
    } else {
      $sql = "UPDATE tblstudent_test SET TotalPoint=:point WHERE student_id=:uid AND test_id=:tid";
      $query = $dbh->prepare($sql);
      $query->bindParam(':point', $point, PDO::PARAM_INT);
      $query->bindParam(':uid', $uid, PDO::PARAM_STR);
      $query->bindParam(':tid', $tid, PDO::PARAM_STR);
      $query->execute();
    }
  }
?>