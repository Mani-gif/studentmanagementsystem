<?php
require_once '../config/database.php';

// 1. SAVE CLASS LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $name = $_POST['class_name']; 
    $year = $_POST['year']; 
    $semester = $_POST['semester'];
    $course_id = $_POST['course_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO classes (name, year, semester, course_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $year, $semester, $course_id]);
        echo "<div class='alert alert-success alert-dismissible fade show'>Class Added!</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// 2. FETCH COURSES (Dropdown-ku)
$courses = $pdo->query("SELECT id, name FROM courses")->fetchAll();

// 3. FETCH CLASSES (Table-ku with JOIN)
$classes = $pdo->query("SELECT classes.*, courses.name as course_name 
                        FROM classes 
                        JOIN courses ON classes.course_id = courses.id 
                        ORDER BY id DESC")->fetchAll();
?>

<div class="form-container mb-4">
    <h5 class="fw-bold mb-3" style="color: var(--admin-red);">Add New Class</h5>
    <form method="POST" action="">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" name="class_name" class="form-control" placeholder="Class Name (Ex: Section A)" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="year" class="form-control" placeholder="Year (2026)" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="semester" class="form-control" placeholder="Semester" required>
            </div>
            <div class="col-md-3">
                <select name="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"><?= $course['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_class" class="btn btn-danger w-100 fw-bold">Add Class</button>
            </div>
        </div>
    </form>
</div>

<div class="table-responsive shadow-sm">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Class Name</th>
                <th>Year/Sem</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($classes as $cls): ?>
            <tr>
                <td><?= htmlspecialchars($cls['name']) ?></td>
                <td><?= htmlspecialchars($cls['year']) ?> - Sem <?= htmlspecialchars($cls['semester']) ?></td>
                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($cls['course_name']) ?></span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>