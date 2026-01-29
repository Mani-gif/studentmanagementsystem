<?php
session_start();
require_once '../../config/database.php'; // Path check pannikonga

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['dept_name'];
    $code = $_POST['dept_code'];

    try {
        $stmt = $pdo->prepare("INSERT INTO departments (name, code) VALUES (?, ?)");
        $stmt->execute([$name, $code]);
        
        // Success aana dashboard-ku thirumba poidum
        header("Location: ../dashboard.php?tab=departments&msg=success");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>