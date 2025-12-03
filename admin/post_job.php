<?php
// IMPORTANT: Adjust the path if db_config.php is not in the parent directory
require_once('../db_config.php'); 
// Define paths for includes (Go UP one directory: ../)
$navbar_path = '../includes/navbar.php';
$footer_path = '../includes/footer.php';
// Initialize a variable to hold messages
$message = '';
$message_type = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Sanitize and Validate Input
    $title = trim($_POST['title'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $job_type = trim($_POST['job_type'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    
    // Basic validation
    if (empty($title) || empty($company) || empty($description)) {
        $message = "Please fill in all required fields (Title, Company, Description).";
        $message_type = "danger";
    } else {
        // 2. Prepare the INSERT query (MySQLi Prepared Statements)
        $sql = "INSERT INTO jobs (title, company, location, job_type, salary, category, description, deadline, contact_number) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Line 34 was where the error occurred before:
        if ($stmt = $conn->prepare($sql)) {
            
            // 3. Bind Parameters
            // s = string, d = double, i = integer, b = blob
            $stmt->bind_param("sssssssss", 
                $title, 
                $company, 
                $location, 
                $job_type, 
                $salary, 
                $category, 
                $description, 
                $deadline, 
                $contact_number
            );

            // 4. Execute the statement
            if ($stmt->execute()) {
                $message = "Success! The job titled '{$title}' has been posted.";
                $message_type = "success";
                
                // Optional: Clear form data after successful submission
                $_POST = array(); 
            } else {
                $message = "Error posting job: " . $stmt->error;
                $message_type = "danger";
            }
            
            // 5. Close the statement
            $stmt->close();
        } else {
            $message = "Database error preparing statement: " . $conn->error;
            $message_type = "danger";
        }
    }
}

// Close MySQLi connection (optional but good practice)
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job - Admin</title>
    <link rel="stylesheet" href="../styles.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .form-container { max-width: 800px; margin: 50px auto; padding: 30px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<?php 
if (file_exists($navbar_path)) {
    include($navbar_path);
}
?>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4" style="color:#e9181c;">Post a New Job Opening</h2>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                
                <div class="mb-3">
                    <label for="title" class="form-label">Job Title *</label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="company" class="form-label">Company Name *</label>
                    <input type="text" class="form-control" id="company" name="company" required value="<?php echo htmlspecialchars($_POST['company'] ?? ''); ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="job_type" class="form-label">Job Type</label>
                        <select class="form-select" id="job_type" name="job_type">
                            <option value="Full-Time" <?php if (isset($_POST['job_type']) && $_POST['job_type'] == 'Full-Time') echo 'selected'; ?>>Full-Time</option>
                            <option value="Part-Time" <?php if (isset($_POST['job_type']) && $_POST['job_type'] == 'Part-Time') echo 'selected'; ?>>Part-Time</option>
                            <option value="Contract" <?php if (isset($_POST['job_type']) && $_POST['job_type'] == 'Contract') echo 'selected'; ?>>Contract</option>
                            <option value="Remote" <?php if (isset($_POST['job_type']) && $_POST['job_type'] == 'Remote') echo 'selected'; ?>>Remote</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="salary" class="form-label">Salary Range (e.g., 20k - 30k ETB)</label>
                        <input type="text" class="form-control" id="salary" name="salary" value="<?php echo htmlspecialchars($_POST['salary'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Category *</label>
                        <input type="text" class="form-control" id="category" name="category" required value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Job Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="6" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="deadline" class="form-label">Application Deadline</label>
                        <input type="date" class="form-control" id="deadline" name="deadline" value="<?php echo htmlspecialchars($_POST['deadline'] ?? ''); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="contact_number" class="form-label">Contact Number (Optional)</label>
                        <input type="tel" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($_POST['contact_number'] ?? ''); ?>">
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-lg text-white" style="background-color:#e9181c;">Post Job</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="../index.php" class="btn btn-outline-secondary">Back to Homepage</a>
            </div>
        </div>
    </div>

<?php 
if (file_exists($footer_path)) {
    include($footer_path);
}
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>