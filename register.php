<?php
require_once __DIR__ . '/includes/db.php';

$msg = '';
$msg_type = '';

if(isset($_POST['register'])){
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $check = $conn->prepare("SELECT id FROM users WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        $msg = "Username already exists!";
        $msg_type = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        if($stmt->execute()){
            $msg = "Registration successful! You can login now.";
            $msg_type = "success";
        } else {
            $msg = "Error: " . $conn->error;
            $msg_type = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - University ERP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
}
.register-card {
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    border: none;
}
.btn-register {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}
.input-group-text {
    background: #f8f9fa;
    border-right: none;
}
.form-control, .form-select {
    border-left: none;
}
</style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card register-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3 fw-bold">Create Account</h3>
                        <p class="text-muted">University ERP System</p>
                    </div>

                    <?php if($msg != ''){ ?>
                    <div class="alert alert-<?=$msg_type?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle-fill"></i> <?=$msg?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php } ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username / Roll No</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="BCS001 / EMP101" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Role</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <select name="role" class="form-select" required>
                                    <option value="">Choose your role</option>
                                    <option value="student">Student</option>
                                    <option value="faculty">Faculty</option>
                                    <option value="accountant">Accountant</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn btn-primary btn-register w-100">
                            <i class="bi bi-check-circle"></i> Register Now
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account? 
                            <a href="login.php" class="fw-semibold text-decoration-none">Login Here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>