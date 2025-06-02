<?php
session_start();
if(!isset($_SESSION['user_id']) ){
    header('location:../index.php');
    exit();
}

include "../dbcon.php";

$trainer_id = $_SESSION['user_id'];

// Get trainer details
$trainer_query = "SELECT s.*, t.specialization, t.bio, t.certification, t.years_experience 
                 FROM staffs s
                 LEFT JOIN trainers t ON s.user_id = t.trainer_id
                 WHERE s.user_id = ?";
$stmt = mysqli_prepare($con, $trainer_query);
mysqli_stmt_bind_param($stmt, "i", $trainer_id);
mysqli_stmt_execute($stmt);
$trainer = mysqli_stmt_get_result($stmt);
$trainer_data = mysqli_fetch_assoc($trainer);

// Get trainer's upcoming sessions
$sessions_query = "SELECT ts.session_id, m.fullname, m.profile_pic, 
                  ts.session_date, ts.status, wp.workout_name
                  FROM training_sessions ts
                  JOIN members m ON ts.user_id = m.user_id
                  JOIN workout_plan wp ON ts.table_id = wp.table_id
                  WHERE ts.trainer_id = ?
                  AND ts.session_date >= NOW()
                  AND ts.status != 'cancelled'
                  ORDER BY ts.session_date ASC
                  LIMIT 5";
$stmt_sessions = mysqli_prepare($con, $sessions_query);
mysqli_stmt_bind_param($stmt_sessions, "i", $trainer_id);
mysqli_stmt_execute($stmt_sessions);
$upcoming_sessions = mysqli_stmt_get_result($stmt_sessions);

// Get stats
$stats_query = "SELECT 
               COUNT(DISTINCT ts.user_id) as total_trainees,
               COUNT(CASE WHEN ts.status = 'completed' THEN 1 END) as completed_sessions,
               COUNT(CASE WHEN ts.status = 'scheduled' THEN 1 END) as upcoming_sessions
               FROM training_sessions ts
               WHERE ts.trainer_id = ?";
