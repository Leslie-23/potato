<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');	
}

include 'dbcon.php';

// Check if user is manager
$user_id = $_SESSION['user_id'];
$role_check = mysqli_query($con, "SELECT role FROM admin WHERE user_id = '$user_id'");
$user_role = mysqli_fetch_assoc($role_check)['role'];

if($user_role != 'manager') {
    $_SESSION['error'] = "Only managers can repair equipment";
    header('location: equipment.php');
    exit();
}

if(isset($_POST['equipment_id'])){
    $equipment_id = mysqli_real_escape_string($con, $_POST['equipment_id']);
    $repair_notes = mysqli_real_escape_string($con, $_POST['repair_notes']);
    $repair_cost = floatval($_POST['repair_cost']);
    $repaired_by = mysqli_real_escape_string($con, $_POST['repaired_by']);
    $repair_date = mysqli_real_escape_string($con, $_POST['repair_date']);
    
    // Update equipment status to 'good'
    $update_query = "UPDATE equipment SET status = 'good' WHERE id = '$equipment_id'";
    $update_result = mysqli_query($con, $update_query);
    
    if($update_result) {
        // Insert repair record
        $insert_query = "INSERT INTO equipment_repairs 
                        (equipment_id, repair_notes, repair_cost, repaired_by, repair_date) 
                        VALUES 
                        ('$equipment_id', '$repair_notes', '$repair_cost', '$repaired_by', '$repair_date')";
        $insert_result = mysqli_query($con, $insert_query);
        
        if($insert_result) {
            $_SESSION['success'] = "Equipment repaired and status updated successfully!";
        } else {
            $_SESSION['error'] = "Repair recorded but status update failed";
        }
    } else {
        $_SESSION['error'] = "Failed to update equipment status.";
    }
    
    header("location: equipment.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request";
    header("location: equipment.php");
    exit();
}
?>