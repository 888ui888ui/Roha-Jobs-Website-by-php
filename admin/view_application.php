<?php
// 1. INCLUDE DATABASE CONNECTION
require_once('../db_config.php');

// Define Paths to common files
$navbar_path = '../includes/navbar.php';

$application = null;
$message = '';

// Check for ID parameter
if (isset($_GET['id'])) {
    $app_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($app_id === false || $app_id <= 0) {
        $message = '<div class="alert alert-danger mt-3">Invalid application ID.</div>';
    } else {
        // 2. FETCH APPLICATION DETAILS
        $sql = "SELECT a.*, j.title AS job_title, j.company 
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                WHERE a.id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $app_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $application = $row;
            } else {
                $message = '<div class="alert alert-warning mt-3">Application not found.</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="alert alert-danger mt-3">Database error fetching details.</div>';
        }
    }
} else {
    header('Location: manage_applications.php');
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .detail-container { max-width: 800px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .detail-item strong { display: inline-block; min-width: 150px; }
        .card-header-app { background-color: #e9181c; color: white; }
    </style>
</head>
<body>

    <div class="detail-container">
        <div class="card">
            <div class="card-header card-header-app">
                <h4 class="mb-0">Application Details</h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>

                <?php if ($application): ?>
                    <h5 class="card-title mb-4">Applying for: **<?php echo htmlspecialchars($application['job_title']); ?>** at <?php echo htmlspecialchars($application['company']); ?></h5>
                    
                    <hr>

                    <div class="detail-item mb-2"><strong>Applicant Name:</strong> <?php echo htmlspecialchars($application['full_name']); ?></div>
                    <div class="detail-item mb-2"><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($application['email']); ?>"><?php echo htmlspecialchars($application['email']); ?></a></div>
                    <div class="detail-item mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($application['phone']); ?></div>
                    <div class="detail-item mb-2"><strong>Address:</strong> <?php echo htmlspecialchars($application['current_address']); ?></div>
                    <div class="detail-item mb-2"><strong>Highest Qualification:</strong> <?php echo htmlspecialchars($application['qualification']); ?></div>
                    <div class="detail-item mb-2"><strong>Years Experience:</strong> <?php echo htmlspecialchars($application['experience']); ?> years</div>
                    <div class="detail-item mb-4"><strong>Applied On:</strong> <?php echo date('F j, Y, g:i a', strtotime($application['applied_at'])); ?></div>
                    
                    <hr>
                    
                    <h6>Cover Letter/Message:</h6>
                    <p class="border p-3 bg-light rounded"><?php echo nl2br(htmlspecialchars($application['cover_letter'] ?? 'No cover letter provided.')); ?></p>
                <?php endif; ?>

            </div>
            <div class="card-footer text-end">
                <a href="manage_applications.php" class="btn btn-secondary">← Back to Applications</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>