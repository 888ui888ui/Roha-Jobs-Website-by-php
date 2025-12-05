<?php
// CRITICAL: This line requires a file named 'db_config.php' to be present 
// in the same directory. This file must contain the connection logic 
// (e.g., $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);).
// Ensure this file exists and provides the $conn object.
require_once('db_config.php'); 

// Define Paths to common files
$navbar_path = 'includes/navbar.php';
$footer_path = 'includes/footer.php';

$message_status = ''; // Variable to hold the success/error status message

// 1. CHECK IF THE FORM WAS SUBMITTED
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. RETRIEVE AND SANITIZE FORM DATA
    $name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message_content = filter_input(INPUT_POST, 'message_content', FILTER_SANITIZE_SPECIAL_CHARS);

    // Basic validation
    if (empty($name) || empty($email) || empty($message_content) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_status = '<div class="alert alert-danger">Please fill in all fields with valid information.</div>';
    } else {
        
        // --- DATABASE INSERTION LOGIC (Saves to the contact_messages table) ---
        
        // Check if database connection is available
        if (!isset($conn) || $conn->connect_error) {
             $message_status = '<div class="alert alert-danger">Database Error: Could not connect. Check db_config.php.</div>';
        } else {
            
            // 3. Prepare the SQL statement using your table name: contact_messages
            // Using prepared statements prevents SQL Injection attacks.
            $sql = "INSERT INTO contact_messages (full_name, email, message_content) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt === false) {
                 // Handle SQL preparation error
                 $message_status = '<div class="alert alert-danger">Database Error: Could not prepare statement. Please check your SQL configuration.</div>';
            } else {
                // 4. Bind parameters (s=string, s=string, s=string)
                $stmt->bind_param("sss", $name, $email, $message_content);

                // 5. Execute the statement
                if ($stmt->execute()) {
                    $message_status = '<div class="alert alert-success">Thank you, ' . htmlspecialchars($name) . '! Your message has been saved successfully. We will contact you soon.</div>';
                    // Optional: Clear POST data to prevent re-submission on refresh
                    $_POST = array(); 
                } else {
                    // Log the actual error for backend debugging
                    error_log("Database insertion failed: " . $stmt->error);
                    $message_status = '<div class="alert alert-warning">Sorry, there was an issue saving your message to the database. Please try again.</div>';
                }
                
                // Close the statement
                $stmt->close();
            }

            // Close the database connection
            $conn->close();
        }

        // --- END DATABASE INSERTION LOGIC ---
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Roha Jobs</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<?php 
if (file_exists($navbar_path)) {
    include($navbar_path);
}
?>

<section class="container py-5">
    <h1 class="text-center mb-4" style="color:#e9181c;">Contact Us</h1>

    <?php echo $message_status; ?>

    <div class="row g-4">

        <div class="col-md-6">
            <!-- Form posts back to itself (contact.php) -->
            <form class="p-4 shadow-sm rounded" style="border-left:4px solid #e9181c;" method="POST" action="contact.php">
                
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Your Name" required 
                        value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="message_content" class="form-label">Message</label>
                    <textarea class="form-control" id="message_content" name="message_content" rows="4" placeholder="Write something..." required><?php echo htmlspecialchars($_POST['message_content'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn px-4" style="background:#e9181c; color:white;">Send Message</button>
            </form>
        </div>

        <div class="col-md-6">
            <h4>Contact Info</h4>
            
            <p><i class="fas fa-map-marker-alt me-2 text-danger"></i> Addis Ababa, Ethiopia</p>
            <p><i class="fas fa-phone me-2 text-danger"></i> +251 911 383 283</p>
            <p><i class="fab fa-whatsapp me-2 text-success"></i> WhatsApp: +971 50 389 23 54</p>
            <p><i class="fas fa-envelope me-2 text-info"></i> Email: info@rohajobs.com (Placeholder)</p>


            <h5 class="mt-4" >Office Address (UAE)</h5>
            <p>
                <i class="fas fa-building me-2 text-danger"></i> Dubai, Abu Dhabi, Al Reem Island near to <br> Carrefore Supermarket
            </p>
            <p>
                <i class="fab fa-whatsapp me-2 text-success"></i> WhatsApp: +971 50 389 23 54
            </p>
        </div>

    </div>

</section>


<?php 
if (file_exists($footer_path)) {
    include($footer_path);
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>