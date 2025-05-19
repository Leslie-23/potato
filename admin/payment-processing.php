<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}

include "../dbcon.php";

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add CSRF protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enhanced sanitization function
function sanitizeInput($data, $type = 'string') {
    global $con;
    $data = trim($data);
    $data = mysqli_real_escape_string($con, $data);
    switch($type) {
        case 'int':
            return (int)$data;
        case 'float':
            return (float)$data;
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        default:
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

// Get all pending payments
$pending_query = "SELECT t.*, m.fullname, m.contact 
                 FROM transactions t
                 JOIN members m ON t.user_id = m.user_id
                 WHERE t.status = 'pending'
                 ORDER BY m.fullname, t.payment_date DESC";
$pending_result = mysqli_query($con, $pending_query);

// Handle payment status update with transaction
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error'] = "CSRF token validation failed";
        header("Location: payment-processing.php");
        exit();
    }

    $transaction_id = sanitizeInput($_POST['transaction_id'], 'int');
    $status = in_array($_POST['status'], ['approved', 'denied']) ? $_POST['status'] : '';
    $admin_notes = sanitizeInput($_POST['admin_notes']);
    $reference_number = sanitizeInput($_POST['reference_number'] ?? '');
    $partial_amount = isset($_POST['partial_amount']) ? sanitizeInput($_POST['partial_amount'], 'float') : 0;

    // Validate inputs
    if(empty($transaction_id)) {
        $_SESSION['error'] = "Invalid transaction ID!";
        header("Location: payment-processing.php");
        exit();
    }

    if(empty($status)) {
        $_SESSION['error'] = "Invalid status selection!";
        header("Location: payment-processing.php");
        exit();
    }

    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // First get the original transaction details
        $get_transaction = "SELECT user_id, amount FROM transactions WHERE id = ?";
        $stmt_get = mysqli_prepare($con, $get_transaction);
        mysqli_stmt_bind_param($stmt_get, "i", $transaction_id);
        mysqli_stmt_execute($stmt_get);
        $transaction = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_get));
        
        if(!$transaction) {
            throw new Exception("Transaction not found");
        }

        // Determine the amount to use
        $final_amount = $transaction['amount'];
        if($status == 'approved' && $partial_amount > 0) {
            if($partial_amount > $transaction['amount']) {
                throw new Exception("Partial amount cannot be greater than original amount");
            }
            $final_amount = $partial_amount;
        }

        // Update transaction
        $update_query = "UPDATE transactions SET 
                        status = ?,
                        processed_by = ?,
                        processed_date = NOW(),
                        admin_notes = ?,
                        reference_number = ?,
                        amount = ?
                        WHERE id = ?";
        
        $stmt_update = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt_update, "sissdi", 
            $status,
            $_SESSION['user_id'],
            $admin_notes,
            $reference_number,
            $final_amount,
            $transaction_id
        );
        
        if(!mysqli_stmt_execute($stmt_update)) {
            throw new Exception("Update failed: " . mysqli_error($con));
        }

        // If approved, update member's status
        if($status == 'approved') {
            $update_member = "UPDATE members SET 
                            paid_date = CURDATE(),
                            p_year = YEAR(CURDATE()),
                            reminder = 0,
                            amount = amount + ?
                            WHERE user_id = ?;";
            
            $stmt_member = mysqli_prepare($con, $update_member);
            mysqli_stmt_bind_param($stmt_member, "di", $final_amount, $transaction['user_id']);
            
            if(!mysqli_stmt_execute($stmt_member)) {
                throw new Exception("Member update failed: " . mysqli_error($con));
            }
        }

        // Log the transaction with more details
        $log_query = "INSERT INTO payment_logs 
                     (transaction_id, user_id, admin_id, old_status, new_status, 
                     amount, reference_number, notes, log_date)
                     VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, NOW())";
        $stmt_log = mysqli_prepare($con, $log_query);
        mysqli_stmt_bind_param($stmt_log, "iiisdss", 
            $transaction_id,
            $transaction['user_id'],
            $_SESSION['user_id'],
            $status,
            $final_amount,
            $reference_number,
            $admin_notes
        );
        mysqli_stmt_execute($stmt_log);

        mysqli_commit($con);
        $_SESSION['success'] = "Payment status updated successfully!";
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: payment-processing.php");
    exit();
}

