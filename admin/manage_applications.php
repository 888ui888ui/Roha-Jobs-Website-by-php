<?php
// 1. INCLUDE DATABASE CONNECTION
require_once('../db_config.php');

// Define Paths to common files
$navbar_path = '../includes/navbar.php';

$message = '';
$applications = [];

// Check for status messages
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'deleted') {
        $message = '<div class="alert alert-success mt-3">✅ Application successfully deleted.</div>';
    } elseif ($_GET['status'] == 'error') {
        $message = '<div class="alert alert-danger mt-3">❌ An error occurred during the operation.</div>';
    }
}

// 2. FETCH ALL APPLICATIONS
$sql = "SELECT a.id, a.full_name, a.email, a.applied_at, j.title AS job_title, j.company 
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        ORDER BY a.applied_at DESC";

if ($result = $conn->query($sql)) {
    $applications = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
} else {
    $message .= '<div class="alert alert-danger mt-3">Error fetching applications: ' . $conn->error . '</div>';
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Applications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .admin-container { max-width: 1400px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-detail { background-color: #e9181c; border-color: #e9181c; color: white; }
        .btn-detail:hover { background-color: #cc1519; border-color: #cc1519; color: white; }
    </style>
</head>
<body>

<?php // include($navbar_path); // Assuming you have an admin navbar ?>

    <div class="admin-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color:#e9181c;">Manage Job Applications</h2>
            <a href="dashboard.php" class="btn btn-secondary">← Back to Job List</a>
        </div>
        
        <?php echo $message; ?>

        <?php if (count($applications) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Applicant Name</th>
                            <th>Email</th>
                            <th>Applied For (Job Title)</th>
                            <th>Company</th>
                            <th>Applied At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['id']); ?></td>
                                <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($app['email']); ?></td>
                                <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($app['company']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($app['applied_at'])); ?></td>
                                <td>
                                    <a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-detail me-2">View</a>
                                    
                                    <form method="POST" action="delete_application.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete the application from <?php echo htmlspecialchars($app['full_name']); ?>?');">
                                        <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No job applications found yet.</div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>