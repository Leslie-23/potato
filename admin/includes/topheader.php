<?php
include '../dbcon.php';
$admin_query = "SELECT * FROM admin";
$admin_result = mysqli_query($con, $admin_query);
$admin_row = mysqli_fetch_assoc($admin_result);
?>

<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav right">
    <li  class="dropdown" id="profile-messages" ><a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle"><i class="fas fa-user-circle"></i>  <span class="text">Welcome Admin <b class="text-success bold"><?php echo $admin_row['name']; ?></b></span><b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a title="View Profile" href="../admin/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
        <li class="divider"></li>
        <li><a href="../admin/view-tasks.php" title="View Tasks"><i class="fas fa-check"></i> My Tasks</a></li>
        <li class="divider"></li>
        <li><a href="../logout.php" title="Click to logout"><i class="fas fa-key"></i> Log Out</a></li>
      </ul>
    </li>
    
    <li class=""><a title="Click to logout" href="../logout.php"><i class="fas fa-power-off"></i> <span class="text">Logout</span></a></li>
  </ul>
</div> 