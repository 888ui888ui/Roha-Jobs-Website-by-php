<?php
// IMPORTANT: Adjust the path if db_config.php is not in the parent directory
require_once('../db_config.php'); 
// Define paths for includes
$navbar_path = '../includes/navbar.php';
$footer_path = '../includes/footer.php';

$message = '';
$job = null; // Variable to hold the job data

// --- A. HANDLE JOB FETCH (GET Request) ---
if (isset($_GET['id'])) {
    $job_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($job_id === false || $job_id <= 0) {
        $message = '<div class="alert alert-danger">Invalid job ID provided.</div>';
    } else {
        // Fetch the existing job data using MySQLi Prepared Statement
        $sql = "SELECT * FROM jobs WHERE id = ?";
        
        // Ensure connection is available and prepare statement
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $job_id); // 'i' for integer
            $stmt->execute();
            $result = $stmt->get_result();
            $job = $result->fetch_assoc(); // Fetch the job data as an associative array
            $stmt->close();

            if (!$job) {
                $message = '<div class="alert alert-warning">Job not found.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Database error preparing fetch statement: ' . $conn->error . '</div>';
        }
    }
} 
// --- B. HANDLE FORM SUBMISSION (POST Request) ---
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_id'])) {
    $job_id = filter_input(INPUT_POST, 'job_id', FILTER_VALIDATE_INT);

    // Retrieve and sanitize all fields
    $title = trim($_POST['title'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $job_type = trim($_POST['job_type'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');

    if ($job_id === false || empty($title) || empty($description)) {
        $message = '<div class="alert alert-danger">Invalid data submitted. Please check required fields.</div>';
    } else {
        // Prepare SQL UPDATE statement using MySQLi Prepared Statement
        $sql = "UPDATE jobs SET 
                    title = ?, 
                    company = ?, 
                    location = ?, 
                    job_type = ?, 
                    salary = ?, 
                    category = ?, 
                    description = ?, 
                    contact_number = ?, 
                    deadline = ?
                WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters (s=string, i=integer)
            $stmt->bind_param("sssssssssi", 
                $title, 
                $company, 
                $location, 
                $job_type, 
                $salary, 
                $category, 
                $description, 
                $contact_number, 
                $deadline, 
                $job_id // ID goes last for the WHERE clause
            );

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Job "' . htmlspecialchars($title) . '" updated successfully! <a href="dashboard.php" class="alert-link">Go to Dashboard</a></div>';

                // Re-fetch the updated job data to populate the form fields again
                $sql_refetch = "SELECT * FROM jobs WHERE id = ?";
                $stmt_refetch = $conn->prepare($sql_refetch);
                $stmt_refetch->bind_param("i", $job_id);
                $stmt_refetch->execute();
                $result_refetch = $stmt_refetch->get_result();
                $job = $result_refetch->fetch_assoc();
                $stmt_refetch->close();

            } else {
                $message = '<div class="alert alert-danger">Error updating job: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="alert alert-danger">Database error preparing update statement: ' . $conn->error . '</div>';
        }
    }
} else {
    // If accessed without an ID, redirect to dashboard
    header('Location: dashboard.php');
    exit;
}
// Close MySQLi connection (optional but good practice)
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Job</title>
    <link rel="stylesheet" href="../styles.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .admin-container { max-width: 800px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .btn-primary { background-color: #e9181c; border-color: #e9181c; }
        .btn-primary:hover { background-color: #cc1519; border-color: #cc1519; }
    </style>
</head>
<body>

<?php 
if (file_exists($navbar_path)) {
    include($navbar_path);
}
?>

    <div class="admin-container">
        <h2 class="mb-4" style="color:#e9181c;">Edit Job Opening (ID: <?php echo htmlspecialchars($job['id'] ?? 'N/A'); ?>)</h2>
        
        <?php echo $message; ?>

        <?php if ($job): ?>
        <form method="POST" action="edit_job.php">
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['id']); ?>">
            
            <div class="form-group">
                <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($job['title']); ?>">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="company" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company" name="company" value="<?php echo htmlspecialchars($job['company']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($job['location']); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category" name="category" required value="<?php echo htmlspecialchars($job['category']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="job_type" class="form-label">Job Type</label>
                        <input type="text" class="form-control" id="job_type" name="job_type" value="<?php echo htmlspecialchars($job['job_type']); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="salary" class="form-label">Salary</label>
                        <input type="text" class="form-control" id="salary" name="salary" value="<?php echo htmlspecialchars($job['salary']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="deadline" class="form-label">Application Deadline</label>
                        <input type="date" class="form-control" id="deadline" name="deadline" value="<?php echo htmlspecialchars($job['deadline']); ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($job['contact_number']); ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Job Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="6" required><?php echo htmlspecialchars($job['description']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">💾 Save Changes</button>
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">← Back to Dashboard</a>
        </form>
        <?php endif; ?>

    </div>

<?php 
if (file_exists($footer_path)) {
    include($footer_path);
}
?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>