<?php
session_start();
include('dbcon.php');

// Fetch workout plans from database
$workout_plans = [];
$plan_query = "SELECT * FROM workout_plan";
$plan_result = mysqli_query($con, $plan_query);
if ($plan_result) {
    while ($row = mysqli_fetch_assoc($plan_result)) {
        $workout_plans[$row['table_id']] = $row['workout_name'];
    }
}

// Function to handle profile picture upload
function handleProfilePictureUpload($gender) {
    $profile_pic_path = '';
    
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        // Validate image file
        $check = getimagesize($_FILES['profile_pic']['tmp_name']);
        if ($check === false) {
            return ['error' => "File is not a valid image."];
        }

        // Validate file size (max 2MB)
        if ($_FILES['profile_pic']['size'] > 2000000) {
            return ['error' => "File is too large (max 2MB)."];
        }

        // Validate file extension
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_extensions)) {
            return ['error' => "Only JPG, JPEG, PNG & GIF files are allowed."];
        }

        // Create upload directory if it doesn't exist
        $target_dir = "uploads/profile_pictures/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Generate unique filename
        $filename = uniqid('profile_', true) . '.' . $ext;
        $target_path = $target_dir . $filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
            $profile_pic_path = $target_path;
        } else {
            return ['error' => "Failed to upload profile picture."];
        }
    } else {
        // Set default avatar based on gender
        $profile_pic_path = ($gender == 'Female') ? 'img/default-female-avatar.png' : 'img/default-male-avatar.png';
    }
    
    return ['path' => $profile_pic_path];
}

