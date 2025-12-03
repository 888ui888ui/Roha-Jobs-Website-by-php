<?php
// 1. INCLUDE DATABASE CONNECTION
require_once('../db_config.php');

// 2. CHECK FOR POST REQUEST AND APPLICATION ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    // Sanitize the ID to ensure it's an integer
    $app_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($app_id === false || $app_id <= 0) {
        header('Location: manage_applications.php?status=error');
        exit;
    }

    // 3. PREPARE SQL DELETE STATEMENT
    $sql = "DELETE FROM applications WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $app_id);
        
        // 4. EXECUTE THE STATEMENT
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Location: manage_applications.php?status=deleted');
            } else {
                 header('Location: manage_applications.php?status=error'); // Not found or already deleted
            }
            $stmt->close();
            $conn->close();
            exit;
        } else {
            // Execution Failure
            $stmt->close();
            $conn->close();
            header('Location: manage_applications.php?status=error');
            exit;
        }
    } else {
        // Preparation Failure
        $conn->close();
        header('Location: manage_applications.php?status=error');
        exit;
    }
} else {
    // If accessed directly or without the ID
    header('Location: manage_applications.php');
    exit;
}
?>