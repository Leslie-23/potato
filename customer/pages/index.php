<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- CSS Links -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/fullcalendar.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="../font-awesome/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    .user-thumb img {
    width: 70px;
    height:40px;
    /* border-radius: 0%; */
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    padding-bottom: 5px;
}

.user-thumb img:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
</style>
</head>
<body>

<!-- Header Section -->
<div id="header">
    <h1><a href="index.php">Perfect Gym System</a></h1>
</div>
<!-- End Header Section --> 

<!-- Top Navigation Menu -->
<?php include '../includes/topheader.php'?>
<!-- End Top Navigation Menu -->

<!-- Sidebar Menu -->
<?php $page="dashboard"; include '../includes/sidebar.php'?>
<!-- End Sidebar Menu -->

<!-- Main Content Section -->
<div id="content">
    <!-- Breadcrumbs -->
    <div id="content-header">
        <div id="breadcrumb"> 
            <a href="index.php" title="You're right here" class="tip-bottom">
                <i class="fas fa-home"></i> Home
            </a>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Quick Action Boxes -->
    <div class="container-fluid">
        <div class="quick-actions_homepage">
            <ul class="quick-actions">
                <li class="bg_ls span"> 
                    <a href="workout-me.php" style="font-size: 16px;"> 
                        <i class="fas fa-dumbbell"></i> Workouts 
                    </a> 
                </li>
                <li class="bg_lg span2"> 
                    <a href="payment.php" style="font-size: 16px;"> 
                        <i class="fas fa-dollar-sign"></i> Payments 
                    </a> 
                </li>
                <li class="bg_lb span2"> 
                    <a href="announcement.php" style="font-size: 16px;"> 
                        <i class="fas fa-bullhorn"></i> Announcements 
                    </a> 
                </li>
            </ul>
        </div>
    <!-- End Quick Action Boxes -->    

        <!-- Dashboard Content Row -->
        <div class="row-fluid">
            <!-- To-Do List Section -->
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title"> 
                        <span class="icon"><i class="fas fa-tasks"></i></span>
                        <h5>My To-Do List</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <?php
                            include "dbcon.php";
                            include "session.php";
                            $qry = "SELECT * FROM todo WHERE user_id='".$_SESSION['user_id']."'";
                            $result = mysqli_query($con, $qry);

                            echo "<table class='table table-striped table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>";
                            
                            while($row = mysqli_fetch_array($result)) {
                                echo "<tbody>
                                    <tr>
                                        <td class='taskDesc'>
                                            <a href='to-do.php'><i class='fas fa-plus-circle'></i></a> ".htmlspecialchars($row['task_desc'])."
                                        </td>
                                        <td class='taskStatus'>
                                            <span class='in-progress'>".htmlspecialchars($row['task_status'])."</span>
                                        </td>
                                        <td class='taskOptions'>
                                            <a href='update-todo.php?id=".$row['id']."' class='tip-top' data-original-title='Update'>
                                                <i class='fas fa-edit'></i>
                                            </a>  
                                            <a href='actions/remove-todo.php?id=".$row['id']."' class='tip-top' data-original-title='Done'>
                                                <i class='fas fa-check'></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>";
                            }
                        ?>
                        </table>
                    </div>
                </div>
            </div> 
            <!-- End To-Do List Section -->
            
            <!-- Workout and Fitness Stats Section -->
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2">
                        <span class="icon"><i class="fas fa-chevron-down"></i></span>
                        <h5>Workout and Fitness Stats</h5>
                    </div>
                    <div class="widget-content nopadding collapse in" id="collapseG2">
                        <?php
                            include "dbcon.php";
                            include "session.php";
                            
                            // Get member fitness data
                            $memberQuery = "SELECT user_height, user_weight ,fitness_goal_1,fitness_goal_2,fitness_goal_3,user_bodytype FROM members_fitness WHERE id = '".$_SESSION['user_id']."'";
                            $memberResult = mysqli_query($con, $memberQuery);
                            $memberData = mysqli_fetch_assoc($memberResult);
                            
                            // Get workout stats
                            // $statsQuery = "SELECT 
                            //                 COUNT(*) as total_workouts,
                            //                 SUM(calories_burned) as total_calories,
                            //                 AVG(duration_minutes) as avg_duration
                            //               FROM workout_logs 
                            //               WHERE user_id = '".$_SESSION['user_id']."'";
                            // $statsResult = mysqli_query($con, $statsQuery);
                            // $statsData = mysqli_fetch_assoc($statsResult);
                            
                            // Calculate BMI if height and weight are available
                            $bmi = null;
                            $bmiCategory = "Data not available";
                            if ($memberData && $memberData['user_height'] > 0 && $memberData['user_weight'] > 0) {
                                $heightInMeters = $memberData['user_height'] / 100;
                                $bmi = $memberData['user_weight'] / ($heightInMeters * $heightInMeters);
                                
                                // Determine BMI category
                                if ($bmi < 18.5) {
                                    $bmiCategory = "Underweight";
                                } elseif ($bmi >= 18.5 && $bmi < 25) {
                                    $bmiCategory = "Normal weight";
                                } elseif ($bmi >= 25 && $bmi < 30) {
                                    $bmiCategory = "Overweight";
                                } else {
                                    $bmiCategory = "Obese";
                                }
                            }
                        ?>
                        <ul class="recent-posts">
                            <li>
                                <div class="user-thumb">
                                    
