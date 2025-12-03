<?php
// 1. INCLUDE DATABASE CONNECTION
// Requires the MySQLi connection object $conn from db_config.php
require_once('../db_config.php');

// 2. CHECK FOR POST REQUEST AND JOB ID
// Deletion should always use POST for security and idempotency
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    
    // Sanitize the ID to ensure it's a valid integer
    $job_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($job_id === false || $job_id <= 0) {
        // Handle invalid ID case
        header('Location: dashboard.php?status=error_id');
        exit;
    }

    // 3. PREPARE SQL DELETE STATEMENT (MySQLi)
    $sql = "DELETE FROM jobs WHERE id = ?";
    
    // Prepare the statement using the MySQLi connection $conn
    if ($stmt = $conn->prepare($sql)) {
        
        // Bind the ID parameter ('i' for integer)
        $stmt->bind_param("i", $job_id);
        
        // 4. EXECUTE THE STATEMENT
        if ($stmt->execute()) {
            // Success: Check if any row was actually deleted
            if ($stmt->affected_rows > 0) {
                header('Location: dashboard.php?status=deleted');
            } else {
                header('Location: dashboard.php?status=not_found');
            }
            $stmt->close();
            $conn->close();
            exit;
        } else {
            // Execution Failure
            $error_message = $stmt->error;
            $stmt->close();
            $conn->close();
            
            // In a real application, you would log this error.
            header('Location: dashboard.php?status=error_delete');
            exit;
        }
    } else {
        // Preparation Failure
        $conn->close();
        header('Location: dashboard.php?status=db_error');
        exit;
    }

} else {
    // If accessed directly or without the ID, redirect
    header('Location: dashboard.php');
    exit;
}
?>