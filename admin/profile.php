<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}

include '../dbcon.php';

// Initialize variables
$error = '';
$success = '';

// Get current admin data
$admin_id = $_SESSION['user_id'];
$query = "SELECT * FROM admin WHERE user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['password']);

    // Validate input
    if (empty($name)) {
        $error = 'Name is required';
    } elseif (!empty($new_password) && empty($old_password)) {
        $error = 'Old password is required to set a new password';
    } else {
        // Check old password if attempting to change password
        if (!empty($new_password)) {
            if (!password_verify($old_password, $admin['password'])) {
                $error = 'Old password is incorrect';
            }
        }

        // Proceed with update if no error
        if (empty($error)) {
            $update_query = "UPDATE admin SET name = ?";
            $params = [$name];
            $types = "s";

            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query .= ", password = ?";
                $params[] = $hashed_password;
                $types .= "s";
            }

            $update_query .= " WHERE user_id = ?";
            $params[] = $admin_id;
            $types .= "i";

            $stmt = $con->prepare($update_query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $success = 'Profile updated successfully!';
                // Refresh admin data
                $result = $con->query("SELECT * FROM admin WHERE user_id = $admin_id");
                $admin = $result->fetch_assoc();
            } else {
                $error = 'Error updating profile: ' . $con->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Profile</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/fullcalendar.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link rel="stylesheet" href="../css/font-awesome.css" />
    <link href="../font-awesome/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link rel="stylesheet" href="../font-awesome/css/all.min.css" />
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800"
      rel="stylesheet"
      type="text/css"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <script
      type="text/javascript"
      src="https://www.gstatic.com/charts/loader.js"
    ></script>
</head>
<body>

<div id="header">
    <h1><a href="dashboard.php">Perfect Gym Admin</a></h1>
</div>

<?php include './includes/topheader.php'; ?>
<?php $page = 'dashboard'; include './includes/sidebar.php'; ?>

<div id="content">
    <div id="content-header">
        <div id="breadcrumb">
            <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a>
            <a href="profile.php" class="current" title="Profile - Update">Profile</a>
        </div>
        <h1 class="text-center">Profile <i class="fas fa-user-cog"></i></h1>
    </div>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span6 offset3">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="fa fa-user"></i></span>
                        <h5>Edit Profile</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" style="margin: 10px;">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success" style="margin: 10px;">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST" class="form-horizontal">
                            <div class="control-group">
                                <label class="control-label">Username</label>
                                <div class="controls">
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" class="span11" required />
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Old Password</label>
                                <div class="controls">
                                    <input type="password" name="old_password" class="span11" placeholder="Enter current password to change it" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">New Password</label>
                                <div class="controls">
                                    <input type="password" name="password" class="span11" placeholder="Leave blank to keep current password" />
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
                <a href="dashboard.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
</body>
</html>
