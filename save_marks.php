<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];
    $exam_type = $_POST['exam_type'];
    $marks_array = $_POST['marks']; // Student ID => Marks Obtained
    $max_marks_array = $_POST['max_marks']; // Student ID => Max Marks
    $date = date('Y-m-d');

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO marks (student_id, subject_id, exam_type, marks_obtained, max_marks, date) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($marks_array as $student_id => $marks_obtained) {
            $max_marks = $max_marks_array[$student_id];
            
            // Basic Validation: Marks obtained max marks-ai vida athigama iruka koodathu
            if ($marks_obtained > $max_marks) {
                throw new Exception("Marks obtained cannot be greater than Max marks!");
            }

            $stmt->execute([$student_id, $subject_id, $exam_type, $marks_obtained, $max_marks, $date]);
        }

        $pdo->commit();
        header("Location: dashboard.php?msg=Marks saved successfully!");
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>