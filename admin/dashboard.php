<?php
// 1. INCLUDE DATABASE CONNECTION (for job retrieval)
require_once('../db_config.php');

// Define paths for includes
$navbar_path = '../includes/navbar.php';
$footer_path = '../includes/footer.php';

$message = '';
$jobs = [];

// --- A. CHECK FOR STATUS MESSAGE ---
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'deleted':
            $message = '<div class="alert alert-success mt-3">✅ Job successfully deleted.</div>';
            break;
        case 'error_id':
            $message = '<div class="alert alert-warning mt-3">⚠️ Invalid Job ID provided.</div>';
            break;
        case 'not_found':
            $message = '<div class="alert alert-warning mt-3">⚠️ Job not found or already deleted.</div>';
            break;
        case 'error_delete':
        case 'db_error':
            $message = '<div class="alert alert-danger mt-3">❌ An error occurred during the database operation.</div>';
            break;
    }
}

// --- B. FETCH ALL JOBS (MySQLi) ---
// Note: Using query() is acceptable for simple SELECT without user input
$sql = 'SELECT id, title, company, location, category, posted_at, deadline FROM jobs ORDER BY posted_at DESC';

if ($result = $conn->query($sql)) {
    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Fetch all jobs into the $jobs array
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
    }
    $result->free(); // Free the result set
} else {
    // Database error on query
    $message .= '<div class="alert alert-danger">Error fetching jobs: ' . $conn->error . '</div>';
}

// Close MySQLi connection (optional but good practice)
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .admin-container { max-width: 1200px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .table th, .table td { vertical-align: middle; }
        .btn-primary { background-color: #e9181c; border-color: #e9181c; }
        .btn-primary:hover { background-color: #cc1519; border-color: #cc1519; }
        .table-dark { background-color: #343a40 !important; color: white; }
    </style>
</head>
<body>
<?php 
// Include Navbar
if (file_exists($navbar_path)) {
    include($navbar_path);
}
?>

    <div class="admin-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color:#e9181c;">Admin Dashboard: Manage Jobs</h2>
            <a href="post_job.php" class="btn btn-primary">➕ Post New Job</a>
        </div>
        
        <?php echo $message; // Display status messages ?>

        <?php if (count($jobs) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Posted On</th>
                            <th>Deadline</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['id']); ?></td>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo htmlspecialchars($job['company']); ?></td>
                                <td><?php echo htmlspecialchars($job['location']); ?></td>
                                <td><?php echo htmlspecialchars($job['category']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($job['posted_at'])); ?></td>
                                <td><?php echo htmlspecialchars($job['deadline']); ?></td>
                                <td class="text-center">
                                    <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-info me-2">Edit</a>
                                    
                                    <form method="POST" action="delete_job.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete the job: <?php echo htmlspecialchars($job['title']); ?>?');">
                                        <input type="hidden" name="id" value="<?php echo $job['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No jobs have been posted yet.</div>
        <?php endif; ?>

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