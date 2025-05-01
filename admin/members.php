<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if(!isset($_SESSION['user_id'])){
header('location:../index.php');	
}



?>
 <?php
include "dbcon.php";

// Initialize search variable
$search = '';
$where = '';

// Check if search parameter exists
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $where = " WHERE fullname LIKE '%$search%' 
               OR username LIKE '%$search%' 
               OR contact LIKE '%$search%' 
               OR address LIKE '%$search%' 
               OR services LIKE '%$search%'";
}

$qry = "SELECT * FROM members $where ORDER BY dor DESC";
$cnt = 1;
$result = mysqli_query($con, $qry);
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
<link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
<link href="../font-awesome/css/all.css" rel="stylesheet" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>
<!--close-Header-part--> 

 
<!--top-Header-menu-->
<?php include 'includes/topheader.php'?>
<!--close-top-Header-menu-->
<!--start-top-serch-->
<!-- <div id="search">
  <input type="hidden" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div> -->
<!--close-top-serch-->

<!--sidebar-menu-->
<?php $page="members"; include 'includes/sidebar.php'?>
<!--sidebar-menu-->

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="#" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Registered Members</a> </div>
    <h1 class="text-center">Registered Members List <i class="fas fa-group"></i></h1>
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span12">

      <div class='widget-box'>
    <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
        <h5>Member table</h5>
    </div>
    <div class='widget-content nopadding'>
        <!-- Search Form -->
        <div class="controls" style="padding: 10px;">
            <form method="GET" action="" class="form-inline">
                <div class="input-append">
                    <input type="text" name="search" class="span11" placeholder="Search members..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                    <?php if(isset($_GET['search'])): ?>
                        <a href="members.php" class="btn btn-danger"><i class="fas fa-times"></i> Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Search Results Count -->
        <div class="alert alert-info" style="margin: 10px;">
            <?php 
            $total_members = mysqli_num_rows($result);
            if(isset($_GET['search']) && !empty($_GET['search'])) {
                echo "Showing $total_members members matching '".htmlspecialchars($_GET['search'])."'";
            } else {
                echo "Showing all $total_members members";
            }
            ?>
        </div>
        
        <!-- Members Table -->
        <table class='table table-bordered table-hover'>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fullname</th>
                    <th>Username</th>
                    <th>Gender</th>
                    <th>Contact Number</th>
                    <th>D.O.R</th>
                    <th>Address</th>
                    <th>Amount</th>
                    <th>Choosen Service</th>
                    <th>Plan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_array($result)) {
                        echo "<tr>
                            <td><div class='text-center'>".$cnt."</div></td>
                            <td><div class='text-center'>".htmlspecialchars($row['fullname'])."</div></td>
                            <td><div class='text-center'>@".htmlspecialchars($row['username'])."</div></td>
                            <td><div class='text-center'>".htmlspecialchars($row['gender'])."</div></td>
                            <td><div class='text-center'>".htmlspecialchars($row['contact'])."</div></td>
                            <td><div class='text-center'>".htmlspecialchars($row['dor'])."</div></td>
                            <td><div class='text-center'>".htmlspecialchars($row['address'])."</div></td>
                            <td><div class='text-center'>$".htmlspecialchars($row['amount'])."</div></td>
                            <td><div class='text-center'>".htmlspecialchars($row['services'])."</div></td>
                            <td><div class='text-center'>".htmlspecialchars($row['plan'])." Month/s</div></td>
                        </tr>";
                        $cnt++;
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No members found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
	
      </div>
    </div>
  </div>
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
