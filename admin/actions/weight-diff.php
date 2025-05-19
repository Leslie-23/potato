<?php

$servername="localhost";
$uname="new_user";
$pass="new_password";
$db="elitefit-23";
$con=mysqli_connect($servername,$uname,$pass,$db);

if(!$con){
    die("Connection Failed");
}

$sql = "SELECT SUM( curr_weight - ini_weight) FROM members WHERE user_id='$id'";
        $amountsum = mysqli_query($con, $sql) or die(mysqli_error($sql));
        $row_amountsum = mysqli_fetch_assoc($amountsum);
        $totalRows_amountsum = mysqli_num_rows($amountsum);
        echo $row_amountsum['SUM( curr_weight - ini_weight)'];

                
?>