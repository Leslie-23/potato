<?php
$servername = "localhost";
$uname = "new_user";
$pass = "new_password";
$db = "elitefit-23";

$conn = mysqli_connect($servername, $uname, $pass, $db);

if (!$conn) {
    die("Connection Failed");
}

$sql = "SELECT * FROM transactions WHERE status = 'pending'";
$query = $conn->query($sql);

echo $query->num_rows;

$conn->close();
?>