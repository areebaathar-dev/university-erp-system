<?php
session_start();
include '../includes/db.php';
if($_SESSION['role']!= 'admin' && $_SESSION['role']!= 'faculty') die("Access Denied");

if(isset($_POST['mark_attendance'])){
  $course_id = intval($_POST['course_id']);
  $date = $_POST['att_date'];
  foreach($_POST['status'] as $student_id => $status){
    $student_id = intval($student_id);
    mysqli_query($conn, "INSERT INTO attendance (student_id, course_id, attendance_date, status) VALUES ($student_id, $course_id, '$date', '$status')");
  }
  echo "<script>alert('Attendance Marked for $date');window.location='mark_attendance.php';</script>";
}
$courses = mysqli_query($conn, "SELECT * FROM courses");
$students = mysqli_query($conn, "SELECT * FROM students");
?>
<!DOCTYPE html><html><head><title>Mark Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>.navbar{background: linear-gradient(135deg,#4e73df 0%,#224abe 100%);}.card{border-radius:15px;}</style>
</head><body class="bg-light">
<nav class="navbar navbar-dark px-4"><span class="navbar-brand"><i class="bi bi-calendar-check"></i> Mark Attendance</span><a href="dashboard.php" class="btn btn-light btn-sm">Dashboard</a></nav>
<div class="container mt-4">
<div class="card p-4">
<form method="POST">
<div class="row g-3 mb-3">
<div class="col-md-6"><label>Select Course</label><select name="course_id" class="form-select" required><?php while($c=mysqli_fetch_assoc($courses)){?><option value="<?=$c['id']?>"><?=$c['course_code']?> - <?=$c['course_name']?></option><?php }?></select></div>
<div class="col-md-6"><label>Date</label><input type="date" name="att_date" class="form-control" value="<?=date('Y-m-d')?>" required></div>
</div>
<table class="table table-bordered"><thead class="table-primary"><tr><th>Roll No</th><th>Student Name</th><th class="text-center">Status</th></tr></thead><tbody>
<?php mysqli_data_seek($students,0); while($s=mysqli_fetch_assoc($students)){?>
<tr><td><?=$s['roll_no']?></td><td><?=$s['name']?></td><td class="text-center">
<div class="btn-group" role="group">
<input type="radio" class="btn-check" name="status[<?=$s['id']?>]" id="p<?=$s['id']?>" value="Present" checked>
<label class="btn btn-outline-success" for="p<?=$s['id']?>">Present</label>
<input type="radio" class="btn-check" name="status[<?=$s['id']?>]" id="a<?=$s['id']?>" value="Absent">
<label class="btn btn-outline-danger" for="a<?=$s['id']?>">Absent</label>
</div></td></tr>
<?php }?></tbody></table>
<button class="btn btn-primary w-100" name="mark_attendance"><i class="bi bi-save"></i> Save Attendance</button>
</form></div></div></body></html>