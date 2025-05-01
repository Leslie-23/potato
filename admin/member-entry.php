
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


<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if(!isset($_SESSION['user_id'])){
header('location:../index.php');	
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System Admin</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/fullcalendar.css" />
<link rel="stylesheet" href="../css/matrix-style.css" />
<link rel="stylesheet" href="../css/matrix-media.css" />
<link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
<link href="../font-awesome/css/all.css" rel="stylesheet" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>
<body>

<!--Header-part--> 
<div id="header">
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>
<!--close-Header-part--> 


<!--top-Header-menu-->
<?php include 'includes/topheader.php'?>
<!--close-top-Header-menu-->
<!--start-top-serch-->
<!-- <div id="search">
  <input type="hidden" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div> -->
<!--close-top-serch-->

<!--sidebar-menu-->
<?php $page='members-entry'; include 'includes/sidebar.php'?>
<!--sidebar-menu-->
<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="tip-bottom">Manamge Members</a> <a href="#" class="current">Add Members</a> </div>
  <h1>Member Entry Form</h1>
</div>
<div class="container-fluid">
  <hr>
  
  <div class="widget-content nopadding">
                    <form action="" method="POST" class="form-horizontal" id="registration-form">
                    <div class="widget-title"> 
        <span class="icon"> <i class="icon-heart"></i> </span>
        <h5>Personal Details Form</h5>
    </div>
                        <div class="control-group">
                            <label class="control-label">Full Name :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="fullname" placeholder="Full Name" required />
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

                        <!-- <div class="text-center">
                            <a href="./login.php">Already have an account? Login</a>
                        </div>
                        <div class="text-center">
                            <button type="button" id="show-fitness-btn" class="btn btn-info">Continue to Fitness Details</button>
                        </div> -->

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
            <input type="number" step="0.1" class="span11" name="user_weight" placeholder="e.g., 70.5" required />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">Height (cm):</label>
        <div class="controls">
            <input type="number" step="0.1" class="span11" name="user_height" placeholder="e.g., 175.5" required />
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
        <button type="submit" name="register" class="btn btn-success btn-block">Complete Registration for User</button>
    </div>


    <?php
                    if (isset($_POST['register'])) {
                        // Process basic registration info with proper null checks
                        $fullname = isset($_POST['fullname']) ? mysqli_real_escape_string($con, $_POST['fullname']) : '';
                        $username = isset($_POST['username']) ? mysqli_real_escape_string($con, $_POST['username']) : '';
                        $password = isset($_POST['password']) ? mysqli_real_escape_string($con, $_POST['password']) : '';
                        $gender = isset($_POST['gender']) ? mysqli_real_escape_string($con, $_POST['gender']) : '';
                        $address = isset($_POST['address']) ? mysqli_real_escape_string($con, $_POST['address']) : '';
                        $contact = isset($_POST['contact']) ? mysqli_real_escape_string($con, $_POST['contact']) : '';
                        $services = isset($_POST['services']) ? mysqli_real_escape_string($con, $_POST['services']) : '';
                        $plan = isset($_POST['plan']) ? mysqli_real_escape_string($con, $_POST['plan']) : '';
                        
                      

                        // Default amount based on plan
                        $amount = 0;
                        switch($plan) {
                            case '1': $amount = 100; break; // Example amount for 1 month
                            case '3': $amount = 250; break; // Example amount for 3 months
                            case '6': $amount = 450; break; // Example amount for 6 months
                            case '12': $amount = 800; break; // Example amount for 1 year
                        }

                        $password = md5($password);

                        // Insert basic member info with service, plan, and amount
                        $qry = "INSERT INTO members(fullname, username, password, dor, gender, services, plan, amount, address, contact, status) 
                                VALUES ('$fullname', '$username', '$password', CURRENT_TIMESTAMP, '$gender', '$services', '$plan', '$amount', '$address', '$contact', 'Pending')";
                        $result = mysqli_query($con, $qry);

                        if ($result) {
                            $member_id = mysqli_insert_id($con);
                            
                           
                        } else {
                            echo "<div class='alert alert-danger'>Registration failed. Please try again. Error: " . mysqli_error($con) . "</div>";
                        }
                    }
                    ?>
                    <?php



if (isset($_POST['register'])) {
    // Process fitness details
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

    // Insert into members_fitness table
    $fitness_qry = "INSERT INTO members_fitness (
        user_weight, user_height, user_bodytype, 
        fitness_goal_1, fitness_goal_2, fitness_goal_3,
        preferred_workout_plan_1, preferred_workout_plan_2, preferred_workout_plan_3,
        experience_level, health_condition, health_condition_desc
    ) VALUES (
        '$user_weight', '$user_height', '$user_bodytype',
        '$fitness_goal_1', '$fitness_goal_2', '$fitness_goal_3',
        '$preferred_workout_plan_1', '$preferred_workout_plan_2', '$preferred_workout_plan_3',
        '$experience_level', '$health_condition', '$health_condition_desc'
    )";
    
    $fitness_result = mysqli_query($con, $fitness_qry);
    
    if ($fitness_result) {
        echo"<div class='alert alert-success'>
        User details saved successfully!<br/>
        <p>Click here to send $fullname an activation link : 
            <a href='validate.php' class='btn btn-primary'>Send</a>
        </p>
      </div>";
    } else {
        echo "<div class='alert alert-danger'>Error saving fitness details: " . mysqli_error($con) . "</div>";
    }
}
?>



        </div>
      </div>

	</div>
  </div>
  
  
</div></div>


<!--end-main-container-part-->

<!--Footer-part-->

<div class="row-fluid">
  <div id="footer" class="span12"> <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi</a> </div>
</div>

<style>
#footer {
  color: white;
}
</style>

<!--end-Footer-part-->

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


<script type="text/javascript">
  // This function is called from the pop-up menus to transfer to
  // a different page. Ignore if the value returned is a null string:
  function goPage (newURL) {

      // if url is empty, skip the menu dividers and reset the menu selection to default
      if (newURL != "") {
      
          // if url is "-", it is this page -- reset the menu:
          if (newURL == "-" ) {
              resetMenu();            
          } 
          // else, send page to designated URL            
          else {  
            document.location.href = newURL;
          }
      }
  }

// resets the menu selection upon entry to this page:
function resetMenu() {
   document.gomenu.selector.selectedIndex = 2;
}
</script>
</body>
</html>