// Process registration form
if (isset($_POST['register'])) {
    // Basic form data
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $date_of_birth = mysqli_real_escape_string($con, $_POST['date_of_birth']);
    $password = $_POST['password'];
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $services = mysqli_real_escape_string($con, $_POST['services']);
    $plan = mysqli_real_escape_string($con, $_POST['plan']);
    
    // Validate age (must be 18+)
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    if ($age < 18) {
        die("<div class='alert alert-danger'>You must be at least 18 years old to register.</div>");
    }
    
    // Validate contact number
    if (!preg_match('/^\d{10}$/', $contact)) {
        die("<div class='alert alert-danger'>Contact number must be 10 digits.</div>");
    }
    
    // Check if username already exists
    $check_qry = "SELECT * FROM members WHERE username = '$username'";
    $check_result = mysqli_query($con, $check_qry);
    if (mysqli_num_rows($check_result) > 0) {
        die("<div class='alert alert-danger'>Username already exists.</div>");
    }
    
    // Check if email already exists
    $check_qry = "SELECT * FROM members WHERE email = '$email'";
    $check_result = mysqli_query($con, $check_qry);
    if (mysqli_num_rows($check_result) > 0) {
        die("<div class='alert alert-danger'>Email already exists.</div>");
    }
    
    // Handle profile picture upload
    $profile_result = handleProfilePictureUpload($gender);
    if (isset($profile_result['error'])) {
        die("<div class='alert alert-danger'>" . $profile_result['error'] . "</div>");
    }
    $profile_pic_path = $profile_result['path'];
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Set amount based on plan
    $amount = 0;
    switch($plan) {
        case '1': $amount = 100; break;
        case '3': $amount = 250; break;
        case '6': $amount = 450; break;
        case '12': $amount = 800; break;
    }
    
    // Insert member data
    $qry = "INSERT INTO members(email, fullname, username, password, dor, gender, services, plan, amount, address, contact, status, date_of_birth, profile_pic) 
            VALUES ('$email', '$fullname', '$username', '$hashedPassword', CURRENT_TIMESTAMP, '$gender', '$services', '$plan', '$amount', '$address', '$contact', 'Pending', '$date_of_birth', '$profile_pic_path')";
    
    $result = mysqli_query($con, $qry);
    
    if ($result) {
        $member_id = mysqli_insert_id($con);
        $_SESSION['user_id'] = $member_id;
        $_SESSION['email'] = $email;
        
        // Process fitness details
        $user_weight = mysqli_real_escape_string($con, $_POST['user_weight']);
        $user_height = mysqli_real_escape_string($con, $_POST['user_height']);
        $user_bodytype = mysqli_real_escape_string($con, $_POST['user_bodytype']);
        $fitness_goal_1 = mysqli_real_escape_string($con, $_POST['fitness_goal_1']);
        $fitness_goal_2 = mysqli_real_escape_string($con, $_POST['fitness_goal_2']);
        $fitness_goal_3 = mysqli_real_escape_string($con, $_POST['fitness_goal_3']);
        $preferred_workout_plan_1 = mysqli_real_escape_string($con, $_POST['preferred_workout_plan_1']);
        $preferred_workout_plan_2 = mysqli_real_escape_string($con, $_POST['preferred_workout_plan_2']);
        $preferred_workout_plan_3 = mysqli_real_escape_string($con, $_POST['preferred_workout_plan_3']);
        $experience_level = mysqli_real_escape_string($con, $_POST['experience_level']);
        $health_condition = mysqli_real_escape_string($con, $_POST['health_condition']);
        $health_condition_desc = mysqli_real_escape_string($con, $_POST['health_condition_desc']);
        
        // Validate workout plans are different
        if ($preferred_workout_plan_1 == $preferred_workout_plan_2 || 
            $preferred_workout_plan_1 == $preferred_workout_plan_3 || 
            $preferred_workout_plan_2 == $preferred_workout_plan_3) {
            die("<script>alert('You cannot select the same workout routine more than once!'); history.back(); </script>");
        }
        
        // Insert fitness data
        $fitness_qry = "INSERT INTO members_fitness (
            user_id, user_weight, user_height, user_bodytype, 
            fitness_goal_1, fitness_goal_2, fitness_goal_3,
            preferred_workout_plan_1, preferred_workout_plan_2, preferred_workout_plan_3,
            experience_level, health_condition, health_condition_desc
        ) VALUES (
            '$member_id', '$user_weight', '$user_height', '$user_bodytype',
            '$fitness_goal_1', '$fitness_goal_2', '$fitness_goal_3',
            '$preferred_workout_plan_1', '$preferred_workout_plan_2', '$preferred_workout_plan_3',
            '$experience_level', '$health_condition', '$health_condition_desc'
        )";
        
        $fitness_result = mysqli_query($con, $fitness_qry);
        
        if ($fitness_result) {
            echo "<div class='alert alert-success'>
                User registration completed successfully!<br/>
                <p>Click here to activate your account and login: 
                    <a href='validate.php' class='btn btn-primary'>Validate Account</a>
                </p>
              </div>";
        } else {
            echo "<div class='alert alert-danger'>Error saving fitness details: " . mysqli_error($con) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Registration failed. Error: " . mysqli_error($con) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System - Customer Registration</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/fullcalendar.css" />
<link rel="stylesheet" href="../css/matrix-style.css" />
<link rel="stylesheet" href="../css/matrix-media.css" />
<link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
    #fitness-details-section {
        display: none;
    }
    .alert {
        margin: 15px;
    }
</style>
</head>

<body>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6 offset3">
            <div class="widget-box">
                <div class="widget-title"> 
                    <span class="icon"> <i class="icon-user fas fa-user"></i> </span>
                    <h5>Customer Registration Form</h5> 
                    <div class="text-center">
                        <button style="margin-top: 6px; background-color: #2c3640; color:#fff; border-radius: 5px">
                            <a href="./login.php"> <span style="font-size: 12px; color:#fff"> Member Login</span> </a>
                        </button>
                    </div>
                </div>
                <div class="widget-content nopadding">
                    <form action="" method="POST" class="form-horizontal" id="registration-form" enctype="multipart/form-data">
                        <div class="control-group">
                            <label class="control-label">Email :</label>
                            <div class="controls">
                                <input type="email" class="span11" name="email" placeholder="e.g user@gmail.com" required />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Full Name :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="fullname" placeholder="e.g Marquee Sanders" required />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Username :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="username" placeholder="@username" required />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Password :</label>
                            <div class="controls">
                                <input type="password" class="span11" name="password" placeholder="Password" required />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Date of Birth</label>
                            <div class="controls">
                                <input type="date" name="date_of_birth" class="span6" required 
                                       max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Profile Picture</label>
                            <div class="controls">
                                <input type="file" name="profile_pic" class="span6" accept="image/*">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Gender :</label>
                            <div class="controls">
                                <select name="gender" required>
                                    <option value="" disabled><strong>Choose Gender</strong></option>
                                    <option value="Male" selected>Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Address :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="address" placeholder="Address" required />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Contact :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="contact" minlength="10" maxlength="10" pattern="\d{10}" placeholder="000 000 0000" title="Please enter exactly 10 digits" required />
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" id="show-fitness-btn" class="btn btn-info">Continue to Fitness Details</button>
                        </div>
                        <div class="text-center">
                            <a href="./login.php">Already have an account? Login</a>
                        </div>

                        <!-- Fitness Details Section -->
                        <div id="fitness-details-section">
                            <hr>
                            <div class="widget-title"> 
                                <span class="icon"> <i class="icon-heart"></i> </span>
                                <h5>Fitness Details Form</h5>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Weight (kg):</label>
                                <div class="controls">
                                    <input type="number" step="0.1" class="span11" name="user_weight" placeholder="e.g., 70.5" min="0" required />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Height (cm):</label>
                                <div class="controls">
                                    <input type="number" step="0.1" class="span11" name="user_height" placeholder="e.g., 175.5" min="0" required />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Body Type:</label>
                                <div class="controls">
                                    <select name="user_bodytype" required>
                                        <option value="Ectomorph">Ectomorph (Lean)</option>
                                        <option value="Mesomorph">Mesomorph (Athletic)</option>
                                        <option value="Endomorph">Endomorph (Round)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Primary Fitness Goal:</label>
                                <div class="controls">
                                    <input type="text" class="span11" name="fitness_goal_1" placeholder="e.g., Weight Loss" required />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Secondary Fitness Goal:</label>
                                <div class="controls">
                                    <input type="text" class="span11" name="fitness_goal_2" placeholder="Optional" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Tertiary Fitness Goal:</label>
                                <div class="controls">
                                    <input type="text" class="span11" name="fitness_goal_3" placeholder="Optional" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Primary Preferred Workout Plan:</label>
                                <div class="controls">
                                    <select name="preferred_workout_plan_1" class="workout-plan-select" required>
                                        <option value="" disabled selected>Select Primary Plan</option>
                                        <?php foreach ($workout_plans as $id => $name): ?>
                                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Secondary Preferred Workout Plan:</label>
                                <div class="controls">
                                    <select name="preferred_workout_plan_2" class="workout-plan-select">
                                        <option value="">None</option>
                                        <?php foreach ($workout_plans as $id => $name): ?>
                                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Tertiary Preferred Workout Plan:</label>
                                <div class="controls">
                                    <select name="preferred_workout_plan_3" class="workout-plan-select">
                                        <option value="">None</option>
                                        <?php foreach ($workout_plans as $id => $name): ?>
                                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Experience Level:</label>
                                <div class="controls">
                                    <select name="experience_level" required>
                                        <option value="1">Beginner</option>
                                        <option value="5">Intermediate</option>
                                        <option value="9">Advanced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Health Conditions:</label>
                                <div class="controls">
                                    <select name="health_condition" required>
                                        <option value="None">None</option>
                                        <option value="Asthma">Asthma</option>
                                        <option value="Diabetes">Diabetes</option>
                                        <option value="Hypertension">Hypertension</option>
                                        <option value="Joint Problems">Joint Problems</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Health Condition Description:</label>
                                <div class="controls">
                                    <textarea class="span11" name="health_condition_desc" placeholder="Please describe any health conditions in detail"></textarea>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Service :</label>
                                <div class="controls">
                                    <select name="services" required>
                                        <option value="" disabled selected>Select Service</option>
                                        <option value="Fitness">Fitness</option>
                                        <option value="Sauna">Sauna</option>
                                        <option value="Cardio">Cardio</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Plan :</label>
                                <div class="controls">
                                    <select name="plan" required>
                                        <option value="" disabled selected>Select Plan</option>
                                        <option value="1">One Month</option>
                                        <option value="3">Three Months</option>
                                        <option value="6">Six Months</option>
                                        <option value="12">One Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="register" class="btn btn-success btn-block">Complete Registration</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/jquery.min.js"></script> 
<script>
$(document).ready(function(){
    $('#show-fitness-btn').click(function(){
        $('#fitness-details-section').slideDown();
        $(this).hide();
        
        // Scroll to the fitness section
        $('html, body').animate({
            scrollTop: $('#fitness-details-section').offset().top
        }, 600);
    });
    
    // Password validation
    function validatePassword(password) {
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
        return regex.test(password);
    }
    
    $('form').submit(function(e) {
        const password = $('[name="password"]').val();
        if (!validatePassword(password)) {
            e.preventDefault();
            alert('Password must contain at least 8 characters, including uppercase, lowercase letters and numbers');
            return false;
        }
        
        const weight = parseFloat($('[name="user_weight"]').val());
        const height = parseFloat($('[name="user_height"]').val());
        
        if (weight <= 0 || height <= 0) {
            e.preventDefault();
            alert('Weight and height must be positive values. Cannot be less than 0');
            return false;
        }
    });
});
</script>

<script src="../js/excanvas.min.js"></script> 
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

</body>
</html>