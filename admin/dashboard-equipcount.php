<?php


$servername="localhost";
$uname="new_user";
$pass="new_password";
$db="elitefit-23";

$con=mysqli_connect($servername,$uname,$pass,$db);

if(!$con){
    die("Connection Failed");
}

$sql = "SELECT * FROM equipment";
                $query = $con->query($sql);

                echo "$query->num_rows";
                
?> 