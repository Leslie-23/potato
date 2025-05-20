<?php
// count-attendance.php
include "dbcon.php";

// Function to count active members
// function countActiveMembers($con) {
//     $query = "SELECT COUNT(*) as active_count FROM members WHERE status = 'Active'";
//     $result = mysqli_query($con, $query);
//     $row = mysqli_fetch_assoc($result);
//     return $row['active_count'];
// }
function countExpiredMembers($con) {
    $query = "SELECT COUNT(*) as expired_count FROM members WHERE status = 'Expired'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['expired_count'];
}

// Function to count today's attendance
// function countTodaysAttendance($con) {
//     $today = date('Y-m-d');
//     $query = "SELECT COUNT(DISTINCT user_id) as attendance_count FROM attendance 
//               WHERE DATE(curr_date) = '$today' AND present = 1";
//     $result = mysqli_query($con, $query);
//     $row = mysqli_fetch_assoc($result);
//     return $row['attendance_count'];
// }

// // Function to count monthly attendance
// function countMonthlyAttendance($con) {
//     $month = date('Y-m');
//     $query = "SELECT COUNT(DISTINCT user_id) as monthly_count FROM attendance 
//               WHERE DATE_FORMAT(curr_date, '%Y-%m') = '$month' AND present = 1";
//     $result = mysqli_query($con, $query);
//     $row = mysqli_fetch_assoc($result);
//     return $row['monthly_count'];
// }

// Get all counts
// $activeMembers = countActiveMembers($con);
$expiredMembers = countExpiredMembers($con);
// $todaysAttendance = countTodaysAttendance($con);
// $monthlyAttendance = countMonthlyAttendance($con);

// Calculate attendance percentage (avoid division by zero)
// $attendancePercentage = $activeMembers > 0 ? round(($todaysAttendance / $activeMembers) * 100, 1) : 0;
?>
<!-- <div class="attendance-stats"> -->
    <!-- <div class="stat-item"> -->
        <!-- <h4>Active Members</h4> -->
        <!-- <div class="stat-value"><?php echo $activeMembers; ?></div> -->
        <!-- <div class="stat-value"><?php echo $pendingMembers; ?></div> -->
        <div class="stat-value"><?php echo $expiredMembers; ?></div>
    <!-- </div> -->
    
    <!-- <div class="stat-item">
        <h4>Today's Attendance</h4>
        <div class="stat-value"><?php echo $todaysAttendance; ?></div>
        <div class="stat-subtext"><?php echo $attendancePercentage; ?>% of active</div>
    </div> -->
    
    <!-- <div class="stat-item">
        <h4>Monthly Attendance</h4>
        <div class="stat-value"><?php echo $monthlyAttendance; ?></div>
    </div> -->
<!-- </div> -->

<!-- 
<style>
.attendance-stats {
    display: flex;
    justify-content: space-around;
    text-align: center;
    margin: 20px 0;
}
.stat-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    flex: 1;
    margin: 0 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}
.stat-subtext {
    font-size: 12px;
    color: #7f8c8d;
}
</style> -->