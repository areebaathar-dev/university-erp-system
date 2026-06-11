<?php
session_start();
include '../includes/db.php';
if($_SESSION['role']!= 'admin') die("Access Denied");

// ADD
if(isset($_POST['add_faculty'])){
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $dept = mysqli_real_escape_string($conn, $_POST['department']);
  $desig = mysqli_real_escape_string($conn, $_POST['designation']);
  mysqli_query($conn, "INSERT INTO faculty (name,email,phone,department,designation) VALUES ('$name','$email','$phone','$dept','$desig')");
  echo "<script>alert('Faculty Added!');window.location='manage_faculty.php';</script>";
}

// DELETE
if(isset($_GET['delete'])){
  mysqli_query($conn, "DELETE FROM faculty WHERE id=".intval($_GET['delete']));
  echo "<script>alert('Deleted!');window.location='manage_faculty.php';</script>";
}

// UPDATE
if(isset($_POST['update_faculty'])){
  $id = intval($_POST['id']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $dept = mysqli_real_escape_string($conn, $_POST['department']);
  $desig = mysqli_real_escape_string($conn, $_POST['designation']);
  mysqli_query($conn, "UPDATE faculty SET name='$name', email='$email', phone='$phone', department='$dept', designation='$desig' WHERE id=$id");
  echo "<script>alert('Updated!');window.location='manage_faculty.php';</script>";
}

$faculty = mysqli_query($conn, "SELECT * FROM faculty ORDER BY id DESC");
$edit_data = isset($_GET['edit'])? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM faculty WHERE id=".intval($_GET['edit']))) : null;
?>
<!DOCTYPE html>
<html><head><title>Manage Faculty</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>.navbar{background: linear-gradient(135deg,#4e73df 0%,#224abe 100%);}.card{border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}.btn-primary{background: linear-gradient(135deg,#4e73df 0%,#224abe 100%); border:none;}</style>
</head><body class="bg-light">
<nav class="navbar navbar-dark px-4"><span class="navbar-brand"><i class="bi bi-people-fill"></i> University ERP - Faculty</span><a href="dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-house"></i> Dashboard</a></nav>
<div class="container mt-4">

<div class="card p-4 mb-4">
<h5><i class="bi bi-person-badge"></i> <?=$edit_data? 'Edit Faculty' : 'Add New Faculty'?></h5>
<form method="POST">
<?php if($edit_data) echo '<input type="hidden" name="id" value="'.$edit_data['id'].'">';?>
<div class="row g-3">
<div class="col-md-6"><input name="name" class="form-control" placeholder="Full Name" value="<?=$edit_data['name']??''?>" required></div>
<div class="col-md-6"><input name="email" type="email" class="form-control" placeholder="Email" value="<?=$edit_data['email']??''?>" required></div>
<div class="col-md-6"><input name="phone" class="form-control" placeholder="Phone 03xx-xxxxxxx" value="<?=$edit_data['phone']??''?>"></div>
<div class="col-md-3"><input name="department" class="form-control" placeholder="Department" value="<?=$edit_data['department']??''?>" required></div>
<div class="col-md-3"><input name="designation" class="form-control" placeholder="Designation" value="<?=$edit_data['designation']??''?>" required></div>
</div>
<button class="btn btn-primary mt-3" name="<?=$edit_data?'update_faculty':'add_faculty'?>"><i class="bi bi-check-circle"></i> Save</button>
<?php if($edit_data) echo '<a href="manage_faculty.php" class="btn btn-secondary mt-3">Cancel</a>';?>
</form>
</div>

<div class="card p-4">
<h5><i class="bi bi-table"></i> Faculty List</h5>
<table class="table table-hover"><thead class="table-primary"><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Dept</th><th>Designation</th><th>Action</th></tr></thead><tbody>
<?php $i=1; while($f = mysqli_fetch_assoc($faculty)){?>
<tr>
  <td><?=$i++?></td>
  <td><?=$f['name']?></td>
  <td><?=$f['email']?></td>
  <td><?= $f['phone'] ?? 'N/A' ?></td>  <!-- Yahan fix kiya hai -->
  <td><span class="badge bg-info"><?=$f['department']?></span></td>
  <td><span class="badge bg-secondary"><?=$f['designation']?></span></td>
  <td><a href="?edit=<?=$f['id']?>" class="btn btn-sm btn-warning">Edit</a> <a href="?delete=<?=$f['id']?>" onclick="return confirm('Delete?')" class="btn btn-sm btn-danger">Delete</a></td>
</tr>
<?php }?></tbody></table>
</div></div></body></html>