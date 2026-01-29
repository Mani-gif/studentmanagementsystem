<?php
session_start();
require_once '../config/database.php';

// Security Check: Only Student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$student_name = $_SESSION['name'];

// 1. Get Student's Class ID and Roll Number
$stmt = $pdo->prepare("SELECT s.id as student_db_id, s.roll_number, c.name as class_name 
                       FROM students s 
                       JOIN classes c ON s.class_id = c.id 
                       WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student_info = $stmt->fetch();
$student_id = $student_info['student_db_id'];

// 2. Fetch Marks grouped by Subject
$stmt = $pdo->prepare("SELECT m.*, sub.name as subject_name, sub.code as subject_code 
                       FROM marks m 
                       JOIN subjects sub ON m.subject_id = sub.id 
                       WHERE m.student_id = ? 
                       ORDER BY m.date DESC");
$stmt->execute([$student_id]);
$marks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Portal - EduPortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --student-green: #16A34A; }
        body { background-color: #f4f7f6; }
        .portal-header { background: white; border-bottom: 2px solid var(--student-green); padding: 15px; }
        .logout-btn { color: var(--student-green); border: 1px solid var(--student-green); }
        .logout-btn:hover { background: var(--student-green); color: white; }
        .mark-card { background: white; border-radius: 15px; border: none; transition: 0.3s; }
        .percentage-badge { background: #dcfce7; color: #166534; font-weight: bold; padding: 5px 12px; border-radius: 20px; }
    </style>
</head>
<body>

    <div class="portal-header d-flex justify-content-between align-items-center shadow-sm">
        <div>
            <h4 class="fw-bold mb-0" style="color: var(--student-green);">Student Portal</h4>
            <p class="text-muted small mb-0">Welcome, <strong><?= htmlspecialchars($student_name) ?></strong> (<?= $student_info['class_name'] ?>)</p>
        </div>
        <a href="../logout.php" class="btn logout-btn px-4 rounded-pill fw-bold"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="bg-white p-3 rounded-4 shadow-sm text-center border-start border-4 border-success">
                    <p class="text-muted small mb-0">Roll Number</p>
                    <h5 class="fw-bold"><?= $student_info['roll_number'] ?></h5>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow text-success"></i> Your Academic Records</h5>

        <?php if(empty($marks)): ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                <p class="mt-2 text-muted">No marks have been uploaded for you yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive bg-white p-3 rounded-4 shadow-sm">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Exam Type</th>
                            <th>Marks Obtained</th>
                            <th>Percentage</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($marks as $m): 
                            $percentage = ($m['marks_obtained'] / $m['max_marks']) * 100;
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= $m['subject_name'] ?></div>
                                <div class="small text-muted"><?= $m['subject_code'] ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= $m['exam_type'] ?></span></td>
                            <td class="fw-bold"><?= $m['marks_obtained'] ?> / <?= $m['max_marks'] ?></td>
                            <td><span class="percentage-badge"><?= number_format($percentage, 1) ?>%</span></td>
                            <td class="small text-muted"><?= date('d M, Y', strtotime($m['date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>