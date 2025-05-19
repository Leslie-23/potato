<?php

session_start();
//the isset function to check username is already loged in and stored on the session
if(!isset($_SESSION['user_id'])){
header('location:../index.php');	
}

include('../dbcon.php');

$user_id = $_GET['id'];


$sql = "DELETE FROM attendance WHERE user_id='".$_GET["id"]."'";
// $sql = "UPDATE attendance 
//         SET status = 'absent', 
//             check_in = NULL,
//             check_out = NULL,
//             duration = NULL
//         WHERE user_id = '$user_id' 
//         AND curr_date = CURDATE()
//         AND status = 'present'";

if($con->query($sql)) {
    $_SESSION['message'] = "Attendance status updated to absent";
} else {
    $_SESSION['error'] = "Error updating attendance: " . $con->error;
}
$res = $con->query($sql) ;


 $attend = "select * from members where user_id = '$user_id'";
  $result_attend = $con->query($attend);
  $row_attend = mysqli_fetch_array($result_attend);
  $cnt = $row_attend['attendance_count'];
 $attend_count = $cnt;
      $sql1 = "update members set attendance_count ='$attend_count' where user_id='$user_id'";
     $con->query($sql1) ;
?>
<script>
// alert("Delete Successfully");
window.location = "../attendance.php";
</script>


 