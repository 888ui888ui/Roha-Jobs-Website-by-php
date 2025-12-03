<?php
// Define paths for includes
$navbar_path = 'includes/navbar.php';
$footer_path = 'includes/footer.php';

// Include database connection (needed for DB operations)
require_once('db_config.php'); 

$job_id = null;
$job_title = "Selected Job";
$message = '';
$message_type = '';
$conn_for_submission = $conn; // Keep a reference to the connection before fetching title

// --- 1. GET JOB ID AND TITLE ---
if (isset($_GET['id'])) {
    $job_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($job_id === false || $job_id <= 0) {
        $message = 'Invalid job ID provided.';
        $message_type = 'danger';
    } else {
        // Fetch job title using MySQLi Prepared Statement
        $sql = "SELECT title FROM jobs WHERE id = ?";
        if ($stmt = $conn_for_submission->prepare($sql)) {
            $stmt->bind_param("i", $job_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $job_title = $row['title'];
            } else {
                $message = 'Job not found.';
                $message_type = 'warning';
            }
            $stmt->close();
        } 
    }
} 

// --- 2. HANDLE FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_id'])) {
    // Collect and sanitize submitted data
    $submitted_job_id = filter_input(INPUT_POST, 'job_id', FILTER_VALIDATE_INT);
    
    // Check required fields (fullName, email, phone, address, qualification, experience)
    if (empty($_POST['fullName']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['address']) || empty($_POST['qualification']) || empty($_POST['experience'])) {
        $message = "Please fill out all required fields.";
        $message_type = "warning";
    } elseif ($submitted_job_id === false || $submitted_job_id <= 0) {
        $message = "Invalid job ID provided during submission.";
        $message_type = "danger";
    } else {
        // Sanitize data
        $full_name = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $qualification = filter_input(INPUT_POST, 'qualification', FILTER_SANITIZE_STRING);
        $experience = filter_input(INPUT_POST, 'experience', FILTER_VALIDATE_INT);
        // Cover letter is optional, so no need for explicit validation, just sanitization
        $cover_letter = filter_input(INPUT_POST, 'coverLetter', FILTER_SANITIZE_STRING) ?? '';
        
        // --- MySQLi INSERTION LOGIC ---
        $insert_sql = "INSERT INTO applications (job_id, full_name, email, phone, current_address, qualification, experience, cover_letter) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn_for_submission->prepare($insert_sql)) {
            $stmt->bind_param("isssssis", 
                $submitted_job_id, $full_name, $email, $phone, $address, $qualification, $experience, $cover_letter
            );

            if ($stmt->execute()) {
                // Fetch the actual job title again for the success message if needed, or use the one fetched earlier
                $success_job_title = $job_title; // Use the title fetched in step 1
                $message = "Thank you! Your application for **{$success_job_title}** has been submitted successfully. We will be in touch shortly.";
                $message_type = "success";
            } else {
                $message = "Error submitting application. Please try again. Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } else {
            $message = "Database error: Could not prepare application statement.";
            $message_type = "danger";
        }
    }
}
$conn_for_submission->close(); // Close connection after all database operations

// ... Rest of the HTML structure remains the same
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for <?php echo htmlspecialchars($job_title); ?></title>
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .apply-container { max-width: 800px; margin: 50px auto; padding: 30px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        .btn-apply { background-color: #e9181c; border-color: #e9181c; }
        .btn-apply:hover { background-color: #cc1519; border-color: #cc1519; }
        .required-star { color: #e9181c; }
    </style>
</head>
<body>

<?php 
// Include Navbar
if (file_exists($navbar_path)) {
    include($navbar_path);
}
?>

    <div class="container">
        <div class="apply-container">
            <h2 class="text-center mb-4" style="color:#e9181c;">Application for: <?php echo htmlspecialchars($job_title); ?></h2>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($job_id !== null && $message_type !== 'danger' && $message_type !== 'warning'): // Only show form if job is valid ?>
            <form method="POST" action="apply_job.php?id=<?php echo $job_id; ?>">
                
                <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">

                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name <span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address <span class="required-star">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number <span class="required-star">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Current Address <span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="qualification" class="form-label">Highest Qualification <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="qualification" name="qualification" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="experience" class="form-label">Years of Relevant Experience <span class="required-star">*</span></label>
                        <input type="number" class="form-control" id="experience" name="experience" min="0" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="coverLetter" class="form-label">Cover Letter/Message (Optional)</label>
                    <textarea class="form-control" id="coverLetter" name="coverLetter" rows="5" placeholder="Tell us why you are the perfect fit for this role..."></textarea>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-lg text-white btn-apply">🚀 Submit Application</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>

<?php 
// Include Footer
if (file_exists($footer_path)) {
    include($footer_path);
}
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>