// Get payment history with search
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$history_query = "SELECT t.*, m.fullname 
                 FROM transactions t
                 JOIN members m ON t.user_id = m.user_id
                 WHERE t.status != 'pending'
                 AND (m.fullname LIKE ? OR t.reference_number LIKE ?)
                 ORDER BY t.processed_date DESC
                 LIMIT 100";

$stmt_history = mysqli_prepare($con, $history_query);
$search_term = "%$search%";
mysqli_stmt_bind_param($stmt_history, "ss", $search_term, $search_term);
mysqli_stmt_execute($stmt_history);
$history_result = mysqli_stmt_get_result($stmt_history);
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
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .member-group {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .member-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .payment-details {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ddd;
        }
        .payment-details:last-child {
            border-bottom: none;
        }
        .partial-payment {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px dashed #ccc;
        }
        .total-amount {
            font-weight: bold;
            color: #333;
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
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="payment.php" title="Go to Payments" class="tip-bottom">Payments </a><a href="#" class="current">Payment Processing</a> </div>
    <h1 class="text-center">Payment Processing <i class="fas fa-money-check-alt"></i></h1>
  </div>
  <!-- <a href="javascript:history.back()" class="btn btn-outline-secondary"> -->
  <!-- <i class="fas fa-arrow-left"></i> Back -->
  <!-- </a> -->
  <div class="container-fluid">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box payment-widget">
          <div class="widget-title"> 
            <span class="icon"><i class="fas fa-users"></i></span>
            <h5>Pending Payments</h5>
          </div>
          <div class="widget-content">
    <?php if(mysqli_num_rows($pending_result) > 0): ?>
        <?php 
        // Group payments by user on the frontend
        $grouped_payments = [];
        $total_pending = 0;
        
        while($payment = mysqli_fetch_assoc($pending_result)) {
            $user_id = $payment['user_id'];
            if(!isset($grouped_payments[$user_id])) {
                $grouped_payments[$user_id] = [
                    'user' => [
                        'fullname' => $payment['fullname'],
                        'contact' => $payment['contact'],
                        'user_id' => $payment['user_id']
                    ],
                    'payments' => [],
                    'total' => 0
                ];
            }
            $grouped_payments[$user_id]['payments'][] = $payment;
            $grouped_payments[$user_id]['total'] += $payment['amount'];
            $total_pending += $payment['amount'];
        }
        ?>
        
        <div class="alert alert-info">
            <strong>Total Pending:</strong> $<?= number_format($total_pending, 2) ?> across <?= count($grouped_payments) ?> members
        </div>
        
        <div class="accordion" id="paymentsAccordion">
            <?php foreach($grouped_payments as $group): ?>
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#paymentsAccordion" href="#collapse<?= $group['user']['user_id'] ?>">
                            <div style="display: flex; justify-content: space-between; width: 100%;">
                                <div>
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($group['user']['fullname']) ?>
                                    <small><?= htmlspecialchars($group['user']['contact']) ?></small>
                                </div>
                                <div>
                                    <span class="badge badge-warning"><?= count($group['payments']) ?> payments</span>
                                    <span class="total-amount">
                                        $<?= number_format($group['total'], 2) ?>
                                    </span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div id="collapse<?= $group['user']['user_id'] ?>" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <?php foreach($group['payments'] as $payment): ?>
                                <div class="payment-card pending">
                                    <form method="POST" class="form-horizontal">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
                                                    <option value="">Select Status</option>
                                                    <option value="approved">Approve</option>
                                                    <option value="denied">Deny</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="partial-payment">
                                            <div class="control-group">
                                                <label class="control-label">Partial Payment Amount (if applicable):</label>
                                                <div class="controls">
                                                    <input type="number" name="partial_amount" class="span12" 
                                                           min="0" max="<?= $payment['amount'] ?>" step="0.01"
                                                           placeholder="Enter amount if approving partial payment">
                                                    <span class="help-block">Leave empty to approve full amount</span>
                                                </div>
                                            </div>
                                            
                                            <div class="control-group">
                                                <label class="control-label">Reference Number (if any):</label>
                                                <div class="controls">
                                                    <input type="text" name="reference_number" class="span12" 
                                                           placeholder="Optional reference number">
                                                </div>
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
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No pending payments found in the system.
        </div>
    <?php endif; ?>
</div>

<!-- Add this CSS to your style section -->
<style>
    .accordion-group {
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .accordion-heading {
        background-color: #f5f5f5;
    }
    .accordion-toggle {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
    }
    .accordion-toggle:hover {
        background-color: #eee;
    }
    .accordion-inner {
        padding: 15px;
        border-top: 1px solid #ddd;
    }
    .accordion-toggle .fa-chevron-down {
        transition: transform 0.2s ease-in-out;
    }
    .accordion-toggle.collapsed .fa-chevron-down {
        transform: rotate(-90deg);
    }
</style>

<!-- Update your JavaScript to handle accordion icons -->
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize accordion to show first item by default
        if($('#paymentsAccordion .accordion-group').length > 0) {
            $('#paymentsAccordion .accordion-group:first .accordion-body').addClass('in');
        }
        
        // Handle accordion toggle icons
        $('#paymentsAccordion').on('show.bs.collapse', function () {
            $(this).find('.in').collapse('hide');
        });
        
        $('#paymentsAccordion').on('shown.bs.collapse', function (e) {
            $(e.target).prev('.accordion-heading').find('.accordion-toggle').addClass('collapsed');
        });
        
        $('#paymentsAccordion').on('hidden.bs.collapse', function (e) {
            $(e.target).prev('.accordion-heading').find('.accordion-toggle').removeClass('collapsed');
        });
    });
</script>
        </div>
      </div>
    </div>
    
    <!-- Payment History Section -->
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box payment-widget">
          <div class="widget-title"> 
            <span class="icon"><i class="fas fa-history"></i></span>
            <h5>Payment History</h5>
            <div class="search-box pull-right">
                <form method="GET" class="form-search">
                    <input type="text" name="search" class="search-query" placeholder="Search by name or reference" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
          </div>
          <div class="widget-content">
            <?php if(mysqli_num_rows($history_result) > 0): ?>
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Processed By</th>
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
                      <td><?= htmlspecialchars($history['reference_number'] ?? 'N/A') ?></td>
                      <td>
                        <span class="badge badge-<?= $history['status'] ?>">
                          <?= ucfirst($history['status']) ?>
                        </span>
                      </td>
                      <td><?= htmlspecialchars($history['processed_by'] ?? 'System') ?></td>
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
    // Initialize data tables for history
    $('.table').dataTable({
      "pageLength": 10,
      "bLengthChange": false,
      "order": [[7, "desc"]] // Sort by processed date
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
    
    <?php if(isset($_SESSION['error'])): ?>
      $.gritter.add({
        title: 'Error',
        text: '<?= addslashes($_SESSION['error']) ?>',
        class_name: 'gritter-error',
        time: 5000
      });
    <?php unset($_SESSION['error']); endif; ?>
    
    // Toggle partial payment fields when status changes
    $('select[name="status"]').change(function() {
        var partialDiv = $(this).closest('.control-group').next('.partial-payment');
        if($(this).val() === 'approved') {
            partialDiv.show();
        } else {
            partialDiv.hide();
        }
    }).trigger('change');
  });
</script>
</body>
</html>