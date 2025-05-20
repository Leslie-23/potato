<?php
session_start();
// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');
    exit();
}

include "dbcon.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get all necessary data at the top
$services_query = "SELECT services, count(*) as number FROM members GROUP BY services";
$services_result = mysqli_query($con, $services_query);
if(!$services_result) die("Services query failed: " . mysqli_error($con));

$gender_query = "SELECT gender, count(*) as enumber FROM members GROUP BY gender";
$gender_result = mysqli_query($con, $gender_query);
if(!$gender_result) die("Gender query failed: " . mysqli_error($con));

$designation_query = "SELECT designation, count(*) as snumber FROM staffs GROUP BY designation";
$designation_result = mysqli_query($con, $designation_query);
if(!$designation_result) die("Designation query failed: " . mysqli_error($con));

$earnings_query = "SELECT SUM(amount) as total_earnings FROM members";
$earnings_result = mysqli_query($con, $earnings_query);
if(!$earnings_result) die("Earnings query failed: " . mysqli_error($con));
$earnings_data = mysqli_fetch_assoc($earnings_result);

$expenses_query = "SELECT SUM(amount) as total_expenses FROM equipment";
$expenses_result = mysqli_query($con, $expenses_query);
if(!$expenses_result) die("Expenses query failed: " . mysqli_error($con));
$expenses_data = mysqli_fetch_assoc($expenses_result);

$todo_query = "SELECT t.*, m.fullname FROM todo t JOIN members m ON t.user_id = m.user_id";
$todo_result = mysqli_query($con, $todo_query);
if(!$todo_result) die("Todo query failed: " . mysqli_error($con));

