<?php
session_start(); // YE ADD KIYA
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    die("Access Denied. <a href='../login.php'>Login as Student</a>");
}

$stu_id = $_SESSION['user_id'];
$course_filter = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$where = "WHERE m.student_id=$stu_id";
if($course_filter > 0) {
    $where .= " AND m.course_id=$course_filter";
}

// Marks data
$marks = $conn->query("SELECT m.exam_type, c.course_code, c.course_name, 
                       m.marks_obtained, m.total_marks, 
                       ROUND((m.marks_obtained/m.total_marks)*100,2) as percentage, 
                       m.exam_date
                       FROM marks m 
                       JOIN courses c ON m.course_id=c.id 
                       $where 
                       ORDER BY c.course_code, m.exam_date DESC");

// Student courses for filter
$courses = $conn->query("SELECT c.id, c.course_code, c.course_name 
                         FROM enrollments e 
                         JOIN courses c ON e.course_id=c.id 
                         WHERE e.student_id=$stu_id");

// Summary stats - check karke fetch
$summary = ['total_exams'=>0,'avg_percentage'=>0,'total_obtained'=>0,'total_possible'=>0];
$sum_query = $conn->query("SELECT 
    COUNT(*) as total_exams,
    ROUND(AVG((m.marks_obtained/m.total_marks)*100),2) as avg_percentage,
    SUM(m.marks_obtained) as total_obtained,
    SUM(m.total_marks) as total_possible
    FROM marks m $where");
if($sum_query) $summary = $sum_query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Marks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f8f9fc; font-family: 'Nunito', sans-serif; }
.navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); transition: transform 0.2s; }
.card:hover { transform: translateY(-3px); }
.table thead { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; }
.stat-card { border-left: 4px solid; }
.stat-card.primary { border-left-color: #4e73df; }
.stat-card.success { border-left-color: #1cc88a; }
.stat-card.info { border-left-color: #36b9cc; }
.stat-card.warning { border-left-color: #f6c23e; }
</style>
</head>
<body>
<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-4"><i class="bi bi-journal-text"></i> My Marks Sheet</span>
    <a href="student_dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</nav>

<div class="container-fluid mt-4">
  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card primary h-100 py-2">
        <div class="card-body">
          <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Exams</div>
          <div class="h5 mb-0 fw-bold"><?=$summary['total_exams']?></div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card success h-100 py-2">
        <div class="card-body">
          <div class="text-xs fw-bold text-success text-uppercase mb-1">Average %</div>
          <div class="h5 mb-0 fw-bold"><?=$summary['avg_percentage']?>%</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card info h-100 py-2">
        <div class="card-body">
          <div class="text-xs fw-bold text-info text-uppercase mb-1">Marks Obtained</div>
          <div class="h5 mb-0 fw-bold"><?=$summary['total_obtained']?></div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card warning h-100 py-2">
        <div class="card-body">
          <div class="text-xs fw-bold text-warning text-uppercase mb-1">Total Marks</div>
          <div class="h5 mb-0 fw-bold"><?=$summary['total_possible']?></div>
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

  <!-- Marks Table -->
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h6 class="m-0 fw-bold">Marks Details</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th>Course Code</th>
              <th>Course Name</th>
              <th>Exam Type</th>
              <th>Obtained</th>
              <th>Total</th>
              <th>Percentage</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if($marks && $marks->num_rows > 0){
              $i = 1;
              while($r = $marks->fetch_assoc()){ 
                $badge_color = $r['percentage'] >= 50 ? 'bg-success' : 'bg-danger';
            ?>
            <tr>
              <td><?=$i++?></td>
              <td><code><?=$r['course_code']?></code></td>
              <td class="fw-semibold"><?=$r['course_name']?></td>
              <td><span class="badge bg-secondary"><?=$r['exam_type']?></span></td>
              <td class="fw-bold"><?=$r['marks_obtained']?></td>
              <td><?=$r['total_marks']?></td>
              <td><span class="badge <?=$badge_color?> fs-6"><?=$r['percentage']?>%</span></td>
              <td><?=date('d M Y', strtotime($r['exam_date']))?></td>
            </tr>
            <?php 
              }
            } else { 
            ?>
            <tr>
              <td colspan="8" class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>No marks record found
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