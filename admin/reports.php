<?php
session_start();
// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');
    exit(); // Always exit after header redirect
}

include "dbcon.php";

// Get gender distribution data
$qry = "SELECT gender, count(*) as number FROM members GROUP BY gender";
$result = mysqli_query($con, $qry);
if(!$result) {
    die("Database query failed: " . mysqli_error($con));
}

// Get earnings data
$query1 = "SELECT SUM(amount) as total_earnings FROM members";
$rezz = mysqli_query($con, $query1);
if(!$rezz) {
    die("Database query failed: " . mysqli_error($con));
}
$earnings_data = mysqli_fetch_assoc($rezz);

// Get expenses data
$query10 = "SELECT SUM(amount) as total_expenses FROM equipment";
$res1000 = mysqli_query($con, $query10);
if(!$res1000) {
    die("Database query failed: " . mysqli_error($con));
}
$expenses_data = mysqli_fetch_assoc($res1000);

// Get services data
$services_query = "SELECT services, count(*) as number FROM members GROUP BY services";
$services_res = mysqli_query($con, $services_query);
if(!$services_res) {
    die("Database query failed: " . mysqli_error($con));
}
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
    <link href="../font-awesome/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
    <!-- Gender Pie Chart Script -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Gender', 'Number'],
                <?php
                while($row = mysqli_fetch_assoc($result)) {
                    echo "['".htmlspecialchars($row["gender"], ENT_QUOTES)."', ".intval($row["number"])."],";
                }
                ?>
            ]);
            
            var options = {
                title: 'Percentage of Male and Female GYM Members',
                pieHole: 0.0,
                backgroundColor: 'transparent'
            };
            
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>

    <!-- Earnings vs Expenses Chart Script -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawEarningsChart);
        
        function drawEarningsChart() {
            var data = google.visualization.arrayToDataTable([
                ['Terms', 'Amount'],
                ['Earnings', <?php echo floatval($earnings_data['total_earnings']); ?>],
                ['Expenses', <?php echo floatval($expenses_data['total_expenses']); ?>]
            ]);
            
            var options = {
                width: 1050,
                legend: { position: 'none' },
                bars: 'horizontal',
                axes: {
                    x: {
                        0: { side: 'top', label: 'Total'}
                    }
                },
                bar: { groupWidth: "100%" },
                backgroundColor: 'transparent'
            };
            
            var chart = new google.charts.Bar(document.getElementById('top_y_div'));
            chart.draw(data, options);
        }
    </script>

    <!-- Services Chart Script -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawServicesChart);
        
        function drawServicesChart() {
            var data = google.visualization.arrayToDataTable([
                ['Services', 'Total Numbers'],
                <?php
                while($service = mysqli_fetch_assoc($services_res)) {
                    echo "['".htmlspecialchars($service['services'], ENT_QUOTES)."', ".intval($service['number'])."],";
                }
                ?>
            ]);
            
            var options = {
                width: 1050,
                legend: { position: 'none' },
                bars: 'horizontal',
                axes: {
                    x: {
                        0: { side: 'top', label: 'Total'}
                    }
                },
                bar: { groupWidth: "100%" },
                backgroundColor: 'transparent'
            };
            
            var chart = new google.charts.Bar(document.getElementById('top_x_div'));
            chart.draw(data, options);
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
<?php $page='chart'; include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> 
            <a href="index.php" title="Go to Home" class="tip-bottom">
                <i class="fas fa-home"></i> Home
            </a> 
            <a href="reports.php" class="current">Chart Representation</a> 
        </div>
        <h1 class="text-center">Earning and Expenses Report <i class="fas fa-chart-bar"></i></h1>
    </div>
    
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div id="top_y_div" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>

    <div id="content-header">
        <h1 class="text-center">Registered Member's Report <i class="fas fa-chart-pie"></i></h1>
    </div>
    
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div id="piechart" style="width: 100%; height: 450px;"></div>
            </div>
        </div>
    </div>

    <div id="content-header">
        <h1 class="text-center">Services Report <i class="fas fa-chart-bar"></i></h1>
    </div>
    
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div id="top_x_div" style="width: 100%; height: 350px;"></div>
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

<style>
    #footer {
        color: white;
    }
    .container-fluid {
        padding: 20px;
    }
    #content-header {
        margin-bottom: 20px;
    }
</style>

<!-- JavaScript Files -->
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>

</body>
</html>