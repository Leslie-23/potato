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

// Get available trainers
$trainers = [];
if(!empty($userPlans)) {
    $planList = "'" . implode("','", $userPlans) . "'";
    
    $trainerQuery = mysqli_query($con, "SELECT DISTINCT s.user_id, s.fullname, t.specialization
                                       FROM staffs s
                                       JOIN trainers t ON s.user_id = t.trainer_id
                                       JOIN trainer_workout_specialization tw ON t.trainer_id = tw.trainer_id
                                       WHERE tw.plan_id IN ($planList) AND s.designation = 'Trainer'");
    
    $trainers = mysqli_fetch_all($trainerQuery, MYSQLI_ASSOC);
    
    if(empty($trainers)) {
        $allTrainersQuery = mysqli_query($con, "SELECT s.user_id, s.fullname, t.specialization
                                              FROM staffs s
                                              JOIN trainers t ON s.user_id = t.trainer_id
                                              WHERE s.designation = 'Trainer' ");
        $trainers = mysqli_fetch_all($allTrainersQuery, MYSQLI_ASSOC);
    }
}

// Handle session cancellation
if(isset($_POST['cancel_session'])) {
    $sessionId = mysqli_real_escape_string($con, $_POST['session_id']);
    $userId = $_SESSION['user_id'];
    
    // Verify the session belongs to the user before cancelling
    $verifyQuery = mysqli_query($con, "SELECT * FROM training_sessions WHERE session_id = '$sessionId' AND user_id = '$userId'");
    
    if(mysqli_num_rows($verifyQuery) > 0) {
        $cancelQuery = "UPDATE training_sessions SET status = 'cancelled' WHERE session_id = '$sessionId'";
        if(mysqli_query($con, $cancelQuery)) {
            $success = "Session cancelled successfully!";
            // Refresh the page to show updated status
            header("Refresh:0");
        } else {
            $error = "Error cancelling session: " . mysqli_error($con);
        }
    } else {
        $error = "You can only cancel your own sessions.";
    }
}

// Handle session booking
if(isset($_POST['book_session'])) {
    $trainerId = mysqli_real_escape_string($con, $_POST['trainer_id']);
    $planId = mysqli_real_escape_string($con, $_POST['plan_id']);
    $sessionDate = mysqli_real_escape_string($con, $_POST['session_date']);
    $userId = $_SESSION['user_id'];
    
    // Get workout duration in weeks
    $durationQuery = mysqli_query($con, "SELECT duration_weeks FROM workout_plan WHERE table_id = '$planId'");
    $durationData = mysqli_fetch_assoc($durationQuery);
    $durationWeeks = $durationData['duration_weeks'] ?? 1; // Default to 1 week if not set
    
    // Calculate end date (session date + duration in weeks)
    $endDate = date('Y-m-d H:i:s', strtotime($sessionDate . " + $durationWeeks weeks"));
    
    // Check for overlapping sessions for the user
    $userOverlapCheck = mysqli_query($con, "SELECT * FROM training_sessions 
                                          WHERE user_id = '$userId'
                                          AND status != 'cancelled'
                                          AND (
                                              (session_date <= '$sessionDate' AND end_date >= '$sessionDate')
                                              OR (session_date <= '$endDate' AND end_date >= '$endDate')
                                              OR (session_date >= '$sessionDate' AND end_date <= '$endDate')
                                          )");
    
    if(mysqli_num_rows($userOverlapCheck) > 0) {
        $error = "You already have a session scheduled during this time period. Please choose another time.";
    }
    // Check for overlapping sessions for the trainer
    else {
        $trainerOverlapCheck = mysqli_query($con, "SELECT * FROM training_sessions 
                                                 WHERE trainer_id = '$trainerId'
                                                 AND status != 'cancelled'
                                                 AND (
                                                     (session_date <= '$sessionDate' AND end_date >= '$sessionDate')
                                                     OR (session_date <= '$endDate' AND end_date >= '$endDate')
                                                     OR (session_date >= '$sessionDate' AND end_date <= '$endDate')
                                                 )");
        
        if(mysqli_num_rows($trainerOverlapCheck) > 0) {
            $error = "The selected trainer is not available during this time. Please choose another time or trainer.";
        } else {
            // Insert the new session with calculated end date
            $insertQuery = "INSERT INTO training_sessions 
                           (user_id, trainer_id, table_id, session_date, end_date, status) 
                           VALUES ('$userId', '$trainerId', '$planId', '$sessionDate', '$endDate', 'scheduled')";
            
            if(mysqli_query($con, $insertQuery)) {
                $success = "Session booked successfully! This is a $durationWeeks-week program ending on " . date('M j, Y', strtotime($endDate)) . ".";
                header("Refresh:0");
            } else {
                $error = "Error booking session: " . mysqli_error($con);
            }
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
                                        $planInfo = mysqli_fetch_assoc(mysqli_query($con, "SELECT workout_name, duration_weeks FROM workout_plan WHERE table_id = '$planId'"));
                                        if($planInfo) {
                                    ?>
                                        <option value="<?php echo $planId; ?>">
                                            <?php echo $planInfo['workout_name']; ?>
                                            (<?php echo $planInfo['duration_weeks'] ?? 'N/A'; ?> weeks)
                                        </option>
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
                                <span class="help-block">Select the start date/time for your session</span>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="book_session" class="btn btn-success">Book Session</button>
                        </div>
                    </form>
                    
                    <!-- Upcoming Sessions -->
                    <h4>Your Upcoming Sessions</h4>
                    <?php
                    $sessionsQuery = mysqli_query($con, "SELECT ts.*, s.fullname as trainer_name, wp.workout_name, wp.duration_weeks
                                                         FROM training_sessions ts
                                                         JOIN staffs s ON ts.trainer_id = s.user_id
                                                         JOIN workout_plan wp ON ts.table_id = wp.table_id
                                                         WHERE ts.user_id = '".$_SESSION['user_id']."' 
                                                         AND ts.session_date >= NOW()
                                                         AND ts.status != 'cancelled'
                                                         ORDER BY ts.session_date ASC");
                    if(mysqli_num_rows($sessionsQuery) > 0): ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Trainer</th>
                                    <th>Workout Plan</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($session = mysqli_fetch_assoc($sessionsQuery)): 
                                    $endDate = !empty($session['end_date']) ? $session['end_date'] : 
                                        date('Y-m-d H:i:s', strtotime($session['session_date'] . " + " . ($session['duration_weeks'] ?? 1) . " weeks"));
                                ?>
                                <tr>
                                    <td><?php echo $session['trainer_name']; ?></td>
                                    <td><?php echo $session['workout_name']; ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($session['session_date'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($endDate)); ?></td>
                                    <td><?php echo $session['duration_weeks'] ?? 'N/A'; ?> weeks</td>
                                    <td><?php echo ucfirst($session['status']); ?></td>
                                    <td>
                                        <?php if($session['status'] == 'scheduled'): ?>
                                            <form method="post" style="margin:0;">
                                                <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                                <button type="submit" name="cancel_session" class="btn btn-danger btn-mini" 
                                                        onclick="return confirm('Are you sure you want to cancel this session?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">No upcoming sessions scheduled.</div>
                    <?php endif; ?>
                    
                    <!-- Cancelled Sessions -->
                    <h4>Your Cancelled Sessions</h4>
                    <?php
                    $cancelledQuery = mysqli_query($con, "SELECT ts.*, s.fullname as trainer_name, wp.workout_name
                                                         FROM training_sessions ts
                                                         JOIN staffs s ON ts.trainer_id = s.user_id
                                                         JOIN workout_plan wp ON ts.table_id = wp.table_id
                                                         WHERE ts.user_id = '".$_SESSION['user_id']."' 
                                                         AND ts.status = 'cancelled'
                                                         ORDER BY ts.session_date DESC");
                    if(mysqli_num_rows($cancelledQuery) > 0): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Trainer</th>
                                    <th>Workout Plan</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($cancelled = mysqli_fetch_assoc($cancelledQuery)): ?>
                                <tr>
                                    <td><?php echo $cancelled['trainer_name']; ?></td>
                                    <td><?php echo $cancelled['workout_name']; ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($cancelled['session_date'])); ?></td>
                                    <td><?php echo ucfirst($cancelled['status']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No cancelled sessions.</p>
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

<script>
// Add confirmation for session cancellation
$(document).ready(function() {
    $('[name="cancel_session"]').click(function() {
        return confirm('Are you sure you want to cancel this session?');
    });
    
    // Set minimum datetime for session booking (current time)
    var now = new Date();
    var minDate = now.toISOString().slice(0,16);
    $('[name="session_date"]').attr('min', minDate);
});
</script>

</body>
</html>