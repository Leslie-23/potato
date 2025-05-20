<?php
// session_start();
// if(!isset($_SESSION['user_id'])) {
//     header('location:../index.php');
//     exit();
// }

// include "../dbcon.php";

// $trainer_id = $_SESSION['user_id'];

// // Get members with active training sessions
// $members_query = "SELECT DISTINCT ts.user_id, m.fullname 
//                  FROM training_sessions ts
//                  JOIN members m ON ts.user_id = m.user_id
//                  WHERE ts.trainer_id = ?";
// $stmt = mysqli_prepare($con, $members_query);
// mysqli_stmt_bind_param($stmt, "i", $trainer_id);
// mysqli_stmt_execute($stmt);
// $members_result = mysqli_stmt_get_result($stmt);

// if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_reminder'])) {
//     $member_id = sanitizeInput($_POST['member_id'], 'int');
//     $message = sanitizeInput($_POST['message']);
//     $priority = sanitizeInput($_POST['priority'], 'int');
    
//     // Verify active session exists
//     $verify_query = "SELECT 1 FROM training_sessions 
//                     WHERE user_id = ? AND trainer_id = ? ";
//     $stmt_verify = mysqli_prepare($con, $verify_query);
//     mysqli_stmt_bind_param($stmt_verify, "ii", $member_id, $trainer_id);
//     mysqli_stmt_execute($stmt_verify);
    
//     if(mysqli_stmt_num_rows($stmt_verify) > 0) {
//         $insert_query = "INSERT INTO reminders 
//                         (trainer_id, member_id, message, priority, created_at)
//                         VALUES (?, ?, ?, ?, NOW())";
//         $stmt_insert = mysqli_prepare($con, $insert_query);
//         mysqli_stmt_bind_param($stmt_insert, "iisi", 
//             $trainer_id, 
//             $member_id,
//             $message,
//             $priority
//         );
        
//         if(mysqli_stmt_execute($stmt_insert)) {
//             $_SESSION['success'] = "Reminder sent successfully!";
//         } else {
//             $_SESSION['error'] = "Failed to send reminder: " . mysqli_error($con);
//         }
//     } else {
//         $_SESSION['error'] = "You have no active training sessions with this member";
//     }
    
//     header("Location: reminder.php");
//     exit();
// }

// // Get sent reminders history
// $history_query = "SELECT r.*, m.fullname as member_name
//                  FROM reminder r
//                  JOIN members m ON r.user_id = m.user_id
//                  WHERE r.id = ?
//                  ORDER BY r.created_at DESC
//                  LIMIT 10";
// $stmt_history = mysqli_prepare($con, $history_query);
// mysqli_stmt_bind_param($stmt_history, "i", $trainer_id);
// mysqli_stmt_execute($stmt_history);
// $history_result = mysqli_stmt_get_result($stmt_history);

// function sanitizeInput($data, $type = 'string') {
//     global $con;
//     $data = trim($data);
//     $data = mysqli_real_escape_string($con, $data);
//     switch($type) {
//         case 'int': return (int)$data;
//         case 'float': return (float)$data;
//         default: return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
//     }
// }
?>

<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}

include "../dbcon.php";

$trainer_id = $_SESSION['user_id'];

// Get members with active/scheduled sessions
$members_query = "SELECT DISTINCT ts.`user_id`, m.`fullname` 
                 FROM `training_sessions` ts
                 JOIN `members` m ON ts.`user_id` = m.`user_id`
                 WHERE ts.`trainer_id` = ?
                 AND ts.`status` IN ('scheduled', 'pending')";
$stmt = mysqli_prepare($con, $members_query);
mysqli_stmt_bind_param($stmt, "i", $trainer_id);
mysqli_stmt_execute($stmt);
$members_result = mysqli_stmt_get_result($stmt);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_reminder'])) {
    $member_id = sanitizeInput($_POST['member_id'], 'int');
    $message = sanitizeInput($_POST['message']);
    $priority = sanitizeInput($_POST['priority'], 'int');
    
    // Verify active session exists
    $verify_query = "SELECT 1 FROM `training_sessions`
                    WHERE `user_id` = ? AND `trainer_id` = ?
                    AND `status` IN ('scheduled', 'pending')";
    $stmt_verify = mysqli_prepare($con, $verify_query);
    mysqli_stmt_bind_param($stmt_verify, "ii", $member_id, $trainer_id);
    mysqli_stmt_execute($stmt_verify);
    
    if(mysqli_stmt_num_rows($stmt_verify) > 0) {
        $insert_query = "INSERT INTO `reminder` 
                        (`name`, `message`, `priority`, `status`, `date`, `user_id`, `trainer_id`, `created_at`)
                        VALUES (?, ?, ?, 'pending', NOW(), ?, ?, NOW())";
        $stmt_insert = mysqli_prepare($con, $insert_query);
        $reminder_name = "Session Reminder"; // You can customize this
        mysqli_stmt_bind_param($stmt_insert, "ssiii", 
            $reminder_name,
            $message,
            $priority,
            $member_id,
            $trainer_id
        );
        
        if(mysqli_stmt_execute($stmt_insert)) {
            $_SESSION['success'] = "Reminder sent successfully!";
        } else {
            $_SESSION['error'] = "Failed to send reminder: " . mysqli_error($con);
        }
    } else {
        $_SESSION['error'] = "No active/scheduled sessions with this member";
    }
    
    header("Location: reminder.php");
    exit();
}

