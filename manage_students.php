<?php
session_start();
include '../includes/db.php';
if($_SESSION['role']!= 'admin') die("Access Denied");

// ADD
if(isset($_POST['add_student'])){
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $roll = mysqli_real_escape_string($conn, $_POST['roll_no']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $dept = mysqli_real_escape_string($conn, $_POST['department']);
  mysqli_query($conn, "INSERT INTO students (name, roll_no, email, phone, department) VALUES ('$name','$roll','$email','$phone','$dept')");
  echo "<script>alert('Student Added Successfully!');window.location='manage_students.php';</script>";
}

// DELETE
if(isset($_GET['delete'])){
  $id = intval($_GET['delete']);
  mysqli_query($conn, "DELETE FROM students WHERE id=$id");
  echo "<script>alert('Deleted!');window.location='manage_students.php';</script>";
}

// UPDATE
if(isset($_POST['update_student'])){
  $id = intval($_POST['id']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $dept = mysqli_real_escape_string($conn, $_POST['department']);
  mysqli_query($conn, "UPDATE students SET name='$name', email='$email', phone='$phone', department='$dept' WHERE id=$id");
  echo "<script>alert('Updated!');window.location='manage_students.php';</script>";
}

$students = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
$edit_data = null;
if(isset($_GET['edit'])){
  $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id=".intval($_GET['edit'])));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Students</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
.navbar {background: linear-gradient(135deg,#4e73df 0%,#224abe 100%);}
.card {border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
.btn-primary {background: linear-gradient(135deg,#4e73df 0%,#224abe 100%); border: none;}
</style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark px-4">
  <span class="navbar-brand"><i class="bi bi-mortarboard-fill"></i> University ERP - Students</span>
  <a href="dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-house"></i> Dashboard</a>
</nav>

<div class="container mt-4">

<!-- Add/Edit Student Card -->
<div class="card p-4 mb-4">
<h5 class="mb-3"><i class="bi bi-person-plus"></i> <?=$edit_data? 'Edit Student' : 'Add New Student'?></h5>
<form method="POST">
  <?php if($edit_data){ echo '<input type="hidden" name="id" value="'.$edit_data['id'].'">'; }?>
  <div class="row g-3">
    <div class="col-md-6"><input name="name" class="form-control" placeholder="Full Name" value="<?=$edit_data['name']??''?>" required></div>
    <div class="col-md-6"><input name="roll_no" class="form-control" placeholder="Roll No" value="<?=$edit_data['roll_no']??''?>" <?=$edit_data?'readonly':''?> required></div>
    <div class="col-md-6"><input name="email" type="email" class="form-control" placeholder="Email" value="<?=$edit_data['email']??''?>" required></div>
    <div class="col-md-6"><input name="phone" class="form-control" placeholder="Phone 03xx-xxxxxxx" value="<?=$edit_data['phone']??''?>" required></div>
    <div class="col-md-12"><input name="department" class="form-control" placeholder="Department - CS, BBA, etc" value="<?=$edit_data['department']??''?>" required></div>
  </div>
  <button class="btn btn-primary mt-3" name="<?=$edit_data?'update_student':'add_student'?>">
    <i class="bi bi-check-circle"></i> <?=$edit_data?'Update Student':'Add Student'?>
  </button>
  <?php if($edit_data){ echo '<a href="manage_students.php" class="btn btn-secondary mt-3">Cancel</a>'; }?>
</form>
</div>

<!-- Students Table Card -->
<div class="card p-4">
<h5 class="mb-3"><i class="bi bi-table"></i> All Students</h5>
<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-primary">
<tr><th>#</th><th>Roll No</th><th>Name</th><th>Email</th><th>Phone</th><th>Department</th><th>Action</th></tr>
</thead>
<tbody>
<?php $i=1; while($s = mysqli_fetch_assoc($students)){?>
<tr>
  <td><?=$i++?></td>
  <td><span class="badge bg-dark"><?=$s['roll_no']?></span></td>
  <td><?=$s['name']?></td>
  <td><?=$s['email']?></td>
  <td><?=$s['phone']?></td>
  <td><span class="badge bg-info text-dark"><?=$s['department']?></span></td>
  <td>
    <a href="?edit=<?=$s['id']?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
    <a href="?delete=<?=$s['id']?>" onclick="return confirm('Delete this student?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</a>
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