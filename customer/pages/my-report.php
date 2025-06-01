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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
#footer { color: white; }
.payment-card { border-left: 4px solid #4CAF50; margin-bottom: 10px; }
.payment-header { background-color: #f5f5f5; padding: 10px; cursor: pointer; }
.payment-details { padding: 15px; background-color: #fff; border: 1px solid #ddd; }
.badge-approved { background-color: #4CAF50; }
.badge-pending { background-color: #FFC107; color: #000; }
.badge-denied { background-color: #F44336; }
</style>

<style>
    /* Hide elements when printing */
    @media print {
        .no-print, #header, #content-header,.sidebar,#sidebar,#user-nav, #footer, #breadcrumb, #content-header {
            display: none !important;
        }
        
        #content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        
        body {
            background: white !important;
            color: black !important;
            font-size: 12pt !important;
        }
        
        /* Add any other print-specific styles here */
        .report-container {
            page-break-after: avoid;
            page-break-inside: avoid;
        }
    }
    
    /* Optional: Style for print button in screen view */
    .no-print {
        margin-top: 20px;
        padding: 20px 0;
        border-top: 1px solid #eee;
    }
</style>

<style>
  
/* Payment History Styles */
.payment-history-container {
  padding: 10px;
}

.payment-item {
  margin-bottom: 5px;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  overflow: hidden;
}

.payment-header {
  background-color: #f9f9f9;
  padding: 12px 15px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.payment-header:hover {
  background-color: #f0f0f0;
}

.payment-summary {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}

.payment-icon {
  margin-right: 10px;
  color: #5a5a5a;
}

.payment-id {
  font-weight: bold;
  margin-right: 15px;
  min-width: 120px;
}

.payment-amount {
  font-weight: bold;
  color: #4CAF50;
  margin-right: 15px;
  min-width: 80px;
}

.payment-date {
  color: #666;
  margin-right: 15px;
  min-width: 100px;
}

.payment-status {
  padding: 3px 8px;
  border-radius: 3px;
  font-size: 12px;
  margin-right: 15px;
}

.badge-approved { background-color: #4CAF50; color: white; }
.badge-pending { background-color: #FFC107; color: #333; }
.badge-denied { background-color: #F44336; color: white; }

.payment-toggle {
  margin-left: auto;
  transition: transform 0.3s ease;
}

.payment-header[aria-expanded="true"] .payment-toggle i {
  transform: rotate(180deg);
}

.payment-details-content {
  padding: 15px;
  background-color: #fff;
}

.table-condensed th {
  font-weight: 600;
  color: #555;
}
</style>

<script>
$(document).ready(function() {
  // Handle accordion toggle animation
  $('.payment-header').on('click', function() {
    $(this).find('.payment-toggle i').toggleClass('fa-chevron-up fa-chevron-down');
  });
});
</script>
</head>
<body>

<!-- Header and Navigation -->
<div id="header">
  <h1><a href="index.php">Perfect Gym System</a></h1>
</div>
<?php include '../includes/topheader.php'?>
<?php $page="report"; include '../includes/sidebar.php'?>

<?php
include 'dbcon.php';
include "session.php";

// Get member basic info
$member_query = "SELECT * FROM members WHERE user_id='".$_SESSION['user_id']."'";
$member_result = mysqli_query($con, $member_query);
$member = mysqli_fetch_array($member_result);

// Get payment history
$payment_query = "SELECT * FROM transactions WHERE user_id='".$_SESSION['user_id']."' ORDER BY payment_date DESC";
$payment_result = mysqli_query($con, $payment_query);

// Get fitness progress
$fitness_query = "SELECT * FROM members_fitness WHERE user_id='".$_SESSION['user_id']."'";
$fitness_result = mysqli_query($con, $fitness_query);
$fitness = mysqli_fetch_array($fitness_result);

// Calculate membership duration
$join_date = new DateTime($member['dor']);
$current_date = new DateTime();
$membership_duration = $current_date->diff($join_date);
?> 
<?php
// Map status to badge class
$status = strtolower($member['status']);
switch ($status) {
    case 'active':
        $badgeClass = 'success'; // Green
        break;
    case 'pending':
        $badgeClass = 'warning'; // Yellow
        break;
    case 'expired':
        $badgeClass = 'dark'; // Black
        break;
    case 'disabled':
        $badgeClass = 'danger'; // Red
        break;
    default:
        $badgeClass = 'secondary'; // Default grey
        break;
}
?>
<!-- Main Content -->
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> 
      <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
      <a href="my-report.php"  title="You're right here" class="current"><i class="fas fa-file-alt"></i> My Report</a> 
    </div>
  </div>
  
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <h5>Member Report</h5>
          </div>
          
          <div class="widget-content">
            <!-- Gym Header -->
            <div class="row-fluid">
              <div class="span4">
                <table class="">
                  <tbody>
                    <tr><td><h4>Perfect GYM Club</h4></td></tr>                   
                    <tr><td>Name: <?php echo $member['fullname']; ?></td></tr>
                    <tr><td>Address: <?php echo $member['address']; ?></td></tr>                    
                    <tr><td>Phone: <?php echo $member['contact']; ?></td></tr>                    
                    <tr><td>Email: <?php echo $member['email']; ?></td></tr>
                  </tbody>
                </table>
              </div>
              
              <div class="span8">
                <table class="table table-bordered table-invoice-full">
                  <thead>
                    <tr>
                      <th class="head0">Membership ID</th>
                      <th class="head1">Services</th>
                      <th class="head0">Plan</th>
                      <th class="head1">Member Since</th>
                      <th class="head0">Status</th>
                      <th class="head1">Attendance</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>EFG-<?php echo $member['user_id']; ?></td>
                      <td><?php echo $member['services']; ?></td>
                      <td><?php echo $member['plan']; ?> Month/s</td>
                      <td><?php echo $member['dor']; ?></td>
                      <td>  <span class="badge badge-<?php echo $badgeClass; ?>"><?php echo ucfirst($member['status']); ?></span></td>
                      <td><?php echo $member['attendance_count']; ?> Days</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- Fitness Progress -->
            <div class="row-fluid">
              <div class="span6">
                <div class="widget-box">
                  <div class="widget-title">
                    <h5>Fitness Progress</h5>
                  </div>
                  <div class="widget-content">
                    <table class="table table-bordered">
                      <tr>
                        <th  title="Weight upon registration">Initial Weight</th>
                        <td><?php echo $fitness['user_weight'] ?? 'N/A'; ?> kg</td>
                      </tr>
                      <tr>
                        <th  title="Weight after Training">Current Weight</th>
                        <td><?php echo $member['curr_weight'] ?? 'N/A'; ?> kg</td>
                      </tr>
                      <tr>
                        <th>Body Type</th>
                        <td><?php echo $fitness['user_bodytype'] ?? 'N/A'; ?></td>
                      </tr>
                      <tr>
                        <th>Fitness Goals</th>
                        <td>
                          1. <?php echo $fitness['fitness_goal_1'] ?? 'Not set'; ?><br>
                          2. <?php echo $fitness['fitness_goal_2'] ?? 'Not set'; ?><br>
                          3. <?php echo $fitness['fitness_goal_3'] ?? 'Not set'; ?>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
                 
              <!-- Payment Summary -->
              <div class="span6">
                <div class="widget-box">
                  <div class="widget-title">
                    <h5>Payment Summary</h5>
                  </div>
                  <div class="widget-content">
                    <?php
                    $total_paid = 0;
                    $last_payment = null;
                    while($payment = mysqli_fetch_array($payment_result)) {
                        if($payment['status'] == 'approved') {
                            $total_paid += $payment['amount'];
                        }
                        $last_payment = $payment;
                    }
                    ?>
                    <table class="table table-bordered">
                      <tr>
                        <th>Total Paid</th>
                        <td>$<?php echo number_format($total_paid, 2); ?></td>
                      </tr>
                      <tr>
                        <th>Last Payment</th>
                        <td>
                          <?php if($last_payment): ?>
                            $<?php echo number_format($last_payment['amount'], 2); ?> on <?php echo $last_payment['payment_date']; ?>
                            <span class="badge badge-<?php echo strtolower($last_payment['status']); ?>">
                              <?php echo ucfirst($last_payment['status']); ?>
                            </span>
                          <?php else: ?>
                            No payments found
                          <?php endif; ?>
                        </td>
                      </tr>
                      <tr>
                        <th>Membership Duration</th>
                        <td>
                          <?php echo $membership_duration->y; ?> years, 
                          <?php echo $membership_duration->m; ?> months
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- Fitness Progress -->
             
              <?php if(!empty($member['ini_weight']) && !empty($member['curr_weight'])): ?>
                              <div class="profile-section span6">
                                  <h3>Fitness Tracker</h3>
                                  <div class="progress progress-striped active">
                                      <div class="bar" style="width: <?php 
                                          $progress = (($member['curr_weight'] - $member['ini_weight']) / $member['ini_weight']) * 100;
                                          echo abs($progress);
                                      ?>%; background-color: <?php echo $progress > 0 ? '#f0ad4e' : '#5cb85c'; ?>"></div>
                                  </div>
                                  <p>
                                      <strong title="Verified by Trainer">Initial Weight:</strong> <?php echo $member['ini_weight']; ?> kg<br>
                                      <strong>Current Weight:</strong> <?php echo $member['curr_weight']; ?> kg<br>
                                      <strong>Change:</strong> <span style="color: <?php echo $progress > 0 ? '#f0ad4e' : '#5cb85c'; ?>">
                                          <?php echo ($progress > 0 ? '+' : '') . number_format($progress, 1); ?>%
                                      </span>
                                  </p>
                                  <p>
                                      <strong>Body Type:</strong> <?php echo $member['ini_bodytype']; ?> → <?php echo $member['curr_bodytype']; ?>
                                  </p>
                              </div>
                              <?php endif; ?>
<!-- Payment History Section -->
<div class="row-fluid">
  <div class="span12">
    <div class="widget-box">
      <div class="widget-title">
        <h5><i class="fas fa-history"></i> Payment History</h5>
      </div>
      <div class="widget-content nopadding">
        <?php
        mysqli_data_seek($payment_result, 0);
        if(mysqli_num_rows($payment_result) > 0): 
        ?>
        <div class="payment-history-container">
          <?php while($payment = mysqli_fetch_array($payment_result)): ?>
          <div class="payment-item">
            <div class="payment-header" data-toggle="collapse" data-target="#payment-<?php echo $payment['id']; ?>">
              <div class="payment-summary">
                <span class="payment-icon"><i class="fas fa-receipt"></i></span>
                <span class="payment-id">Transaction #<?php echo $payment['id']; ?></span>
                <span class="payment-amount">$<?php echo number_format($payment['amount'], 2); ?></span>
                <span class="payment-date"><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></span>
                <span class="payment-status badge-<?php echo strtolower($payment['status']); ?>">
                  <?php echo ucfirst($payment['status']); ?>
                </span>
                <span class="payment-toggle"><i class="fas fa-chevron-down"></i></span>
              </div>
            </div>
            <div id="payment-<?php echo $payment['id']; ?>" class="collapse payment-details">
              <div class="payment-details-content">
                <table class="table table-condensed">
                  <tr>
                    <th width="30%">Payment Method:</th>
                    <td><?php echo ucwords(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                  </tr>
                  <tr>
                    <th>Processed By:</th>
                    <td>
                      <?php 
                      if($payment['processed_by']) {
                          $staff_query = "SELECT fullname FROM staffs WHERE user_id=".$payment['processed_by'];
                          $staff_result = mysqli_query($con, $staff_query);
                          echo (mysqli_num_rows($staff_result) > 0) ? mysqli_fetch_array($staff_result)['fullname'] : 'System';
                      } else {
                          echo 'Pending processing';
                      }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <th>Processed On:</th>
                    <td><?php echo $payment['processed_date'] ? date('M j, Y H:i', strtotime($payment['processed_date'])) : 'N/A'; ?></td>
                  </tr>
                  <tr>
                    <th>Notes:</th>
                    <td><?php echo !empty($payment['admin_notes']) ? $payment['admin_notes'] : 'No additional notes'; ?></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info" style="margin: 15px;">
          <i class="fas fa-info-circle"></i> No payment history found.
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
            
            <!-- Member Message -->
            <div class="row-fluid">
              <div class="span8">
                <h4>Dear <?php echo $member['fullname']; ?>,</h4>
                <p>Your membership is currently <strong><?php echo $member['status']; ?></strong>.</p>
                <p>Thank you for being a valued member since <?php echo $member['dor']; ?>.</p>
              </div>
              <div class="span4 text-right">
                <h4>Approved By:</h4>
                <img src="../img/report/stamp-sample.png" width="124px;" alt=""><br>
                <small>Auto-generated report</small>
              </div>
              
            </div>
            <div class="form-actions no-print" style="text-align: center; margin-top: 20px;">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="icon-print"></i> Print Report
                </button>
                  <button onclick="window.location.reload()" class="btn" style="margin-left:10px;">
      <i class="icon-refresh"></i> Refresh
  </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<div class="row-fluid">
  <div id="footer" class="span12"><?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi</div>
</div>

<!-- JavaScript -->
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
<script>
$(document).ready(function(){
    // Toggle payment details
    $('.payment-header').click(function(){
        $(this).find('i').toggleClass('fa-credit-card fa-minus');
    });
});
</script>
<script>
<script>
function printReport() {
    // Create print header with user info
    const printHeader = document.createElement('div');
    printHeader.className = 'print-header';
    printHeader.innerHTML = `
        <h2>My Report</h2>
        <p>Generated for: <?php echo htmlspecialchars($user['fullname']); ?> | Date: ${new Date().toLocaleDateString()}</p>
    `;
    
    // Create print footer
    const printFooter = document.createElement('div');
    printFooter.className = 'print-footer';
    printFooter.innerHTML = `
        Page <span class="page-number"></span> | <?php echo date('Y'); ?> <?php echo $site_name; ?>
    `;
    
    // Insert them into the report
    const reportContent = document.getElementById('content').cloneNode(true);
    reportContent.insertBefore(printHeader, reportContent.firstChild);
    reportContent.appendChild(printFooter);
    
    // Open print window
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>My Report - <?php echo htmlspecialchars($user['fullname']); ?></title>
            <style>
                ${document.querySelector('style[media="print"]').innerHTML}
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            ${reportContent.innerHTML}
            <script>
                // Update page numbers
                const pages = document.querySelectorAll('.page-number');
                for (let i = 0; i < pages.length; i++) {
                    pages[i].textContent = (i + 1);
                }
            <\/script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Delay print to allow content to load
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
    
    return false;
}
</script>

</body>
</html>