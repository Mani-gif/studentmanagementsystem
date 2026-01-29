<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_student'])) {
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pdo->beginTransaction();
    $u = $pdo->prepare("INSERT INTO users (email, password, role, name) VALUES (?, ?, 'student', ?)");
    $u->execute([$_POST['email'], $hashed, $_POST['name']]);
    $uid = $pdo->lastInsertId();
    $s = $pdo->prepare("INSERT INTO students (user_id, roll_number, class_id) VALUES (?, ?, ?)");
    $s->execute([$uid, $_POST['roll'], $_POST['class_id']]);
    $pdo->commit();
    echo "<div class='alert alert-success'>Student Registered!</div>";
}
$classes = $pdo->query("SELECT * FROM classes")->fetchAll();
?>
<div class="form-box shadow-sm">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Student Name" required></div>
            <div class="col-md-4"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="col-md-4">
                <select name="class_id" class="form-select" required>
                    <option value="">Assign Class</option>
                    <?php foreach($classes as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-6"><input type="text" name="roll" class="form-control" placeholder="Roll Number" required></div>
            <div class="col-md-6"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            <button type="submit" name="save_student" class="btn btn-danger w-100 mt-2">Register Student</button>
        </div>
    </form>
</div>