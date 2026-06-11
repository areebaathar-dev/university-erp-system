<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Access Denied. <a href='../login.php'>Login as Admin</a>");
}

// Stats
$total_students = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'] ?? 0;
$total_faculty = $conn->query("SELECT COUNT(*) as c FROM faculty")->fetch_assoc()['c'] ?? 0;
$total_courses = $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'] ?? 0;

// THIS MONTH'S FEE - Changed from Today's Fee
$monthly_fee = $conn->query("
    SELECT SUM(amount_paid) as total 
    FROM fee_payments 
    WHERE MONTH(payment_date) = MONTH(CURDATE()) 
    AND YEAR(payment_date) = YEAR(CURDATE())
")->fetch_assoc()['total'] ?? 0;

// Recent activity
$recent_fee = $conn->query("SELECT fp.*, s.name, s.roll_no FROM fee_payments fp JOIN students s ON fp.student_id=s.id ORDER BY fp.payment_id DESC LIMIT 5");

$recent_att = $conn->query("SELECT a.*, s.name, c.course_code FROM attendance a JOIN students s ON a.student_id=s.id JOIN courses c ON a.course_id=c.id ORDER BY a.att_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root {
    --primary: #4e73df;
    --success: #1cc88a;
    --info: #36b9cc;
    --warning: #f6c23e;
}
body { background: #f8f9fc; font-family: 'Nunito', sans-serif; }
.navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); transition: transform 0.2s; }
.card:hover { transform: translateY(-3px); }
.card-header { border-radius: 12px 12px 0 0 !important; font-weight: 700; }
.stat-card { border-left: 4px solid var(--primary); }
.stat-card.success { border-left-color: var(--success); }
.stat-card.warning { border-left-color: var(--warning); }
.stat-card.info { border-left-color: var(--info); }
.btn-outline-primary:hover { background: var(--primary); border-color: var(--primary); }
.table thead { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; }
.badge-receipt { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); font-size: 0.85rem; padding: 6px 12px; }
.badge-status { border-radius: 20px; padding: 5px 10px; }
</style>
</head>
<body>
<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-4"><i class="bi bi-speedometer2"></i> Admin Panel</span>
    <a href="../logout.php" class="btn btn-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</nav>

<div class="container-fluid mt-4">
  <h3 class="fw-bold text-dark mb-4">Welcome Admin</h3>
  
  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Students</div>
              <div class="h5 mb-0 fw-bold"><?=$total_students?></div>
            </div>
            <div class="col-auto">
              <i class="bi bi-people fs-2 text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card success h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Faculty</div>
              <div class="h5 mb-0 fw-bold"><?=$total_faculty?></div>
            </div>
            <div class="col-auto">
              <i class="bi bi-person-badge fs-2 text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card warning h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-warning text-uppercase mb-1">Total Courses</div>
              <div class="h5 mb-0 fw-bold"><?=$total_courses?></div>
            </div>
            <div class="col-auto">
              <i class="bi bi-book fs-2 text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card info h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-info text-uppercase mb-1">This Month's Fee</div>
              <div class="h5 mb-0 fw-bold">PKR <?=number_format($monthly_fee)?></div>
            </div>
            <div class="col-auto">
              <i class="bi bi-calendar-month fs-2 text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-dark text-white">
          <h6 class="m-0 fw-bold"><i class="bi bi-lightning-charge"></i> Quick Actions</h6>
        </div>
        <div class="card-body">
          <a href="manage_students.php" class="btn btn-outline-primary m-1"><i class="bi bi-person-plus"></i> Manage Students</a>
          <a href="manage_faculty.php" class="btn btn-outline-success m-1"><i class="bi bi-person-badge"></i> Manage Faculty</a>
          <a href="manage_courses.php" class="btn btn-outline-warning m-1"><i class="bi bi-book"></i> Manage Courses</a>
          <a href="fee_report.php" class="btn btn-outline-info m-1"><i class="bi bi-receipt"></i> Fee Report</a>
          <a href="attendance_report.php" class="btn btn-outline-secondary m-1"><i class="bi bi-calendar-check"></i> Attendance Report</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Recent Fee Collections -->
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-header py-3 bg-success text-white">
          <h6 class="m-0 fw-bold"><i class="bi bi-credit-card-2-front"></i> Recent Fee Collections</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr><th>Receipt</th><th>Student</th><th>Amount</th><th>Date</th></tr>
              </thead>
              <tbody>
                <?php if($recent_fee && $recent_fee->num_rows > 0): ?>
                  <?php while($r = $recent_fee->fetch_assoc()){ ?>
                  <tr>
                    <td><span class="badge badge-receipt text-white"><?=$r['receipt_no']?></span></td>
                    <td class="fw-semibold"><?=$r['roll_no']?></td>
                    <td class="text-success fw-bold">PKR <?=number_format($r['amount_paid'])?></td>
                    <td><?=date('d M Y', strtotime($r['payment_date']))?></td>
                  </tr>
                  <?php } ?>
                <?php else: ?>
                  <tr><td colspan="4" class="text-center text-muted py-4">No recent fee collections</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Attendance -->
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-header py-3 bg-primary text-white">
          <h6 class="m-0 fw-bold"><i class="bi bi-clipboard-check"></i> Recent Attendance</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr><th>Student</th><th>Course</th><th>Status</th><th>Date</th></tr>
              </thead>
              <tbody>
                <?php if($recent_att && $recent_att->num_rows > 0): ?>
                  <?php while($a = $recent_att->fetch_assoc()){ ?>
                  <tr>
                    <td><?=$a['name']?></td>
                    <td><code><?=$a['course_code']?></code></td>
                    <td>
                      <span class="badge badge-status <?=($a['status']=='Present')?'bg-success':'bg-danger'?> text-white">
                        <?=$a['status']?>
                      </span>
                    </td>
                    <td><?=date('d M Y', strtotime($a['att_date']))?></td>
                  </tr>
                  <?php } ?>
                <?php else: ?>
                  <tr><td colspan="4" class="text-center text-muted py-4">No recent attendance</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>