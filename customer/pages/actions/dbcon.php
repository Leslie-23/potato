<?php
$con = mysqli_connect("localhost","new_user","new_password","elitefit-23");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
?> 
