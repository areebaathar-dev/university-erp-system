<?php
session_start();
include '../includes/db.php';
if($_SESSION['role']!= 'faculty' && $_SESSION['role']!= 'admin') die("Access Denied");

// ADD MARKS
if(isset($_POST['add_marks'])){
  $student_id = intval($_POST['student_id']);
  $course_id = intval($_POST['course_id']);
  $exam_type = mysqli_real_escape_string($conn, $_POST['exam_type']);
  $marks = intval($_POST['marks_obtained']);
  $total = intval($_POST['total_marks']);
  $date = $_POST['exam_date'];
  mysqli_query($conn, "INSERT INTO marks (student_id, course_id, exam_type, marks_obtained, total_marks, exam_date) VALUES ($student_id,$course_id,'$exam_type',$marks,$total,'$date')");
  echo "<script>alert('Marks Added Successfully!');window.location='add_marks.php';</script>";
}

// DELETE MARKS
if(isset($_GET['delete'])){
  mysqli_query($conn, "DELETE FROM marks WHERE id=".intval($_GET['delete']));
  echo "<script>alert('Deleted!');window.location='add_marks.php';</script>";
}

// UPDATE MARKS
if(isset($_POST['update_marks'])){
  $id = intval($_POST['id']);
  mysqli_query($conn, "UPDATE marks SET student_id=$_POST[student_id], course_id=$_POST[course_id], exam_type='$_POST[exam_type]', marks_obtained=$_POST[marks_obtained], total_marks=$_POST[total_marks], exam_date='$_POST[exam_date]' WHERE id=$id");
  echo "<script>alert('Updated!');window.location='add_marks.php';</script>";
}

$students = mysqli_query($conn, "SELECT * FROM students");
$courses = mysqli_query($conn, "SELECT * FROM courses");
$marks = mysqli_query($conn, "SELECT m.*, s.name as sname, s.roll_no, c.course_code FROM marks m JOIN students s ON m.student_id=s.id JOIN courses c ON m.course_id=c.id ORDER BY m.id DESC");

$edit_data = null;
if(isset($_GET['edit'])){
  $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM marks WHERE id=".intval($_GET['edit'])));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Marks - University ERP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
.navbar {background: linear-gradient(135deg,#4e73df 0%,#224abe 100%);}
.card {border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
.btn-primary {background: linear-gradient(135deg,#4e73df 0%,#224abe 100%); border: none;}
.badge {font-size: 0.9rem;}
</style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark px-4">
  <span class="navbar-brand"><i class="bi bi-journal-check"></i> University ERP - Add Marks</span>
  <a href="dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-house"></i> Dashboard</a>
</nav>

<div class="container mt-4">

<!-- Add/Edit Marks Form -->
<div class="card p-4 mb-4">
<h5 class="mb-3"><i class="bi bi-plus-circle"></i> <?=$edit_data? 'Edit Marks' : 'Add New Marks'?></h5>
<form method="POST">
  <?php if($edit_data) echo '<input type="hidden" name="id" value="'.$edit_data['id'].'">';?>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Select Student</label>
      <select name="student_id" class="form-select" required>
        <?php mysqli_data_seek($students,0); while($s=mysqli_fetch_assoc($students)){?>
        <option value="<?=$s['id']?>" <?=$edit_data && $edit_data['student_id']==$s['id']?'selected':''?>><?=$s['roll_no']?> - <?=$s['name']?></option>
        <?php }?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Select Course</label>
      <select name="course_id" class="form-select" required>
        <?php mysqli_data_seek($courses,0); while($c=mysqli_fetch_assoc($courses)){?>
        <option value="<?=$c['id']?>" <?=$edit_data && $edit_data['course_id']==$c['id']?'selected':''?>><?=$c['course_code']?> - <?=$c['course_name']?></option>
        <?php }?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Exam Type</label>
      <select name="exam_type" class="form-select">
        <option <?=$edit_data && $edit_data['exam_type']=='Quiz'?'selected':''?>>Quiz</option>
        <option <?=$edit_data && $edit_data['exam_type']=='Mid'?'selected':''?>>Mid</option>
        <option <?=$edit_data && $edit_data['exam_type']=='Final'?'selected':''?>>Final</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Marks Obtained</label>
      <input type="number" name="marks_obtained" class="form-control" value="<?=$edit_data['marks_obtained']??''?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Total Marks</label>
      <input type="number" name="total_marks" class="form-control" value="<?=$edit_data['total_marks']??'100'?>" required>
    </div>
    <div class="col-md-12">
      <label class="form-label">Exam Date</label>
      <input type="date" name="exam_date" class="form-control" value="<?=$edit_data['exam_date']??date('Y-m-d')?>" required>
    </div>
  </div>
  <button class="btn btn-primary mt-3" name="<?=$edit_data?'update_marks':'add_marks'?>">
    <i class="bi bi-save"></i> <?=$edit_data?'Update Marks':'Add Marks'?>
  </button>
  <?php if($edit_data) echo '<a href="add_marks.php" class="btn btn-secondary mt-3">Cancel</a>';?>
</form>
</div>

<!-- Marks Report Table -->
<div class="card p-4">
<h5 class="mb-3"><i class="bi bi-table"></i> All Marks Records</h5>
<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-primary">
<tr><th>#</th><th>Roll No</th><th>Student</th><th>Course</th><th>Exam</th><th>Marks</th><th>Percentage</th><th>Date</th><th>Action</th></tr>
</thead>
<tbody>
<?php $i=1; while($m = mysqli_fetch_assoc($marks)){ 
  $perc = round(($m['marks_obtained']/$m['total_marks'])*100, 2);
  $badge = $perc >= 50 ? 'bg-success' : 'bg-danger';
?>
<tr>
  <td><?=$i++?></td>
  <td><span class="badge bg-dark"><?=$m['roll_no']?></span></td>
  <td><?=$m['sname']?></td>
  <td><span class="badge bg-info text-dark"><?=$m['course_code']?></span></td>
  <td><span class="badge bg-secondary"><?=$m['exam_type']?></span></td>
  <td><?=$m['marks_obtained']?> / <?=$m['total_marks']?></td>
  <td><span class="badge <?=$badge?>"><?=$perc?>%</span></td>
  <td><?=date('d M Y', strtotime($m['exam_date']))?></td>
  <td>
    <a href="?edit=<?=$m['id']?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
    <a href="?delete=<?=$m['id']?>" onclick="return confirm('Delete this record?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
  </td>
</tr>
<?php }?>
</tbody>
</table>
</div>
</div>

</div>
</body>
</html>