// Get sent reminders history
$history_query = "SELECT r.*, m.`fullname` as member_name
                 FROM `reminder` r
                 JOIN `members` m ON r.`user_id` = m.`user_id`
                 WHERE r.`trainer_id` = ?
                 ORDER BY r.`created_at` DESC
                 LIMIT 10";
$stmt_history = mysqli_prepare($con, $history_query);
mysqli_stmt_bind_param($stmt_history, "i", $trainer_id);
mysqli_stmt_execute($stmt_history);
$history_result = mysqli_stmt_get_result($stmt_history);

function sanitizeInput($data, $type = 'string') {
    global $con;
    $data = trim($data);
    $data = mysqli_real_escape_string($con, $data);
    switch($type) {
        case 'int': return (int)$data;
        case 'float': return (float)$data;
        default: return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}
?>


<!-- REST OF YOUR HTML REMAINS THE SAME -->

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Trainer - Send Reminders</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/all.min.css" rel="stylesheet" />
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .reminder-card {
            border-left: 4px solid;
            padding: 15px;
            margin-bottom: 15px;
            background: #f9f9f9;
            border-radius: 3px;
        }
        .priority-high { border-left-color: #d9534f; }
        .priority-medium { border-left-color: #f0ad4e; }
        .priority-low { border-left-color: #5cb85c; }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>

<!--top-Header-menu-->
<?php include '../includes/header.php'?>

<!--sidebar-menu-->
<?php $page='reminders'; include '../includes/sidebar.php'?>

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> 
    <a href="#" class="current">Send Reminders</a> </div>
    <h1>Send Member Reminders</h1>
  </div>
  
  <div class="container-fluid">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row-fluid">
      <div class="span6">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="fas fa-envelope"></i></span>
            <h5>Send New Reminder</h5>
          </div>
          <div class="widget-content form-container">
            <form method="POST" action="reminder.php">
              <div class="control-group">
                <label class="control-label">Select Member:</label>
                <div class="controls">
                  <select name="member_id" class="span12" required>
                    <option value="">-- Select Member --</option>
                    <?php while($member = mysqli_fetch_assoc($members_result)): ?>
                      <option value="<?= $member['user_id'] ?>"><?= htmlspecialchars($member['fullname']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Priority:</label>
                <div class="controls">
                  <select name="priority" class="span12" required>
                    <option value="1">High Priority</option>
                    <option value="2" selected>Medium Priority</option>
                    <option value="3">Low Priority</option>
                  </select>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Message:</label>
                <div class="controls">
                  <textarea name="message" class="span12" rows="5" required 
                  placeholder="Type your reminder/announcement here..."></textarea>
                </div>
              </div>
              
              <div class="form-actions">
                <button type="submit" name="send_reminder" class="btn btn-success">
                  <i class="fas fa-paper-plane"></i> Send Reminder
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <div class="span6">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="fas fa-history"></i></span>
            <h5>Recent Reminders</h5>
          </div>
          <div class="widget-content">
            <?php if(mysqli_num_rows($history_result) > 0): ?>
              <?php while($reminder = mysqli_fetch_assoc($history_result)): ?>
                <div class="reminder-card priority-<?= 
                    $reminder['priority'] == 1 ? 'high' : 
                    ($reminder['priority'] == 2 ? 'medium' : 'low') ?>">
                  <h5><?= htmlspecialchars($reminder['member_name']) ?></h5>
                  <p><?= nl2br(htmlspecialchars($reminder['message'])) ?></p>
                  <small class="text-muted">
                    <?= date('M j, Y g:i A', strtotime($reminder['created_at'])) ?>
                    • Priority: 
                    <?= $reminder['priority'] == 1 ? 'High' : 
                       ($reminder['priority'] == 2 ? 'Medium' : 'Low') ?>
                  </small>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p class="text-muted">No reminders sent yet</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Footer-part-->
<div class="row-fluid">
  <div id="footer" class="span12"> <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi</div>
</div>

<!--end-Footer-part-->

<script src="../js/jquery.min.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/matrix.js"></script>

</body>
</html>