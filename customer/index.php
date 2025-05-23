<?php
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
                </div>
                <div class="widget-content nopadding">
                    <form action="" method="POST" class="form-horizontal" id="registration-form">
                        
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
                                <input type="text" class="span11" name="contact" placeholder="+233 00 000 0000" required />
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" id="show-fitness-btn" class="btn btn-info">Continue to Fitness Details</button>
                        </div>
                        <div class="text-center">
                            <a href="./login.php">Already have an account? Login</a>
                        </div>

                        <!-- Fitness Details Section -->
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

    <!-- Maintained Services and Plan fields -->
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

                        </div>
                    </form>

                    <?php
                    if (isset($_POST['register'])) {
                        // Process basic registration info with proper null checks
                        $email = isset($_POST['email']) ? mysqli_real_escape_string($con, $_POST['email']) : '';
                        $fullname = isset($_POST['fullname']) ? mysqli_real_escape_string($con, $_POST['fullname']) : '';
                        $username = isset($_POST['username']) ? mysqli_real_escape_string($con, $_POST['username']) : '';
                        $date_of_birth = isset($_POST['date_of_birth']) ? mysqli_real_escape_string($con, $_POST['date_of_birth']) : '';
                        $profile_pic = isset($_POST['profile_pic']) ? mysqli_real_escape_string($con, $_POST['profile_pic']) : '';
                        $password = isset($_POST['password']) ? mysqli_real_escape_string($con, $_POST['password']) : '';
                        $gender = isset($_POST['gender']) ? mysqli_real_escape_string($con, $_POST['gender']) : '';
                        $address = isset($_POST['address']) ? mysqli_real_escape_string($con, $_POST['address']) : '';
                        $contact = isset($_POST['contact']) ? mysqli_real_escape_string($con, $_POST['contact']) : '';
                        $services = isset($_POST['services']) ? mysqli_real_escape_string($con, $_POST['services']) : '';
                        $plan = isset($_POST['plan']) ? mysqli_real_escape_string($con, $_POST['plan']) : '';
                        
                        // Check if username already exists
                        $check_qry = "SELECT * FROM members WHERE username = '$username'";
                        $check_result = mysqli_query($con, $check_qry);
                        if (mysqli_num_rows($check_result) > 0) {
                            echo "<div class='alert alert-danger'>Username already exists.</div>";
                            exit;
                        }
                        // Check if email already exists
                        $check_qry = "SELECT * FROM members WHERE email = '$email'";
                        $check_result = mysqli_query($con, $check_qry);
                        if (mysqli_num_rows($check_result) > 0) {
                            echo "<div class='alert alert-danger'>Email already exists.</div>";
                            exit;
                        }



                        // store email in session
                        $_SESSION['email'] = $email;
                      

                        
                      
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            // Validate date of birth (must be 18+)
                            $dob = $_POST['date_of_birth'];
                            $today = new DateTime();
                            $birthdate = new DateTime($dob);
                            $age = $today->diff($birthdate)->y;
                            
                            if ($age < 18) {
                                die("You must be at least 18 years old to register.");
                            }
                        }
                        // Handle profile picture upload
    $profilePic = null;
    if (!empty($_FILES['profile_pic']['name'])) {
        $uploadDir = '../uploads/profile_pics/';
        $fileName = uniqid() . '_' . basename($_FILES['profile_pic']['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES['profile_pic']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
                $profilePic = $fileName;
            }
        }
    }
    
    // If no picture uploaded, use default based on gender
    if (empty($profilePic)) {
        function getDefaultAvatar($gender) {
    $gender = strtolower($gender);
    return ($gender == 'female') ? '../img/default-female-avatar.png' : '../img/default-male-avatar.png';
}
        $gender = $_POST['gender']; // Assuming you have a gender field
        $profilePic = getDefaultAvatar($gender);
    }


                        // Default amount based on plan
                        $amount = 0;
                        switch($plan) {
                            case '1': $amount = 100; break; // Example amount for 1 month
                            case '3': $amount = 250; break; // Example amount for 3 months
                            case '6': $amount = 450; break; // Example amount for 6 months
                            case '12': $amount = 800; break; // Example amount for 1 year
                        }

                        // $password = md5($password);
                        function hashPassword($password) {
                            // Use cost parameter of 12 (good balance between security and performance)
                            $options = ['cost' => 12];
                            return password_hash($password, PASSWORD_BCRYPT, $options);
                        }
                        $hashedPassword = hashPassword($_POST['password']);

                        // Insert basic member info with service, plan, and amount
                        $qry = "INSERT INTO members(email,fullname, username, password, dor, gender, services, plan, amount, address, contact, status,date_of_birth,profile_pic) 
                                VALUES ('$email','$fullname', '$username', '$hashedPassword', CURRENT_TIMESTAMP, '$gender', '$services', '$plan', '$amount', '$address', '$contact', 'Pending','$date_of_birth','$profile_pic')";
                        $result = mysqli_query($con, $qry);

                        if ($result) {
                            $member_id = mysqli_insert_id($con);
                              //store user_id in session
                        $_SESSION['user_id'] = $member_id;
                            
                           
                        } else {
                            echo "<div class='alert alert-danger'>Registration failed. Please try again. Error: " . mysqli_error($con) . "</div>";
                        }
                    }
                    ?>
                    <?php



