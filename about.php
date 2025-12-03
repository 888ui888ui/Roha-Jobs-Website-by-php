<?php 
// 1. Define Paths to common files
// Good practice is to centralize common elements in an 'includes' folder
$navbar_path = 'includes/navbar.php';
$footer_path = 'includes/footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Roha Jobs</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

<?php 
if (file_exists($navbar_path)) {
    include($navbar_path);
} else {
    // Fallback if the include file is not found
    echo '<nav class="navbar navbar-light bg-light">Navbar Missing!</nav>';
}
?>

<section class="container py-5">
    <h1 class="text-center mb-4" style="color:red;">About Roha Jobs</h1>

    <p class="lead text-center">
        Roha Jobs is a professional job-matching platform connecting job seekers with employers 
        across Ethiopia. Our mission is to simplify hiring and help job seekers find their next opportunity fast.
    </p>

    <div class="row mt-5">

        <div class="col-md-6">
            <h3 style="color:#AA8B3F;">Our Vision</h3>
            <p>
                To become Ethiopia’s most trusted agency bridging skilled workers with reputable 
                organizations locally and internationally.
            </p>
        </div>

        <div class="col-md-6">
            <h3 style="color:#AA8B3F;">Our Mission</h3>
            <p>
                Delivering reliable, transparent, and efficient job placement solutions using 
                modern digital tools and professional recruitment methods.
            </p>
        </div>

    </div>
</section>

<?php 
if (file_exists($footer_path)) {
    include($footer_path);
} else {
    echo '<footer>Footer Missing!</footer>';
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>