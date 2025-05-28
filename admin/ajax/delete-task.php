<?php
include '../../dbcon.php';
$id = $_POST['id'];
$sql = "DELETE FROM tasks WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
?>
