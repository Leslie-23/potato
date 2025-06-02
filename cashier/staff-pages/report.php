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

// Debug: Show the date range being used
echo "<!-- Date Range: $start_date to $end_date -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Advanced Financial Analytics - Gym System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../font-awesome/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
</head>
<body>

<!-- Header and navigation includes -->
<div id="header">
  <h1><a href="dashboard.php">Gym Financial System</a></h1>
</div>

<?php $page="report"; include '../includes/header.php'?>
<?php $page="report"; include '../includes/sidebar.php'?>

<div id="content">
    <div id="content-header">
        <div class="container-fluid">
            <h1 class="text-center"><i class="icon-bar-chart"></i> Advanced Financial Analytics</h1>
            <div class="row-fluid report-controls">
                <form method="get" class="form-horizontal">
                    <div class="span4">
                        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" 
                               class="span12" max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="span4">
                        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" 
                               class="span12" max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="span4">
                        <button type="submit" class="btn btn-primary span6"><i class="icon-filter"></i> Apply Filters</button>
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
                    </div>
                    <div class="widget-content">
                        <?php
                        // Corrected financial query for your database
                        $finance_query = "SELECT 
                            COALESCE(SUM(CASE WHEN status = 'approved' THEN amount ELSE 0 END), 0) AS revenue,
                            COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) AS pending,
                            COUNT(DISTINCT CASE WHEN status = 'approved' THEN user_id END) AS active_members,
                            COALESCE((SELECT SUM(repair_cost) FROM equipment_repairs 
                             WHERE repair_date BETWEEN '$start_date' AND '$end_date'), 0) AS maintenance_cost,
                            COALESCE((SELECT COUNT(*) FROM members 
                             WHERE dor BETWEEN '$start_date' AND '$end_date'), 0) AS new_members,
                            COALESCE((SELECT COUNT(*) FROM members 
                             WHERE status = 'Active' 
                             AND paid_date IS NOT NULL
                             AND DATE_ADD(paid_date, INTERVAL CAST(plan AS UNSIGNED) MONTH) 
                             BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)), 0) AS expiring_soon
                            FROM transactions
                            WHERE payment_date BETWEEN '$start_date' AND '$end_date'";
                        
                        $finance_result = mysqli_query($con, $finance_query);
                        $finance_data = $finance_result ? mysqli_fetch_assoc($finance_result) : [
                            'revenue' => 0,
                            'pending' => 0,
                            'active_members' => 0,
                            'maintenance_cost' => 0,
                            'new_members' => 0,
                            'expiring_soon' => 0
                        ];
                        
                        echo "<!-- Finance Query Results: " . print_r($finance_data, true) . " -->";
                        ?>
                        <div class="row-fluid  financial-kpis" >
                            <div class="span2">
                                <div class="kpi-card bg-success" >
                                    <h3>$<?= number_format((float)$finance_data['revenue'], 0) ?></h3>
                                    <small style="background-color: green; padding: 5px; color: white">Total Revenue</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card warning">
                                    <h3>$<?= number_format((float)$finance_data['pending'], 0) ?></h3>
                                    <small  style="background-color: yellow; padding: 5px; color: grey">Pending Payments</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card important">
                                    <h3>$<?= number_format((float)$finance_data['maintenance_cost'], 0) ?></h3>
                                    <small  style="background-color: blue; padding: 5px; color: white">Equipment Costs</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card info">
                                    <h3><?= (int)$finance_data['new_members'] ?></h3>
                                    <small  style="background-color: green; padding: 5px; color: white">New Members</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card inverse">
                                    <h3><?= (int)$finance_data['active_members'] ?></h3>
                                    <small  style="background-color: bisque; padding: 5px; color: black">Active Members</small>
                                </div>
                            </div>
                            <div class="span2">
                                <div class="kpi-card danger">
                                    <h3><?= (int)$finance_data['expiring_soon'] ?></h3>
                                    <small  style="background-color: red; padding: 5px; color: white">Expiring Soon</small>
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
                        // Corrected equipment query for your database
                        $equipment_query = "SELECT 
                            e.name,
                            e.amount AS initial_cost,
                            COUNT(r.id) AS repairs,
                            COALESCE(SUM(r.repair_cost), 0) AS total_repair_cost,
                            (SELECT COUNT(*) FROM training_sessions ts
                             JOIN members m ON ts.user_id = m.user_id
                             WHERE m.services = 'Fitness'
                             AND ts.session_date BETWEEN '$start_date' AND '$end_date') AS usage_count,
                            CASE 
                                WHEN e.amount > 0 THEN (COALESCE(SUM(r.repair_cost), 0) / e.amount) * 100 
                                ELSE 0 
                            END AS repair_vs_value
                            FROM equipment e
                            LEFT JOIN equipment_repairs r ON e.id = r.equipment_id
                                AND r.repair_date BETWEEN '$start_date' AND '$end_date'
                            GROUP BY e.id
                            ORDER BY total_repair_cost DESC";
                        
                        $equipment_result = mysqli_query($con, $equipment_query);
                        echo "<!-- Equipment Query: $equipment_query -->";
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
                                <?php 
                                if($equipment_result && mysqli_num_rows($equipment_result) > 0):
                                    while($row = mysqli_fetch_assoc($equipment_result)): 
                                        $roi_score = isset($row['usage_count']) && $row['usage_count'] > 0 ? 
                                            ($row['initial_cost'] / $row['usage_count']) + $row['total_repair_cost'] : 0;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td>$<?= number_format((float)$row['initial_cost'], 0) ?></td>
                                    <td><?= (int)$row['repairs'] ?></td>
                                    <td class="<?= $row['total_repair_cost'] > 500 ? 'text-error' : '' ?>">
                                        $<?= number_format((float)$row['total_repair_cost'], 0) ?>
                                    </td>
                                    <td><?= number_format((int)$row['usage_count']) ?></td>
                                    <td>
                                        <div class="progress progress-danger">
                                            <div class="bar" style="width: <?= min(100, (float)$row['repair_vs_value']) ?>%"></div>
                                        </div>
                                        <?= number_format((float)$row['repair_vs_value'], 1) ?>%
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $roi_score < 50 ? 'success' : 
                                            ($roi_score < 100 ? 'warning' : 'important') ?>">
                                            <?= number_format((float)$roi_score, 1) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No equipment data found for selected period</td>
                                </tr>
                                <?php endif; ?>
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
                        // Corrected membership query for your database
                        $membership_query = "SELECT 
                            m.plan,
                            COUNT(*) AS total_members,
                            COALESCE(SUM(t.amount), 0) AS revenue,
                            COALESCE(AVG(t.amount), 0) AS avg_revenue,
                            COALESCE(AVG(p.payment_count), 0) AS avg_payments,
                            COALESCE(AVG(r.retention_days), 0) AS avg_retention_days
                        FROM members m
                        LEFT JOIN (
                            SELECT user_id, COUNT(*) AS payment_count, SUM(amount) AS amount
                            FROM transactions
                            WHERE status = 'approved'
                            AND payment_date BETWEEN '$start_date' AND '$end_date'
                            GROUP BY user_id
                        ) t ON m.user_id = t.user_id
                        LEFT JOIN (
                            SELECT user_id, COUNT(*) AS payment_count
                            FROM transactions
                            WHERE status = 'approved'
                            GROUP BY user_id
                        ) p ON m.user_id = p.user_id
                        LEFT JOIN (
                            SELECT user_id, DATEDIFF(MAX(payment_date), MIN(payment_date)) AS retention_days
                            FROM transactions
                            WHERE status = 'approved'
                            GROUP BY user_id
                        ) r ON m.user_id = r.user_id
                        WHERE m.status = 'Active'
                        GROUP BY m.plan
                        ORDER BY revenue DESC";
                        
                        $membership_result = mysqli_query($con, $membership_query);
                        echo "<!-- Membership Query: $membership_query -->";
                        ?>
                        <div class="row-fluid">
                            <?php 
                            if($membership_result && mysqli_num_rows($membership_result) > 0):
                                while($row = mysqli_fetch_assoc($membership_result)): 
                                    $clv = $row['avg_revenue'] * ($row['avg_retention_days'] / 30);
                            ?>
                            <div class="span4">
                                <div class="membership-card">
                                    <div class="header"><?= htmlspecialchars($row['plan']) ?>-Month Plan</div>
                                    <div class="content">
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <h4>$<?= number_format((float)$row['revenue'], 0) ?></h4>
                                                <small>Total Revenue</small>
                                            </div>
                                            <div class="span6">
                                                <h4><?= number_format((float)$clv, 0) ?></h4>
                                                <small>Customer Lifetime Value</small>
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <div class="mini-chart">
                                                    <span class="sparkline">5,3,7,8,4,5,9,6,3,8></span>
                                                    <small>Revenue Trend</small>
                                                </div>
                                            </div>
                                            <div class="span6">
                                                <div class="progress vertical">
                                                    <div class="bar" style="height: <?= min(100, ($row['avg_payments']/10)*100) ?>%"></div>
                                                </div>
                                                <small>Payment Frequency</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                endwhile;
                            else: ?>
                            <div class="span12">
                                <p class="text-center">No membership data found for selected period</p>
                            </div>
                            <?php endif; ?>
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
                        // Corrected payment method query for your database
                        $payment_query = "SELECT 
                            payment_method,
                            COUNT(*) AS transactions,
                            COALESCE(SUM(amount), 0) AS total,
                            (COUNT(*)/(SELECT COUNT(*) FROM transactions 
                             WHERE payment_date BETWEEN '$start_date' AND '$end_date'
                             AND status = 'approved'))*100 AS percentage
                        FROM transactions
                        WHERE payment_date BETWEEN '$start_date' AND '$end_date'
                        AND status = 'approved'
                        GROUP BY payment_method";
                        
                        $payment_result = mysqli_query($con, $payment_query);
                        echo "<!-- Payment Method Query: $payment_query -->";
                        ?>
                        <table class="table table-hover">
                            <?php 
                            if($payment_result && mysqli_num_rows($payment_result) > 0):
                                while($row = mysqli_fetch_assoc($payment_result)): ?>
                            <tr>
                                <td width="30%"><?= ucfirst(htmlspecialchars($row['payment_method'])) ?></td>
                                <td>
                                    <div class="progress progress-striped">
                                        <div class="bar" style="width: <?= min(100, (float)$row['percentage']) ?>%"></div>
                                    </div>
                                </td>
                                <td width="25%">
                                    <?= number_format((float)$row['percentage'], 1) ?>%<br>
                                    <small>$<?= number_format((float)$row['total'], 0) ?></small>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No payment data found for selected period</td>
                            </tr>
                            <?php endif; ?>
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
                        // Corrected maintenance alerts query for your database
                        $alert_query = "SELECT 
                            e.name,
                            e.amount,
                            COUNT(r.id) AS repairs,
                            COALESCE(SUM(r.repair_cost), 0) AS total_cost,
                            DATEDIFF(NOW(), COALESCE(MAX(r.repair_date), NOW())) AS days_since_last_repair
                        FROM equipment e
                        LEFT JOIN equipment_repairs r ON e.id = r.equipment_id
                        GROUP BY e.id
                        HAVING total_cost > (e.amount * 0.3) OR repairs > 3
                        ORDER BY total_cost DESC";
                        
                        $alert_result = mysqli_query($con, $alert_query);
                        echo "<!-- Maintenance Alerts Query: $alert_query -->";
                        ?>
                        <ul class="recent-alerts">
                            <?php 
                            if($alert_result && mysqli_num_rows($alert_result) > 0):
                                while($row = mysqli_fetch_assoc($alert_result)): ?>
                            <li class="alert-item">
                                <div class="alert-icon">
                                    <i class="icon-bell"></i>
                                </div>
                                <div class="alert-content">
                                    <strong><?= htmlspecialchars($row['name']) ?></strong><br>
                                    <span class="text-error">$<?= number_format((float)$row['total_cost'], 0) ?> spent</span> | 
                                    <?= (int)$row['repairs'] ?> repairs | 
                                    Last repair <?= (int)$row['days_since_last_repair'] ?> days ago
                                </div>
                            </li>
                            <?php 
                                endwhile;
                            else: ?>
                            <li class="alert-item">
                                <div class="alert-content">
                                    <p>No maintenance alerts found</p>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title">
                        <h5>Member Plans</h5>
                    </div>
                    <div class="widget-content">
                        <?php
                        // Simple plan distribution
                        $plan_query = "SELECT plan, COUNT(*) AS count 
                                      FROM members 
                                      WHERE status='Active'
                                      GROUP BY plan";
                        $plan_result = mysqli_query($con, $plan_query);
                        ?>
                        <table class="table table-bordered">
                            <tr>
                                <th>Plan (Months)</th>
                                <th>Members</th>
                            </tr>
                            <?php while($row = mysqli_fetch_assoc($plan_result)): ?>
                            <tr>
                                <td><?= $row['plan'] ?></td>
                                <td><?= $row['count'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Your existing CSS styles */
.kpi-card {
    padding: 15px;
    border-radius: 4px;
    text-align: center;
    margin-bottom: 10px;
}
/* ... other styles ... */
</style>

<!-- <script>
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
-->
<script> 
$(document).ready(function() {
    // Initialize all sparkline charts
    $('.sparkline').each(function(){
        var values = $(this).text().split(',').map(Number);
        $(this).sparkline(values, {
            type: 'bar',
            height: '40px',
            barColor: '#4CAF50',
            barWidth: 5,
            barSpacing: 2
        });
    });
});
</script>
</script>
</body>
</html>