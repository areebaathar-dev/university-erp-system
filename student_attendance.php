<?php
session_start(); // YE ADD KIYA
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    die("Access Denied. <a href='../login.php'>Login as Student</a>");
}

$stu_id = $_SESSION['user_id'];
$course_filter = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$where = "WHERE a.student_id=$stu_id";
if($course_filter > 0) {
    $where .= " AND a.course_id=$course_filter";
}

// Attendance data
$att = $conn->query("SELECT a.att_date, c.course_code, c.course_name, a.status 
                     FROM attendance a 
                     JOIN courses c ON a.course_id=c.id 
                     $where 
                     ORDER BY a.att_date DESC");

// Student courses for filter
$courses = $conn->query("SELECT c.id, c.course_code, c.course_name 
                         FROM enrollments e 
                         JOIN courses c ON e.course_id=c.id 
                         WHERE e.student_id=$stu_id");

// Summary stats - check karke fetch
$summary = ['total'=>0,'present'=>0,'absent'=>0,'percentage'=>0];
$sum_query = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(status='Present') as present,
    SUM(status='Absent') as absent,
    ROUND(IFNULL((SUM(status='Present')/NULLIF(COUNT(*),0))*100,0),2) as percentage
    FROM attendance a $where");
if($sum_query) $summary = $sum_query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f8f9fc; font-family: 'Nunito', sans-serif; }
.navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); transition: transform 0.2s; }
.card:hover { transform: translateY(-3px); }
.table thead { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; }
.badge-status { border-radius: 20px; padding: 6px 12px; font-weight: 600; }
.stat-card { border-left: 4px solid; }
.stat-card.primary { border-left-color: #4e73df; }
.stat-card.success { border-left-color: #1cc88a; }
.stat-card.danger { border-left-color: #e74a3b; }
.stat-card.info { border-left-color: #36b9cc; }
</style>
</head>
<body>
<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-4"><i class="bi bi-calendar-check"></i> My Attendance</span>
    <a href="student_dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</nav>

<div class="container-fluid mt-4">
  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card primary h-100 py-2">
        <div class="card-body">
          <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Classes</div>
          <div class="h5 mb-0 fw-bold"><?=$summary['total']?></div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card success h-100 py-2">
        <div class="card-body">
          <div class="text-xs fw-bold text-success text-uppercase mb-1">Present</div>
          <div class="h5 mb-0 fw-bold"><?=$summary['present']?></div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card danger h-100 py-2">
        <div class="card-body">
          <div class="text-xs fw-bold text-danger text-uppercase mb-1">Absent</div>
          <div class="h5 mb-0 fw-bold"><?=$summary['absent']?></div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card info h-100 py-2">
        <div class="card-body">
          <div class="text-xs fw-bold text-info text-uppercase mb-1">Percentage</div>
          <div class="h5 mb-0 fw-bold"><?=$summary['percentage']?>%</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Course Filter -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Filter by Course</label>
          <select name="course_id" class="form-select" onchange="this.form.submit()">
            <option value="0">All Courses</option>
            <?php 
            if($courses && $courses->num_rows > 0){
              while($c = $courses->fetch_assoc()){ 
            ?>
                <option value="<?=$c['id']?>" <?=$course_filter==$c['id']?'selected':''?>>
                  <?=$c['course_code']?> - <?=$c['course_name']?>
                </option>
            <?php 
              } 
            } 
            ?>
          </select>
        </div>
      </form>
    </div>
  </div>

  <!-- Attendance Table -->
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h6 class="m-0 fw-bold">Attendance Records</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th>Date</th>
              <th>Course Code</th>
              <th>Course Name</th>
              <th width="15%">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if($att && $att->num_rows > 0){
              $i = 1;
              while($r = $att->fetch_assoc()){ 
            ?>
            <tr>
              <td><?=$i++?></td>
              <td><?=date('d M Y', strtotime($r['att_date']))?></td>
              <td><code><?=$r['course_code']?></code></td>
              <td class="fw-semibold"><?=$r['course_name']?></td>
              <td>
                <span class="badge badge-status <?=($r['status']=='Present')?'bg-success':'bg-danger'?> text-white">
                  <i class="bi <?=($r['status']=='Present')?'bi-check-circle':'bi-x-circle'?> me-1"></i>
                  <?=$r['status']?>
                </span>
              </td>
            </tr>
            <?php 
              }
            } else { 
            ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>No attendance record found
              </td>
            </tr>
            <?php 
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>