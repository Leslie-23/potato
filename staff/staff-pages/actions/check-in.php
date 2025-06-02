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
    
    // Check if already checked in today
    $check_qry = "SELECT * FROM attendance 
                 WHERE user_id = '$member_id' 
                 AND curr_date = '$current_date'";
    $check_res = mysqli_query($con, $check_qry);
    
    if(mysqli_num_rows($check_res) == 0) {
        // Record check-in
        $insert_qry = "INSERT INTO attendance 
                      (user_id, curr_date, curr_time, check_in, status) 
                      VALUES 
                      ('$member_id', '$current_date', '$current_time', '$current_time', 'present')";
        mysqli_query($con, $insert_qry);
    }
    
    header('location: ../attendance.php');
}
?>