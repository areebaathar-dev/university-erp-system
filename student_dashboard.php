<?php
session_start(); // YE ZAROORI THA
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    die("Access Denied. <a href='../login.php'>Login as Student</a>");
}

$stu_user_id = $_SESSION['user_id'];

// FIXED: Query check karo pehle, phir fetch karo
$stu_query = $conn->query("SELECT id, roll_no, name FROM students WHERE id=$stu_user_id");

if(!$stu_query || $stu_query->num_rows == 0){
    die("<div class='alert alert-danger m-5'>Student record not found for User ID: $stu_user_id. Database me students table check karo.</div>");
}

$stu = $stu_query->fetch_assoc();
$stu_id = $stu['id'];

// Stats - har query ko check karke fetch karo
$total_courses = 0;
$att_query = $conn->query("SELECT COUNT(*) as c FROM enrollments WHERE student_id=$stu_id");
if($att_query) $total_courses = $att_query->fetch_assoc()['c'] ?? 0;

$attendance_per = 0;
$att_per_query = $conn->query("SELECT ROUND((SUM(status='Present')/COUNT(*))*100,2) as per FROM attendance WHERE student_id=$stu_id");
if($att_per_query) $attendance_per = $att_per_query->fetch_assoc()['per'] ?? 0;

$avg_marks = 0;
$marks_query = $conn->query("SELECT ROUND(AVG((marks_obtained/total_marks)*100),2) as avg FROM marks WHERE student_id=$stu_id");
if($marks_query) $avg_marks = $marks_query->fetch_assoc()['avg'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f8f9fc; font-family: 'Nunito', sans-serif; }
.navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); transition: transform 0.2s; }
.card:hover { transform: translateY(-3px); }
.stat-card { border-left: 4px solid; }
.stat-card.info { border-left-color: #36b9cc; }
.stat-card.success { border-left-color: #1cc88a; }
.stat-card.warning { border-left-color: #f6c23e; }
.btn-outline-primary:hover { background: #4e73df; border-color: #4e73df; }
</style>
</head>
<body>
<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-4"><i class="bi bi-mortarboard"></i> Student Panel - <?=$stu['name']?> (<?=$stu['roll_no']?>)</span>
    <a href="../logout.php" class="btn btn-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</nav>

<div class="container-fluid mt-4">
  <h3 class="fw-bold text-dark mb-4">Welcome <?=$stu['name']?> 👋</h3>
  
  <div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card stat-card info h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-info text-uppercase mb-1">Enrolled Courses</div>
              <div class="h5 mb-0 fw-bold"><?=$total_courses?></div>
            </div>
            <div class="col-auto"><i class="bi bi-book fs-2 text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card stat-card success h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-success text-uppercase mb-1">Attendance %</div>
              <div class="h5 mb-0 fw-bold"><?=$attendance_per?>%</div>
            </div>
            <div class="col-auto"><i class="bi bi-calendar-check fs-2 text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card stat-card warning h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-warning text-uppercase mb-1">Average Marks</div>
              <div class="h5 mb-0 fw-bold"><?=$avg_marks?>%</div>
            </div>
            <div class="col-auto"><i class="bi bi-graph-up fs-2 text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <a href="student_attendance.php" class="btn btn-outline-primary w-100 py-3">
        <i class="bi bi-calendar3 me-2"></i> View My Attendance
      </a>
    </div>
    <div class="col-md-6 mb-3">
      <a href="student_marks.php" class="btn btn-outline-warning w-100 py-3">
        <i class="bi bi-journal-text me-2"></i> View My Marks
      </a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>