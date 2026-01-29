<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_subject'])) {
    $stmt = $pdo->prepare("INSERT INTO subjects (name, code, class_id, faculty_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['code'], $_POST['class_id'], $_POST['faculty_id']]);
    echo "<div class='alert alert-success'>Subject Added!</div>";
}

$classes = $pdo->query("SELECT * FROM classes")->fetchAll();
$faculty = $pdo->query("SELECT f.id, u.name FROM faculty f JOIN users u ON f.user_id = u.id")->fetchAll();
?>
<div class="form-box shadow-sm">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Subject Name" required></div>
            <div class="col-md-2"><input type="text" name="code" class="form-control" placeholder="Code" required></div>
            <div class="col-md-3">
                <select name="class_id" class="form-select" required>
                    <option value="">Select Class</option>
                    <?php foreach($classes as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="faculty_id" class="form-select" required>
                    <option value="">Faculty</option>
                    <?php foreach($faculty as $f) echo "<option value='{$f['id']}'>{$f['name']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" name="save_subject" class="btn btn-danger w-100">Save</button></div>
        </div>
    </form>
</div>