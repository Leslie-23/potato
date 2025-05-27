<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('location: ../index.php');
    exit();
}

include './dbcon.php';

// Date handling with validation
$start_date = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-t');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Advanced Financial Analytics - Gym System</title>
    <!-- Include all necessary headers -->
     <meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/matrix-style.css" />
<link rel="stylesheet" href="../css/matrix-media.css" />
<link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="../font-awesome/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Sparklines Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
</head>
<body>

<!-- Header and navigation includes -->

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.php">Gym Financial System</a></h1>
</div>
<!--close-Header-part--> 

<!--top-Header-menu-->
<?php $page="report"; include '../includes/header.php'?>
<!--close-top-Header-menu-->

<!--sidebar-menu-->
<?php $page="report"; include '../includes/sidebar.php'?>
<!--sidebar-menu-->

<div id="content">
    <div id="content-header">
        <div class="container-fluid">
            <h1 class="text-center"><i class="icon-bar-chart"></i> Advanced Financial Analytics</h1>
            <div class="row-fluid report-controls">
                <form method="get" class="form-horizontal">
                    <div class="span4">
                        <input type="date" name="start_date" value="<?= $start_date ?>" 
                               class="span12" max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="span4">
                        <input type="date" name="end_date" value="<?= $end_date ?>" 
                               class="span12" max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="span4">
                        <!-- <button type="submit" class="btn btn-primary span6"><i class="icon-filter"></i> Apply Filters</button> -->
                        <button type="button" onclick="window.print()" class="btn btn-success span6">
                            <i class="icon-print"></i> Print Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Financial Health Dashboard -->
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title bg-blue">
                        <h5><i class="icon-money"></i> Financial Health Overview</h5>
                        <div class="buttons">
                            <a href="#" class="btn btn-mini btn-info" 
                               data-title="Financial Health Metrics" 
                               data-content="Shows key financial indicators for selected period">
                                <i class="icon-question-sign"></i>
                            </a>
                        </div>
                    </div>
                    <div class="widget-content">
                        <?php
                        // Comprehensive financial query
                        $finance_query = "SELECT 
                            SUM(CASE WHEN status = 'approved' THEN amount ELSE 0 END) AS revenue,
                            SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) AS pending,
                            COUNT(DISTINCT CASE WHEN status = 'approved' THEN user_id END) AS active_members,
                            (SELECT SUM(repair_cost) FROM equipment_repairs 
                             WHERE repair_date BETWEEN '$start_date' AND '$end_date') AS maintenance_cost,
                            (SELECT COUNT(*) FROM members 
                             WHERE dor BETWEEN '$start_date' AND '$end_date') AS new_members,
                            (SELECT COUNT(*) FROM members 
                             WHERE DATE_ADD(dor, INTERVAL plan MONTH) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) AS expiring_soon
                            FROM transactions
                            WHERE payment_date BETWEEN '$start_date' AND '$end_date'";
                        
                        $finance_data = mysqli_fetch_assoc(mysqli_query($con, $finance_query));
                        ?>
                        <div class="row-fluid financial-kpis">
                            <div class="span2">
                                <div class="kpi-card success">
                                    <h3>$<?= number_format($finance_data['revenue'], 0) ?></h3>
                                    <small>Total Revenue</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card warning">
                                    <h3>$<?= number_format($finance_data['pending'], 0) ?></h3>
                                    <small>Pending Payments</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card important">
                                    <h3>$<?= number_format($finance_data['maintenance_cost'], 0) ?></h3>
                                    <small>Equipment Costs</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card info">
                                    <h3><?= $finance_data['new_members'] ?></h3>
                                    <small>New Members</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card inverse">
                                    <h3><?= $finance_data['active_members'] ?></h3>
                                    <small>Active Members</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card danger">
                                    <h3><?= $finance_data['expiring_soon'] ?></h3>
                                    <small>Expiring Soon</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipment ROI Analysis -->
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title bg-green">
                        <h5><i class="icon-wrench"></i> Equipment Cost vs Revenue Impact</h5>
                    </div>
                    <div class="widget-content">
                        <?php
                        $equipment_query = "SELECT 
                            e.name,
                            e.amount AS initial_cost,
                            COUNT(r.id) AS repairs,
                            SUM(r.repair_cost) AS total_repair_cost,
                            (SELECT COUNT(*) FROM attendance a 
                             WHERE a.user_id IN (SELECT user_id FROM members WHERE services = 'Fitness')
                             AND a.curr_date BETWEEN '$start_date' AND '$end_date') AS usage_count,
                            (SUM(r.repair_cost) / e.amount) * 100 AS repair_vs_value
                            FROM equipment e
                            LEFT JOIN equipment_repairs r ON e.id = r.equipment_id
                            GROUP BY e.id
                            ORDER BY total_repair_cost DESC";
                        $equipment_result = mysqli_query($con, $equipment_query);
                        ?>
                        <table class="table table-bordered table-striped">
                            <thead class="bg-gray">
                                <tr>
                                    <th>Equipment</th>
                                    <th>Initial Cost</th>
                                    <th>Repairs</th>
                                    <th>Repair Costs</th>
                                    <th>Usage Count</th>
                                    <th>Cost vs Value</th>
                                    <th>ROI Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($equipment_result)): 
                                    $roi_score = $row['usage_count'] > 0 ? 
                                        ($row['initial_cost'] / $row['usage_count']) + $row['total_repair_cost'] : 0;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td>$<?= number_format($row['initial_cost'], 0) ?></td>
                                    <td><?= $row['repairs'] ?></td>
                                    <td class="<?= $row['total_repair_cost'] > 500 ? 'text-error' : '' ?>">
                                        $<?= number_format($row['total_repair_cost'] ?? 0, 0) ?>
                                    </td>
                                    <td><?= number_format($row['usage_count']) ?></td>
                                    <td>
                                        <div class="progress progress-danger">
                                            <div class="bar" style="width: <?= $row['repair_vs_value'] ?>%"></div>
                                        </div>
                                        <?= number_format($row['repair_vs_value']?? 0 , 1) ?>%
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $roi_score < 50 ? 'success' : 
                                            ($roi_score < 100 ? 'warning' : 'important') ?>">
                                            <?= number_format($roi_score ?? 0, 1) ?>
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

        <!-- Membership Profitability Matrix -->
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title bg-purple">
                        <h5><i class="icon-user"></i> Membership Profitability Analysis</h5>
                    </div>
                    <div class="widget-content">
                        <?php
                        $membership_query = "SELECT 
    m.plan,
    COUNT(*) AS total_members,
    SUM(t.amount) AS revenue,
    AVG(t.amount) AS avg_revenue,
    AVG(payment_counts.payments) AS avg_payments,
    AVG(retention_days.days) AS avg_retention_days
