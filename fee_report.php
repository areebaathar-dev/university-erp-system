<?php
require_once __DIR__ . '/../includes/db.php';
if($_SESSION['role'] != 'admin') die("Access Denied");
$payments = $conn->query("SELECT fp.*, s.name, s.roll_no FROM fee_payments fp JOIN students s ON fp.student_id=s.id ORDER BY fp.payment_id DESC");
$total = $conn->query("SELECT SUM(amount_paid) as t FROM fee_payments")->fetch_assoc()['t'] ?? 0;
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Fee Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>body{background:#f8f9fc;font-family:'Nunito',sans-serif}.navbar{background:linear-gradient(135deg,#4e73df 0%,#224abe 100%)}.card{border-radius:12px;border:none;box-shadow:0 0.15rem 1.75rem 0 rgba(58,59,69,0.15)}.table thead{background:linear-gradient(135deg,#4e73df 0%,#224abe 100%);color:white}.badge-receipt{background:linear-gradient(135deg,#36b9cc 0%,#258391 100%)}</style>
</head><body>
<nav class="navbar navbar-dark"><div class="container-fluid"><span class="navbar-brand fs-4"><i class="bi bi-receipt"></i> Fee Report</span><a href="dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a></div></nav>
<div class="container-fluid mt-4">
<div class="alert alert-success"><h5 class="mb-0">Total Fee Collected: <strong>PKR <?=number_format($total)?></strong></h5></div>
<div class="card"><div class="card-header bg-info text-white"><h6 class="m-0 fw-bold">All Payments</h6></div><div class="card-body"><div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>#</th><th>Receipt</th><th>Student</th><th>Amount</th><th>Date</th></tr></thead><tbody><?php $i=1; while($p=$payments->fetch_assoc()){ ?><tr><td><?=$i++?></td><td><span class="badge badge-receipt text-white"><?=$p['receipt_no']?></span></td><td><?=$p['roll_no']?> - <?=$p['name']?></td><td class="text-success fw-bold">PKR <?=number_format($p['amount_paid'])?></td><td><?=date('d M Y',strtotime($p['payment_date']))?></td></tr><?php } ?></tbody></table></div></div></div></div>
</body></html>