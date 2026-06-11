<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    die("Access Denied");
}

$fac_user_id = $_SESSION['user_id'];
$fac = $conn->query("SELECT id FROM faculty WHERE user_id=$fac_user_id")->fetch_assoc();
$fac_id = $fac['id'];
$message = "";

// Save marks
if(isset($_POST['save_marks'])){
    $course_id = intval($_POST['course_id']);
    $exam_type = $_POST['exam_type'];
    $total_marks = floatval($_POST['total_marks']);
    $exam_date = $_POST['exam_date'];
    
    // Fix: 6 ? lagaye 6 columns ke liye
    $stmt = $conn->prepare("INSERT INTO marks (student_id, course_id, exam_type, marks_obtained, total_marks, exam_date) VALUES (?, ?, ?)");
    
    foreach($_POST['marks'] as $student_id => $marks_obtained){
        if($marks_obtained !== ''){
            $student_id = intval($student_id);
            $marks_obtained = floatval($marks_obtained);
            $stmt->bind_param("iisdds", $student_id, $course_id, $exam_type, $marks_obtained, $total_marks, $exam_date);
            $stmt->execute();
        }
    }
    $message = "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle'></i> Marks saved successfully!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// Get courses for this faculty
$courses = $conn->query("SELECT id, course_code, course_name FROM courses WHERE faculty_id=$fac_id ORDER BY course_code");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Marks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f8f9fc; font-family: 'Nunito', sans-serif; }
.navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
.card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); }
.table thead { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; }
.btn-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; border-radius: 8px; font-weight: 600; }
</style>
</head>
<body>
<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-4"><i class="bi bi-journal-plus"></i> Add Marks</span>
    <a href="faculty_dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</nav>

<div class="container-fluid mt-4">
  <?=$message?>
  
  <div class="card mb-4">
    <div class="card-header bg-warning text-dark">
      <h6 class="m-0 fw-bold"><i class="bi bi-gear"></i> Step 1: Select Course & Exam</h6>
    </div>
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Select Course</label>
          <select name="course_id" class="form-select" onchange="this.form.submit()" required>
            <option value="">-- Select --</option>
            <?php while($c = $courses->fetch_assoc()){ ?>
              <option value="<?=$c['id']?>" <?=(isset($_GET['course_id']) && $_GET['course_id']==$c['id'])?'selected':''?>>
                <?=$c['course_code']?> - <?=$c['course_name']?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Exam Type</label>
          <select name="exam_type" class="form-select" required>
            <option value="Quiz" <?=(isset($_GET['exam_type']) && $_GET['exam_type']=='Quiz')?'selected':''?>>Quiz</option>
            <option value="Midterm" <?=(isset($_GET['exam_type']) && $_GET['exam_type']=='Midterm')?'selected':''?>>Midterm</option>
            <option value="Final" <?=(isset($_GET['exam_type']) && $_GET['exam_type']=='Final')?'selected':''?>>Final</option>
            <option value="Assignment" <?=(isset($_GET['exam_type']) && $_GET['exam_type']=='Assignment')?'selected':''?>>Assignment</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Total Marks</label>
          <input type="number" name="total_marks" class="form-control" value="<?=$_GET['total_marks']??100?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Exam Date</label>
          <input type="date" name="exam_date" class="form-control" value="<?=$_GET['exam_date']??date('Y-m-d')?>" required>
        </div>
      </form>
    </div>
  </div>

  <?php 
  if(isset($_GET['course_id']) && $_GET['course_id'] > 0){
    $course_id = intval($_GET['course_id']);
    $exam_type = $_GET['exam_type'] ?? 'Quiz';
    $total_marks = $_GET['total_marks'] ?? 100;
    $exam_date = $_GET['exam_date'] ?? date('Y-m-d');
    
    $students = $conn->query("SELECT s.id, s.roll_no, s.name FROM students s 
                              JOIN enrollments e ON s.id=e.student_id 
                              WHERE e.course_id=$course_id ORDER BY s.roll_no");
    
    if($students && $students->num_rows > 0){
  ?>
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h6 class="m-0 fw-bold"><i class="bi bi-list-ol"></i> Step 2: Enter Marks for <?=$exam_type?> - Total: <?=$total_marks?></h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="course_id" value="<?=$course_id?>">
        <input type="hidden" name="exam_type" value="<?=$exam_type?>">
        <input type="hidden" name="total_marks" value="<?=$total_marks?>">
        <input type="hidden" name="exam_date" value="<?=$exam_date?>">
        
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th width="10%">Roll No</th>
                <th>Student Name</th>
                <th width="20%">Marks Obtained</th>
              </tr>
            </thead>
            <tbody>
              <?php while($s = $students->fetch_assoc()){ 
                $existing = $conn->query("SELECT marks_obtained FROM marks WHERE student_id={$s['id']} AND course_id=$course_id AND exam_type='$exam_type' AND exam_date='$exam_date'")->fetch_assoc();
              ?>
              <tr>
                <td class="fw-semibold"><?=$s['roll_no']?></td>
                <td><?=$s['name']?></td>
                <td>
                  <input type="number" name="marks[<?=$s['id']?>]" class="form-control" 
                         min="0" max="<?=$total_marks?>" step="0.5" 
                         value="<?=$existing['marks_obtained']??''?>" placeholder="0-<?=$total_marks?>">
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <button name="save_marks" class="btn btn-primary">
          <i class="bi bi-save"></i> Save All Marks
        </button>
      </form>
    </div>
  </div>
  <?php 
    } else {
      echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle'></i> No students enrolled in this course.</div>";
    }
  }
  ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>