FROM members m
JOIN transactions t ON m.user_id = t.user_id
LEFT JOIN (
    SELECT user_id, COUNT(*) AS payments 
    FROM transactions 
    WHERE status = 'approved'
    GROUP BY user_id
) payment_counts ON m.user_id = payment_counts.user_id
LEFT JOIN (
    SELECT user_id, DATEDIFF(MAX(payment_date), MIN(payment_date)) AS days
    FROM transactions 
    GROUP BY user_id
) retention_days ON m.user_id = retention_days.user_id
WHERE t.payment_date BETWEEN '$start_date' AND '$end_date'
GROUP BY m.plan
ORDER BY revenue DESC";
                        $membership_result = mysqli_query($con, $membership_query);
                        ?>
                        <div class="row-fluid">
                            <?php while($row = mysqli_fetch_assoc($membership_result)): 
                                $clv = $row['avg_revenue'] * ($row['avg_retention_days'] / 30);
                            ?>
                            <div class="span4">
                                <div class="membership-card">
                                    <div class="header"><?= $row['plan'] ?>-Month Plan</div>
                                    <div class="content">
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <h4>$<?= number_format($row['revenue']) ?></h4>
                                                <small>Total Revenue</small>
                                            </div>
                                            <div class="span6">
                                                <h4><?= number_format($clv) ?></h4>
                                                <small>Customer Lifetime Value</small>
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <div class="mini-chart">
                                                    <span class="sparkline"><?= implode(',', [5,3,7,8,4,5,9,6,3,8])?></span>
                                                    <small>Revenue Trend</small>
                                                </div>
                                            </div>
                                            <div class="span6">
                                                <div class="progress vertical">
                                                    <div class="bar" style="height: <?= 
                                                        ($row['avg_payments']/10)*100 ?>%"></div>
                                                </div>
                                                <small>Payment Frequency</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Analytics Tiles -->
        <div class="row-fluid">
            <!-- Payment Method Analysis -->
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title bg-orange">
                        <h5><i class="icon-credit-card"></i> Payment Method Trends</h5>
                    </div>
                    <div class="widget-content">
                        <?php
                        $payment_query = "SELECT 
                            payment_method,
                            COUNT(*) AS transactions,
                            SUM(amount) AS total,
                            (COUNT(*)/(SELECT COUNT(*) FROM transactions WHERE payment_date BETWEEN '$start_date' AND '$end_date'))*100 AS percentage
                            FROM transactions
                            WHERE payment_date BETWEEN '$start_date' AND '$end_date'
                            GROUP BY payment_method";
                        $payment_result = mysqli_query($con, $payment_query);
                        ?>
                        <table class="table table-hover">
                            <?php while($row = mysqli_fetch_assoc($payment_result)): ?>
                            <tr>
                                <td width="30%"><?= ucfirst($row['payment_method']) ?></td>
                                <td>
                                    <div class="progress progress-striped">
                                        <div class="bar" style="width: <?= $row['percentage'] ?>%"></div>
                                    </div>
                                </td>
                                <td width="25%">
                                    <?= number_format($row['percentage'], 1) ?>%<br>
                                    <small>$<?= number_format($row['total']) ?></small>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Equipment Maintenance Forecast -->
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title bg-red">
                        <h5><i class="icon-warning-sign"></i> Maintenance Alerts</h5>
                    </div>
                    <div class="widget-content">
                        <?php
                    $alert_result = "SELECT * FROM (
                    SELECT 
                        e.name,
                        e.amount,
                        COUNT(r.id) AS repairs,
                        SUM(r.repair_cost) AS total_cost,
                        DATEDIFF(NOW(), MAX(r.repair_date)) AS days_since_last_repair
                    FROM equipment e
                    LEFT JOIN equipment_repairs r ON e.id = r.equipment_id
                    GROUP BY e.id
                ) AS sub
                WHERE total_cost > (amount * 0.3) OR repairs > 3
                ORDER BY total_cost DESC";



                        ?>
                         <ul class="recent-alerts">
                            <!-- <?php if($row = mysqli_fetch_assoc($alert_result)): ?> -->
                            <li class="alert-item">
                                <div class="alert-icon">
                                    <i class="icon-bell"></i>
                                </div>
                                <div class="alert-content">
                                    <strong><?= $row['name'] ?></strong><br>
                                    <span class="text-error">$<?= number_format($row['total_cost']) ?> spent</span> | 
                                    <?= $row['repairs'] ?> repairs | 
                                    Last repair <?= $row['days_since_last_repair'] ?> days ago
                                </div>
                            </li>
                            <?php elseif($row = !mysqli_fetch_assoc($alert_result)): echo "<p>No alerts found.</p>";  ?>
                                <?php endif; ?>

                        </ul> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced styling */