$announcement_query = "SELECT * FROM announcements";
$announcement_result = mysqli_query($con, $announcement_query);
if(!$announcement_result) die("Announcement query failed: " . mysqli_error($con));
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
    <link rel="stylesheet" href="../css/font-awesome.css" />
    <link href="../font-awesome/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link rel="stylesheet" href="../font-awesome/css/all.min.css" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
    <!-- Chart Scripts -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawAllCharts);
        
        function drawAllCharts() {
            drawServicesChart();
            drawEarningsExpensesChart();
            drawGenderChart();
            drawDesignationChart();
        }
        
        // Services Chart
        function drawServicesChart() {
            var data = google.visualization.arrayToDataTable([
                ['Services', 'Total Numbers'],
                <?php
                mysqli_data_seek($services_result, 0);
                while($row = mysqli_fetch_assoc($services_result)) {
                    echo "['".htmlspecialchars($row['services'])."', ".intval($row['number'])."],";
                }
                ?>
            ]);
            
            var options = {
                width: 710,
                legend: { position: 'none' },
                bars: 'vertical',
                axes: {
                    x: { 0: { side: 'top', label: 'Total'} }
                },
                bar: { groupWidth: "100%" },
                backgroundColor: 'transparent'
            };
            
            var chart = new google.charts.Bar(document.getElementById('top_x_div'));
            chart.draw(data, options);
        }
        
        // Earnings vs Expenses Chart
        function drawEarningsExpensesChart() {
            var data = google.visualization.arrayToDataTable([
                ['Category', 'Amount'],
                ['Earnings', <?php echo floatval($earnings_data['total_earnings']); ?>],
                ['Expenses', <?php echo floatval($expenses_data['total_expenses']); ?>]
            ]);
            
            var options = {
                width: 1050,
                legend: { position: 'none' },
                bars: 'horizontal',
                axes: {
                    x: { 0: { side: 'top', label: 'Total'} }
                },
                bar: { groupWidth: "100%" },
                backgroundColor: 'transparent'
            };
            
            var chart = new google.charts.Bar(document.getElementById('top_y_div'));
            chart.draw(data, options);
        }
        
        // Gender Distribution Chart
        function drawGenderChart() {
            var data = google.visualization.arrayToDataTable([
                ['Gender', 'Number'],
                <?php
                mysqli_data_seek($gender_result, 0);
                while($row = mysqli_fetch_assoc($gender_result)) {
                    echo "['".htmlspecialchars($row['gender'])."', ".intval($row['enumber'])."],";
                }
                ?>
            ]);
            
            var options = {
                pieHole: 0.4,
                backgroundColor: 'transparent'
            };
            
            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);
        }
        
        // Staff Designation Chart
        function drawDesignationChart() {
            var data = google.visualization.arrayToDataTable([
                ['Designation', 'Number'],
                <?php
                mysqli_data_seek($designation_result, 0);
                while($row = mysqli_fetch_assoc($designation_result)) {
                    echo "['".htmlspecialchars($row['designation'])."', ".intval($row['snumber'])."],";
                }
                ?>
            ]);
            
            var options = {
                pieHole: 0.4,
                backgroundColor: 'transparent'
            };
            
            var chart = new google.visualization.PieChart(document.getElementById('donutchart2022'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>

<!-- Header Part -->
<div id="header">
    <h1><a href="dashboard.php">Perfect Gym Admin</a></h1>
</div>

<!-- Top Header Menu -->
<?php include 'includes/topheader.php'; ?>

<!-- Sidebar Menu -->
<?php $page='dashboard'; include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div id="content">
    <!-- Breadcrumbs -->
    <div id="content-header">
        <div id="breadcrumb"> 
            <a href="index.php" title="You're right here" class="tip-bottom">
                <i class="fas fa-home"></i> Home
            </a>
        </div>
    </div>

    <!-- Action Boxes -->
    <div class="container-fluid">
        <div class="quick-actions_homepage">
            <ul class="quick-actions">
                <li class="bg_ls span " style="background-color: #22b86c"> 
                    <a href="index.php" style="font-size: 16px;">
                        <i class="fas fa-user-check"></i> 
                        <span class="label label-important"><?php include 'actions/dashboard-activecount.php'?></span> 
                        Active Members 
                    </a> 
                </li>
                <li class="bg_lo span3" style="background-color: #10aaf8"> 
                    <a href="members.php" style="font-size: 16px;"> 
                        <i class="fas fa-users"></i>
                        <span class="label label-important"><?php include 'dashboard-usercount.php'?></span> 
                        Registered Members
                    </a> 
                </li>
                <li class="bg_lg span3"> 
                    <a href="payment.php" style="font-size: 16px;"> 
                        <i class="fas fa-dollar-sign"></i> 
                        Total Earnings: $<?php include 'income-count.php' ?>
                    </a> 
                </li>
                <li class="bg_lb span2"> 
                    <a href="announcement.php" style="font-size: 16px;"> 
                        <i class="fas fa-bullhorn"></i>
                        <span class="label label-important"><?php include 'actions/count-announcements.php'?></span>
                        Announcements 
                    </a> 
                </li>
            </ul>
        </div>

        <!-- Services Report -->
        <div class="row-fluid">
            <div class="widget-box">
                <div class="widget-title bg_lg">
                    <span class="icon"><i class="fas fa-file"></i></span>
                    <h5>Services Report</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span8">
                            <div id="top_x_div" style="width: 100%; height: 290px;"></div>
                        </div>
                        <div class="span4">
                            <ul class="site-stats">
                                <li class="bg_lg"><i class="fas fa-users"></i> <strong><?php include 'dashboard-usercount.php';?></strong> <small>Total Members</small></li>
                                <li class="bg_lg"><i class="fas fa-user-clock"></i> <strong><?php include 'actions/dashboard-staff-count.php';?></strong> <small>Staff Users</small></li>
                                <li class="bg_ls"><i class="fas fa-dumbbell"></i> <strong><?php include 'actions/count-equipments.php';?></strong> <small>Available Equipments</small></li>
                                <li class="bg_ly"><i class="fas fa-file-invoice-dollar"></i> <strong>$<?php include 'actions/total-exp.php';?></strong> <small>Total Expenses</small></li>
                                <li class="bg_lr"><i class="fas fa-user-ninja"></i> <strong><?php include 'actions/count-trainers.php';?></strong> <small>Active Gym Trainers</small></li>
                                <li class="bg_lb"><i class="fas fa-calendar-check"></i> <strong><?php include 'actions/count-attendance.php';?></strong> <small>Present Members</small></li>
                                <li class="bg_lb"><i class="fas fa-hand"></i> <strong><?php include 'actions/count-pending.php';?></strong> <small>pending Members</small></li>
                                <li class="bg_lh"><i class="fas fa-cancel"></i> <strong><?php include 'actions/count-expired.php';?></strong> <small>Expired Members</small></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings & Expenses Report -->
        <div class="row-fluid">
            <div class="widget-box">
                <div class="widget-title bg_lg">
                    <span class="icon"><i class="fas fa-file"></i></span>
                    <h5>Earnings & Expenses Reports</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span12">
                            <div id="top_y_div" style="width: 100%; height: 180px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member and Staff Overview -->
        <div class="row-fluid">
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseGender">
                        <span class="icon"><i class="fas fa-chevron-down"></i></span>
                        <h5>Registered Gym Members by Gender: Overview</h5>
                    </div>
                    <div class="widget-content nopadding collapse in" id="collapseGender">
                        <div id="donutchart" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseDesignation">
                        <span class="icon"><i class="fas fa-chevron-down"></i></span>
                        <h5>Staff Members by Designation: Overview</h5>
                    </div>
                    <div class="widget-content nopadding collapse in" id="collapseDesignation">
                        <div id="donutchart2022" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>   
            </div>
        </div>

        <!-- Announcements and Todo List -->
        <div class="row-fluid">
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseAnnouncements">
                        <span class="icon"><i class="fas fa-chevron-down"></i></span>
                        <h5>Gym Announcement</h5>
                    </div>
                    <div class="widget-content nopadding collapse in" id="collapseAnnouncements">
                        <ul class="recent-posts">
                            <?php while($row = mysqli_fetch_assoc($announcement_result)): ?>
                            <li>
                                <div class="user-thumb">
                                    <img width="70" height="40" alt="User" src="../img/demo/av1.jpg">
                                </div>
                                <div class="article-post">
                                    <span class="user-info">By: System Administrator / Date: <?= htmlspecialchars($row['date']) ?></span>
                                    <p><a href="#"><?= htmlspecialchars($row['message']) ?></a></p>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <li>
                                <a href="manage-announcement.php">
                                    <button class="btn btn-warning btn-mini">View All</button>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

           <div class="span6">
  <div class="widget-box">
    <div class="widget-title"> <span class="icon"><i class="icon-bar-chart"></i></span>
      <h5>Member Progress Report</h5>
    </div>
    <div class="widget-content">
      <div class="report-stats">
        <?php
        include "dbcon.php";
        
        // Member progress statistics
        $progressQuery = "SELECT 
                          COUNT(*) as total_members,
                          SUM(CASE WHEN ini_weight IS NOT NULL AND curr_weight IS NOT NULL THEN 1 ELSE 0 END) as tracking_progress,
                          AVG(curr_weight - ini_weight) as avg_weight_change,
                          COUNT(DISTINCT user_id) as active_members
                        FROM members
                        WHERE status = 'Active'";
        $progressResult = mysqli_query($con, $progressQuery);
        $progressData = mysqli_fetch_assoc($progressResult);
        
        // Session statistics
        $sessionQuery = "SELECT 
                          COUNT(*) as total_sessions,
                          SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_sessions,
                          SUM(CASE WHEN status = 'missed' THEN 1 ELSE 0 END) as missed_sessions,
                          AVG(TIMESTAMPDIFF(MINUTE, session_date, end_date)) as avg_session_duration
                        FROM training_sessions";
        $sessionResult = mysqli_query($con, $sessionQuery);
        $sessionData = mysqli_fetch_assoc($sessionResult);
        ?>
        
        <div class="stat-row">
          <h4>Member Statistics</h4>
          <div class="stat-item">
            <span class="stat-label">Total Active Members:</span>
            <span class="stat-value"><?php echo $progressData['active_members']; ?></span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Tracking Progress:</span>
            <span class="stat-value"><?php echo $progressData['tracking_progress']; ?></span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Avg Weight Change (kg):</span>
            <span class="stat-value"><?php echo number_format($progressData['avg_weight_change'], 2); ?></span>
          </div>
        </div>
        
        <div class="stat-row">
          <h4>Training Session Statistics</h4>
          <div class="stat-item">
            <span class="stat-label">Total Sessions:</span>
            <span class="stat-value"><?php echo $sessionData['total_sessions']; ?></span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Completed:</span>
            <span class="stat-value"><?php echo $sessionData['completed_sessions']; ?></span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Missed:</span>
            <span class="stat-value"><?php echo $sessionData['missed_sessions']; ?></span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Avg Duration (min):</span>
            <span class="stat-value"><?php echo number_format($sessionData['avg_session_duration'], 1); ?></span>
          </div>
        </div>
        
        <div class="progress-table">
          <h4>Top Progressing Members</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Member</th>
                <th>Initial Weight</th>
                <th>Current Weight</th>
                <th>Change</th>
                <th>Body Type Change</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $topProgressQuery = "SELECT 
                                    fullname, 
                                    ini_weight, 
                                    curr_weight, 
                                    (curr_weight - ini_weight) as weight_change,
                                    ini_bodytype,
                                    curr_bodytype
                                  FROM members
                                  WHERE ini_weight IS NOT NULL 
                                    AND curr_weight IS NOT NULL
                                    AND status = 'Active'
                                  ORDER BY ABS(curr_weight - ini_weight) DESC
                                  LIMIT 5";
              $topProgressResult = mysqli_query($con, $topProgressQuery);
              
              while($row = mysqli_fetch_assoc($topProgressResult)) {
                $changeClass = ($row['weight_change'] > 0) ? 'text-success' : 'text-error';
                echo "<tr>
                  <td>{$row['fullname']}</td>
                  <td>{$row['ini_weight']} kg</td>
                  <td>{$row['curr_weight']} kg</td>
                  <td class='$changeClass'>" . number_format($row['weight_change'], 1) . " kg</td>
                  <td>{$row['ini_bodytype']} → {$row['curr_bodytype']}</td>
                </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<div class="row-fluid">
    <div id="footer" class="span12">
        <?= date("Y") ?> &copy; Developed By Leslie Paul Ajayi
    </div>
</div>

<style>
    #footer {
        color: white;
    }
    .widget-content {
        padding: 15px;
    }
    .todo .txt small {
        color: #777;
        font-size: 0.9em;
    }
</style>

<!-- JavaScript Files -->
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
</body>
</html>