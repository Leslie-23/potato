<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}

include "../dbcon.php";

$staff_id = $_SESSION['user_id'];

// Get staff details
$staff_query = "SELECT * FROM staffs WHERE user_id = ?";
$stmt = mysqli_prepare($con, $staff_query);
mysqli_stmt_bind_param($stmt, "i", $staff_id);
mysqli_stmt_execute($stmt);
$staff_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Get stats based on role
$stats_query = "";
if($staff_data['designation'] == 'cashier') {
    $stats_query = "SELECT 
                   COUNT(*) as total_transactions,
                   SUM(amount) as total_revenue,
                   COUNT(DISTINCT user_id) as members_served
                   FROM transactions
                   WHERE processed_by = ?";
} elseif($staff_data['designation'] == 'manager') {
    $stats_query = "SELECT 
                   COUNT(*) as total_members,
                   COUNT(CASE WHEN status = 'Active' THEN 1 END) as active_members,
                   COUNT(DISTINCT user_id) as total_staff
                   FROM members";
} else {
    // Default stats for other staff
    $stats_query = "SELECT 
                   COUNT(*) as total_tasks,
                   COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_tasks
                   FROM tasks
                   WHERE admin_id = ?";
}

$stmt_stats = mysqli_prepare($con, $stats_query);
if($staff_data['designation'] == 'manager') {
    // No parameter needed for manager query
    mysqli_stmt_execute($stmt_stats);
} else {
    mysqli_stmt_bind_param($stmt_stats, "i", $staff_id);
    mysqli_stmt_execute($stmt_stats);
}
$stats_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_stats));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Profile</title>
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
            background: linear-gradient(135deg, #5bc0de 0%, #2e6da4 100%);
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
            border-left: 4px solid #5bc0de;
        }
        .stats-value {
            font-size: 1.8em;
            font-weight: bold;
            color: #5bc0de;
        }
        .activity-card {
            border-left: 4px solid #5cb85c;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .edit-profile-btn {
            position: absolute;
            right: 20px;
            top: 20px;
        }
        .role-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            background: #337ab7;
            color: white;
            display: inline-block;
            margin-top: 5px;
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
    <a href="#" class="current">My Profile</a> </div>
    <h1>Staff Profile</h1>
  </div>
  
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        
        <!-- Profile Header -->
        <div class="profile-header">
          <div class="row-fluid">
            <div class="span2 text-center">
              <img src="<?= $staff_data['image_url'] ? '../'.$staff_data['image_url'] : '../img/default-male-avatar.png' ?>" 
                   class="profile-img" alt="Profile Image">
            </div>
            <div class="span6">
              <h2><?= htmlspecialchars($staff_data['fullname']) ?></h2>
              <span class="role-badge"><?= ucfirst($staff_data['designation']) ?></span>
              <p class="text-muted" style="margin-top: 15px;">
                <i class="icon-envelope"></i> <?= htmlspecialchars($staff_data['email']) ?>
                <span class="muted">|</span>
                <i class="icon-phone"></i> <?= htmlspecialchars($staff_data['contact']) ?>
              </p>
              <p><i class="icon-map-marker"></i> <?= htmlspecialchars($staff_data['address']) ?></p>
            </div>
            <div class="span4">
              <div class="row-fluid">
                <?php if($staff_data['designation'] == 'cashier'): ?>
                  <div class="span6 stats-card">
                    <h5>Transactions</h5>
                    <div class="stats-value"><?= $stats_data['total_transactions'] ?></div>
                  </div>
                  <div class="span6 stats-card">
                    <h5>Revenue</h5>
                    <div class="stats-value">₵<?= number_format($stats_data['total_revenue'], 2) ?></div>
                  </div>
                <?php elseif($staff_data['designation'] == 'manager'): ?>
                  <div class="span6 stats-card">
                    <h5>Members</h5>
                    <div class="stats-value"><?= $stats_data['total_members'] ?></div>
                  </div>
                  <div class="span6 stats-card">
                    <h5>Staff</h5>
                    <div class="stats-value"><?= $stats_data['total_staff'] ?></div>
                  </div>
                <?php else: ?>
                  <div class="span6 stats-card">
                    <h5 style="color: #337ab7;">Tasks</h5>
                    <div class="stats-value"><?= $stats_data['total_tasks'] ?></div>
                  </div>
                  <div class="span6 stats-card">
                    <h5 style="color: #337ab7;">Completed</h5>
                    <div class="stats-value"><?= $stats_data['completed_tasks'] ?></div>
                  </div>
                <?php endif; ?>
              </div>
              <div class="row-fluid">
                <?php if($staff_data['designation'] == 'cashier'): ?>
                  <div class="span12 stats-card">
                    <h5>Members Served</h5>
                    <div class="stats-value"><?= $stats_data['members_served'] ?></div>
                  </div>
                <?php elseif($staff_data['designation'] == 'manager'): ?>
                  <div class="span12 stats-card">
                    <h5>Active Members</h5>
                    <div class="stats-value"><?= $stats_data['active_members'] ?></div>
                  </div>
                <?php else: ?>
                  <div class="span12 stats-card">
                    <h5 style="color: #337ab7;">Completion Rate</h5>
                    <div class="stats-value"><?= $stats_data['total_tasks'] > 0 ? round(($stats_data['completed_tasks']/$stats_data['total_tasks'])*100) : 0 ?>%</div>
                  </div>
                <?php endif; ?>
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
                    <td width="30%"><strong>Username</strong></td>
                    <td><?= htmlspecialchars($staff_data['username']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Full Name</strong></td>
                    <td><?= htmlspecialchars($staff_data['fullname']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Email</strong></td>
                    <td><?= htmlspecialchars($staff_data['email']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Contact</strong></td>
                    <td><?= htmlspecialchars($staff_data['contact']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Address</strong></td>
                    <td><?= htmlspecialchars($staff_data['address']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Gender</strong></td>
                    <td><?= htmlspecialchars($staff_data['gender']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Designation</strong></td>
                    <td><?= ucfirst(htmlspecialchars($staff_data['designation'])) ?></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          
          <!-- Right Column -->
          <div class="span6">
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="icon-tasks"></i></span>
                <h5>Recent Activity</h5>
              </div>
              <div class="widget-content">
                <?php if($staff_data['designation'] == 'cashier'): ?>
                  <?php
                  $activity_query = "SELECT t.*, m.fullname 
                                   FROM transactions t
                                   JOIN members m ON t.user_id = m.user_id
                                   WHERE t.processed_by = ?
                                   ORDER BY t.payment_date DESC
                                   LIMIT 5";
                  $stmt_activity = mysqli_prepare($con, $activity_query);
                  mysqli_stmt_bind_param($stmt_activity, "i", $staff_id);
                  mysqli_stmt_execute($stmt_activity);
                  $activities = mysqli_stmt_get_result($stmt_activity);
                  
                  if(mysqli_num_rows($activities) > 0): ?>
                    <?php while($activity = mysqli_fetch_assoc($activities)): ?>
                      <div class="activity-card">
                        <div class="row-fluid">
                          <div class="span8">
                            <h5><?= htmlspecialchars($activity['fullname']) ?></h5>
                            <p class="muted">Payment processed</p>
                          </div>
                          <div class="span4 text-right">
                            <p class="text-success">₵<?= number_format($activity['amount'], 2) ?></p>
                            <small class="muted">
                              <?= date('M j, Y', strtotime($activity['payment_date'])) ?>
                            </small>
                          </div>
                        </div>
                      </div>
                    <?php endwhile; ?>
                    <a href="transactions.php" class="btn btn-small btn-block">
                      <i class="icon-list"></i> View All Transactions
                    </a>
                  <?php else: ?>
                    <div class="alert alert-info">
                      No recent transactions found
                    </div>
                  <?php endif; ?>
                
                <?php elseif($staff_data['designation'] == 'manager'): ?>
                  <?php
                  $activity_query = "SELECT * FROM announcements 
                                   ORDER BY date DESC
                                   LIMIT 5";
                  $activities = mysqli_query($con, $activity_query);
                  
                  if(mysqli_num_rows($activities) > 0): ?>
                    <?php while($activity = mysqli_fetch_assoc($activities)): ?>
                      <div class="activity-card">
                        <h5>Announcement</h5>
                        <p><?= htmlspecialchars($activity['message']) ?></p>
                        <small class="muted">
                          <?= date('M j, Y', strtotime($activity['date'])) ?>
                        </small>
                      </div>
                    <?php endwhile; ?>
                    <a href="announcements.php" class="btn btn-small btn-block">
                      <i class="icon-list"></i> View All Announcements
                    </a>
                  <?php else: ?>
                    <div class="alert alert-info">
                      No recent announcements
                    </div>
                  <?php endif; ?>
                
                <?php else: ?>
                  <?php
                  $activity_query = "SELECT * FROM tasks 
                                   WHERE admin_id = ?
                                   ORDER BY created_at DESC
                                   LIMIT 5";
                  $stmt_activity = mysqli_prepare($con, $activity_query);
                  mysqli_stmt_bind_param($stmt_activity, "i", $staff_id);
                  mysqli_stmt_execute($stmt_activity);
                  $activities = mysqli_stmt_get_result($stmt_activity);
                  
                  if(mysqli_num_rows($activities) > 0): ?>
                    <?php while($activity = mysqli_fetch_assoc($activities)): ?>
                      <div class="activity-card">
                        <h5><?= htmlspecialchars($activity['title']) ?></h5>
                        <p><?= htmlspecialchars(substr($activity['description'], 0, 100)) ?>...</p>
                        <small class="muted">
                          Due: <?= date('M j, Y', strtotime($activity['end_datetime'])) ?>
                        </small>
                      </div>
                    <?php endwhile; ?>
                    <a href="tasks.php" class="btn btn-small btn-block">
                      <i class="icon-list"></i> View All Tasks
                    </a>
                  <?php else: ?>
                    <div class="alert alert-info">
                      No recent tasks
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
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