<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_dept'])) {
    $stmt = $pdo->prepare("INSERT INTO departments (name, code) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['code']]);
    echo "<div class='alert alert-success py-2'>Department Added!</div>";
}
?>

<style>
    /* Button Hover Fix */
    .btn-save-dept {
        background-color: #DC2626;
        color: white;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-save-dept:hover {
        background-color: #991B1B !important; /* Darker Red on Hover */
        color: #ffffff !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transform: translateY(-1px);
    }

    .form-box-custom {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #eee;
    }
</style>

<div class="form-box-custom shadow-sm mb-4">
    <h6 class="fw-bold mb-3" style="color: #DC2626;">Add New Department</h6>
    <form method="POST">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <input type="text" name="name" class="form-control" placeholder="Department Name (e.g. Commerce)" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="code" class="form-control" placeholder="Code (e.g. COM)" required>
            </div>
            <div class="col-md-3">
                <button type="submit" name="save_dept" class="btn btn-save-dept w-100 fw-bold">
                    <i class="bi bi-plus-circle me-1"></i> Save Dept
                </button>
            </div>
        </div>
    </form>
</div>