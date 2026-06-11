<?php
require_once __DIR__ . '/includes/db.php';

$msg = "";
$msg_type = "";

if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username=?");
    if(!$stmt) die("SQL Error: " . $conn->error);
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Role ke hisaab se redirect
            if($user['role'] == 'student') {
                header("Location: student/student_dashboard.php");
                exit();
            }
            elseif($user['role'] == 'faculty') {
                header("Location: faculty/faculty_dashboard.php");
                exit();
            }
            elseif($user['role'] == 'accountant') {
                header("Location: accountant/accountant_dashboard.php");
                exit();
            }
            elseif($user['role'] == 'admin') {
                header("Location: admin/admin_dashboard.php");
                exit();
            }
        } else {
            $msg = "Galat password! Please try again.";
            $msg_type = "danger";
        }
    } else {
        $msg = "User not found! Register first.";
        $msg_type = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - University ERP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
}
.login-card {
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    border: none;
    overflow: hidden;
}
.btn-login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}
.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
.input-group-text {
    background: #f8f9fa;
    border-right: none;
}
.form-control {
    border-left: none;
}
</style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-mortarboard-fill text-primary" style="font-size: 3.5rem;"></i>
                        <h3 class="mt-3 fw-bold">Welcome Back</h3>
                        <p class="text-muted">University ERP System</p>
                    </div>

                    <?php if($msg != ''){ ?>
                    <div class="alert alert-<?=$msg_type?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?=$msg?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php } ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username / Roll No</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Enter your username" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn btn-primary btn-login w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-0 text-muted">Don't have an account? 
                            <a href="register.php" class="fw-semibold text-decoration-none">Register Here</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3 text-white">
                <small>© 2026 University ERP. All rights reserved.</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const passInput = document.querySelector('input[name="password"]');
    const icon = document.getElementById('toggleIcon');
    if(passInput.type === 'password') {
        passInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
</body>
</html>