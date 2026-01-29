<?php
session_start();
require_once 'config/database.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role']; // admin, faculty, or student

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->execute([$email, $role]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        
        header("Location: " . $role . "/dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password for the selected role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPortal - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --admin-red: #DC2626;
            --faculty-blue: #2563EB;
            --student-green: #16A34A;
            --current-theme: var(--admin-red); /* Default */
        }
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .login-card { max-width: 400px; width: 100%; border: none; border-radius: 15px; }
        .role-box { 
            cursor: pointer; border: 2px solid #eee; border-radius: 12px; padding: 15px; 
            transition: 0.3s; text-align: center; flex: 1; margin: 5px;
        }
        .role-box i { font-size: 1.5rem; display: block; margin-bottom: 5px; }
        
        /* Dynamic Theme Classes */
        .role-box.active[data-role="admin"] { border-color: var(--admin-red); background: var(--admin-red); color: white; }
        .role-box.active[data-role="faculty"] { border-color: var(--faculty-blue); background: var(--faculty-blue); color: white; }
        .role-box.active[data-role="student"] { border-color: var(--student-green); background: var(--student-green); color: white; }
        
        .btn-login { background-color: var(--current-theme); color: white; border: none; padding: 12px; border-radius: 8px; }
        .btn-login:hover { opacity: 0.9; color: white; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="login-card p-4">
        <div class="text-center mb-4">
            <div class="bg-light d-inline-block p-3 rounded-4 mb-2">
                <i class="bi bi-mortarboard-fill fs-1 text-dark"></i>
            </div>
            <h2 class="fw-bold mb-0">Manivasu - mini project</h2><br>
            <p class="text-muted">Student Management System</p>
        </div>

        <form method="POST" action="">
            <label class="small fw-bold text-muted mb-2">SELECT ROLE</label>
            <div class="d-flex justify-content-between mb-4">
                <div class="role-box active" data-role="admin" onclick="setRole('admin', '#DC2626')">
                    <i class="bi bi-people"></i> Admin
                </div>
                <div class="role-box" data-role="faculty" onclick="setRole('faculty', '#2563EB')">
                    <i class="bi bi-mortarboard"></i> Faculty
                </div>
                <div class="role-box" data-role="student" onclick="setRole('student', '#16A34A')">
                    <i class="bi bi-book"></i> Student
                </div>
            </div>

            <input type="hidden" name="role" id="selectedRole" value="admin">

            <div class="mb-3">
                <label class="form-label small fw-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
            <?php endif; ?>

            <button type="submit" class="btn btn-login w-100 fw-bold" id="loginBtn">Login</button>
        </form>
    </div>

    <script>
        function setRole(role, color) {
            // Update hidden input
            document.getElementById('selectedRole').value = role;
            
            // Update UI Selection
            document.querySelectorAll('.role-box').forEach(box => box.classList.remove('active'));
            document.querySelector(`[data-role="${role}"]`).classList.add('active');
            
            // Update Button Color
            document.getElementById('loginBtn').style.backgroundColor = color;
        }
    </script>
</body>
</html>