<?php
include('dbcon.php');
// Get member data including new fields
$sql = "SELECT username, fullname, email, address, gender, contact, profile_pic, date_of_birth 
        FROM members WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
function getDefaultAvatar($gender) {
    $gender = strtolower($gender);
    return ($gender == 'female') ? '../img/default-female-avatar.png' : '../img/default-male-avatar.png';
}
// Set default avatar if no profile picture
if (empty($member['profile_pic'])) {
    $member['profile_pic'] = getDefaultAvatar($member['gender']);
}

// Calculate age from date of birth
$dob = new DateTime($member['date_of_birth']);
$today = new DateTime();
$age = $today->diff($dob)->y;
?>
                               <div class="user-thumb">
    <img src="<?php echo htmlspecialchars($member['profile_pic']); ?>" 
        width="70" height="70" 
        alt="Profile Picture" 
        style="border-radius: 50%; object-fit: cover; border: 2px solid #fff;">
</div>
                                </div>
                                <div class="article-post">
                                    <h4>Your Fitness Summary</h4>
                                    <div class="progress progress-striped active" style="display: none"; id="loadingProgress">
                                        <div class="bar" style="width: 80%;" ></div>
                                    </div>
                                    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Show progress bar
    const progressBar = document.getElementById('loadingProgress');
    const barInner = progressBar.querySelector('.bar');
    progressBar.style.display = 'block';
    
    // Animate progress
    let width = 0;
    const interval = setInterval(function() {
        if (width >= 100) {
            clearInterval(interval);
            // Hide after animation completes or page finishes loading
            setTimeout(() => progressBar.style.display = 'none', 300);
        } else {
            width += 10;
            barInner.style.width = width + '%';
        }
    }, 300); // Adjust speed as needed
    
    // Fallback - hide after 3 seconds if still visible
    setTimeout(() => {
        if (progressBar.style.display !== 'none') {
            clearInterval(interval);
            progressBar.style.display = 'none';
        }
    }, 5000);
});
</script>
                                    <table class="table table-bordered">
                                        <!-- <tr>
                                            <td><strong>Total Workouts</strong></td>
                                            <td>
                                                <?php echo $statsData['total_workouts'] ?? 0; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Calories Burned</strong></td>
                                            <td><?php echo $statsData['total_calories'] ?? 0; ?> kcal</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Avg. Duration</strong></td>
                                            <td><?php echo round($statsData['avg_duration'] ?? 0); ?> mins</td>
                                        </tr> -->
                                        <tr>
                                            <th><strong>BMI</strong></th>
                                            <td>
                                                <?php 
                                                    if ($bmi) {
                                                        echo round($bmi, 1) . " ($bmiCategory)";
                                                    } else {
                                                        echo "N/A";
                                                    }
                                                ?>
                                            </td>
                                            <tr>
                                                <th>Fitness Goals</th>
                                                <td> <b><?php echo $memberData['fitness_goal_1']; ?></b></td>
                                                <td> <b><?php echo $memberData['fitness_goal_2']; ?></b></td>
                                                <td> <b><?php echo $memberData['fitness_goal_3']; ?></b></td>
                                            </tr>
                                            <tr>
                                                <th>Height</th>
                                                <td><b><?php echo $memberData['user_height']; ?>cm</b></td>
                                                <td><b><?php echo $memberData['user_height']/100; ?>m</b></td>
                                            </tr>
                                            <tr>
                                                <th>Weight</th>
                                                <td><b><?php echo $memberData['user_weight']; ?>kg</b></td>
                                                <td><b><?php echo $memberData['user_weight']* 2.2; ?>lbs</b></td>
                                            </tr>
                                            <tr>
                                                <th>Body Type</th>
                                                <td><b><?php echo $memberData['user_bodytype']; ?></b></td>
                                            </tr>
                                        </tr>
                                    </table>
                                    <a href="workout-me.php">
                                        <button class="btn btn-success btn-mini">View Workout History</button>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div> 
            <!-- End Workout and Fitness Stats Section -->
        </div>
        
        <!-- Announcements Section -->
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseAnnouncements">
                        <span class="icon"><i class="fas fa-chevron-down"></i></span>
                        <h5>Gym Announcements</h5>
                    </div>
                    <div class="widget-content nopadding collapse in" id="collapseAnnouncements">
                        <ul class="recent-posts">
                            <li>
                                <?php
                                    include "dbcon.php";
                                    // Query to get announcements with sender information
                                    $qry = "SELECT a.*, 
                                                   COALESCE(s.designation, ad.role) AS sender_role,
                                                   COALESCE(s.fullname, ad.name) AS sender_name
                                            FROM announcements a
                                            LEFT JOIN staffs s ON a.id = s.user_id
                                            LEFT JOIN admin ad ON a.id = ad.user_id
                                            ORDER BY a.date DESC
                                            LIMIT 5"; // Show only 5 latest announcements
                                    
                                    $result = mysqli_query($con, $qry);
                                    
                                    if (!$result) {
                                        die("Database query failed: " . mysqli_error($con));
                                    }
                                    
                                    if (mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)) {
                                            
                                            echo "<div class='article-post'>"; 
                                            
                                            // Display sender information safely
                                            $senderName = htmlspecialchars($row['sender_name'] ?? 'System Administrator', ENT_QUOTES, 'UTF-8');
                                            $senderRole = htmlspecialchars($row['sender_role'] ?? 'Administrator', ENT_QUOTES, 'UTF-8');
                                            $date = htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8');
                                            $message = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
                                            
                                            echo "<span class='user-info'> By: $senderName ($senderRole) / Date: $date </span>";
                                            echo "<p><a href='#'>$message</a></p>";
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<p>No announcements found.</p>";
                                    }
                                    
                                    mysqli_close($con);
                                ?>                               
                                <a href="announcement.php">
                                    <button class="btn btn-warning btn-mini">View All Announcements</button>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div> 
        </div>
        <!-- End Announcements Section -->
    </div>
    <!-- End Container Fluid -->
