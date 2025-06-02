<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');    
}

include "dbcon.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System Admin</title>
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
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .attendance-status {
        font-weight: bold;
    }
    .checked-in {
        color: #5cb85c;
    }
    .checked-out {
        color: #d9534f;
    }
    .duration {
        color: #337ab7;
    }
    #footer {
        color: white;
    }
    .time-display {
        font-family: monospace;
        font-size: 0.9em;
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
<?php $page="attendance"; include './includes/sidebar.php'?>
<!--sidebar-menu-->

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="attendance.php" class="current">Manage Attendance</a> </div>
    <h1 class="text-center">Attendance List <i class="icon icon-calendar"></i></h1>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">

      <div class='widget-box'>
          <div class='widget-title'> <span class='icon'> <i class='icon-th'></i> </span>
            <h5>Attendance Table - <?php echo date('l, F j, Y'); ?></h5>
            <div class='buttons'>
                <a href='attendance-report.php' class='btn btn-info'><i class='icon-list-alt'></i> View Reports</a>
            </div>
          </div>
          <div class='widget-content nopadding'>
          
          <?php
          include "dbcon.php";
          // Get today's date in correct format
          date_default_timezone_set('Africa/Accra');
          $current_date = date('Y-m-d');
          
          // Get all active members
          $qry = "SELECT * FROM members WHERE status = 'Active'";
          $result = mysqli_query($con, $qry);
          
          if(mysqli_num_rows($result) > 0) {
              echo "<table class='table table-bordered table-striped'>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Member Name</th>
                      <th>Contact</th>
                      <th>Service</th>
                      <th>Check-In Time</th>
                      <th>Check-Out Time</th>
                      <th>Duration</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>";
              
              $cnt = 1;
              while($row = mysqli_fetch_array($result)) {
                  $member_id = $row['user_id'];
                  
                  // Check attendance for today
                  $attendance_qry = "SELECT * FROM attendance 
                                    WHERE user_id = '$member_id' 
                                    AND curr_date = '$current_date'
                                    ORDER BY id DESC LIMIT 1";
                  $attendance_res = mysqli_query($con, $attendance_qry);
                  $attendance = mysqli_fetch_assoc($attendance_res);
                  
                  echo "<tr>
                      <td><div class='text-center'>$cnt</div></td>
                      <td>".htmlspecialchars($row['fullname'])."</td>
                      <td>".htmlspecialchars($row['contact'])."</td>
                      <td>".htmlspecialchars($row['services'])."</td>";
                      
                  if($attendance) {
                      // Member has attendance record for today
                      $check_in = $attendance['check_in'] ? date('h:i A', strtotime($attendance['check_in'])) : '--';
                      $check_out = $attendance['check_out'] ? date('h:i A', strtotime($attendance['check_out'])) : '--';
                      $duration = $attendance['duration'] ? $attendance['duration'] : '--';
                      
                      echo "<td class='time-display'>$check_in</td>
                          <td class='time-display'>$check_out</td>
                          <td class='duration'>$duration</td>";
                      
                      if($attendance['check_out']) {
                          echo "<td class='attendance-status checked-out'>Checked Out</td>
                              <td><div class='text-center'>
                                  <span class='label label-success'>Completed</span>
                              </div></td>";
                      } else {
                          echo "<td class='attendance-status checked-in'>Checked In</td>
                              <td><div class='text-center'>
                                  <a href='actions/check-out.php?id=$member_id' class='btn btn-danger'>
                                      <i class='icon-time'></i> Check Out
                                  </a>
                              </div></td>";
                      }
                  } else {
                      // No attendance record for today
                      echo "<td class='time-display'>--</td>
                          <td class='time-display'>--</td>
                          <td class='duration'>--</td>
                          <td class='attendance-status'>Absent</td>
                          <td><div class='text-center'>
                              <a href='actions/check-in.php?id=$member_id' class='btn btn-info'>
                                  <i class='icon-map-marker'></i> Check In
                              </a>
                          </div></td>";
                  }
                  
                  echo "</tr>";
                  $cnt++;
              }
              
              echo "</tbody></table>";
          } else {
              echo "<div class='alert alert-info'>No active members found.</div>";
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

</body>
</html>