$stmt_stats = mysqli_prepare($con, $stats_query);
mysqli_stmt_bind_param($stmt_stats, "i", $trainer_id);
mysqli_stmt_execute($stmt_stats);
$stats = mysqli_stmt_get_result($stmt_stats);
$stats_data = mysqli_fetch_assoc($stats);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Trainer Profile</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/uniform.css" />
    <link rel="stylesheet" href="../css/select2.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        .profile-header {
            background: linear-gradient(135deg, #337ab7 0%, #2e6da4 100%);
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .stats-card {
            background: white;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #337ab7;
        }
        .stats-value {
            font-size: 1.8em;
            font-weight: bold;
            color: #337ab7;
        }
        .session-card {
            border-left: 4px solid #5cb85c;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .session-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .badge-scheduled { background: #5cb85c; color: white; }
        .badge-completed { background: #5bc0de; color: white; }
        .badge-pending { background: #f0ad4e; color: white; }
        .edit-profile-btn {
            position: absolute;
            right: 20px;
            top: 20px;
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
<?php $page='profile'; include '../includes/sidebar.php'?>

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
    <a href="#" class="current">Trainer Profile</a> </div>
    <h1>My Profile</h1>
  </div>
  
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        
        <!-- Profile Header -->
        <div class="profile-header">
          <div class="row-fluid">
            <div class="span2 text-center">
              <img src="<?= $trainer_data['image_url'] ?: '../img/default-trainer-avatar.jpg' ?>" 
                   class="profile-img" alt="Profile Image">
            </div>
            <div class="span6">
              <h2><?= htmlspecialchars($trainer_data['fullname']) ?></h2>
              <h4><?= htmlspecialchars($trainer_data['designation']) ?></h4>
              <p class="text-muted">
                <i class="icon-star"></i> <?= htmlspecialchars($trainer_data['specialization']) ?>
                <span class="muted">|</span>
                <i class="icon-certificate"></i> <?= htmlspecialchars($trainer_data['certification']) ?>
              </p>
              <p><?= htmlspecialchars($trainer_data['bio']) ?></p>
            </div>
            <div class="span4">
              <div class="row-fluid">
                <div class="span6 stats-card">
                  <h5 style="color: #337ab7;">Trainees</h5>
                  <div class="stats-value"><?= $stats_data['total_trainees'] ?></div>
                </div>
                <div class="span6 stats-card">
                  <h5 style="color: #337ab7;">Experience</h5>
                  <div class="stats-value"><?= $trainer_data['years_experience'] ?> yrs</div>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span6 stats-card">
                  <h5 style="color: #337ab7;">Completed</h5>
                  <div class="stats-value"><?= $stats_data['completed_sessions'] ?></div>
                </div>
                <div class="span6 stats-card">
                  <h5 style="color: #337ab7;">Upcoming</h5>
                  <div class="stats-value"><?= $stats_data['upcoming_sessions'] ?></div>
                </div>
              </div>
            </div>
          </div>
          <a href="edit-profile.php" class="btn btn-primary edit-profile-btn">
            <i class="icon-edit"></i> Edit Profile
          </a>
        </div>
        
        <div class="row-fluid">
          <!-- Left Column -->
          <div class="span6">
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="icon-user"></i></span>
                <h5>Personal Information</h5>
              </div>
              <div class="widget-content">
                <table class="table table-bordered">
                  <tr>
                    <td width="30%"><strong>Email</strong></td>
                    <td><?= htmlspecialchars($trainer_data['email']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Contact</strong></td>
                    <td><?= htmlspecialchars($trainer_data['contact']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Address</strong></td>
                    <td><?= htmlspecialchars($trainer_data['address']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Gender</strong></td>
                    <td><?= htmlspecialchars($trainer_data['gender']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Specialization</strong></td>
                    <td><?= htmlspecialchars($trainer_data['specialization']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Certification</strong></td>
                    <td><?= htmlspecialchars($trainer_data['certification']) ?></td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="icon-list"></i></span>
                <h5>Workout Specializations</h5>
              </div>
              <div class="widget-content">
                <ul class="unstyled">
                  <?php
                  $specializations_query = "SELECT wp.workout_name 
                                          FROM trainer_workout_specialization tws
                                          JOIN workout_plan wp ON tws.plan_id = wp.table_id
                                          WHERE tws.trainer_id = ?";
                  $stmt_spec = mysqli_prepare($con, $specializations_query);
                  mysqli_stmt_bind_param($stmt_spec, "i", $trainer_id);
                  mysqli_stmt_execute($stmt_spec);
                  $specializations = mysqli_stmt_get_result($stmt_spec);
                  
                  if(mysqli_num_rows($specializations) > 0) {
                      while($spec = mysqli_fetch_assoc($specializations)) {
                          echo '<li><i class="icon-ok"></i> '.htmlspecialchars($spec['workout_name']).'</li>';
                      }
                  } else {
                      echo '<li class="text-muted">No specializations added yet</li>';
                  }
                  ?>
                </ul>
              </div>
            </div>
          </div>
          
          <!-- Right Column -->
          <div class="span6">
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="icon-calendar"></i></span>
                <h5>Upcoming Sessions</h5>
              </div>
              <div class="widget-content">
                <?php if(mysqli_num_rows($upcoming_sessions) > 0): ?>
                  <?php while($session = mysqli_fetch_assoc($upcoming_sessions)): ?>
                    <div class="session-card">
                      <div class="row-fluid">
                        <div class="span1">
                          <img src="<?= $session['profile_pic'] ? '../../customer/uploads/profiles/'.$session['profile_pic'] : '../../img/default-member-avatar.png' ?>" 
                               class="session-avatar" alt="Member Avatar">
                        </div>
                        <div class="span7">
                          <h5><?= htmlspecialchars($session['fullname']) ?></h5>
                          <p class="muted"><?= htmlspecialchars($session['workout_name']) ?></p>
                        </div>
                        <div class="span4 text-right">
                          <span class="badge-status badge-<?= $session['status'] ?>">
                            <?= ucfirst($session['status']) ?>
                          </span>
                          <p class="muted">
                            <small><?= date('M j, g:i A', strtotime($session['session_date'])) ?></small>
                          </p>
                        </div>
                      </div>
                    </div>
                  <?php endwhile; ?>
                  <a href="sessions.php" class="btn btn-small btn-block">
                    <i class="icon-list"></i> View All Sessions
                  </a>
                <?php else: ?>
                  <div class="alert alert-info">
                    No upcoming sessions scheduled
                  </div>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="icon-bar-chart"></i></span>
                <h5>Performance Metrics</h5>
              </div>
              <div class="widget-content">
                <div class="alert alert-info">
                  <strong>Coming Soon:</strong> Trainer performance dashboard with client progress tracking and session analytics.
                </div>
              </div>
            </div>
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
<script src="../js/jquery.ui.custom.js"></script> 
<script src="../js/bootstrap.min.js"></script>  
<script src="../js/matrix.js"></script> 
<script src="../js/jquery.validate.js"></script> 
<script src="../js/jquery.uniform.js"></script> 
<script src="../js/select2.min.js"></script> 
<script src="../js/matrix.tables.js"></script>

</body>
</html>