.kpi-card {
    padding: 15px;
    border-radius: 4px;
    text-align: center;
    margin-bottom: 10px;
}
.kpi-card h3 { margin: 5px 0; }
.success { background: #dff0d8; border: 1px solid #d6e9c6; }
.warning { background: #fcf8e3; border: 1px solid #faebcc; }
.important { background: #f2dede; border: 1px solid #ebccd1; }
.info { background: #d9edf7; border: 1px solid #bce8f1; }
.inverse { background: #333; color: white; }
.danger { background: #f2dede; border: 1px solid #ebccd1; }

.membership-card {
    border: 1px solid #eee;
    margin: 10px;
    border-radius: 4px;
}
.membership-card .header {
    background: #f8f8f8;
    padding: 10px;
    font-weight: bold;
}
.progress.vertical {
    height: 100px;
    width: 20px;
    position: relative;
    margin: 0 auto;
}
</style>

<script>
// Add interactive features
$(document).ready(function(){
    $('[data-title]').popover({
        trigger: 'hover',
        placement: 'top'
    });
    
    $('.sparkline').each(function(){
        const values = $(this).text().split(',').map(Number);
        $(this).sparkline(values, {
            type: 'bar',
            height: '40px',
            barColor: '#4CAF50'
        });
    });
});
</script>
<script>
$(document).ready(function() {
    // Initialize sparkline charts
    $('.sparkline').sparkline('html', {
        type: 'line',  // or 'bar', 'pie', etc.
        width: '100px',
        height: '30px',
        lineColor: '#4CAF50', // green color
        fillColor: '#f8f9fa',
        spotColor: false
    });
});
</script>
</body>
</html>
<?php echo 'End of file reached.'; ?>