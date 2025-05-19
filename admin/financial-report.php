<?php
session_start();
// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');
    exit();
}

include "../dbcon.php";

// Get payment status distribution
$status_query = "SELECT status, COUNT(*) as count FROM transactions GROUP BY status";
$status_result = mysqli_query($con, $status_query);

// Get monthly earnings data
$monthly_query = "SELECT 
                    DATE_FORMAT(payment_date, '%Y-%m') as month,
                    SUM(amount) as total 
                  FROM transactions 
                  WHERE status = 'approved'
                  GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                  ORDER BY month";
$monthly_result = mysqli_query($con, $monthly_query);

// Get payment method distribution
$method_query = "SELECT payment_method, COUNT(*) as count FROM transactions GROUP BY payment_method";
$method_result = mysqli_query($con, $method_query);

// Get admin processing stats
$admin_query = "SELECT 
                a.username, 
                COUNT(p.log_id) as processed_count,
                SUM(p.amount) as total_processed
              FROM payment_logs p
              JOIN admin a ON p.admin_id = a.user_id
              GROUP BY p.admin_id";
$admin_result = mysqli_query($con, $admin_query);

// Get recent transactions
$recent_query = "SELECT 
                t.id, 
                m.fullname, 
                t.amount, 
                t.payment_method,
                t.payment_date,
                t.status,
                a.username as processed_by
              FROM transactions t
              JOIN members m ON t.user_id = m.user_id
              LEFT JOIN admin a ON t.processed_by = a.user_id
              ORDER BY t.payment_date DESC
              LIMIT 10";
$recent_result = mysqli_query($con, $recent_query);

// Get total approved payments
$total_query = "SELECT SUM(amount) as total FROM transactions WHERE status = 'approved'";
$total_result = mysqli_query($con, $total_query);
$total_data = mysqli_fetch_assoc($total_result);

// Get pending payments
$pending_query = "SELECT SUM(amount) as total FROM transactions WHERE status = 'pending'";
$pending_result = mysqli_query($con, $pending_query);
$pending_data = mysqli_fetch_assoc($pending_result);

