<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}

include "../dbcon.php";

// Function to sanitize input
function sanitizeInput($data) {
    global $con;
    return mysqli_real_escape_string($con, trim($data));
}

// Get pending payments grouped by user
$pending_group_query = "SELECT m.user_id, m.fullname, m.contact, 
                       COUNT(t.id) as payment_count,
                       SUM(t.amount) as total_amount
                       FROM members m
                       JOIN transactions t ON m.user_id = t.user_id
                       WHERE t.status = 'pending'
                       GROUP BY m.user_id
                       ORDER BY m.fullname ASC";
$pending_group_result = mysqli_query($con, $pending_group_query);

// Function to get individual payments for a user
function getUserPayments($user_id) {
    global $con;
    $query = "SELECT * FROM transactions 
              WHERE user_id = $user_id AND status = 'pending'
              ORDER BY payment_date DESC";
    return mysqli_query($con, $query);
}

// Handle payment status update
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $transaction_id = (int)$_POST['transaction_id'];
    $status = in_array($_POST['status'], ['approved', 'denied']) ? $_POST['status'] : '';
    $admin_notes = sanitizeInput($_POST['admin_notes']);
    
    if(empty($transaction_id) || empty($status)) {
        $error = "Invalid request parameters!";
    } else {
        $update_query = "UPDATE transactions SET 
                        status = '$status',
                        processed_by = {$_SESSION['user_id']},
                        processed_date = NOW(),
                        admin_notes = '$admin_notes'
                        WHERE id = $transaction_id";
        
        if(mysqli_query($con, $update_query)) {
            $_SESSION['success'] = "Payment status updated successfully!";
            
            // If approved, update member's paid_date
            if($status == 'approved') {
                $get_user_query = "SELECT user_id FROM transactions WHERE id = $transaction_id";
                $user_result = mysqli_query($con, $get_user_query);
                
                if(mysqli_num_rows($user_result) > 0) {
                    $user_id = mysqli_fetch_assoc($user_result)['user_id'];
                    
                    $update_member = "UPDATE members SET 
                                    paid_date = CURDATE(),
                                    p_year = YEAR(CURDATE()),
                                    reminder = 0
                                    WHERE user_id = $user_id";
                    mysqli_query($con, $update_member);
                }
            }
            
            header("Location: payment-processing.php");
            exit();
        } else {
            $error = "Error updating payment: " . mysqli_error($con);
        }
    }
}

// Get payment history
$history_query = "SELECT t.*, m.fullname 
                 FROM transactions t
                 JOIN members m ON t.user_id = m.user_id
                 WHERE t.status != 'pending'
                 ORDER BY t.processed_date DESC
                 LIMIT 50";
