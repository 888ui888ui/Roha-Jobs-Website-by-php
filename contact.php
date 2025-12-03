<?php
// Define Paths to common files (assuming you created the 'includes' folder)
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
        // 3. EMAIL SETUP (Change these details to your server's configuration)
        $to = "your.admin.email@example.com"; // Change this to your actual receiving email
        $subject = "New Contact Form Submission from Roha Jobs";
        $headers = "From: " . $name . " <" . $email . ">\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $email_body = "You have received a new message from the contact form on your job portal.\n\n";
        $email_body .= "Name: " . $name . "\n";
        $email_body .= "Email: " . $email . "\n";
        $email_body .= "Message:\n" . $message_content;

        // 4. SEND EMAIL
        // Note: The mail() function requires a properly configured web server (e.g., SMTP settings on XAMPP/WAMP/Live Server)
        if (mail($to, $subject, $email_body, $headers)) {
            $message_status = '<div class="alert alert-success">Thank you, ' . htmlspecialchars($name) . '! Your message has been sent successfully.</div>';
            // Optional: Clear POST data to prevent re-submission on refresh
            $_POST = array(); 
        } else {
            $message_status = '<div class="alert alert-warning">Sorry, there was an issue sending your message. Please try again or contact us directly.</div>';
        }
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
            <h4 style="color:#AA8B3F;">Contact Info</h4>
            <p>📍 Addis Ababa, Ethiopia</p>
            <p>📞 +251 912 34 56 78</p>
            <p>📧 rohajobs.com</p>

            <h5 class="mt-4" style="color:#AA8B3F;">Office Hours</h5>
            <p>Monday – Friday: 9:00 AM – 5:00 PM</p>
            <p>Saturday: 9:00 AM – 12:00 PM</p>
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