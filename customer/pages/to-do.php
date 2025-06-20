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
<?php $page="todo"; $userid="user_id"; include '../includes/sidebar.php'?>

<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="to-do.php" title="You're right here"  class="tip-bottom current" class="current"><i class="icon-list"></i> To-Do</a></div>
  </div>
<!--End-breadcrumbs-->

<!--Action boxes-->
  <div class="container-fluid">
    
<!--End-Action boxes-->    

    <div class="row-fluid">
	  
    <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-pencil"></i> </span>
            <h5>My Personal To-Do Lists</h5>
          </div>
          <div class="widget-content nopadding">
            <form id="form-wizard" class="form-horizontal" action="add-to-do.php" method="POST">
              <div id="form-wizard-1" class="step">

              <div class="control-group">
                <label class="control-label">Please Enter Your Task :</label>
                <div class="controls">
                    <input type="text" class="span11" name="task_desc" placeholder="I'll be doing 12 set up and . . ." />
                </div>
                </div>

                 <div class="control-group">
                    <label class="control-label">Please Select a Status:</label>
                    <div class="controls">
                        <select name="task_status" required="required" id="select">
                        <option value="In Progress">In Progress</option>
                        <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>

              <div class="form-actions">
              <input type="hidden" name="userid" value="<?php echo $userid; ?>">
                <input id="add" class="btn btn-primary" type="submit" value="Add To List" />
                <div id="status"></div>
              </div>
              <div id="submitted"></div>
            </form>
          </div><!--end of widget-content -->
          <!-- Upcoming Sessions -->
          <h4>Your Upcoming Sessions</h4>
                    <?php
                    include('dbcon.php');
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
                    <div class="form-actions">
  <a href="trainer-sessions.php" class="btn btn-primary" style="text-decoration: none; color: white;">
    Sessions & Schedules
  </a>
</div>

        </div><!--end of widget box-->
      </div><!--end of span 12 -->
	  
	  
	  
    </div><!-- End of row-fluid -->
  </div><!-- End of container-fluid -->
</div><!-- End of content-ID -->
</div>
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
