<?php
session_start();
require_once '../config/database.php';

$faculty_user_id = $_SESSION['user_id'];

// Get faculty's assigned subjects
$stmt = $pdo->prepare("SELECT s.id, s.name, c.name as class_name 
                       FROM subjects s 
                       JOIN classes c ON s.class_id = c.id 
                       JOIN faculty f ON s.faculty_id = f.id
                       WHERE f.user_id = ?");
$stmt->execute([$faculty_user_id]);
$subjects = $stmt->fetchAll();

$report_data = [];
if (isset($_GET['view_subject'])) {
    $sub_id = $_GET['view_subject'];
    $stmt = $pdo->prepare("SELECT m.*, u.name as student_name, s.roll_number 
                           FROM marks m 
                           JOIN students s ON m.student_id = s.id 
                           JOIN users u ON s.user_id = u.id 
                           WHERE m.subject_id = ? 
                           ORDER BY m.date DESC");
    $stmt->execute([$sub_id]);
    $report_data = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Faculty Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h4 class="fw-bold mb-4">Academic Reports</h4>
        
        <div class="card p-3 mb-4 shadow-sm border-0">
            <form method="GET">
                <label class="small fw-bold">Select Subject to View Report</label>
                <div class="d-flex gap-2">
                    <select name="view_subject" class="form-select">
                        <option value="">-- Choose Subject --</option>
                        <?php foreach($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['name'] ?> (<?= $s['class_name'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary px-4">View</button>
                </div>
            </form>
        </div>

        <?php if(!empty($report_data)): ?>
            <div class="table-responsive bg-white p-3 rounded shadow-sm">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Roll No</th>
                            <th>Student</th>
                            <th>Exam Type</th>
                            <th>Score</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($report_data as $r): 
                            $percentage = ($r['marks_obtained'] / $r['max_marks']) * 100;
                        ?>
                        <tr>
                            <td><?= $r['roll_number'] ?></td>
                            <td class="fw-bold"><?= $r['student_name'] ?></td>
                            <td><?= $r['exam_type'] ?></td>
                            <td><?= $r['marks_obtained'] ?> / <?= $r['max_marks'] ?></td>
                            <td><span class="badge bg-info text-dark"><?= number_format($percentage, 1) ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>