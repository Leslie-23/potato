<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}


include "dbcon.php";

// Get user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM members WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

// Handle form submission
if(isset($_POST['update_profile'])) {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $date_of_birth = mysqli_real_escape_string($con, $_POST['date_of_birth']);
    


    
// Handle profile picture upload
$profile_pic = $user['profile_pic']; // Keep current by default

if(!empty($_FILES['profile_pic']['name'])) {
    $target_dir = "../uploads/profiles/";  // Added ../ to match your structure
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Validate image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if($check === false) {
        $error_msg = "File is not a valid image";
    } elseif ($_FILES["profile_pic"]["size"] > 2000000) {
        $error_msg = "File is too large (max 2MB)";
    } else {
        $ext = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(in_array($ext, $allowed)) {
            // Delete old profile picture if it exists and isn't a default avatar
            if (!empty($user['profile_pic']) && 
                strpos($user['profile_pic'], 'default-') === false &&
                file_exists($user['profile_pic'])) {
                @unlink($user['profile_pic']);
            }
            
            // Generate unique filename
            $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
            $target_file = $target_dir . $new_filename;
            
            if(move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $profile_pic = "uploads/profiles/" . $new_filename; // Store relative path without ../
            } else {
                $error_msg = "Error uploading file";
                // Keep existing profile pic if upload fails
                $profile_pic = $user['profile_pic'];
            }
        } else {
            $error_msg = "Only JPG, JPEG, PNG & GIF files are allowed";
        }
    }
}
    
    
    // Update query
    $update_query = "UPDATE members SET 
                    fullname = '$fullname',
                    gender = '$gender',
                    address = '$address',
                    contact = '$contact',
                    email = '$email',
                    date_of_birth = '$date_of_birth',
                    profile_pic = '$profile_pic'
                    WHERE user_id = '$user_id'";
    
    if(mysqli_query($con, $update_query)) {
        $success_msg = "Profile updated successfully!";
        // Refresh user data
        $result = mysqli_query($con, $query);
        $user = mysqli_fetch_assoc($result);
    } else {
        $error_msg = "Error updating profile: " . mysqli_error($con);
    }
}

