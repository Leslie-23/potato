<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}

include "../dbcon.php";

$staff_id = $_SESSION['user_id'];

// Get staff details
$staff_query = "SELECT * FROM staffs WHERE user_id = ?";
$stmt = mysqli_prepare($con, $staff_query);
mysqli_stmt_bind_param($stmt, "i", $staff_id);
mysqli_stmt_execute($stmt);
$staff_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    
    // Validate and sanitize inputs
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    
    if(empty($fullname)) $errors[] = "Full name is required";
    if(empty($email)) $errors[] = "Email is required";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if(empty($contact)) $errors[] = "Contact number is required";
    
    if(empty($errors)) {
        // Begin transaction
        mysqli_begin_transaction($con);
        
        try {
            // Update staff table
            $update_staff = "UPDATE staffs SET 
                            fullname = ?, 
                            email = ?, 
                            contact = ?, 
                            address = ?,
                            gender = ?
                            WHERE user_id = ?";
            $stmt_update = mysqli_prepare($con, $update_staff);
            mysqli_stmt_bind_param($stmt_update, "sssssi", 
                $fullname, $email, $contact, $address, $gender, $staff_id);
            mysqli_stmt_execute($stmt_update);
            
            // Handle profile picture upload
            if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/profiles/';
                $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                $filename = 'staff_' . $staff_id . '_' . time() . '.' . $file_ext;
                $target_file = $upload_dir . $filename;
                
                // Validate image
                $check = getimagesize($_FILES['profile_pic']['tmp_name']);
                if($check !== false) {
                    if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                        // Update profile picture in database
                        $update_pic = "UPDATE staffs SET image_url = ? WHERE user_id = ?";
                        $stmt_pic = mysqli_prepare($con, $update_pic);
                        $image_path = 'uploads/profiles/' . $filename;
                        mysqli_stmt_bind_param($stmt_pic, "si", $image_path, $staff_id);
                        mysqli_stmt_execute($stmt_pic);
                        
                        // Delete old profile picture if it exists
                        if(!empty($staff_data['image_url']) && file_exists('../'.$staff_data['image_url'])) {
                            unlink('../'.$staff_data['image_url']);
                        }
                    }
                }
            }
            
            // Commit transaction
            mysqli_commit($con);
            
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($con);
            $errors[] = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Staff Profile</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/uniform.css" />
    <link rel="stylesheet" href="../css/select2.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        .profile-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #eee;
            margin-bottom: 15px;
        }
        .form-section {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .form-section h4 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .password-toggle {
            cursor: pointer;
            color: #337ab7;
        }
        .password-toggle:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>

<!--top-Header-menu-->
<?php include '../includes/header.php'?>

<!--sidebar-menu-->
<?php $page='profile'; include '../includes/sidebar.php'?>

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
    <a href="profile.php">Profile</a> <a href="#" class="current">Edit Profile</a> </div>
    <h1>Edit Staff Profile</h1>
  </div>
  
  <div class="container-fluid">
    <?php if(!empty($errors)): ?>
        <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error:</strong> 
            <?php foreach($errors as $error): ?>
                <br><?= htmlspecialchars($error) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="icon-edit"></i></span>
            <h5>Edit Your Profile Information</h5>
          </div>
          <div class="widget-content">
            <form method="POST" action="edit-profile.php" class="form-horizontal" enctype="multipart/form-data">
              
              <div class="form-section">
                <h4>Basic Information</h4>
                
                <div class="control-group">
                  <label class="control-label">Profile Picture:</label>
                  <div class="controls">
                    <img src="<?= $staff_data['image_url'] ? '../'.$staff_data['image_url'] : '../img/default-staff-avatar.jpg' ?>" 
                         class="profile-preview" id="profile-preview">
                    <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Username:</label>
                  <div class="controls">
                    <input type="text" class="span12" value="<?= htmlspecialchars($staff_data['username']) ?>" disabled>
                    <span class="help-block">Username cannot be changed</span>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Full Name:</label>
                  <div class="controls">
                    <input type="text" class="span12" name="fullname" 
                           value="<?= htmlspecialchars($staff_data['fullname']) ?>" required>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Email:</label>
                  <div class="controls">
                    <input type="email" class="span12" name="email" 
                           value="<?= htmlspecialchars($staff_data['email']) ?>" required  disabled> <span class="help-block">email cannot be changed</span>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Contact Number:</label>
                  <div class="controls">
                    <input type="text" class="span12" name="contact" 
                           value="<?= htmlspecialchars($staff_data['contact']) ?>" required>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Address:</label>
                  <div class="controls">
                    <input type="text" class="span12" name="address" 
                           value="<?= htmlspecialchars($staff_data['address']) ?>">
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Gender:</label>
                  <div class="controls">
                    <select class="span12" name="gender">
                      <option value="Male" <?= $staff_data['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                      <option value="Female" <?= $staff_data['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                      <!-- <option value="Other" <?= $staff_data['gender'] == 'Other' ? 'selected' : '' ?>>Other</option> -->
                    </select>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Designation:</label>
                  <div class="controls">
                    <input type="text" class="span12" value="<?= ucfirst(htmlspecialchars($staff_data['designation'])) ?>" disabled>
                    <span class="help-block">Designation can only be changed by admin</span>
                  </div>
                </div>
              </div>
              
              <div class="form-section">
                <h4>Password Change</h4>
                <div class="alert alert-info">
                  <strong>Note:</strong> To change your password, please contact the system administrator.
                </div>
              </div>
              
              <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="profile.php" class="btn">Cancel</a>
              </div>
            </form>
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

<script src="../js/jquery.min.js"></script> 
<script src="../js/jquery.ui.custom.js"></script> 
<script src="../js/bootstrap.min.js"></script>  
<script src="../js/matrix.js"></script> 
<script src="../js/jquery.validate.js"></script> 
<script src="../js/jquery.uniform.js"></script> 
<script src="../js/select2.min.js"></script> 

<script>
$(document).ready(function() {
    // Profile picture preview
    $('#profile_pic').change(function(e) {
        if(this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profile-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Form validation
    $('form').validate({
        rules: {
            fullname: "required",
            email: {
                required: true,
                email: true
            },
            contact: "required"
        },
        messages: {
            fullname: "Please enter your full name",
            email: {
                required: "Please enter your email",
                email: "Please enter a valid email address"
            },
            contact: "Please enter your contact number"
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight: function(element) {
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element) {
            $(element).parents('.control-group').removeClass('error');
        }
    });
});
</script>

</body>
</html>