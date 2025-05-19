


<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');	
}

include "dbcon.php";

// Get member's payment status
$user_id = $_SESSION['user_id'];
$member_query = "SELECT * FROM members WHERE user_id = '$user_id'";
$member_result = mysqli_query($con, $member_query);
$member = mysqli_fetch_assoc($member_result);

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_payment'])) {
    $amount = floatval($_POST['amount']);
    $payment_date = date('Y-m-d');
    
    // Update payment details
    $update_query = "UPDATE members SET 
                    paid_date = '$payment_date',
                    p_year = YEAR('$payment_date'),
                    reminder = 0
                    WHERE user_id = '$user_id'";
    
    if (mysqli_query($con, $update_query)) {
        // Record payment in transactions table (you'll need to create this)
        $payment_method = $_POST['payment_method']; 
        $transaction_query = "INSERT INTO transactions 
                            (user_id, amount, payment_date, payment_method)
                            VALUES 
                            ('$user_id', '$amount', '$payment_date', '$payment_method')";
        mysqli_query($con, $transaction_query);
        
        $_SESSION['payment_success'] = "Payment of $$amount processed successfully!";
        header("Location: payment.php");
        exit();
    } else {
        $error = "Payment failed: " . mysqli_error($con);
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System - My Payments</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/fullcalendar.css" />
<link rel="stylesheet" href="../css/matrix-style.css" />
<link rel="stylesheet" href="../css/matrix-media.css" />
<link href="../font-awesome/css/all.min.css" rel="stylesheet" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!--Header-part--> 
<div id="header">
  <h1><a href="dashboard.php">Perfect Gym</a></h1>
</div>

<!--top-Header-menu--> 
<?php include '../includes/topheader.php'?>

<!--sidebar-menu-->
<?php $page='payment'; include '../includes/sidebar.php'?>

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> 
        <a href="index.php" title="Go to Home" class="tip-bottom">
            <i class="fas fa-home"></i> Home
        </a> 
        <a href="payment.php" class="current">My Payments</a> 
    </div>
    <h1 class="text-center">My Payment Information <i class="fas fa-receipt"></i></h1>
  </div>
  
  <div class="container-fluid">
    <?php if(isset($_SESSION['payment_success'])): ?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $_SESSION['payment_success']; unset($_SESSION['payment_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="row-fluid">
      <div class="span12">
        <div class='widget-box'>
          <div class='widget-title'> 
            <span class='icon'> <i class='fas fa-credit-card'></i> </span>
            <h5>Payment Details</h5>
          </div>
          
          <div class='widget-content'>
            <div class="row-fluid">
                <div class="span6">
                    <div class="payment-info">
                        <h4>Your Membership Details</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($member['fullname']); ?></p>
                        <p><strong>Current Plan:</strong> <?php echo htmlspecialchars($member['plan']); ?> Month/s</p>
                        <p><strong>Service:</strong> <?php echo htmlspecialchars($member['services']); ?></p>
                        <p><strong>Amount Due:</strong> $<?php echo htmlspecialchars($member['amount']); ?></p>
                        <p><strong>Last Payment Date:</strong> 
                            <?php echo ($member['paid_date'] == '0000-00-00' || empty($member['paid_date'])) ? 
                                "No payments recorded" : 
                                htmlspecialchars($member['paid_date']); ?>
                        </p>
                    </div>
                </div>
                
                <div class="span6">
                    <div class="payment-form">
                        <h4>Make a Payment</h4>
                        <form method="POST" action="payment.php">
                            <div class="control-group">
                                <label class="control-label">Amount to Pay ($)</label>
                                <div class="controls">
                                    <input type="number" step="0.01" name="amount" 
                                           value="<?php echo htmlspecialchars($member['amount']); ?>" 
                                           class="span12" required>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label">Payment Method</label>
                                <div class="controls">
                                    <select class="span12" name="payment_method" required>
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="Debit Card">Debit Card</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="make_payment" class="btn btn-success btn-large">
                                    <i class="fas fa-dollar-sign"></i> Process Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <h4>Payment History</h4>
            <table class='table table-bordered table-hover'>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include('dbcon.php');
                    $history_query = "SELECT * FROM transactions 
                                     WHERE user_id = '$user_id' 
                                     ORDER BY payment_date DESC";
                    $history_result = mysqli_query($con, $history_query);
                    
                    if(mysqli_num_rows($history_result) > 0) {
                        while($row = mysqli_fetch_assoc($history_result)) {
                            echo "<tr>
                                <td>".htmlspecialchars($row['payment_date'])."</td>
                                <td>$".htmlspecialchars($row['amount'])."</td>
                                <td>".htmlspecialchars($row['payment_method'])."</td>
                                <td><span class='badge badge-success'>Completed</span></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No payment history found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php
// Add this where you want to show payment status (after the payment form)
// $user_id = $_SESSION['user_id'];
$payment_status_query = "SELECT * FROM transactions 
                        WHERE user_id = $user_id 
                        ORDER BY payment_date DESC 
                        LIMIT 1";
$payment_status_result = mysqli_query($con, $payment_status_query);
$latest_payment = mysqli_fetch_assoc($payment_status_result);
?>

<div class="payment-status mt-4">
    <h4><i class="fas fa-info-circle"></i> Payment Status</h4>
    <?php if($latest_payment): ?>
        <div class="alert alert-<?= 
            $latest_payment['status'] == 'approved' ? 'success' : 
            ($latest_payment['status'] == 'denied' ? 'danger' : 'warning'); ?>">
            <h5>
                <?php if($latest_payment['status'] == 'pending'): ?>
                    <i class="fas fa-clock"></i> Payment Pending Review
                <?php elseif($latest_payment['status'] == 'approved'): ?>
                    <i class="fas fa-check-circle"></i> Payment Approved
                <?php else: ?>
                    <i class="fas fa-times-circle"></i> Payment Denied
                <?php endif; ?>
            </h5>
            <p>Amount: $<?= $latest_payment['amount']; ?></p>
            <p>Date: <?= $latest_payment['payment_date']; ?></p>
            <p>Method: <?= $latest_payment['payment_method']; ?></p>
            <?php if($latest_payment['status'] == 'denied' && !empty($latest_payment['admin_notes'])): ?>
                <hr>
                <p><strong>Admin Notes:</strong> <?= htmlspecialchars($latest_payment['admin_notes']); ?></p>
                <a href="payment.php" class="btn btn-primary btn-sm">Try Again</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No payment history found.
        </div>
    <?php endif; ?>
</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer-part -->
<div class="row-fluid">
  <div id="footer" class="span12"> <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi</div>
</div>

<style>
#footer {
  color: white;
}
.payment-info, .payment-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}
</style>

<!-- JavaScript Files -->
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>

</body>
</html>