// Handle password change
if(isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if(password_verify($current_password, $user['password'])) {
        if($new_password == $confirm_password) {
            // Validate password strength
            if(strlen($new_password) < 8) {
                $pwd_error_msg = "Password must be at least 8 characters long!";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
                $update_pwd_query = "UPDATE members SET password = '$hashed_password' WHERE user_id = '$user_id'";
                
                if(mysqli_query($con, $update_pwd_query)) {
                    $pwd_success_msg = "Password changed successfully!";
                    
                    // Send email notification
                    // $to = $user['email'];
                    // $subject = "Password Changed - EliteFit Gym";
                    // $message = "Hello ".$user['fullname'].",\n\nYour password has been successfully changed.\n\n";
                    // $message .= "If you didn't make this change, please contact support immediately.\n\n";
                    // $message .= "Thanks,\nEliteFit Team";
                    // $headers = "From: no-reply@elitefit.com";
                    
                    // mail($to, $subject, $message, $headers);
                } else {
                    $pwd_error_msg = "Error updating password: " . mysqli_error($con);
                }
            }
        } else {
            $pwd_error_msg = "New passwords do not match!";
        }
    } else {
        $pwd_error_msg = "Current password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile | EliteFit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet" /><link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #f8f9fa;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .profile-section {
            margin-bottom: 30px;
        }
        .profile-section h3 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .form-actions {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>

<!--Header-part-->
<!--Header-part-->
<div id="header">
  <h1><a href="index.php">Perfect Gym System</a></h1>
</div>
<!--close-Header-part--> 
<?php include '../includes/topheader.php'; ?>
<?php $page="profile";  ?>
<!--close-Header-part--> 

<!--sidebar-menu-->
<?php include '../includes/sidebar.php'; ?>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#" class="current"><i class="fas fa-user"></i> My Profile</a></div>
        <!-- <h1>My Profile</h1> -->
    </div>
    
    <div class="container-fluid">
        <?php if(isset($success_msg)): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <div class="row-fluid">
            <div class="span12">
                <div class="profile-container">
                    <div class="profile-header">
                    <?php
include('dbcon.php');
// Get member data including new fields
$sql = "SELECT username, fullname, email, address, gender, contact, profile_pic, date_of_birth 
        FROM members WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

function getDefaultAvatar($gender) {
    $gender = strtolower($gender);
    return ($gender == 'female') ? '../img/default-female-avatar.png' : '../img/default-male-avatar.png';
}

// Determine which image to display
$display_image = "";
if (!empty($member['profile_pic']) && file_exists("../" . $member['profile_pic'])) {
    $display_image = "../" . $member['profile_pic'];  // Prepend ../ for correct path
} else {
    $display_image = getDefaultAvatar($member['gender']);
}

// Debug
echo "<!-- Debug: profile_pic in DB: " . htmlspecialchars($member['profile_pic']) . " -->";
echo "<!-- Debug: display_image: " . htmlspecialchars($display_image) . " -->";
echo "<!-- Debug: file exists: " . (file_exists($display_image) ? 'YES' : 'NO') . " -->";
?>

<img src="<?php echo htmlspecialchars($display_image); ?>" 
     width="300" height="300" 
     alt="<?php echo htmlspecialchars($member['fullname']); ?>'s Profile Picture" 
     style="border-radius: 8px; object-fit: cover; border: 2px solid #fff;"
     onerror="this.onerror=null; this.src='<?php echo htmlspecialchars(getDefaultAvatar($member['gender'])); ?>'">
                        <h2><?php echo $user['fullname']; ?></h2>
                        <p>Member since: <?php echo date('F j, Y', strtotime($user['dor'])); ?></p>
                    </div>
                    
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="profile-section">
                                <h3>Personal Information</h3>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="control-group">
                                        <label class="control-label">Full Name:</label>
                                        <div class="controls">
                                            <input type="text" class="span12" name="fullname" value="<?php echo $user['fullname']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Username:</label>
                                        <div class="controls">
                                            <input type="text" disabled title="username updates are currently being fixed" class="span12" name="username" value="<?php echo $user['username']; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Gender:</label>
                                        <div class="controls">
                                            <select class="span12" name="gender" required>
                                                <option value="Male" <?php echo $user['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                                <option value="Female" <?php echo $user['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                                <option value="Other" <?php echo $user['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Date of Birth:</label>
                                        <div class="controls">
                                            <input type="date" class="span12" name="date_of_birth" value="<?php echo $user['date_of_birth']; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Address:</label>
                                        <div class="controls">
                                            <input type="text" class="span12" name="address" value="<?php echo $user['address']; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Contact Number:</label>
                                        <div class="controls">
                                            <input type="text" class="span12" name="contact" value="<?php echo $user['contact']; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Email:</label>
                                        <div class="controls">
                                            <input type="email" class="span12" disabled title="Email updates are currently being fixed" name="email" value="<?php echo $user['email']; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Profile Picture:</label>
                                        <div class="controls">
                                            <input type="file" name="profile_pic" accept="image/*">
                                        </div>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="submit" name="update_profile" class="btn btn-success ">  <i class="fas fa-save"></i> Update Profile</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="span6">
                            <div class="profile-section">
                                <h3>Account Information</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Username</th>
                                        <td><?php echo $user['username']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Membership Status</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $user['status'] == 'Active' ? 'success' : 
                                                     ($user['status'] == 'Pending' ? 'warning' : 'important'); ?>">
                                                <?php echo $user['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Membership Plan</th>
                                        <td><?php echo $user['plan'] . ' months'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Service</th>
                                        <td><?php echo $user['services']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Last Payment</th>
                                        <td><?php echo $user['paid_date'] ? date('F j, Y', strtotime($user['paid_date'])) : 'Not available'; ?></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="profile-section">
                                <h3>Change Password</h3>
                                <?php if(isset($pwd_success_msg)): ?>
                                    <div class="alert alert-success"><?php echo $pwd_success_msg; ?></div>
                                <?php endif; ?>
                                
                                <?php if(isset($pwd_error_msg)): ?>
                                    <div class="alert alert-danger"><?php echo $pwd_error_msg; ?></div>
                                <?php endif; ?>
                                
                                <form method="post">
                                    <div class="control-group">
                                        <label class="control-label">Current Password:</label>
                                        <div class="controls">
                                            <input type="password" class="span12" name="current_password" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">New Password:</label>
                                        <div class="controls">
                                            <input type="password" class="span12" name="new_password" required>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Confirm New Password:</label>
                                        <div class="controls">
                                            <input type="password" class="span12" name="confirm_password" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form>
                            </div>
                            
                            <?php if(!empty($user['ini_weight']) && !empty($user['curr_weight'])): ?>
                            <div class="profile-section">
                                <h3>Fitness Progress</h3>
                                <div class="progress progress-striped active">
                                    <div class="bar" style="width: <?php 
                                        $progress = (($user['curr_weight'] - $user['ini_weight']) / $user['ini_weight']) * 100;
                                        echo abs($progress);
                                    ?>%; background-color: <?php echo $progress > 0 ? '#f0ad4e' : '#5cb85c'; ?>"></div>
                                </div>
                                <p>
                                    <strong>Initial Weight:</strong> <?php echo $user['ini_weight']; ?> kg<br>
                                    <strong>Current Weight:</strong> <?php echo $user['curr_weight']; ?> kg<br>
                                    <strong>Change:</strong> <span style="color: <?php echo $progress > 0 ? '#f0ad4e' : '#5cb85c'; ?>">
                                        <?php echo ($progress > 0 ? '+' : '') . number_format($progress, 1); ?>%
                                    </span>
                                </p>
                                <p>
                                    <strong>Body Type:</strong> <?php echo $user['ini_bodytype']; ?> → <?php echo $user['curr_bodytype']; ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end-main-container-part-->

<!--Footer-part-->
<div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y");?> &copy; EliteFit Gym Management System</div>
</div>

<!--Scripts-->
<script src="../js/jquery.min.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/matrix.js"></script>

</body>
</html>