<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_faculty'])) {
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pdo->beginTransaction();
    $u = $pdo->prepare("INSERT INTO users (email, password, role, name) VALUES (?, ?, 'faculty', ?)");
    $u->execute([$_POST['email'], $hashed, $_POST['name']]);
    $uid = $pdo->lastInsertId();
    $f = $pdo->prepare("INSERT INTO faculty (user_id, employee_id) VALUES (?, ?)");
    $f->execute([$uid, $_POST['emp_id']]);
    $pdo->commit();
    echo "<div class='alert alert-success'>Faculty Registered!</div>";
}
?>
<div class="form-box shadow-sm">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Full Name" required></div>
            <div class="col-md-4"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="col-md-4"><input type="text" name="emp_id" class="form-control" placeholder="Employee ID" required></div>
            <div class="col-md-12"><input type="password" name="password" class="form-control" placeholder="Set Password" required></div>
            <button type="submit" name="save_faculty" class="btn btn-danger w-100 mt-2">Register Faculty</button>
        </div>
    </form>
</div>