if (isset($_POST['register'])) {
    // Process fitness details
    $user_id = isset($_SESSION['user_id']) ? mysqli_real_escape_string($con, $_SESSION['user_id']) : null;

if (!$user_id) {
    die("Error: User not authenticated or user ID not set.");
}

    $user_weight = isset($_POST['user_weight']) ? mysqli_real_escape_string($con, $_POST['user_weight']) : 0;
    $user_height = isset($_POST['user_height']) ? mysqli_real_escape_string($con, $_POST['user_height']) : 0;
    $user_bodytype = isset($_POST['user_bodytype']) ? mysqli_real_escape_string($con, $_POST['user_bodytype']) : '';
    $fitness_goal_1 = isset($_POST['fitness_goal_1']) ? mysqli_real_escape_string($con, $_POST['fitness_goal_1']) : '';
    $fitness_goal_2 = isset($_POST['fitness_goal_2']) ? mysqli_real_escape_string($con, $_POST['fitness_goal_2']) : '';
    $fitness_goal_3 = isset($_POST['fitness_goal_3']) ? mysqli_real_escape_string($con, $_POST['fitness_goal_3']) : '';
    $preferred_workout_plan_1 = isset($_POST['preferred_workout_plan_1']) ? mysqli_real_escape_string($con, $_POST['preferred_workout_plan_1']) : '';
    $preferred_workout_plan_2 = isset($_POST['preferred_workout_plan_2']) ? mysqli_real_escape_string($con, $_POST['preferred_workout_plan_2']) : '';
    $preferred_workout_plan_3 = isset($_POST['preferred_workout_plan_3']) ? mysqli_real_escape_string($con, $_POST['preferred_workout_plan_3']) : '';
    $experience_level = isset($_POST['experience_level']) ? mysqli_real_escape_string($con, $_POST['experience_level']) : '';
    $health_condition = isset($_POST['health_condition']) ? mysqli_real_escape_string($con, $_POST['health_condition']) : '';
    $health_condition_desc = isset($_POST['health_condition_desc']) ? mysqli_real_escape_string($con, $_POST['health_condition_desc']) : '';
    $services = isset($_POST['services']) ? mysqli_real_escape_string($con, $_POST['services']) : '';
    $plan = isset($_POST['plan']) ? mysqli_real_escape_string($con, $_POST['plan']) : '';

    // validation on the preferred_workout_plan. the 1,2 and 3must be different

    if ($preferred_workout_plan_1 == $preferred_workout_plan_2 || $preferred_workout_plan_1 == $preferred_workout_plan_3 || $preferred_workout_plan_2 == $preferred_workout_plan_3) {
        echo "<script>alert('You cannot select the same workout routine more than once!'); history.back(); </script>";
        exit();
    }

    // Insert into members_fitness table
    $fitness_qry = "INSERT INTO members_fitness (  user_id,
        user_weight, user_height, user_bodytype, 
        fitness_goal_1, fitness_goal_2, fitness_goal_3,
        preferred_workout_plan_1, preferred_workout_plan_2, preferred_workout_plan_3,
        experience_level, health_condition, health_condition_desc
    ) VALUES (    '$user_id',
        '$user_weight', '$user_height', '$user_bodytype',
        '$fitness_goal_1', '$fitness_goal_2', '$fitness_goal_3',
        '$preferred_workout_plan_1', '$preferred_workout_plan_2', '$preferred_workout_plan_3',
        '$experience_level', '$health_condition', '$health_condition_desc'
    )";
    
    $fitness_result = mysqli_query($con, $fitness_qry);
    
    if ($fitness_result) {
        echo"<div class='alert alert-success'>
        User Personal and Fitness details saved successfully!<br/>
        <p>Click here to activate your account and login: 
            <a href='validate.php' class='btn btn-primary'>Validate Account</a>
        </p>
      </div>";
    } else {
        echo "<div class='alert alert-danger'>Error saving fitness details: " . mysqli_error($con) . "</div>";
    }

    if ($user_weight <= 0 || $user_height <= 0) {
        die("Error: Weight and height must be positive values");
    }
}
?>
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
});
</script>
<script>
// Additional validation before form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const weight = parseFloat(document.querySelector('[name="weight"]').value);
    const height = parseFloat(document.querySelector('[name="height"]').value);
    
    if (weight <= 0 || height <= 0) {
        e.preventDefault();
        alert('Weight and height must be positive values. Cannot be less than 0');
    }
});


function validatePassword(password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
    return regex.test(password);
}

// Usage in form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.querySelector('[name="password"]').value;
    if (!validatePassword(password)) {
        e.preventDefault();
        alert('Password must contain at least 8 characters, including uppercase, lowercase letters and numbers');
    }
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