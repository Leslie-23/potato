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
<link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
<?php $page="announcement"; include '../includes/sidebar.php'?>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
 <a href="announcement.php" title="You're right here" class="tip-bottom current" class="current"><i class="icon-bullhorn"></i> Announcements</a></div>
  </div>
<!--End-breadcrumbs-->

<!--Action boxes-->
  <div class="container-fluid">
    
<!--End-Action boxes-->    

    <div class="row-fluid">
	  
    <div class="span12">
    <div class="widget-box">
          <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="icon-chevron-down"></i></span>
          <h5>Gym Announcements</h5>
                    </div>
                    <div class="widget-content nopadding collapse in" id="collapseAnnouncements">
                        <ul class="recent-posts">
                            <li>
                            <?php
              include "dbcon.php";
              // Query to get announcements with sender information
              $qry = "SELECT a.*, 
                             COALESCE(s.designation, ad.role) AS sender_role,
                             COALESCE(s.fullname, ad.name) AS sender_name
                      FROM announcements a
                      LEFT JOIN staffs s ON a.user_id = s.user_id
                      LEFT JOIN admin ad ON a.user_id = ad.user_id
                      ORDER BY a.date "; // Show only 5 latest announcements
              
              $result = mysqli_query($con, $qry);
              
              if (!$result) {
                  die("Database query failed: " . mysqli_error($con));
              }
              
              if (mysqli_num_rows($result) > 0) {
                  while($row = mysqli_fetch_assoc($result)) {
                      echo "<div class='article-post'>"; 
                      
                      // Get sender info safely
                      $senderName = htmlspecialchars($row['sender_name'] ?? 'System Administrator', ENT_QUOTES, 'UTF-8');
                      $senderRole = htmlspecialchars($row['sender_role'] ?? 'Administrator', ENT_QUOTES, 'UTF-8');
                      $date = htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8');
                      $message = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
                      
                      // Determine icon based on role
                      $icon = 'icon-user'; // default icon
                      switch(strtolower($senderRole)) {
                          case 'admin':
                              $icon = 'icon-star';
                              break;
                          case 'trainer':
                              $icon = 'icon-trophy';
                              break;
                          case 'cashier':
                              $icon = 'icon-shopping-cart';
                              break;
                          case 'gym assistant':
                              $icon = 'icon-heart';
                              break;
                          case 'manager':
                              $icon = 'icon-briefcase';
                              break;
                      }
                      
                      // Display announcement with role-specific icon
                      echo "<div class='user-info'>";
                      echo "<i class='$icon'></i> "; // Role icon
                      echo "By: $senderName ($senderRole) / Date: $date";
                      echo "</div>";
                      echo "<p>$message</p>";
                      echo "</div>";
                  }
              } else {
                  echo "<p>No announcements found.</p>";
              }
              
              mysqli_close($con);
            ?>       <?php
            include "dbcon.php";
            // Query to get announcements with sender information
            $qry = "SELECT a.*, 
                           COALESCE(s.designation, ad.role) AS sender_role,
                           COALESCE(s.fullname, ad.name) AS sender_name
                    FROM announcements a
                    LEFT JOIN staffs s ON a.user_id = s.user_id
                    LEFT JOIN admin ad ON a.user_id = ad.user_id
                    ORDER BY a.date 
                    "; // Show only 5 latest announcements
            
            $result = mysqli_query($con, $qry);
            
            if (!$result) {
                die("Database query failed: " . mysqli_error($con));
            }
            
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='article-post'>"; 
                    
                    // Get sender info safely
                    $senderName = htmlspecialchars($row['sender_name'] ?? 'System Administrator', ENT_QUOTES, 'UTF-8');
                    $senderRole = htmlspecialchars($row['sender_role'] ?? 'Administrator', ENT_QUOTES, 'UTF-8');
                    $date = htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8');
                    $message = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
                    
                    // Determine icon based on role
                    $icon = 'icon-user'; // default icon
                    switch(strtolower($senderRole)) {
                        case 'admin':
                            $icon = 'icon-star';
                            break;
                        case 'trainer':
                            $icon = 'icon-trophy';
                            break;
                        case 'cashier':
                            $icon = 'icon-shopping-cart';
                            break;
                        case 'gym assistant':
                            $icon = 'icon-heart';
                            break;
                        case 'manager':
                            $icon = 'icon-briefcase';
                            break;
                    }
                    
                    // Display announcement with role-specific icon
                    // echo "<div class='user-info'>";
                    // echo "<i class='$icon'></i> "; // Role icon
                    // echo "By: $senderName ($senderRole) / Date: $date";
                    // echo "</div>";
                    // echo "<p>$message</p>";
                    // echo "</div>";
                }
            } else {
                echo "<p>No announcements found.</p>";
            }
            
            mysqli_close($con);
          ?>                                 
                                <a href="index.php">
                                    <button class="btn btn-success btn-mini">Back to Dashboard</button>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div> 
        </div>
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
