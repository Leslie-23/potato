<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym Equipment Dashboard</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    

    <style>
        .equipment-card {
            transition: all 0.3s ease;
        }
        .equipment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-good { background-color: #5cb85c; color: white; }
        .status-damaged { background-color: #d9534f; color: white; }
        .status-out_of_order { background-color: #f0ad4e; color: white; }
        .maintenance-progress {
            height: 20px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<!--Header-part-->
<div id="header">
    <h1><a href="dashboard.html">Gym Equipment Manager</a></h1>
</div>
<!--close-Header-part--> 

<!--sidebar-menu-->
<?php $page="equipment"; include '../includes/sidebar.php' ; include '../includes/header.php'?>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.php" title="Dashboard" class="tip-bottom"><i class="icon-home"></i> Equipment Dashboard</a></div>
    </div>

    <div class="container-fluid">
        <!-- Equipment Summary Cards -->
        <div class="row-fluid">
            <div class="span3">
                <div class="widget-box equipment-card">
                    <div class="widget-title bg_lb  "  style="background-color: #22b86c !important;">
                        <span class="icon  "><i class="icon-th "></i></span>
                        <h5>Total Equipment</h5>
                    </div>
                    <div class="widget-content">
                        <div class="text-center">
                            <h2><?php 
                                include "dbcon.php";
                                $qry = "SELECT COUNT(*) as total FROM equipment";
                                $result = mysqli_query($con, $qry);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['total'];
                            ?></h2>
                            <p>Categories in inventory</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="span3">
                <div class="widget-box equipment-card">
                    <div class="widget-title bg_ly"  style="background-color: #22b86c !important;">
                        <span class="icon"><i class="icon-ok"></i></span>
                        <h5>Operational</h5>
                    </div>
                    <div class="widget-content">
                        <div class="text-center">
                            <h2><?php 
                                $qry = "SELECT COUNT(*) as good FROM equipment WHERE status='good'";
                                $result = mysqli_query($con, $qry);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['good'];
                            ?></h2>
                            <p>Fully functional</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="span3">
                <div class="widget-box equipment-card">
                    <div class="widget-title bg_lo"  style="background-color: #fbad3f !important;">
                        <span class="icon"><i class="icon-warning-sign"></i></span>
                        <h5>Needs Repair</h5>
                    </div>
                    <div class="widget-content">
                        <div class="text-center">
                            <h2><?php 
                                $qry = "SELECT COUNT(*) as repair FROM equipment WHERE status='out_of_order' OR status='damaged'";
                                $result = mysqli_query($con, $qry);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['repair'];
                            ?></h2>
                            <p>Requires attention</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="span3">
                <div class="widget-box equipment-card">
                    <div class="widget-title bg_ls" style="background-color: #10aaf8 !important;">
                        <span class="icon"><i class="icon-money"></i></span>
                        <h5>Inventory Value</h5>
                    </div>
                    <div class="widget-content">
                        <div class="text-center">
                            <h2>$<?php 
                                $qry = "SELECT SUM(amount*quantity) as value FROM equipment";
                                $result = mysqli_query($con, $qry);
                                $row = mysqli_fetch_assoc($result);
                                echo number_format($row['value']);
                            ?></h2>
                            <p>Total equipment value</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr/>
        
        <!-- Equipment Status Reports -->
        <div class="row-fluid">
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-tasks"></i></span>
                        <h5>Equipment Status Overview</h5>
                    </div>
                    <div class="widget-content">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $statusQuery = "SELECT status, COUNT(*) as count FROM equipment GROUP BY status";
                                $statusResult = mysqli_query($con, $statusQuery);
                                $totalQuery = "SELECT COUNT(*) as total FROM equipment";
                                $totalResult = mysqli_query($con, $totalQuery);
                                $totalRow = mysqli_fetch_assoc($totalResult);
                                $total = $totalRow['total'];
                                
                                while($row = mysqli_fetch_assoc($statusResult)) {
                                    $percentage = round(($row['count'] / $total) * 100, 1);
                                    $statusClass = '';
                                    if($row['status'] == 'good') $statusClass = 'status-good';
                                    if($row['status'] == 'damaged') $statusClass = 'status-damaged';
                                    if($row['status'] == 'out_of_order') $statusClass = 'status-out_of_order';
                                    
                                    echo "<tr>
                                        <td><span class='status-badge $statusClass'>".ucfirst(str_replace('_', ' ', $row['status']))."</span></td>
                                        <td>{$row['count']}</td>
                                        <td>
                                            <div class='progress progress-striped active'>
                                                <div class='bar' style='width: $percentage%;'></div>
                                            </div>
                                            $percentage%
                                        </td>
                                        <td><a href='equipment.php?filter={$row['status']}' class='btn btn-mini'>View</a></td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-calendar"></i></span>
                        <h5>Maintenance Schedule</h5>
                    </div>
                    <div class="widget-content">
                        <ul class="unstyled">
                            <?php
                            $maintenanceQuery = "SELECT * FROM equipment WHERE status != 'good' ORDER BY date DESC LIMIT 5";
                            $maintenanceResult = mysqli_query($con, $maintenanceQuery);
                            
                            while($row = mysqli_fetch_assoc($maintenanceResult)) {
                                $statusClass = '';
                                if($row['status'] == 'damaged') $statusClass = 'status-damaged';
                                if($row['status'] == 'out_of_order') $statusClass = 'status-out_of_order';
                                
                                echo "<li class='clearfix'>
                                    <div class='pull-left'>
                                        <strong>{$row['name']}</strong><br>
                                        <small>Added: {$row['date']}</small>
                                    </div>
                                    <div class='pull-right'>
                                        <span class='status-badge $statusClass'>".ucfirst(str_replace('_', ' ', $row['status']))."</span><br>
                                        <a href='edit-equipment.php?id={$row['id']}' class='btn btn-mini'>Repair</a>
                                    </div>
                                </li><hr style='margin:5px 0'>";
                            }
                            ?>
                        </ul>
                        <a href="equipment.php" class="btn btn-block">View All Equipment</a>
                    </div>
                </div>
            </div>
            
            <div class="span6">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-th-list"></i></span>
                        <h5>Recently Added Equipment</h5>
                    </div>
                    <div class="widget-content">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Equipment</th>
                                    <th>Qty</th>
                                    <th>Vendor</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recentQuery = "SELECT * FROM equipment ORDER BY date DESC LIMIT 8";
                                $recentResult = mysqli_query($con, $recentQuery);
                                
                                while($row = mysqli_fetch_assoc($recentResult)) {
                                    $statusClass = '';
                                    if($row['status'] == 'good') $statusClass = 'status-good';
                                    if($row['status'] == 'damaged') $statusClass = 'status-damaged';
                                    if($row['status'] == 'out_of_order') $statusClass = 'status-out_of_order';
                                    
                                    echo "<tr>
                                        <td>{$row['name']}</td>
                                        <td>{$row['quantity']}</td>
                                        <td>{$row['vendor']}</td>
                                        <td><span class='status-badge $statusClass'>".ucfirst(str_replace('_', ' ', $row['status']))."</span></td>
                                        <td>
                                            <a href='edit-equipment.php?id={$row['id']}' class='btn btn-mini btn-primary'><i class='icon-edit'></i></a>
                                            <a href='delete-equipment.php?id={$row['id']}' class='btn btn-mini btn-danger' onclick='return confirm(\"Are you sure?\")'><i class='icon-trash'></i></a>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-shopping-cart"></i></span>
                        <h5>Top Vendors</h5>
                    </div>
                    <div class="widget-content">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th>Items Supplied</th>
                                    <th>Amount</th>
                                    <th>Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $vendorQuery = "SELECT vendor, COUNT(*) as items, SUM(amount*quantity) as total_value ,quantity ,amount
                                               FROM equipment 
                                               GROUP BY vendor , quantity ,amount
                                               ORDER BY total_value DESC 
                                               LIMIT 5";
                                $vendorResult = mysqli_query($con, $vendorQuery);
                                
                                while($row = mysqli_fetch_assoc($vendorResult)) {
                                    echo "<tr>
                                        <td>{$row['vendor']}</td>
                                        <td>{$row['quantity']}</td>
                                        <td>{$row['amount']}</td>
                                        <td>$".number_format($row['total_value'])."</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <a href="equipment-entry.php" class="btn btn-success btn-block"><i class="icon-plus"></i> Add New Equipment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Footer-part-->
<div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y");?> &copy; Gym Equipment Management System</div>
</div>

<!-- Scripts -->
<script src="../js/jquery.min.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/matrix.js"></script>

</body>
</html>