<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
$admin_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Tasks | Admin Calendar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="../css/bootstrap.min.css" rel="stylesheet" />
  <link href="../css/bootstrap-responsive.min.css" rel="stylesheet" />
  <link href="../css/matrix-style.css" rel="stylesheet" />
  <link href="../css/matrix-media.css" rel="stylesheet" />
  <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
  <style>
    #calendar {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
    }
  </style>
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
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>
<!-- Header -->
<?php include './includes/topheader.php'; ?>
<!-- Sidebar -->
<?php $page ='dashboard'; include './includes/sidebar.php'; ?>

<!-- Main Content -->
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> 
      <a href="dashboard.php" class="tip-bottom"><i class="fas fa-home"></i> Home</a> 
      <a href="my-tasks.php" class="current">My Tasks</a> 
    </div>
    <h1 class="text-center">Task & Calendar Manager <i class="fas fa-calendar-alt"></i></h1>
  </div>

  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span10 offset1">
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon"><i class="fas fa-calendar"></i></span>
            <h5>Task Calendar</h5>
          </div>
          <div class="widget-content nopadding">
            <div id="calendar"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    editable: true,
    selectable: true,
    eventSources: [{
      url: 'ajax/fetch-tasks.php',
      method: 'GET',
      extraParams: {
        admin_id: '<?php echo $admin_id; ?>'
      },
      failure: () => alert('There was an error fetching tasks!')
    }],
    select: function (info) {
      const title = prompt('Enter Task Title');
      if (title) {
        $.post('ajax/create-task.php', {
          title: title,
          start: info.startStr,
          end: info.endStr,
          admin_id: '<?php echo $admin_id; ?>'
        }, function () {
          calendar.refetchEvents();
        });
      }
      calendar.unselect();
    },
    eventClick: function (info) {
      const newTitle = prompt('Update Task Title', info.event.title);
      if (newTitle) {
        $.post('ajax/update-task.php', {
          id: info.event.id,
          title: newTitle
        }, function () {
          calendar.refetchEvents();
        });
      } else if (confirm('Delete this task?')) {
        $.post('ajax/delete-task.php', {
          id: info.event.id
        }, function () {
          calendar.refetchEvents();
        });
      }
    },
    eventDrop: function (info) {
      $.post('ajax/update-task.php', {
        id: info.event.id,
        start: info.event.startStr,
        end: info.event.endStr
      }, function () {
        calendar.refetchEvents();
      });
    }
  });
  calendar.render();
});
</script>

</body>
</html>
