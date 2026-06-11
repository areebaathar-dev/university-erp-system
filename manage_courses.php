<?php
require_once __DIR__ . '/../includes/db.php';
if($_SESSION['role'] != 'admin') die("Access Denied");
$courses = $conn->query("SELECT c.*, f.name as faculty_name FROM courses c LEFT JOIN faculty f ON c.faculty_id=f.id ORDER BY c.id DESC");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Manage Courses</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>body{background:#f8f9fc;font-family:'Nunito',sans-serif}.navbar{background:linear-gradient(135deg,#4e73df 0%,#224abe 100%)}.card{border-radius:12px;border:none;box-shadow:0 0.15rem 1.75rem 0 rgba(58,59,69,0.15)}.table thead{background:linear-gradient(135deg,#4e73df 0%,#224abe 100%);color:white}</style>
</head><body>
<nav class="navbar navbar-dark"><div class="container-fluid"><span class="navbar-brand fs-4"><i class="bi bi-book"></i> Manage Courses</span><a href="dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a></div></nav>
<div class="container-fluid mt-4"><div class="card"><div class="card-header bg-warning text-dark"><h6 class="m-0 fw-bold">All Courses</h6></div><div class="card-body"><div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>#</th><th>Course Code</th><th>Course Name</th><th>Credits</th><th>Faculty</th></tr></thead><tbody><?php $i=1; while($c=$courses->fetch_assoc()){ ?><tr><td><?=$i++?></td><td><code><?=$c['course_code']?></code></td><td class="fw-semibold"><?=$c['course_name']?></td><td><?=$c['credits']?></td><td><?=$c['faculty_name']??'N/A'?></td></tr><?php } ?></tbody></table></div></div></div></div>
</body></html>