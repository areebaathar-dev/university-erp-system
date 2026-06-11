<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access Denied");
}

$sql = "SELECT a.*, s.name as student_name, s.roll_no, c.course_code, c.course_name 
        FROM attendance a 
        JOIN students s ON a.student_id = s.id 
        JOIN courses c ON a.course_id = c.id 
        ORDER BY a.attendance_date DESC LIMIT 10";

$attendance = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attendance Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>body{background:#f8f9fc;font-family:'Nunito',sans-serif}.navbar{background:linear-gradient(135deg,#4e73df 0%,#224abe 100%)}.card{border-radius:12px;border:none;box-shadow:0 0.15rem 1.75rem 0 rgba(58,59,69,0.15)}.table thead{background:linear-gradient(135deg,#4e73df 0%,#224abe 100%);color:white}</style>
</head>
<body>
<nav class="navbar navbar-dark"><div class="container-fluid"><span class="navbar-brand fs-4"><i class="bi bi-calendar-check"></i> Attendance Report</span><a href="dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a></div></nav>
<div class="container-fluid mt-4"><div class="card"><div class="card-header bg-primary text-white"><h6 class="m-0 fw-bold">Recent Attendance - Last 100 Records</h6></div><div class="card-body"><div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>#</th><th>Student</th><th>Course</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php 
$i=1; 
if($attendance->num_rows > 0){ 
    while($a=$attendance->fetch_assoc()){ 
?>
<tr>
    <td><?=$i++?></td>
    <td><?=$a['roll_no']?> - <?=htmlspecialchars($a['student_name'])?></td>
    <td><code><?=$a['course_code']?></code> - <?=htmlspecialchars($a['course_name'])?></td>
    <td><span class="badge <?=$a['status']=='Present'?'bg-success':'bg-danger'?>"><?=$a['status']?></span></td>
    <td><?=date('d M Y',strtotime($a['attendance_date']))?></td>
</tr>
<?php 
    } // while close
} else { 
?>
<tr><td colspan="5" class="text-center text-muted py-4">No attendance records found</td></tr>
<?php 
} // if close 
?>
</tbody></table></div></div></div></div>
</body>
</html>