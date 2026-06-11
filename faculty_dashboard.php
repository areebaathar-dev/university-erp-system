<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../login.php"); exit(); 
}

$fac_user_id = $_SESSION['user_id'];
$fac = $conn->query("SELECT id, name FROM faculty WHERE user_id=$fac_user_id")->fetch_assoc();
$fac_id = $fac['id'];
$fac_name = $fac['name'];

// Stats nikal lo
$total_courses = $conn->query("SELECT COUNT(*) as c FROM courses WHERE faculty_id=$fac_id")->fetch_assoc()['c'];
$total_students = $conn->query("SELECT COUNT(DISTINCT e.student_id) as c FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE c.faculty_id=$fac_id")->fetch_assoc()['c'];
$today_att = $conn->query("SELECT COUNT(*) as c FROM attendance a JOIN courses c ON a.course_id=c.id WHERE c.faculty_id=$fac_id AND a.attendance_date=CURDATE()")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Faculty Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f8f9fc; font-family: 'Nunito', sans-serif; }
.navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
.card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); transition: 0.3s; }
.card:hover { transform: translateY(-5px); }
.stat-card { border-left: 4px solid #4e73df; }
</style>
</head>
<body>
<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-4"><i class="bi bi-person-badge"></i> Welcome, Prof. <?=$fac_name?></span>
    <a href="../logout.php" class="btn btn-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</nav>

<div class="container-fluid mt-4">
  <!-- Stats Row -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card stat-card">
        <div class="card-body">
          <h6 class="text-muted">Total Courses</h6>
          <h2 class="fw-bold"><?=$total_courses?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card stat-card" style="border-left-color:#1cc88a">
        <div class="card-body">
          <h6 class="text-muted">Total Students</h6>
          <h2 class="fw-bold text-success"><?=$total_students?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card stat-card" style="border-left-color:#36b9cc">
        <div class="card-body">
          <h6 class="text-muted">Today's Attendance</h6>
          <h2 class="fw-bold text-info"><?=$today_att?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Action Cards -->
  <div class="row">
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-header bg-primary text-white"><i class="bi bi-calendar-check"></i> Attendance</div>
        <div class="card-body">
          <a href="faculty_mark_attendance.php" class="btn btn-primary w-100 mb-2"><i class="bi bi-plus-circle"></i> Mark Attendance</a>
          <a href="faculty_view_attendance.php" class="btn btn-outline-primary w-100"><i class="bi bi-bar-chart"></i> View Reports</a>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-header bg-success text-white"><i class="bi bi-journal-plus"></i> Marks</div>
        <div class="card-body">
          <a href="faculty_add_marks.php" class="btn btn-success w-100 mb-2"><i class="bi bi-plus-circle"></i> Add Marks</a>
          <a href="faculty_view_marks.php" class="btn btn-outline-success w-100"><i class="bi bi-list-check"></i> View Marks</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>