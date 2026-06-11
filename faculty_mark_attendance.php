<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    die("Access Denied. <a href='../login.php'>Login</a>");
}

$fac_user_id = $_SESSION['user_id'];
$fac = $conn->query("SELECT id FROM faculty WHERE user_id=$fac_user_id")->fetch_assoc();
if(!$fac) die("Faculty record not found. Contact Admin.");
$fac_id = $fac['id'];
$message = "";

// Step 1: Initialize Attendance
if(isset($_POST['init_attendance'])){
    $course_id = intval($_POST['course_id']);
    $date = $_POST['date'];
    
    $conn->query("CALL mark_attendance_bulk($course_id, '$date', $fac_id)");
    if($conn->errno == 0){
        $message = "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle'></i> All students marked as Absent. Update present students below.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $message = "<div class='alert alert-danger alert-dismissible fade show'><i class='bi bi-exclamation-triangle'></i> Error: ".$conn->error."<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Step 2: Update Attendance
if(isset($_POST['update_attendance'])){
    $stmt = $conn->prepare("UPDATE attendance SET status=? WHERE id=?");
    foreach($_POST['status'] as $att_id => $status){
        $status = $status == 'Present' ? 'Present' : 'Absent';
        $stmt->bind_param("si", $status, $att_id);
        $stmt->execute();
    }
    $message = "<div class='alert alert-info alert-dismissible fade show'><i class='bi bi-info-circle'></i> Attendance updated successfully!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// Get faculty courses
$courses = $conn->query("SELECT id, course_code, course_name, department FROM courses WHERE faculty_id=$fac_id ORDER BY course_code");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mark Attendance - University ERP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f8f9fc; font-family: 'Nunito', sans-serif; }
.navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); }
.card-header { border-radius: 12px 12px 0 0 !important; font-weight: 700; }
.table thead { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; }
.btn-warning { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); border: none; border-radius: 8px; font-weight: 600; }
.btn-success { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border: none; border-radius: 8px; font-weight: 600; }
</style>
</head>
<body>
<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-4"><i class="bi bi-plus-circle"></i> Mark Attendance</span>
    <a href="faculty_dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</nav>

<div class="container-fluid mt-4">
  <div class='alert alert-info'><i class="bi bi-person-check"></i> Logged in as Faculty ID: <?=$fac_id?></div>
  
  <?=$message?>

  <div class="card mb-4">
    <div class="card-header bg-warning text-dark">
      <h6 class="m-0 fw-bold"><i class="bi bi-calendar-date"></i> Initialize Attendance</h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-lg-5">
            <label class="form-label fw-semibold">Select Course</label>
            <select name="course_id" class="form-select" required>
              <option value="">-- Select Course --</option>
              <?php 
              if($courses && $courses->num_rows > 0){
                while($c = $courses->fetch_assoc()){ 
              ?>
                <option value="<?=$c['id']?>"><?=$c['course_code']?> - <?=$c['course_name']?> [<?=$c['department']?>]</option>
              <?php 
                } 
              } else { 
              ?>
                <option value="">No courses assigned to you</option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-4">
            <label class="form-label fw-semibold">Date</label>
            <input type="date" name="date" class="form-control" value="<?=date('Y-m-d')?>" required>
          </div>
          <div class="col-lg-3">
            <label class="form-label d-none d-lg-block">&nbsp;</label>
            <button name="init_attendance" class="btn btn-warning w-100">
              <i class="bi bi-play-fill"></i> Initialize
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php 
  if(isset($_POST['init_attendance'])){
    $course_id = intval($_POST['course_id']);
    $date = $_POST['date'];
    $att_list = $conn->query("SELECT a.id, s.roll_no, s.name, a.status 
                              FROM attendance a 
                              JOIN students s ON a.student_id = s.id 
                              WHERE a.course_id=$course_id AND a.attendance_date='$date'
                              ORDER BY s.roll_no");
    if($att_list && $att_list->num_rows > 0){
  ?>
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h6 class="m-0 fw-bold"><i class="bi bi-list-check"></i> Mark Present/Absent - <?=date('d M Y', strtotime($date))?></h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="course_id" value="<?=$course_id?>">
        <input type="hidden" name="date" value="<?=$date?>">
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead>
              <tr><th width="15%">Roll No</th><th>Student Name</th><th width="25%" class="text-center">Status</th></tr>
            </thead>
            <tbody>
              <?php while($row = $att_list->fetch_assoc()){ ?>
              <tr>
                <td class="fw-semibold"><span class="badge bg-dark"><?=$row['roll_no']?></span></td>
                <td><?=$row['name']?></td>
                <td class="text-center">
                  <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="status[<?=$row['id']?>]" id="p<?=$row['id']?>" value="Present" <?=$row['status']=='Present'?'checked':''?>>
                    <label class="btn btn-outline-success" for="p<?=$row['id']?>"><i class="bi bi-check-lg"></i> Present</label>
                    
                    <input type="radio" class="btn-check" name="status[<?=$row['id']?>]" id="a<?=$row['id']?>" value="Absent" <?=$row['status']=='Absent'?'checked':''?>>
                    <label class="btn btn-outline-danger" for="a<?=$row['id']?>"><i class="bi bi-x-lg"></i> Absent</label>
                  </div>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <button name="update_attendance" class="btn btn-success w-100 mt-3">
          <i class="bi bi-save"></i> Save Attendance
        </button>
      </form>
    </div>
  </div>
  <?php } else { 
      echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle'></i> No students enrolled. Check if students department matches course department: <b>CS</b></div>"; 
  } 
  } ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>