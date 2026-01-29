<?php
require_once '../config/database.php';

// 1. DATA SAVE LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $name = $_POST['course_name'];
    $code = $_POST['course_code'];
    $dept_id = $_POST['dept_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO courses (name, code, department_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $code, $dept_id]);
        echo "<div class='alert alert-success'>Course added successfully!</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// 2. FETCH DEPARTMENTS (Dropdown-la kaata)
$dept_stmt = $pdo->query("SELECT * FROM departments");
$all_depts = $dept_stmt->fetchAll();

// 3. FETCH COURSES (Table-la kaata)
// Ingathan JOIN use panrom, appothan Dept Name-ah edukka mudiyum
$course_stmt = $pdo->query("SELECT courses.*, departments.name as dept_name 
                            FROM courses 
                            LEFT JOIN departments ON courses.department_id = departments.id 
                            ORDER BY id DESC");
$courses = $course_stmt->fetchAll();
?>

<div class="form-container mb-4 p-3 bg-white shadow-sm rounded">
    <h5 class="fw-bold mb-3" style="color: var(--admin-red);">Add New Course</h5>
    <form method="POST" action="">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="course_name" class="form-control" placeholder="Course Name (e.g. B.Sc CS)" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="course_code" class="form-control" placeholder="Course Code" required>
            </div>
            <div class="col-md-3">
                <select name="dept_id" class="form-select" required>
                    <option value="">Select Department</option>
                    <?php foreach($all_depts as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_course" class="btn btn-danger w-100 fw-bold">Add</button>
            </div>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-hover bg-white rounded shadow-sm">
        <thead class="table-light">
            <tr>
                <th>Course Name</th>
                <th>Code</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($courses as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['code']) ?></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($c['dept_name']) ?></span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>