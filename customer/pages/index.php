<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- CSS Links -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/fullcalendar.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="../font-awesome/css/all.min.css" /> <!-- Updated to use all.min.css for latest icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
</head>
<body>

<!-- Header Section -->
<div id="header">
    <h1><a href="index.php">Perfect Gym System</a></h1>
</div>
<!-- End Header Section --> 

<!-- Top Navigation Menu -->
<?php include '../includes/topheader.php'?>
<!-- End Top Navigation Menu -->

<!-- Sidebar Menu -->
<?php $page="dashboard"; include '../includes/sidebar.php'?>
<!-- End Sidebar Menu -->

<!-- Main Content Section -->
<div id="content">
    <!-- Breadcrumbs -->
    <div id="content-header">
        <div id="breadcrumb"> 
            <a href="index.php" title="You're right here" class="tip-bottom">
                <i class="fas fa-home"></i> Home
            </a>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Quick Action Boxes -->
    <div class="container-fluid">
        <div class="quick-actions_homepage">
            <ul class="quick-actions">
                <li class="bg_ls span"> 
                    <a href="workout-me.php" style="font-size: 16px;"> 
                        <i class="fas fa-dumbbell"></i> Workouts 
                    </a> 
                </li>
                <li class="bg_lg span2"> 
                    <a href="payment.php" style="font-size: 16px;"> 
                        <i class="fas fa-dollar-sign"></i> Payments 
                    </a> 
                </li>
                <li class="bg_lb span2"> 
                    <a href="announcement.php" style="font-size: 16px;"> 
                        <i class="fas fa-bullhorn"></i> Announcements 
                    </a> 
                </li>
            </ul>
        </div>
    <!-- End Quick Action Boxes -->    

        <!-- Dashboard Content Row -->
        <div class="row-fluid">
            <!-- To-Do List Section -->
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title"> 
                        <span class="icon"><i class="fas fa-tasks"></i></span>
                        <h5>My To-Do List</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <?php
                            include "dbcon.php";
                            include "session.php";
                            $qry = "SELECT * FROM todo WHERE user_id='".$_SESSION['user_id']."'";
                            $result = mysqli_query($con, $qry);

                            echo "<table class='table table-striped table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>";
                            
                            while($row = mysqli_fetch_array($result)) {
                                echo "<tbody>
                                    <tr>
                                        <td class='taskDesc'>
                                            <a href='to-do.php'><i class='fas fa-plus-circle'></i></a> ".$row['task_desc']."
                                        </td>
                                        <td class='taskStatus'>
                                            <span class='in-progress'>".$row['task_status']."</span>
                                        </td>
                                        <td class='taskOptions'>
                                            <a href='update-todo.php?id=".$row['id']."' class='tip-top' data-original-title='Update'>
                                                <i class='fas fa-edit'></i>
                                            </a>  
                                            <a href='actions/remove-todo.php?id=".$row['id']."' class='tip-top' data-original-title='Done'>
                                                <i class='fas fa-check'></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>";
                            }
                        ?>
                        </table>
                    </div>
                </div>
            </div> 
            <!-- End To-Do List Section -->
            
            <!-- Announcements Section -->
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2">
                        <span class="icon"><i class="fas fa-chevron-down"></i></span>
                        <h5>Gym Announcement</h5>
                    </div>
                    <div class="widget-content nopadding collapse in" id="collapseG2">
                        <ul class="recent-posts">
                            <li>
                                <?php
                                    include "dbcon.php";
                                    $qry = "SELECT * FROM announcements";
                                    $result = mysqli_query($con, $qry);
                                    
                                    while($row = mysqli_fetch_array($result)) {
                                        echo "<div class='user-thumb'> 
                                            <img width='70' height='40' alt='User' src='../img/demo/av1.jpg'> 
                                        </div>";
                                        echo "<div class='article-post'>"; 
                                        echo "<span class='user-info'> By: System Administrator / Date: ".$row['date']." </span>";
                                        echo "<p><a href='#'>".$row['message']."</a></p>";
                                    }
                                ?>
                                <a href="announcement.php">
                                    <button class="btn btn-warning btn-mini">View All</button>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div> 
            <!-- End Announcements Section -->
        </div>
        <!-- End Dashboard Content Row -->
    </div>
    <!-- End Container Fluid -->
</div>
<!-- End Main Content Section -->

<!-- Footer Section -->
<div class="row-fluid">
    <div id="footer" class="span12"> 
        <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi
    </div>
</div>
<!-- End Footer Section -->

<!-- Custom Styles -->
<style>
    #footer {
        color: white;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        max-width: 460px;
        margin: auto;
        text-align: center;
        font-family: arial;
    }

    .title {
        color: grey;
        font-size: 18px;
    }
    
    /* Added for better icon visibility */
    .taskOptions a {
        margin: 0 5px;
        color: #555;
    }
    
    .taskOptions a:hover {
        color: #333;
    }
</style>

<!-- JavaScript Files -->
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

<!-- Page Navigation Script -->
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