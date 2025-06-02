<?php
session_start();
include "../dbcon.php";

if(!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}

if(isset($_GET['id'])) {
    $member_id = $_GET['id'];
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    // Get the check-in record
    $check_qry = "SELECT * FROM attendance 
                 WHERE user_id = '$member_id' 
                 AND curr_date = '$current_date'
                 AND check_out IS NULL
                 ORDER BY id DESC LIMIT 1";
    $check_res = mysqli_query($con, $check_qry);
    
    if(mysqli_num_rows($check_res) > 0) {
        $attendance = mysqli_fetch_assoc($check_res);
        $check_in_time = $attendance['check_in'];
        
        // Calculate duration
        $check_in = new DateTime($check_in_time);
        $check_out = new DateTime($current_time);
        $duration = $check_out->diff($check_in);
        $duration_str = $duration->format('%H:%I:%S');
        
        // Update with check-out time and duration
        $update_qry = "UPDATE attendance 
                      SET check_out = '$current_time', 
                          duration = '$duration_str',
                          status = 'completed'
                      WHERE id = ".$attendance['id'];
        mysqli_query($con, $update_qry);
    }
    
    header('location: ../attendance.php');
}
?>