// Get denied payments
$denied_query = "SELECT SUM(amount) as total FROM transactions WHERE status = 'denied'";
$denied_result = mysqli_query($con, $denied_query);
$denied_data = mysqli_fetch_assoc($denied_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym System - Financial Reports</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/fullcalendar.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
    <!-- Payment Status Chart -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawStatusChart);
        
        function drawStatusChart() {
            var data = google.visualization.arrayToDataTable([
                ['Status', 'Count'],
                <?php
                while($row = mysqli_fetch_assoc($status_result)) {
                    echo "['".htmlspecialchars($row["status"])."', ".intval($row["count"])."],";
                }
                ?>
            ]);
            
            var options = {
                title: 'Payment Status Distribution',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                colors: ['#5cb85c', '#f0ad4e', '#d9534f'] // Green, Yellow, Red
            };
            
            var chart = new google.visualization.PieChart(document.getElementById('status_chart'));
            chart.draw(data, options);
        }
    </script>

    <!-- Monthly Earnings Chart -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawMonthlyChart);
        
        function drawMonthlyChart() {
            var data = google.visualization.arrayToDataTable([
                ['Month', 'Amount'],
                <?php
                while($row = mysqli_fetch_assoc($monthly_result)) {
                    echo "['".htmlspecialchars($row["month"])."', ".floatval($row["total"])."],";
                }
                ?>
            ]);
            
            var options = {
                chart: {
                    title: 'Monthly Revenue',
                    subtitle: 'Approved payments by month'
                },
                bars: 'vertical',
                height: 400,
                colors: ['#337ab7'],
                backgroundColor: 'transparent'
            };
            
            var chart = new google.charts.Bar(document.getElementById('monthly_chart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>

    <!-- Payment Method Chart -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawMethodChart);
        
        function drawMethodChart() {
            var data = google.visualization.arrayToDataTable([
                ['Method', 'Count'],
                <?php
                while($row = mysqli_fetch_assoc($method_result)) {
                    echo "['".htmlspecialchars($row["payment_method"])."', ".intval($row["count"])."],";
                }
                ?>
            ]);
            
            var options = {
                title: 'Payment Methods Used',
                pieSliceText: 'value',
                backgroundColor: 'transparent',
                is3D: true
            };
            
            var chart = new google.visualization.PieChart(document.getElementById('method_chart'));
            chart.draw(data, options);
        }
    </script>

    <!-- Admin Processing Chart -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawAdminChart);
        
        function drawAdminChart() {
            var data = google.visualization.arrayToDataTable([
                ['Admin', 'Transactions Processed', { role: 'annotation' }, 'Amount Processed', { role: 'annotation' }],
                <?php
                while($row = mysqli_fetch_assoc($admin_result)) {
                    echo "['".htmlspecialchars($row["username"])."', 
                          ".intval($row["processed_count"]).", 
                          '".intval($row["processed_count"])."', 
                          ".floatval($row["total_processed"]).", 
                          '$".number_format($row["total_processed"], 2)."'],";
                }
                ?>
            ]);
            
            var options = {
                chart: {
                    title: 'Admin Processing Stats',
                    subtitle: 'Number of transactions and total amount processed'
                },
                bars: 'vertical',
                height: 400,
                backgroundColor: 'transparent',
                series: {
                    0: {color: '#5bc0de'},
                    1: {color: '#5cb85c'}
                }
            };
            
            var chart = new google.charts.Bar(document.getElementById('admin_chart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
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
<?php $page='financials'; include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> 
            <a href="index.php" title="Go to Home" class="tip-bottom">
                <i class="fas fa-home"></i> Home
            </a> 
            <a href="#" class="current">Financial Reports</a> 
        </div>
        <h1 class="text-center">Financial Dashboard <i class="fas fa-chart-line"></i></h1>
    </div>
    
    <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row-fluid">
            <div class="span4">
                <div class="widget-box">
                    <div class="widget-title bg-success">
                        <span class="icon"><i class="fas fa-check-circle"></i></span>
                        <h5>Approved Payments</h5>
                    </div>
                    <div class="widget-content">
                        <h2>$<?= number_format($total_data['total'], 2) ?></h2>
                        <p>Total revenue collected</p>
                    </div>
                </div>
            </div>
            <div class="span4">
                <div class="widget-box">
                    <div class="widget-title bg-warning">
                        <span class="icon"><i class="fas fa-clock"></i></span>
                        <h5>Pending Payments</h5>
                    </div>
                    <div class="widget-content">
                        <h2>$<?= number_format($pending_data['total'], 2) ?></h2>
                        <p>Awaiting approval</p>
                    </div>
                </div>
            </div>
            <div class="span4">
                <div class="widget-box">
                    <div class="widget-title bg-danger">
                        <span class="icon"><i class="fas fa-times-circle"></i></span>
                        <h5>Denied Payments</h5>
                    </div>
                    <div class="widget-content">
                        <h2>$<?= number_format($denied_data['total'], 2) ?></h2>
                        <p>Rejected transactions</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row 1 -->
        <div class="row-fluid">
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="fas fa-chart-pie"></i></span>
                        <h5>Payment Status Distribution</h5>
                    </div>
                    <div class="widget-content">
                        <div id="status_chart" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                        <h5>Monthly Revenue</h5>
                    </div>
                    <div class="widget-content">
                        <div id="monthly_chart" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row 2 -->
        <div class="row-fluid">
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="fas fa-credit-card"></i></span>
                        <h5>Payment Methods</h5>
                    </div>
                    <div class="widget-content">
                        <div id="method_chart" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="fas fa-user-shield"></i></span>
                        <h5>Admin Processing</h5>
                    </div>
                    <div class="widget-content">
                        <div id="admin_chart" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="fas fa-history"></i></span>
                        <h5>Recent Transactions</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Processed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                                    <td>$<?= number_format($row['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                    <td><?= date('M j, Y', strtotime($row['payment_date'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $row['status'] ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['processed_by'] ?? 'System') ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="row-fluid">
    <div id="footer" class="span12"> 
        <?php echo date("Y"); ?> &copy; Developed By Leslie Paul Ajayi
    </div>
</div>

<!-- JavaScript Files -->
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>

<style>
    .widget-title.bg-success { background: #5cb85c; color: white; }
    .widget-title.bg-warning { background: #f0ad4e; color: white; }
    .widget-title.bg-danger { background: #d9534f; color: white; }
    .widget-box { margin-bottom: 20px; }
    .widget-content h2 { font-size: 28px; margin: 10px 0 5px; }
    .badge-approved { background-color: #5cb85c; }
    .badge-pending { background-color: #f0ad4e; }
    .badge-denied { background-color: #d9534f; }
</style>

</body>
</html>