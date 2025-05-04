
<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System</title>
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
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="index.php">Perfect Gym System</a></h1>
</div>
<!--close-Header-part--> 

<!--top-Header-menu-->
<?php include '../includes/topheader.php'?>
<!--close-top-Header-menu-->

<!--sidebar-menu-->
<?php $page="session"; include '../includes/sidebar.php'?>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="You're right here" class="tip-bottom"><i class="icon-home"></i> Home</a></div>
  </div>
<!--End-breadcrumbs-->

<!--Action boxes-->
  <div class="container-fluid">
<?php
include 'dbcon.php';

// Get user's preferred workout plans
// Get user's preferred workout plans
$userPlans = [];
$planQuery = mysqli_query($con, "SELECT preferred_workout_plan_1, preferred_workout_plan_2, preferred_workout_plan_3 
                                FROM members_fitness 
                                WHERE user_id = '".$_SESSION['user_id']."'");
if(mysqli_num_rows($planQuery) > 0) {
    $planData = mysqli_fetch_assoc($planQuery);
    $userPlans = array_filter([
        $planData['preferred_workout_plan_1'],
        $planData['preferred_workout_plan_2'],
        $planData['preferred_workout_plan_3']
    ], function($value) { return !empty($value); });
}

// Get available trainers - modified to get ALL trainers if none assigned to plans
$trainers = [];
if(!empty($userPlans)) {
    $planList = "'" . implode("','", $userPlans) . "'";
    
    // First try to get trainers specialized in user's preferred plans
    $trainerQuery = mysqli_query($con, "SELECT DISTINCT s.user_id, s.fullname, t.specialization
                                       FROM staffs s
                                       JOIN trainers t ON s.user_id = t.trainer_id
                                       JOIN trainer_workout_specialization tw ON t.trainer_id = tw.trainer_id
                                       WHERE tw.plan_id IN ($planList) AND s.designation = 'Trainer'");
    
    $trainers = mysqli_fetch_all($trainerQuery, MYSQLI_ASSOC);
    
    // If no trainers found for specific plans, get ALL active trainers
    if(empty($trainers)) {
        $allTrainersQuery = mysqli_query($con, "SELECT s.user_id, s.fullname, t.specialization
                                              FROM staffs s
                                              JOIN trainers t ON s.user_id = t.trainer_id
                                              WHERE s.designation = 'Trainer' ");
        $trainers = mysqli_fetch_all($allTrainersQuery, MYSQLI_ASSOC);
    }
}


// Handle session booking
if(isset($_POST['book_session'])) {
    $trainerId = mysqli_real_escape_string($con, $_POST['trainer_id']);
    $planId = mysqli_real_escape_string($con, $_POST['plan_id']);
    $sessionDate = mysqli_real_escape_string($con, $_POST['session_date']);
    
    // Check if the trainer is available at the selected time
    $checkAvailability = mysqli_query($con, "SELECT * FROM training_sessions 
                                           WHERE trainer_id = '$trainerId' 
                                           AND session_date = '$sessionDate'");
    
    if(mysqli_num_rows($checkAvailability) > 0) {
        $error = "The selected trainer is not available at that time. Please choose another time.";
    } else {
        $insertQuery = "INSERT INTO training_sessions (user_id, trainer_id, table_id, session_date, status) 
                       VALUES ('".$_SESSION['user_id']."', '$trainerId', '$planId', '$sessionDate', 'scheduled')";
        if(mysqli_query($con, $insertQuery)) {
            $success = "Session booked successfully!";
            // Refresh the page to show the new booking
            header("Refresh:0");
        } else {
            $error = "Error booking session: " . mysqli_error($con);
        }
    }
}
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-calendar"></i> </span>
                    <h5>Book Training Session</h5>
                </div>
                <div class="widget-content">
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" class="form-horizontal">
                        <div class="control-group">
                            <label class="control-label">Select Workout Plan:</label>
                            <div class="controls">
                                <select name="plan_id" required>
                                    <?php foreach($userPlans as $planId): 
                                        $planInfo = mysqli_fetch_assoc(mysqli_query($con, "SELECT workout_name FROM workout_plan WHERE table_id = '$planId'"));
                                        if($planInfo) {
                                    ?>
                                        <option value="<?php echo $planId; ?>"><?php echo $planInfo['workout_name']; ?></option>
                                    <?php } endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label">Select Trainer:</label>
                            <div class="controls">
                                <select name="trainer_id" required>
                                    <?php if(!empty($trainers)): ?>
                                        <?php foreach($trainers as $trainer): ?>
                                            <option value="<?php echo $trainer['user_id']; ?>">
                                                <?php echo $trainer['fullname'] . " - " . $trainer['specialization']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No trainers available for selected plans</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label">Session Date/Time:</label>
                            <div class="controls">
                                <input type="datetime-local" name="session_date" min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="book_session" class="btn btn-success">Book Session</button>
                        </div>
                    </form>
                    
                    <!-- Upcoming Sessions -->
                    <h4>Your Upcoming Sessions</h4>
                    <?php
                    $sessionsQuery = mysqli_query($con, "SELECT ts.*, s.fullname as trainer_name, wp.workout_name 
                                                         FROM training_sessions ts
                                                         JOIN staffs s ON ts.trainer_id = s.user_id
                                                         JOIN workout_plan wp ON ts.table_id = wp.table_id
                                                         WHERE ts.user_id = '".$_SESSION['user_id']."' 
                                                         AND ts.session_date >= NOW()
                                                         ORDER BY ts.session_date ASC");
                    if(mysqli_num_rows($sessionsQuery) > 0): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Trainer</th>
                                    <th>Workout Plan</th>
                                    <th>Date/Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($session = mysqli_fetch_assoc($sessionsQuery)): ?>
                                <tr>
                                    <td><?php echo $session['trainer_name']; ?></td>
                                    <td><?php echo $session['workout_name']; ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($session['session_date'])); ?></td>
                                    <td><?php echo ucfirst($session['status']); ?></td>
                                    <td>
                                        <?php if($session['status'] == 'scheduled'): ?>
                                            <button type="button" class="btn btn-danger btn-mini" name="cancel_session" data-toggle="modal" data-target="#cancelModal" data-id="<?php echo $session['session_id']; ?>">Cancel 
                                                <?php
                                                // code to cancel the status by setting it to cancelled.
                                                if(isset($_POST['cancel_session']) && $_POST['session_id'] == $session['session_id']) {
                                                    $cancelQuery = "UPDATE training_sessions SET status = 'cancelled' WHERE session_id = '".$_POST['session_id']."'";
                                                    if(mysqli_query($con, $cancelQuery)) {
                                                        $success = "Session cancelled successfully.";
                                                    } else {
                                                        $error = "Error cancelling session: " . mysqli_error($con);
                                                    }
                                                }
                                                ?>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No upcoming sessions scheduled.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="row-fluid">
      <div class="span12"></div>
    </div>
  </div>
</div>

<!--Footer-part-->
<div class="row-fluid">
  <div id="footer" class="span12"> <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi</div>
</div>

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

</body>
</html>


