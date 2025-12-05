<?php
// 1. INCLUDE DATABASE CONNECTION (for job and message retrieval)
// Path assumes this file is in an 'admin' subdirectory
require_once('../db_config.php');

// Define paths for includes
$navbar_path = '../includes/navbar.php';
$footer_path = '../includes/footer.php';

$message = '';
$jobs = [];
$messages = []; // New array for contact messages

// --- A. CHECK FOR STATUS MESSAGE ---
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'deleted':
            $message = '<div class="alert alert-success mt-3">✅ Job successfully deleted.</div>';
            break;
        case 'message_deleted':
            $message = '<div class="alert alert-success mt-3">✅ Contact message successfully deleted.</div>';
            break;
        case 'error_id':
            $message = '<div class="alert alert-warning mt-3">⚠️ Invalid ID provided.</div>';
            break;
        case 'not_found':
            $message = '<div class="alert alert-warning mt-3">⚠️ Item not found or already deleted.</div>';
            break;
        case 'error_delete':
        case 'db_error':
            $message = '<div class="alert alert alert-danger mt-3">❌ An error occurred during the database operation.</div>';
            break;
    }
}

// --- B. FETCH ALL JOBS (MySQLi) ---
$sql_jobs = 'SELECT id, title, company, location, category, posted_at, deadline FROM jobs ORDER BY posted_at DESC';

if ($result_jobs = $conn->query($sql_jobs)) {
    if ($result_jobs->num_rows > 0) {
        while ($row = $result_jobs->fetch_assoc()) {
            $jobs[] = $row;
        }
    }
    $result_jobs->free();
} else {
    $message .= '<div class="alert alert-danger">Error fetching jobs: ' . $conn->error . '</div>';
}

// --- C. FETCH ALL CONTACT MESSAGES (MySQLi) ---
// Assuming contact_messages table has: id, full_name, email, message_content, submission_date
$sql_messages = 'SELECT id, full_name, email, message_content, submitted_at FROM contact_messages ORDER BY submitted_at DESC';

if ($result_messages = $conn->query($sql_messages)) {
    if ($result_messages->num_rows > 0) {
        while ($row = $result_messages->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    $result_messages->free();
} else {
    $message .= '<div class="alert alert-danger">Error fetching messages: ' . $conn->error . '</div>';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .admin-container { max-width: 1400px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
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
        <h2 class="mb-4" style="color:#e9181c;">Admin Dashboard</h2>
        
        <?php echo $message; // Display status messages ?>

        <!-- Bootstrap Tabs for Navigation -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs-tab-pane" type="button" role="tab" aria-controls="jobs-tab-pane" aria-selected="true">
                    <i class="fas fa-briefcase me-1"></i> Manage Jobs
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages-tab-pane" type="button" role="tab" aria-controls="messages-tab-pane" aria-selected="false">
                    <i class="fas fa-envelope me-1"></i> Manage Contact Messages
                    <?php if (count($messages) > 0): ?>
                        <span class="badge bg-danger ms-2"><?php echo count($messages); ?></span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            
            <!-- Tab Pane 1: Manage Jobs -->
            <div class="tab-pane fade show active" id="jobs-tab-pane" role="tabpanel" aria-labelledby="jobs-tab" tabindex="0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="m-0">Job Listings</h4>
                    <a href="post_job.php" class="btn btn-primary">➕ Post New Job</a>
                </div>
                
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
                                            <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-info me-2"><i class="fas fa-edit"></i> Edit</a>
                                            
                                            <!-- Delete Job Form -->
                                            <form method="POST" action="delete_job.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete the job: <?php echo htmlspecialchars($job['title']); ?>?');">
                                                <input type="hidden" name="id" value="<?php echo $job['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
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
            
            <!-- Tab Pane 2: Manage Contact Messages -->
            <div class="tab-pane fade" id="messages-tab-pane" role="tabpanel" aria-labelledby="messages-tab" tabindex="0">
                <h4 class="mb-4">Received Contact Messages</h4>

                <?php if (count($messages) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th style="min-width: 300px;">Message Content</th>
                                    <th>Date Sent</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $msg): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($msg['id']); ?></td>
                                        <td><?php echo htmlspecialchars($msg['full_name']); ?></td>
                                        <td><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                                        <td><?php echo nl2br(htmlspecialchars(substr($msg['message_content'], 0, 100)) . (strlen($msg['message_content']) > 100 ? '...' : '')); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($msg['submitted_at'])); ?></td>
                                        <td class="text-center">
                                            <!-- Delete Message Form (uses the new delete_message.php script) -->
                                            <form method="POST" action="delete_message.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this message from <?php echo htmlspecialchars($msg['full_name']); ?>?');">
                                                <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">There are no contact messages at this time.</div>
                <?php endif; ?>
            </div>

        </div> <!-- End tab-content -->
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