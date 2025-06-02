<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('location:../index.php');    
}

include "dbcon.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System - Attendance Reports</title>
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
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/fullcalendar.css" />
<link rel="stylesheet" href="../css/matrix-style.css" />
<link rel="stylesheet" href="../css/matrix-media.css" />
<link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
<link href="../font-awesome/css/all.css" rel="stylesheet" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<style>
    .report-filters {
        background: #f9f9f9;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        border: 1px solid #eee;
    }
    .time-display {
        font-family: monospace;
    }
    .present {
        color: #5cb85c;
        font-weight: bold;
    }
    .absent {
        color: #d9534f;
    }
    .duration {
        color: #337ab7;
    }
    #footer {
        color: white;
    }
    .export-btn {
        margin-left: 10px;
    }
    .summary-card {
        background: white;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .summary-value {
        font-size: 1.5em;
        font-weight: bold;
    }
</style>
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>
<!--close-Header-part--> 

<!--top-Header-menu-->
<?php include './includes/topheader.php'?>
<!--close-top-Header-menu-->

<!--sidebar-menu-->
<?php $page="attendance-report"; include './includes/sidebar.php'?>
<!--sidebar-menu-->

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="attendance.php">Attendance</a> <a href="#" class="current">Attendance Reports</a> </div>
    <h1 class="text-center">Attendance Reports <i class="icon icon-bar-chart"></i></h1>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">

        <div class="report-filters">
            <form method="get" action="attendance-report.php" class="form-horizontal">
                <div class="control-group">
                    <label class="control-label">Date Range:</label>
                    <div class="controls">
                        <input type="date" min = "<?php echo date('Y-m-01'); ?>" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); ?>">
                        <span>to</span>
                        <input type="date" max name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Member:</label>
                    <div class="controls">
                        <select name="member_id">
                            <option value="">All Members</option>
                            <?php
                            $members_qry = "SELECT user_id, fullname FROM members WHERE status = 'Active' ORDER BY fullname";
                            $members_res = mysqli_query($con, $members_qry);
                            while($member = mysqli_fetch_assoc($members_res)) {
                                $selected = (isset($_GET['member_id']) && $_GET['member_id'] == $member['user_id']) ? 'selected' : '';
                                echo "<option value='{$member['user_id']}' $selected>{$member['fullname']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Status:</label>
                    <div class="controls">
                        <select name="status">
                            <option value="">All Statuses</option>
                            <option value="present" <?php echo (isset($_GET['status']) && $_GET['status'] == 'present') ? 'selected' : ''; ?>>Present</option>
                            <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="attendance-report.php" class="btn">Reset</a>
                    <a href="" class="btn btn-success export-btn disabled" disabled title="Available in the next update">Export to Excel</a>
                </div>
            </form>
        </div>

        <?php
        // Build the query based on filters
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        $member_id = isset($_GET['member_id']) ? $_GET['member_id'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';

        $where = "WHERE a.curr_date BETWEEN '$start_date' AND '$end_date'";
        if(!empty($member_id)) {
            $where .= " AND a.user_id = '$member_id'";
        }
        if(!empty($status)) {
            $where .= " AND a.status = '$status'";
        }

        // Get summary statistics
        $summary_qry = "SELECT 
                        COUNT(DISTINCT a.user_id) as total_members,
                        COUNT(DISTINCT CASE WHEN a.status = 'present' OR a.status = 'completed' THEN a.user_id END) as present_members,
                        COUNT(a.id) as total_sessions,
                        AVG(TIME_TO_SEC(a.duration)) as avg_duration_sec
                        FROM attendance a
                        INNER JOIN members m ON a.user_id = m.user_id
                        $where";
        $summary_res = mysqli_query($con, $summary_qry);
        $summary = mysqli_fetch_assoc($summary_res);

        // Format average duration
        $avg_duration = '--';
   if($summary['avg_duration_sec']) {
    $duration_sec = (float)$summary['avg_duration_sec'];
    $hours = floor($duration_sec / 3600);
    $remaining = fmod($duration_sec, 3600);
    $minutes = floor($remaining / 60);
    $seconds = fmod($remaining, 60);
    $avg_duration = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}
        ?>

        <div class="row-fluid">
            <div class="span3">
                <div class="summary-card">
                    <h5>Total Members</h5>
                    <div class="summary-value"><?php echo $summary['total_members']; ?></div>
                </div>
            </div>
            <div class="span3">
                <div class="summary-card">
                    <h5>Present Members</h5>
                    <div class="summary-value"><?php echo $summary['present_members']; ?></div>
                </div>
            </div>
            <div class="span3">
                <div class="summary-card">
                    <h5>Total Sessions</h5>
                    <div class="summary-value"><?php echo $summary['total_sessions']; ?></div>
                </div>
            </div>
            <div class="span3">
                <div class="summary-card">
                    <h5>Avg. Duration</h5>
                    <div class="summary-value duration"><?php echo $avg_duration; ?></div>
                </div>
            </div>
        </div>

        <div class='widget-box'>
            <div class='widget-title'> 
                <span class='icon'> <i class='icon-th'></i> </span>
                <h5>Attendance Records</h5>
                <div class='buttons'>
                    <span class='badge badge-info'>
                        <?php echo date('F j, Y', strtotime($start_date)); ?> to <?php echo date('F j, Y', strtotime($end_date)); ?>
                    </span>
                </div>
            </div>
            <div class='widget-content nopadding'>
          
                <?php
                // Get attendance records
                $attendance_qry = "SELECT a.*, m.fullname, m.contact, m.services 
                                  FROM attendance a
                                  INNER JOIN members m ON a.user_id = m.user_id
                                  $where
                                  ORDER BY a.curr_date DESC, a.check_in DESC";
                $attendance_res = mysqli_query($con, $attendance_qry);

                if(mysqli_num_rows($attendance_res) > 0) {
                    echo "<table class='table table-bordered table-striped'>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Contact</th>
                                <th>Service</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>";

                    while($record = mysqli_fetch_assoc($attendance_res)) {
                        $check_in = $record['check_in'] ? date('h:i A', strtotime($record['check_in'])) : '--';
                        $check_out = $record['check_out'] ? date('h:i A', strtotime($record['check_out'])) : '--';
                        $duration = $record['duration'] ? $record['duration'] : '--';
                        
                        $status_class = ($record['status'] == 'completed') ? 'present' : (($record['status'] == 'present') ? 'present' : 'absent');
                        $status_text = ($record['status'] == 'completed') ? 'Completed' : (($record['status'] == 'present') ? 'Present' : 'Absent');

                        echo "<tr>
                            <td>".date('M j, Y', strtotime($record['curr_date']))."</td>
                            <td>{$record['fullname']}</td>
                            <td>{$record['contact']}</td>
                            <td>{$record['services']}</td>
                            <td class='time-display'>$check_in</td>
                            <td class='time-display'>$check_out</td>
                            <td class='duration'>$duration</td>
                            <td class='$status_class'>$status_text</td>
                        </tr>";
                    }

                    echo "</tbody></table>";
                } else {
                    echo "<div class='alert alert-info'>No attendance records found for the selected filters.</div>";
                }
                ?>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!--end-main-container-part-->

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
<script src="../js/jquery.dataTables.min.js"></script> 
<script src="../js/matrix.tables.js"></script>

<script>
$(document).ready(function() {
    // Initialize datepicker
    $('input[type="date"]').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    // Initialize select2
    $('select').select2();
});
</script>

</body>
</html>