</div>
<!-- End Main Content Section -->

<!-- Footer Section -->
<div class="row-fluid">
    <div id="footer" class="span12"> 
        <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi
    </div>
</div>
<!-- End Footer Section -->

<!-- Custom Styles -->
<style>
    #footer {
        color: white;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        max-width: 460px;
        margin: auto;
        text-align: center;
        font-family: arial;
    }

    .title {
        color: grey;
        font-size: 18px;
    }
    
    .taskOptions a {
        margin: 0 5px;
        color: #555;
    }
    
    .taskOptions a:hover {
        color: #333;
    }
    
    /* BMI indicator colors */
    .bmi-underweight {
        color: #3498db; /* Blue for underweight */
    }
    .bmi-normal {
        color: #2ecc71; /* Green for normal */
    }
    .bmi-overweight {
        color: #f39c12; /* Orange for overweight */
    }
    .bmi-obese {
        color: #e74c3c; /* Red for obese */
    }
</style>

<!-- JavaScript Files -->
<script src="../js/excanvas.min.js"></script> 
<script src="../js/jquery.min.js"></script> 
<script src="../js/jquery.ui.custom.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/jquery.flot.min.js"></script> 
<script src="../js/jquery.flot.resize.min.js"></script> 
<script src="../js/jquery.peity.min.js"></script> 
<script src="../js/fullcalendar.min.js"></script> 
<script src="../js/matrix.js"></script> 
<script src="../js/matrix.dashboard.js"></script> 
<script src="../js/jquery.gritter.min.js"></script> 
<script src="../js/matrix.interface.js"></script> 
<script src="../js/matrix.chat.js"></script> 
<script src="../js/jquery.validate.js"></script> 
<script src="../js/matrix.form_validation.js"></script> 
<script src="../js/jquery.wizard.js"></script> 
<script src="../js/jquery.uniform.js"></script> 
<script src="../js/select2.min.js"></script> 
<script src="../js/matrix.popover.js"></script> 
<script src="../js/jquery.dataTables.min.js"></script> 
<script src="../js/matrix.tables.js"></script> 

<!-- Page Navigation Script -->
<script type="text/javascript">
    function goPage (newURL) {
        if (newURL != "") {
            if (newURL == "-" ) {
                resetMenu();            
            } else {  
                document.location.href = newURL;
            }
        }
    }

    function resetMenu() {
        document.gomenu.selector.selectedIndex = 2;
    }
    
    // Initialize tooltips
    $(document).ready(function(){
        $('[data-original-title]').tooltip();
    });
</script>
</body>
</html>