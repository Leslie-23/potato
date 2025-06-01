<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');
    exit();
}

include "dbcon.php";

// Initialize variables
$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_plan'])) {
        // Save/update fitness plan
        $user_weight = mysqli_real_escape_string($con, $_POST['user_weight']);
        $user_height = mysqli_real_escape_string($con, $_POST['user_height']);
        $user_bodytype = mysqli_real_escape_string($con, $_POST['user_bodytype']);
        $preferred_workout_plan_1 = mysqli_real_escape_string($con, $_POST['preferred_workout_plan_1']);
        $fitness_goal_1 = mysqli_real_escape_string($con, $_POST['fitness_goal_1']);
        $fitness_goal_2 = mysqli_real_escape_string($con, $_POST['fitness_goal_2']);
        $fitness_goal_3 = mysqli_real_escape_string($con, $_POST['fitness_goal_3']);
        $health_condition = mysqli_real_escape_string($con, $_POST['health_condition']);
        $preferred_workout_plan_2 = mysqli_real_escape_string($con, $_POST['preferred_workout_plan_2']);
        $preferred_workout_plan_3 = mysqli_real_escape_string($con, $_POST['preferred_workout_plan_3']);
        $experience_level = mysqli_real_escape_string($con, $_POST['experience_level']);
        $health_condition_desc = mysqli_real_escape_string($con, $_POST['health_condition_desc']);

        // Check if plan exists
        $check_query = "SELECT * FROM members_fitness WHERE id = '$user_id'";
        $check_result = mysqli_query($con, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Update existing plan
            $query = "UPDATE members_fitness SET 
                      user_weight = '$user_weight',
                      user_height = '$user_height',
                      user_bodytype = '$user_bodytype',
                      preferred_workout_plan_1 = '$preferred_workout_plan_1',
                      fitness_goal_1 = '$fitness_goal_1',
                      fitness_goal_2 = '$fitness_goal_2',
                      fitness_goal_3 = '$fitness_goal_3',
                      health_condition = '$health_condition',
                      preferred_workout_plan_2 = '$preferred_workout_plan_2',
                      preferred_workout_plan_3 = '$preferred_workout_plan_3',
                      experience_level = '$experience_level',
                      health_condition_desc = '$health_condition_desc'
                      WHERE id = '$user_id'";
        } else {
            // Create new plan
            $query = "INSERT INTO members_fitness (
                      user_id, user_weight, user_height, user_bodytype, 
                      preferred_workout_plan_1, fitness_goal_1, fitness_goal_2, 
                      fitness_goal_3, health_condition, preferred_workout_plan_2, 
                      preferred_workout_plan_3, experience_level, health_condition_desc
                      ) VALUES (
                      '$user_id', '$user_weight', '$user_height', '$user_bodytype', 
                      '$preferred_workout_plan_1', '$fitness_goal_1', '$fitness_goal_2', 
                      '$fitness_goal_3', '$health_condition', '$preferred_workout_plan_2', 
                      '$preferred_workout_plan_3', '$experience_level', '$health_condition_desc'
                      )";
        }

        if (mysqli_query($con, $query)) {
            $message = "Fitness plan saved successfully!";
            
            // Also update the members table with current weight/bodytype
            $update_member = "UPDATE members SET 
                             curr_weight = '$user_weight',
                             curr_bodytype = '$user_bodytype',
                             progress_date = NOW()
                             WHERE user_id = '$user_id'";
            mysqli_query($con, $update_member);
        } else {
            $error = "Error saving fitness plan: " . mysqli_error($con);
        }
    }
}

// Get member data
$member_query = "SELECT * FROM members WHERE user_id = '$user_id'";
$member_result = mysqli_query($con, $member_query);
$member = mysqli_fetch_assoc($member_result);

// Get fitness plan data
$fitness_query = "SELECT * FROM members_fitness WHERE id = '$user_id'";
$fitness_result = mysqli_query($con, $fitness_query);
$fitness_plan = mysqli_num_rows($fitness_result) > 0 ? mysqli_fetch_assoc($fitness_result) : null;
?>

<?php
// Get all workout plans
$plans_query = "SELECT * FROM workout_plan ORDER BY workout_name";
$plans_result = mysqli_query($con, $plans_query);
$plans = [];
while($row = mysqli_fetch_assoc($plans_result)) {
    $plans[] = $row;
}

