<?php
session_start();
require_once '../config/database.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

$id = $_GET['id'] ?? null;
$tab = $_GET['tab'] ?? null;

if (!$id || !$tab) { header("Location: dashboard.php"); exit(); }

// 1. Fetch current data
if ($tab == 'faculty' || $tab == 'students') {
    $stmt = $pdo->prepare("SELECT t.*, u.name, u.email FROM $tab t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
} else {
    $stmt = $pdo->prepare("SELECT * FROM $tab WHERE id = ?");
}
$stmt->execute([$id]);
$item = $stmt->fetch();

// 2. Update Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $identifier = $_POST['identifier'];

    try {
        if ($tab == 'faculty' || $tab == 'students') {
            $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?")->execute([$name, $identifier, $item['user_id']]);
        } else {
            // For dept, courses, classes, subjects
            $pdo->prepare("UPDATE $tab SET name = ?, code = ? WHERE id = ?")->execute([$name, $identifier, $id]);
        }
        header("Location: dashboard.php?tab=$tab&msg=Updated successfully");
        exit();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .edit-card { max-width: 450px; margin: 80px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .btn-save { background-color: #DC2626; color: white; border: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-card">
            <h5 class="fw-bold mb-4">Edit <?= ucfirst(rtrim($tab, 's')) ?></h5>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Identifier (Code/Email)</label>
                    <?php $val = $item['code'] ?? $item['roll_number'] ?? $item['employee_id'] ?? $item['email'] ?? ''; ?>
                    <input type="text" name="identifier" class="form-control" value="<?= htmlspecialchars($val) ?>" required>
                </div>
                <button type="submit" class="btn btn-save w-100 py-2">Update Changes</button>
                <a href="dashboard.php?tab=<?= $tab ?>" class="btn btn-link w-100 mt-2 text-muted">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>