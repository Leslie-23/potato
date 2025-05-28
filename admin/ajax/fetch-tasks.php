<?php
include '../../dbcon.php';
$admin_id = $_GET['admin_id'];
$sql = "SELECT * FROM tasks WHERE admin_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start_datetime'],
        'end' => $row['end_datetime']
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
?>