// Get fitness plan with joined workout plan names
$fitness_query = "SELECT mf.*, 
                 wp1.workout_name as plan_name_1, 
                 wp2.workout_name as plan_name_2, 
                 wp3.workout_name as plan_name_3
                 FROM members_fitness mf
                 LEFT JOIN workout_plan wp1 ON mf.preferred_workout_plan_1 = wp1.table_id
                 LEFT JOIN workout_plan wp2 ON mf.preferred_workout_plan_2 = wp2.table_id
                 LEFT JOIN workout_plan wp3 ON mf.preferred_workout_plan_3 = wp3.table_id
                 WHERE mf.id = '$user_id'";
$fitness_result = mysqli_query($con, $fitness_query);
$fitness_plan = mysqli_num_rows($fitness_result) > 0 ? mysqli_fetch_assoc($fitness_result) : null;
?>

<?php
include "dbcon.php";
include "session.php";

// Get member data including date of birth
$sql = "SELECT date_of_birth FROM members WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

// Calculate age from date of birth
if (!empty($member['date_of_birth'])) {
    $dob = new DateTime($member['date_of_birth']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
} else {
    $age = "Not specified";
}
?>

                                 
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

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym System - Workouts</title>
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
    <style>
        .fitness-card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .fitness-card .card-header {
            font-weight: bold;
        }
        .workout-plan {
            border-left: 4px solid #007bff;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .goal-list {
            list-style-type: none;
            padding-left: 0;
        }
        .goal-list li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .health-alert {
            background-color: #fff3cd;
        }
    </style>
    <style>
    .user-thumb img {
    width: 240px;
    height:120px;
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
.user-info {
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f8f8;
    border-radius: 4px;
}

.user-info p {
    margin: 5px 0;
    font-size: 14px;
}

/* .age-display {
    font-size: 16px;
    margin-bottom: 15px;
    padding: 8px 12px;
    background: #f0f8ff;
    border-left: 4px solid #3498db;
    border-radius: 3px;
} */
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
<?php $page="workouts"; include '../includes/sidebar.php'?>
<!-- End Sidebar Menu -->

<!-- Main Content Section -->
<div id="content">
    <!-- Breadcrumbs -->
    <div id="content-header">
        <div id="breadcrumb"> 
            <a href="index.php" title="Go to Home" class="tip-bottom">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="workout-me.php" title="You're right here" class="current">Workouts & Fitness Plan</a>
        </div>
        <h1>My Workouts & Fitness Plan</h1>
    </div>
    <!-- End Breadcrumbs -->

    <div class="container-fluid">
        <?php if ($message): ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box fitness-card">
                    <div class="widget-title bg_lg">
                        <span class="icon"><i class="fas fa-user"></i></span>
                        <h5>My Fitness Profile</h5>
                    </div>
                    <div class="widget-content">
                        <form method="POST" action="workout-me.php">
                            <div class="row-fluid">
                                <div class="span6">
                                    <div class="control-group">
                                        <label class="control-label">Current Weight (kg)</label>
                                        <div class="controls">
                                            <input type="number" step="0.1" class="span12" name="user_weight" 
                                                   value="<?php echo $fitness_plan ? htmlspecialchars($fitness_plan['user_weight']) : "" ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Height (cm)</label>
                                        <div class="controls">
                                            <input type="number" class="span12" name="user_height" 
                                                   value="<?php echo $fitness_plan ? htmlspecialchars($fitness_plan['user_height']) : ''; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Body Type</label>
                                        <div class="controls">
                                            <select class="span12" name="user_bodytype" required>
                                                <option value="" disabled selected>Choose Body type</option>
                                                <option value="Ectomorph" <?php echo ($fitness_plan && $fitness_plan['user_bodytype'] == 'Ectomorph') || $member['curr_bodytype'] == 'Ectomorph' ? 'selected' : ''; ?>>Ectomorph (Lean)</option>
                                                <option value="Mesomorph" <?php echo ($fitness_plan && $fitness_plan['user_bodytype'] == 'Mesomorph') || $member['curr_bodytype'] == 'Mesomorph' ? 'selected' : ''; ?>>Mesomorph (Athletic)</option>
                                                <option value="Endomorph" <?php echo ($fitness_plan && $fitness_plan['user_bodytype'] == 'Endomorph') || $member['curr_bodytype'] == 'Endomorph' ? 'selected' : ''; ?>>Endomorph (Round)</option>
                                            </select>
<!-- 
                                            <option value="Ectomorph">Ectomorph (Lean)</option>
                                            <option value="Mesomorph">Mesomorph (Athletic)</option>
                                            <option value="Endomorph">Endomorph (Round)</option> -->
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Experience Level</label>
                                        <div class="controls">
                                            <select class="span12" name="experience_level" required>
                                                <option value="" disabled >Enter Experience level</option>
                                                <option value="1" <?php echo $fitness_plan && $fitness_plan['experience_level'] == 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                                                <option value="5" <?php echo $fitness_plan && $fitness_plan['experience_level'] == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                                <option value="9" <?php echo $fitness_plan && $fitness_plan['experience_level'] == 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="span6">
                                    <div class="control-group">
                                        <label class="control-label">Primary Fitness Goal</label>
                                        <div class="controls">
                                            <input type="text" class="span12" name="fitness_goal_1" 
                                                   value="<?php echo $fitness_plan ? htmlspecialchars($fitness_plan['fitness_goal_1']) : ''; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Secondary Fitness Goal</label>
                                        <div class="controls">
                                            <input type="text" class="span12" name="fitness_goal_2" 
                                                   value="<?php echo $fitness_plan ? htmlspecialchars($fitness_plan['fitness_goal_2']) : ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Tertiary Fitness Goal</label>
                                        <div class="controls">
                                            <input type="text" class="span12" name="fitness_goal_3" 
                                                   value="<?php echo $fitness_plan ? htmlspecialchars($fitness_plan['fitness_goal_3']) : ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Any Health Conditions?</label>
                                        <div class="controls">
                                            <select class="span12" name="health_condition" id="health_condition" required>
                                                <option value="No" <?php echo $fitness_plan && $fitness_plan['health_condition'] == 'No' ? 'selected' : ''; ?>>No</option>
                                                <option value="Yes" <?php echo $fitness_plan && $fitness_plan['health_condition'] == 'Yes' ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row-fluid">
                                <div class="span12">
                                    <div class="control-group" id="health_condition_desc_group" style="<?php echo ($fitness_plan && $fitness_plan['health_condition'] == 'Yes') ? '' : 'display: none;'; ?>">
                                        <label class="control-label">Health Condition Description</label>
                                        <div class="controls">
                                            <textarea class="span12" name="health_condition_desc" rows="3"><?php echo $fitness_plan ? htmlspecialchars($fitness_plan['health_condition_desc']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row-fluid">
    <div class="span4">
        <div class="control-group">
            <label class="control-label">Primary Workout Plan</label>
            <div class="controls">
                <select class="span12" name="preferred_workout_plan_1" required>
                    <option value="">Select a plan</option>
                    <?php foreach ($plans as $plan): ?>
                    <option value="<?php echo $plan['table_id']; ?>" 
                        <?php echo ($fitness_plan && $fitness_plan['preferred_workout_plan_1'] == $plan['table_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($plan['workout_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="span4">
        <div class="control-group">
            <label class="control-label">Secondary Workout Plan</label>
            <div class="controls">
                <select class="span12" name="preferred_workout_plan_2">
                    <option value="">Select a plan (optional)</option>
                    <?php foreach ($plans as $plan): ?>
                    <option value="<?php echo $plan['table_id']; ?>" 
                        <?php echo ($fitness_plan && $fitness_plan['preferred_workout_plan_2'] == $plan['table_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($plan['workout_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="span4">
        <div class="control-group">
            <label class="control-label">Tertiary Workout Plan</label>
            <div class="controls">
                <select class="span12" name="preferred_workout_plan_3">
                    <option value="">Select a plan (optional)</option>
                    <?php foreach ($plans as $plan): ?>
                    <option value="<?php echo $plan['table_id']; ?>" 
                        <?php echo ($fitness_plan && $fitness_plan['preferred_workout_plan_3'] == $plan['table_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($plan['workout_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <p class="text-warning">Be mindful, Data changes here may impact overall perfomace and reporting. Seek clearance from Trainer</p>
</div>
                            
                            <div class="form-actions">
                                <button type="submit" name="save_plan" class="btn btn-success">
                                    <i class="fas fa-save"></i> Save Fitness Plan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Display current plan -->
                <?php if ($fitness_plan): ?>
                <div class="widget-box fitness-card">
                    <div class="widget-title bg_ly">
                        <span class="icon"><i class="fas fa-file-alt"></i></span>
                        <h5>My Current Fitness Plan</h5>
                    </div>
                    <div class="widget-content">
                        <div class="row-fluid">
                            <div class="span6">
                                <h4>Personal Stats</h4>
                                <p><strong>Weight:</strong> <?php echo htmlspecialchars($fitness_plan['user_weight']); ?> kg</p>
                                <p><strong>Height:</strong> <?php echo htmlspecialchars($fitness_plan['user_height']); ?> cm</p>
                                <p><strong>Body Type:</strong> <?php echo htmlspecialchars($fitness_plan['user_bodytype']); ?></p>
                                <p><strong>Experience Level:</strong> <?php 
                                if ($fitness_plan['experience_level'] == 1)
                                    $fitness_level="Beginner";
                                if ($fitness_plan['experience_level'] == 5)
                                    $fitness_level="Intermediate";
                                if ($fitness_plan['experience_level'] == 9)
                                    $fitness_level="Advanced";
                                
                                // echo htmlspecialchars($fitness_plan['experience_level']) ;
                            //    echo " <div style="padding:2px"></div>";
                                echo $fitness_level; 
                                ?>
                                </p>
                                <div class="age-display">
    <strong>Age:</strong> <?php echo htmlspecialchars($age); ?>
</div>
                            </div>
                            
                           
                            <div class="span6">
                                <h4>Fitness Goals</h4>
                               
                                
                                <ul class="goal-list">
                                    <?php if (!empty($fitness_plan['fitness_goal_1'])): ?>
                                    <li><?php echo htmlspecialchars($fitness_plan['fitness_goal_1']); ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($fitness_plan['fitness_goal_2'])): ?>
                                    <li><?php echo htmlspecialchars($fitness_plan['fitness_goal_2']); ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($fitness_plan['fitness_goal_3'])): ?>
                                    <li><?php echo htmlspecialchars($fitness_plan['fitness_goal_3']); ?></li>
                                    <?php endif; ?>
                                </ul>
                                
                            </div>
                            <div class="user-thumb">
                                    <!-- <img src="<?php echo htmlspecialchars($member['profile_pic']); ?>" 
                                    width="240" height="120" 
                                    alt="Profile Picture" 
                                    > -->
                        </div>
                        
                        <div class="row-fluid">
    <div class="span12">
        <h4>Workout Plans</h4>
        <div class="workout-plan">
            <h5>Primary Plan</h5>
            <p><?php echo !empty($fitness_plan['plan_name_1']) ? htmlspecialchars($fitness_plan['plan_name_1']) : 'Not specified'; ?></p>
        </div>
        
        <?php if (!empty($fitness_plan['preferred_workout_plan_2'])): ?>
        <div class="workout-plan">
            <h5>Secondary Plan</h5>
            <p><?php echo !empty($fitness_plan['plan_name_2']) ? htmlspecialchars($fitness_plan['plan_name_2']) : 'Not specified'; ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($fitness_plan['preferred_workout_plan_3'])): ?>
        <div class="workout-plan">
            <h5>Tertiary Plan</h5>
            <p><?php echo !empty($fitness_plan['plan_name_3']) ? htmlspecialchars($fitness_plan['plan_name_3']) : 'Not specified'; ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
                        
                        <?php if ($fitness_plan['health_condition'] == 'Yes' && !empty($fitness_plan['health_condition_desc'])): ?>
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="alert health-alert">
                                    <h4>Health Considerations</h4>
                                    <p><?php echo htmlspecialchars($fitness_plan['health_condition_desc']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No fitness plan saved yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<!-- Footer Section -->
<div class="row-fluid">
    <div id="footer" class="span12"> 
        <?php echo date("Y"); ?> &copy; Developed By Leslie Paul Ajayi
    </div>
</div>

<!-- JavaScript Files -->
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>

<script>
$(document).ready(function() {
    // Show/hide health condition description based on selection
    $('#health_condition').change(function() {
        if ($(this).val() == 'Yes') {
            $('#health_condition_desc_group').show();
        } else {
            $('#health_condition_desc_group').hide();
        }
    });
});
</script>
</body>
</html>