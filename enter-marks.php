<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: ../login.php"); exit();
}

$faculty_user_id = $_SESSION['user_id'];

// Get faculty's subjects for the dropdown
$stmt = $pdo->prepare("SELECT subjects.id, subjects.name, subjects.class_id, classes.name as class_name 
                       FROM subjects 
                       JOIN classes ON subjects.class_id = classes.id 
                       JOIN faculty ON subjects.faculty_id = faculty.id
                       WHERE faculty.user_id = ?");
$stmt->execute([$faculty_user_id]);
$subjects = $stmt->fetchAll();

// Logic to fetch students when a subject is selected
$students = [];
$selected_subject = "";
if (isset($_GET['subject_id'])) {
    $selected_subject = $_GET['subject_id'];
    // Find class_id for this subject
    $stmt = $pdo->prepare("SELECT class_id FROM subjects WHERE id = ?");
    $stmt->execute([$selected_subject]);
    $sub_info = $stmt->fetch();
    
    if($sub_info) {
        $stmt = $pdo->prepare("SELECT s.id, u.name, s.roll_number 
                               FROM students s 
                               JOIN users u ON s.user_id = u.id 
                               WHERE s.class_id = ?");
        $stmt->execute([$sub_info['class_id']]);
        $students = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Marks - EduPortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm border-0 p-4">
            <h4 class="fw-bold text-primary mb-4">Enter Student Marks</h4>
            
            <form method="GET" class="mb-4">
                <label class="small fw-bold">Select Subject</label>
                <select name="subject_id" class="form-select mb-2" onchange="this.form.submit()">
                    <option value="">-- Choose Subject --</option>
                    <?php foreach($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($selected_subject == $s['id']) ? 'selected' : '' ?>>
                            <?= $s['name'] ?> (<?= $s['class_name'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if($selected_subject && !empty($students)): ?>
                <form action="save_marks.php" method="POST">
                    <input type="hidden" name="subject_id" value="<?= $selected_subject ?>">
                    <div class="mb-3">
                        <input type="text" name="exam_type" class="form-control" placeholder="Exam Type (Ex: Internal-1, Final)" required>
                    </div>
                    
                    <table class="table table-bordered bg-white">
                        <thead class="table-dark">
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Marks Obtained</th>
                                <th>Max Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $st): ?>
                            <tr>
                                <td><?= $st['roll_number'] ?></td>
                                <td><?= $st['name'] ?></td>
                                <td><input type="number" name="marks[<?= $st['id'] ?>]" class="form-control" required></td>
                                <td><input type="number" name="max_marks[<?= $st['id'] ?>]" class="form-control" value="100" required></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary w-100">Save All Marks</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>