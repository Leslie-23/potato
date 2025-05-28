<?php
include '../../dbcon.php';
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];
$admin_id = $_POST['admin_id'];

$sql = "INSERT INTO tasks (title, start_datetime, end_datetime, admin_id) VALUES (?, ?, ?, ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("sssi", $title, $start, $end, $admin_id);
$stmt->execute();
?>
