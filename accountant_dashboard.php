<?php
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'accountant') {
    die("Access Denied. <a href='../login.php'>Login as Accountant</a>");
}

$msg = '';
$msg_type = '';

// Fee Submit
if(isset($_POST['submit_fee'])){
    $student_id = intval($_POST['student_id']);
    $amount = floatval($_POST['amount']);
    
    if($student_id > 0 && $amount > 0){
        $receipt_no = 'REC' . date('Ymd') . str_pad($student_id, 4, '0', STR_PAD_LEFT) . rand(100,999);
        $payment_date = date('Y-m-d');
        
        // FIXED: 4 columns = 4 ?
        $stmt = $conn->prepare("INSERT INTO fee_payments (student_id, amount_paid, receipt_no, payment_date) VALUES (?, ?, ?, ?)");
        
        if(!$stmt){
            $msg = "SQL Prepare Error: " . $conn->error;
            $msg_type = "danger";
        } else {
            $stmt->bind_param("idss", $student_id, $amount, $receipt_no, $payment_date);
            
            if($stmt->execute()){
                $msg = "Fee submitted successfully! Receipt No: $receipt_no";
                $msg_type = "success";
                $_POST = [];
            } else {
                $msg = "Insert Error: " . $stmt->error;
                $msg_type = "danger";
            }
        }
    } else {
        $msg = "Please select student and enter valid amount.";
        $msg_type = "warning";
    }
}

// Stats
$total_collected = $conn->query("SELECT SUM(amount_paid) as total FROM fee_payments")->fetch_assoc()['total'] ?? 0;
$total_students = $conn->query("SELECT COUNT(DISTINCT student_id) as total FROM fee_payments")->fetch_assoc()['total'] ?? 0;

// Students for dropdown
$students = $conn->query("SELECT id, roll_no, name FROM students ORDER BY name");

// Recent Payments
$payments = $conn->query("SELECT f.payment_id, f.receipt_no, s.roll_no, s.name, f.amount_paid, f.payment_date 
                 FROM fee_payments f 
                 JOIN students s ON f.student_id=s.id 
                 ORDER BY f.payment_id DESC 
                 LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accountant Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
:root { --primary: #4e73df; --success: #1cc88a; --info: #36b9cc; }
body { background: #f8f9fc; font-family: 'Nunito', sans-serif; }
.navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); transition: transform 0.2s; }
.card:hover { transform: translateY(-3px); }
.card-header { border-radius: 12px 12px 0 0 !important; font-weight: 700; }
.stat-card { border-left: 4px solid var(--primary); }
.stat-card.success { border-left-color: var(--success); }
.btn-submit { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border: none; border-radius: 8px; font-weight: 600; padding: 10px; transition: all 0.3s; }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(28,200,138,0.4); }
.select2-container .select2-selection--single { height: 42px; border-radius: 8px; border: 1px solid #d1d3e2; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 40px; }
.table thead { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; }
.badge-receipt { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); font-size: 0.85rem; padding: 6px 12px; }
</style>
</head>
<body>
<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-4"><i class="bi bi-cash-stack"></i> Accountant Panel - Fee Collection</span>
    <a href="../logout.php" class="btn btn-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</nav>

<div class="container-fluid mt-4">
  
  <?php if($msg != ''){ ?>
  <div class="alert alert-<?=$msg_type?> alert-dismissible fade show shadow-sm" role="alert">
    <i class="bi bi-<?=($msg_type=='success'?'check-circle-fill':'exclamation-triangle-fill')?>"></i> <?=$msg?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php } ?>

  <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Collected</div>
              <div class="h5 mb-0 fw-bold">PKR <?=number_format($total_collected)?></div>
            </div>
            <div class="col-auto"><i class="bi bi-wallet2 fs-2 text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card success h-100 py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <div class="text-xs fw-bold text-success text-uppercase mb-1">Students Paid</div>
              <div class="h5 mb-0 fw-bold"><?=$total_students?></div>
            </div>
            <div class="col-auto"><i class="bi bi-people fs-2 text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header bg-success text-white">
      <h6 class="m-0 fw-bold"><i class="bi bi-receipt-cutoff"></i> Submit Student Fee</h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-lg-6">
            <label class="form-label fw-semibold">Select Student</label>
            <select name="student_id" class="form-select select2" required>
              <option value="">🔍 Search by Roll No or Name...</option>
              <?php 
              if($students && $students->num_rows > 0){
                $students->data_seek(0);
                while($s = $students->fetch_assoc()){ 
              ?>
                <option value="<?=$s['id']?>"><?=$s['roll_no']?> - <?=$s['name']?></option>
              <?php 
                } 
              } 
              ?>
            </select>
          </div>
          <div class="col-lg-4">
            <label class="form-label fw-semibold">Amount (PKR)</label>
            <div class="input-group">
              <span class="input-group-text bg-light">₨</span>
              <input type="number" name="amount" class="form-control" placeholder="50000" step="0.01" min="1" required>
            </div>
          </div>
          <div class="col-lg-2">
            <label class="form-label d-none d-lg-block">&nbsp;</label>
            <button type="submit" name="submit_fee" class="btn btn-submit text-white w-100">
              <i class="bi bi-check-circle"></i> Submit Fee
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
      <h6 class="m-0 fw-bold"><i class="bi bi-clock-history"></i> Recent Payments</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th>Receipt No</th>
              <th>Student Roll</th>
              <th>Student Name</th>
              <th>Amount</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if($payments && $payments->num_rows > 0){
              $i = 1;
              while($p = $payments->fetch_assoc()){ 
            ?>
            <tr>
              <td><?=$i++?></td>
              <td><span class="badge badge-receipt text-white"><?=$p['receipt_no']?></span></td>
              <td class="fw-semibold"><?=$p['roll_no']?></td>
              <td><?=$p['name']?></td>
              <td class="text-success fw-bold">PKR <?=number_format($p['amount_paid'])?></td>
              <td><?=date('d M Y', strtotime($p['payment_date']))?></td>
            </tr>
            <?php 
              } 
            } else { 
            ?>
            <tr><td colspan="6" class="text-center text-muted py-5">
              <i class="bi bi-inbox fs-1 d-block mb-2"></i>No payment record found
            </td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Search student...",
        allowClear: true,
        width: '100%'
    });
});
</script>
</body>
</html>