$history_result = mysqli_query($con, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym System Admin - Payment Processing</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/fullcalendar.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <style>
        .payment-widget {
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 4px;
            overflow: hidden;
        }
        .payment-widget .widget-title {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            background-color: #f5f5f5;
        }
        .payment-widget .widget-content {
            padding: 15px;
            background-color: #fff;
        }
        .payment-card {
            border-left: 4px solid;
            padding: 15px;
            margin-bottom: 15px;
            background: #f9f9f9;
            border-radius: 3px;
        }
        .payment-card.pending {
            border-left-color: #f0ad4e;
        }
        .payment-card.approved {
            border-left-color: #5cb85c;
        }
        .payment-card.denied {
            border-left-color: #d9534f;
        }
        .badge-pending {
            background-color: #f0ad4e;
        }
        .badge-approved {
            background-color: #5cb85c;
        }
        .badge-denied {
            background-color: #d9534f;
        }
        .accordion-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
        }
        .accordion-toggle:hover {
            background-color: #f5f5f5;
        }
        .accordion-group {
            margin-bottom: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .accordion-inner {
            padding: 10px;
            border-top: 1px solid #ddd;
        }
        .payment-details {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ddd;
        }
        .payment-details:last-child {
            border-bottom: none;
        }
        .status-badge {
            margin-left: 5px;
        }
    </style>
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>
<!--close-Header-part--> 

<!--top-Header-menu-->
<?php include './includes/topheader.php'?>
<!--close-top-Header-menu-->

<!--sidebar-menu-->
<?php $page='payment'; include './includes/sidebar.php'?>
<!--sidebar-menu-->

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Payment Processing</a> </div>
    <h1 class="text-center">Payment Processing <i class="fas fa-money-check-alt"></i></h1>
  </div>
  <a href="javascript:history.back()" class="btn btn-outline-secondary">
  <i class="fas fa-arrow-left"></i> Back
</a>
  <div class="container-fluid">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box payment-widget">
          <div class="widget-title"> 
            <span class="icon"><i class="fas fa-users"></i></span>
            <h5>Pending Payments by Member</h5>
          </div>
          <div class="widget-content">
            <?php if(mysqli_num_rows($pending_group_result) > 0): ?>
              <div class="accordion" id="paymentAccordion">
                <?php while($user = mysqli_fetch_assoc($pending_group_result)): ?>
                  <div class="accordion-group">
                    <div class="accordion-heading">
                      <a class="accordion-toggle" data-toggle="collapse" data-parent="#paymentAccordion" href="#collapse<?= $user['user_id'] ?>">
                        <i class="fas fa-user"></i> <?= htmlspecialchars($user['fullname']) ?>
                        <span class="badge badge-warning"><?= $user['payment_count'] ?> payments</span>
                        <span class="pull-right">Total: $<?= number_format($user['total_amount'], 2) ?></span>
                      </a>
                    </div>
                    <div id="collapse<?= $user['user_id'] ?>" class="accordion-body collapse">
                      <div class="accordion-inner">
                        <?php $payments = getUserPayments($user['user_id']); ?>
                        <?php if(mysqli_num_rows($payments) > 0): ?>
                          <?php while($payment = mysqli_fetch_assoc($payments)): ?>
                            <div class="payment-card pending">
                              <form method="POST" class="form-horizontal">
                                <input type="hidden" name="transaction_id" value="<?= $payment['id'] ?>">
                                
                                <div class="payment-details">
                                  <p><strong>Payment Reference:</strong> <?= htmlspecialchars($payment['reference_number'] ?? 'N/A') ?></p>
                                  <p><strong>Amount:</strong> $<?= number_format($payment['amount'], 2) ?></p>
                                  <p><strong>Date:</strong> <?= date('M j, Y h:i A', strtotime($payment['payment_date'])) ?></p>
                                  <p><strong>Method:</strong> <?= htmlspecialchars($payment['payment_method']) ?></p>
                                  <?php if(!empty($payment['notes'])): ?>
                                    <p><strong>Member Notes:</strong> <?= htmlspecialchars($payment['notes']) ?></p>
                                  <?php endif; ?>
                                </div>
                                
                                <div class="control-group">
                                  <label class="control-label">Status:</label>
                                  <div class="controls">
                                    <select name="status" class="span12" required>
                                      <option value="approved">Approve</option>
                                      <option value="denied">Deny</option>
                                    </select>
                                  </div>
                                </div>
                                
                                <div class="control-group">
                                  <label class="control-label">Admin Notes:</label>
                                  <div class="controls">
                                    <textarea name="admin_notes" class="span12" rows="2" placeholder="Optional notes about this transaction"></textarea>
                                  </div>
                                </div>
                                
                                <div class="form-actions">
                                  <button type="submit" name="update_status" class="btn btn-success">
                                    <i class="fas fa-save"></i> Update Status
                                  </button>
                                </div>
                              </form>
                            </div>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <p class="text-muted">No pending payments found for this member</p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>
            <?php else: ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No pending payments found in the system.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Payment History Section -->
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box payment-widget">
          <div class="widget-title"> 
            <span class="icon"><i class="fas fa-history"></i></span>
            <h5>Recent Payment History</h5>
          </div>
          <div class="widget-content">
            <?php if(mysqli_num_rows($history_result) > 0): ?>
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Processed On</th>
                    <th>Admin Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while($history = mysqli_fetch_assoc($history_result)): ?>
                    <tr>
                      <td><?= htmlspecialchars($history['fullname']) ?></td>
                      <td>$<?= number_format($history['amount'], 2) ?></td>
                      <td><?= date('M j, Y', strtotime($history['payment_date'])) ?></td>
                      <td><?= htmlspecialchars($history['payment_method']) ?></td>
                      <td>
                        <span class="badge badge-<?= $history['status'] ?>">
                          <?= ucfirst($history['status']) ?>
                        </span>
                      </td>
                      <td><?= date('M j, Y h:i A', strtotime($history['processed_date'])) ?></td>
                      <td><?= !empty($history['admin_notes']) ? htmlspecialchars($history['admin_notes']) : 'N/A' ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p class="text-muted">No payment history found</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Footer-part-->
<div class="row-fluid">
  <div id="footer" class="span12"> <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi</div>
</div>

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
  $(document).ready(function() {
    // Initialize accordion to show first item by default
    if($('#paymentAccordion .accordion-group').length > 0) {
      $('#paymentAccordion .accordion-group:first .accordion-body').addClass('in');
    }
    
    // Initialize data tables for history
    $('.table').dataTable({
      "pageLength": 10,
      "bLengthChange": false,
      "order": [[5, "desc"]]
    });
    
    // Show success/error messages
    <?php if(isset($_SESSION['success'])): ?>
      $.gritter.add({
        title: 'Success',
        text: '<?= addslashes($_SESSION['success']) ?>',
        class_name: 'gritter-success',
        time: 3000
      });
    <?php unset($_SESSION['success']); endif; ?>
    
    <?php if(isset($error)): ?>
      $.gritter.add({
        title: 'Error',
        text: '<?= addslashes($error) ?>',
        class_name: 'gritter-error',
        time: 5000
      });
    <?php endif; ?>
  });
  
  function goPage (newURL) {
    if (newURL != "") {
      if (newURL == "-") {
        resetMenu();            
      } else {  
        document.location.href = newURL;
      }
    }
  }

  function resetMenu() {
    document.gomenu.selector.selectedIndex = 2;
  }
</script>
</body>
</html>