<?php
// Path assumes this file is in an 'admin' subdirectory
require_once('../db_config.php');

// Check if the form was submitted with a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if ID is provided and is a valid integer
    $message_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$message_id) {
        // Redirect if ID is invalid
        header("Location: dashboard.php?status=error_id");
        exit();
    }

    // Check database connection
    if ($conn->connect_error) {
        header("Location: dashboard.php?status=db_error");
        exit();
    }

    // Prepare the DELETE statement to prevent SQL injection
    $sql = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Handle preparation error
        error_log("SQL Prepare Error: " . $conn->error);
        header("Location: dashboard.php?status=db_error");
        exit();
    }

    // Bind the ID parameter (i for integer)
    $stmt->bind_param("i", $message_id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Success: Message deleted
            $stmt->close();
            $conn->close();
            header("Location: dashboard.php?status=message_deleted");
            exit();
        } else {
            // Not found: No row was deleted
            $stmt->close();
            $conn->close();
            header("Location: dashboard.php?status=not_found");
            exit();
        }
    } else {
        // Handle execution error
        error_log("SQL Execute Error: " . $stmt->error);
        $stmt->close();
        $conn->close();
        header("Location: dashboard.php?status=error_delete");
        exit();
    }

} else {
    // If accessed directly without POST data
    header("Location: dashboard.php");
    exit();
}
?>