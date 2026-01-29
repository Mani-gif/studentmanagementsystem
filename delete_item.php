<?php
session_start();
require_once '../../config/database.php';

if (isset($_GET['id']) && isset($_GET['tab'])) {
    $id = $_GET['id'];
    $tab = $_GET['tab'];

    try {
        if ($tab == 'faculty' || $tab == 'students') {
            // User-ai delete panna munnadi avanga user_id-ah fetch pannanum
            $stmt = $pdo->prepare("SELECT user_id FROM $tab WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            
            if ($user) {
                // First: Specific table-la irunthu delete pannanum (Child)
                $pdo->prepare("DELETE FROM $tab WHERE id = ?")->execute([$id]);
                // Second: Main users table-la irunthu delete pannanum (Parent)
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user['user_id']]);
            }
        } else {
            // Normal table delete (Department, Course, etc.)
            $stmt = $pdo->prepare("DELETE FROM $tab WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        header("Location: ../dashboard.php?tab=$tab&msg=Deleted successfully");
    } catch (PDOException $e) {
        header("Location: ../dashboard.php?tab=$tab&error=Cannot delete: Record is linked to other data");
    }
}
?>