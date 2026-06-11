<?php
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'accountant') {
    die("Access Denied. <a href='../login.php'>Login as Accountant</a>");
}

$acc_user_id = $_SESSION['user_id'];
$message = "";

// Collect Fee
if(isset($_POST['collect_fee'])){
    $student_id = intval($_POST['student_id']);
    $fee_id = intval($_POST['fee_id']);
    $amount = floatval($_POST['amount']);
    $method = $_POST['payment_method'];
    $remarks = $_POST['remarks'];
    $date = date('Y-m-d');
    $receipt = 'RCP' . date('Ymd') . rand(1000,9999);
    
    $stmt = $conn->prepare("INSERT INTO fee_payments (student_id, fee_id, amount_paid, payment_date, receipt_no, collected_by, payment_method, remarks) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("iidsisss", $student_id, $fee_id, $amount, $date, $receipt, $acc_user_id, $method, $remarks);
    
    if($stmt->execute()){
        $message = "<div class='alert alert-success'>Fee collected! Receipt No: <b>$receipt</b></div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: ".$conn->error."</div>";
    }
}

// Get students + fees
$students = $conn->query("SELECT id, roll_no, name FROM students ORDER BY name");
$fees = $conn->query("SELECT id, class, semester, amount, due_date FROM fee_structure ORDER BY class");

// Today's collections
$today = date('Y-m-d');
$today_collections = $conn->query("SELECT fp.*, s.name, s.roll_no, fs.class 
                                  FROM fee_payments fp
                                  JOIN students s ON fp.student_id = s.id
                                  JOIN fee_structure fs ON fp.fee_id = fs.id
                                  WHERE fp.payment_date='$today'
                                  ORDER BY fp.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Fee Collection</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3>Fee Collection - Accountant Panel</h3>
  <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
  
  <?=$message?>

  <!-- Collect Fee Form -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Collect New Fee</div>
    <div class="card-body">
      <form method="POST">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Select Student</label>
            <select name="student_id" class="form-select" required>
              <option value="">-- Select Student --</option>
              <?php while($s = $students->fetch_assoc()){ ?>
                <option value="<?=$s['id']?>"><?=$s['roll_no']?> - <?=$s['name']?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label>Fee Structure</label>
            <select name="fee_id" class="form-select" required id="feeSelect">
              <option value="">-- Select Fee --</option>
              <?php 
              $fees->data_seek(0);
              while($f = $fees->fetch_assoc()){ ?>
                <option value="<?=$f['id']?>" data-amount="<?=$f['amount']?>">
                  <?=$f['class']?> - <?=$f['semester']?> - Rs. <?=number_format($f['amount'])?>
                </option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label>Payment Method</label>
            <select name="payment_method" class="form-select" required>
              <option value="Cash">Cash</option>
              <option value="Bank">Bank Transfer</option>
              <option value="Online">Online</option>
            </select>
          </div>
          <div class="col-md-8 mb-3">
            <label>Remarks</label>
            <input type="text" name="remarks" class="form-control" placeholder="Optional">
          </div>
          <div class="col-12">
            <button name="collect_fee" class="btn btn-success">Collect Fee & Generate Receipt</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Today's Collections -->
  <div class="card">
    <div class="card-header bg-dark text-white">Today's Collections - <?=date('d M, Y')?></div>
    <div class="card-body">
      <?php if($today_collections->num_rows > 0){ ?>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Receipt No</th>
            <th>Student</th>
            <th>Class</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $today_collections->fetch_assoc()){ ?>
          <tr>
            <td><?=$row['receipt_no']?></td>
            <td><?=$row['roll_no']?> - <?=$row['name']?></td>
            <td><?=$row['class']?></td>
            <td>Rs. <?=number_format($row['amount_paid'])?></td>
            <td><?=$row['payment_method']?></td>
            <td><?=date('h:i A', strtotime($row['payment_date']))?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
        <p class="text-muted">No fee collected today yet.</p>
      <?php } ?>
    </div>
  </div>
</div>

<script>
// Auto fill amount when fee selected
document.getElementById('feeSelect').addEventListener('change', function(){
    var amount = this.options[this.selectedIndex].getAttribute('data-amount');
    document.getElementById('amount').value = amount;
});
</script>
</body>
</html>