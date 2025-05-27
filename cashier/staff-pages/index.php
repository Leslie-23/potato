<?php
session_start();
if(!isset($_SESSION['user_id']) ) {
    header('location:../index.php');    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System - Cashier Dashboard</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/matrix-style.css" />
<link rel="stylesheet" href="../css/matrix-media.css" />
<link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.php">Gym Financial System</a></h1>
</div>
<!--close-Header-part--> 

<!--top-Header-menu-->
<?php $page="dashboard"; include '../includes/header.php'?>
<!--close-top-Header-menu-->

<!--sidebar-menu-->
<?php $page="dashboard"; include '../includes/sidebar.php'?>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" class="tip-bottom"><i class="icon-home"></i> Cashier Dashboard</a></div>
  </div>
<!--End-breadcrumbs-->

<!--Financial Metrics-->
<div class="container-fluid">
    <div class="row-fluid">
        <div class="widget-box widget-plain">
            <div class="center">
                <ul class="stat-boxes2">
                    <li>
                        <div class="left peity_bar_good"><span>+12%</span></div>
                        <div class="right"> 
                            <strong><?php
include "dbcon.php";
$query = "SELECT SUM(amount) AS total 
          FROM transactions 
          WHERE DATE(payment_date) = CURDATE() 
          AND status = 'approved'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
echo number_format($row['total'] ?? 0, 2);
?></strong> 
                            Today's Payments
                        </div>
                    </li>
                    <li>
                        <div class="left peity_bar_neutral"><span>±0%</span></div>
                        <div class="right"> 
                            <strong><?php
include "dbcon.php";
$query = "SELECT COUNT(*) AS count 
          FROM transactions 
          WHERE status = 'pending'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
echo $row['count'] ?? 0;
?></strong> 
                            Pending Transactions
                        </div>
                    </li>
                    <li>
                        <div class="left peity_line_good"><span>+15%</span></div>
                        <div class="right"> 
                            <strong><?php
include "dbcon.php";
$query = "SELECT SUM(amount) AS total 
          FROM transactions 
          WHERE MONTH(payment_date) = MONTH(CURDATE()) 
          AND YEAR(payment_date) = YEAR(CURDATE())
          AND status = 'approved'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
echo number_format($row['total'] ?? 0, 2);
?></strong> 
                            Monthly Revenue
                        </div>
                    </li>
                    <li>
                        <div class="left peity_bar_bad"><span>-5%</span></div>
                        <div class="right"> 
                            <strong><?php
include "dbcon.php";
$query = "SELECT AVG(amount) AS average 
          FROM transactions 
          WHERE status = 'approved'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
echo number_format($row['average'] ?? 0, 2);
?></strong> 
                            Avg. Payment
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="icon-time"></i></span>
                    <h5>Recent Transactions</h5>
                </div>
                <div class="widget-content nopadding">
                    <?php
                    include "dbcon.php";
                    $query = "SELECT t.*, m.fullname 
                              FROM transactions t
                              JOIN members m ON t.user_id = m.user_id
                              ORDER BY t.payment_date DESC 
                              LIMIT 8";
                    $result = mysqli_query($con, $query);
                    ?>
                    
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_array($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td>$<?php echo number_format($row['amount'], 2); ?></td>
                                <td><?php echo date('M j, Y', strtotime($row['payment_date'])); ?></td>
                                <td><?php echo strtoupper($row['payment_method']); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo ($row['status'] == 'approved') ? 'success' : 
                                             (($row['status'] == 'pending') ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Financial Metrics-->

</div>
<!--end-main-container-part-->

<!--Footer-part-->
<div class="row-fluid">
  <div id="footer" class="span12"><?php echo date("Y");?> &copy; Gym Financial System</div>
</div>
<!--end-Footer-part-->

<!-- Scripts -->
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
</body>
</html>