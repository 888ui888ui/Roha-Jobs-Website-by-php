<?php
// INCLUDE DATABASE CONNECTION (Uses MySQLi, which defines $conn)
require_once('db_config.php');

// Define Paths to include files
$navbar_path = 'includes/navbar.php';
$footer_path = 'includes/footer.php';

// --- MySQLi Query Logic ---
$sql = "SELECT id, title, company, location, job_type, salary, category, description, posted_at, deadline, contact_number
        FROM jobs 
        ORDER BY posted_at DESC 
        LIMIT 6"; // Fetch 6 jobs for the homepage

$jobs = [];
$db_error_message = null;

try {
    // Execute query using the $conn (MySQLi) object
    $conn->set_charset("utf8"); // Ensure character set is set for safety
    $result = $conn->query($sql);

    // Fetch data
    if ($result) {
        $jobs = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (\Exception $e) {
    // Handle database error
    $db_error_message = '<div class="alert alert-danger container mt-3">Could not load jobs: ' . $conn->error . '</div>';
}

// Close MySQLi connection after fetching data (optional)
if (isset($conn)) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZELE Jobs</title>
    <link rel="stylesheet" href="styles.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        /* ================================== */
        /* HERO SECTION OVERLAY STYLES */
        /* ================================== */
        .hero-full-width {
            /* Full width section, removing container padding */
            padding: 0 !important; 
            margin: 0 !important;
            /* Using a fluid container for max width */
        }
        #heroCarousel {
            position: relative; /* Base for absolute text positioning */
            width: 100%;
        }
        .carousel-item img {
            /* Enforcing the user-requested size */
            height: 456px; 
            width: 100%;
            object-fit: cover;
        }
        .carousel-overlay {
            position: absolute;
            top: 20%; /* Position from the top */
            left: 5%; /* Position from the left */
            z-index: 10; /* Ensure text is above the image */
            color: white; /* Default text color */
            padding: 15px;
            /* Optional: Add a subtle background or shadow for readability over busy images */
            /* background: rgba(0, 0, 0, 0.4); */
            border-radius: 8px;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 1.0);
        }
        .carousel-overlay .hero-title {
            font-size: 4rem;
            font-weight: 900;
            color: white; 
            margin-bottom: 0.5rem;
        }
        .carousel-overlay .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 500;
            color: white; 
        }
        /* Style for the typing/animated text */
        #animated-subtitle {
            font-weight: bold;
            color: #FFD700; /* Bright gold for visibility on dark images */
            display: inline-block;
            min-width: 150px; /* Prevent layout shift */
        }
         /* Style for the Marquee */
        .welcome-marquee {
            background-color: #ff0000; /* Red background for contrast */
            color: white;
            padding: 5px 0;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 0; /* Remove bottom margin here */
        }
        /* Style for the job cards (retained from previous request) */
        .job-card-image-format {
            /* Add your job card styling here */
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .job-list-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
    </style>
</head>

<body>

<?php include($navbar_path); ?>

<!-- WELCOME MARQUEE SECTION -->
<div class="welcome-marquee">
    <marquee behavior="scroll" direction="left">WELCOME TO ROHA JOBS! Find your next career opportunity today.</marquee>
</div>
<!-- END WELCOME MARQUEE SECTION -->


<!-- UPDATED HERO SECTION: Full-Width Carousel with Text Overlay -->
<section class="hero-full-width container-fluid">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        
        <!-- TEXT OVERLAY CONTAINER -->
        <div class="carousel-overlay">
            <h1 class="hero-title">Roha Jobs</h1>
            <p class="hero-subtitle">
                <span id="static-text">We are </span>
                <span id="animated-subtitle"></span>
                .
            </p>
        </div>
        <!-- END TEXT OVERLAY CONTAINER -->

        <div class="carousel-inner">
            <?php 
            // Define your 6 image paths here (ensure these paths are correct, e.g., using /zele/images/)
            // NOTE: Ensure your images are named slide1.jpg, slide2.jpg, etc., and are 1280x456 px.
            $images = ['slide1.jpg', 'slide2.jpg', 'slide3.jpg', 'slide4.jpg', 'slide5.jpg', 'slide6.jpg'];
            
            foreach ($images as $index => $image): 
                // The first item MUST have the 'active' class
                $active_class = ($index === 0) ? 'active' : '';
            ?>
            <div class="carousel-item <?php echo $active_class; ?>">
                <!-- Using absolute path /zele/images/ as previously fixed -->
                <img src="/zele/images/<?php echo htmlspecialchars($image); ?>" class="d-block w-100" alt="Job Slider Image <?php echo $index + 1; ?>">
            </div>
            <?php endforeach; ?>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<!-- END UPDATED HERO SECTION -->


<section class="featured-jobs-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Latest Job Openings</h2>
        </div>
        
        <?php echo $db_error_message ?? ''; // Display any DB connection error ?>

        <div class="job-list-container">
            <?php if (count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): 
                    // Display job cards dynamically from the database
                    $snippet = substr(htmlspecialchars($job['description']), 0, 100) . '...';
                ?>
                <div class="job-card-image-format attractive-job-card-image">
                    
                    <div class="job-header-top">
                        <div></div> 
                        <div class="top-right-elements">
                            <span class="rohajobs-source"><?php echo htmlspecialchars($job['company']); ?></span>
                            <div class="bookmark-icon">
                                <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zM2 2v13.5L8 12l6 3.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2"/></svg></a>
                            </div>
                        </div>
                    </div>

                    <span class="category-tag" style="color: #ffff; background-color:#ff0000;"><?php echo htmlspecialchars($job['category']); ?></span>

                    <div class="job-main-content">
                        <div class="job-details-left">
                            <h3 class="job-title-main"><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p class="job-post-info">
                                <?php echo date('F jS, Y', strtotime($job['posted_at'])); ?> <span style="color: #95298E;"><?php echo htmlspecialchars($job['company']); ?></span> </p>
                            
                            <div class="job-metadata">
                                <span class="metadata-item">📍 <?php echo htmlspecialchars($job['location']); ?></span>
                                <span class="metadata-item">🗓️ Deadline: <?php echo htmlspecialchars($job['deadline']); ?></span>
                                <span class="metadata-item">🏢 <?php echo htmlspecialchars($job['job_type']); ?></span>
                                <span class="metadata-item">💰 Salary: <?php echo htmlspecialchars($job['salary']); ?></span>
                                <span class="metadata-item">📞 Contact: <?php echo htmlspecialchars($job['contact_number']); ?></span>
                            </div>
                        </div>
                    </div>

                    <p class="job-description-snippet">
                        <?php echo $snippet; ?>
                    </p>

                    <div class="job-footer mt-4">
                        <div></div> 
                        <!-- Link uses absolute path -->
                        <a href="/zele/apply_job.php?id=<?php echo $job['id']; ?>" class="btn btn-category btn-apply-now">Apply Now</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">No jobs found.</div>
            <?php endif; ?>
        </div>
        
    </div>
</section>

<!-- SHOW MORE JOBS BUTTON SECTION -->
<section class="text-center pb-5">
    <div class="container">
        <!-- Link uses absolute path -->
        <a href="/zele/find-jobs.php" class="btn btn-lg btn-primary" style="background-color: #AA8B3F; border-color: #AA8B3F;">Show More Jobs</a>
    </div>
</section>
<!-- END SHOW MORE JOBS BUTTON SECTION -->

<section class="services-section py-5">
    <div class="container">
        
        <div class="text-center mb-5">
            <h2 class="section-title">Our Professional Services</h2>
            <p class="section-subtitle">Level up your career with our expert assistance.</p>
        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-4 col-sm-6">
                <div class="service-card text-center">
                    <div class="service-icon">📝</div>
                    <h5 class="service-title">CV Assistance</h5>
                    <p class="service-description">Professional review and writing services to create a compelling, ATS-friendly resume.</p>
                    <a href="#" class="btn btn-service-action">Learn More</a>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="service-card text-center">
                    <div class="service-icon">🔗</div>
                    <h5 class="service-title">LinkedIn Makeover</h5>
                    <p class="service-description">Optimize your profile to attract recruiters and expand your professional network effortlessly.</p>
                    <a href="#" class="btn btn-service-action">Learn More</a>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="service-card text-center">
                    <div class="service-icon">🗣️</div>
                    <h5 class="service-title">Interview Training</h5>
                    <p class="service-description">One-on-one coaching to master common interview questions and build confidence.</p>
                    <a href="#" class="btn btn-service-action">Learn More</a>
                </div>
            </div>

        </div>
    </div>
</section>


<?php 
// Include Footer
if (file_exists($footer_path)) {
    include($footer_path);
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // JAVASCRIPT FOR DYNAMIC SUBTITLE EFFECT
    document.addEventListener('DOMContentLoaded', function() {
        const words = ["speedy", "availability", "trusted", "reliable"];
        let wordIndex = 0;
        const animatedSubtitle = document.getElementById('animated-subtitle');

        function typeEffect(text, callback) {
            let i = 0;
            const typing = setInterval(() => {
                if (i < text.length) {
                    animatedSubtitle.textContent += text.charAt(i);
                    i++;
                } else {
                    clearInterval(typing);
                    setTimeout(callback, 1000); // Wait 1 second before deleting
                }
            }, 100); // Typing speed
        }

        function deleteEffect(callback) {
            let text = animatedSubtitle.textContent;
            const deleting = setInterval(() => {
                if (text.length > 0) {
                    text = text.slice(0, -1);
                    animatedSubtitle.textContent = text;
                } else {
                    clearInterval(deleting);
                    wordIndex = (wordIndex + 1) % words.length;
                    setTimeout(callback, 500); // Wait 0.5 second before typing next word
                }
            }, 50); // Deleting speed
        }

        function loopWords() {
            typeEffect(words[wordIndex], () => {
                deleteEffect(loopWords);
            });
        }

        loopWords();
    });
</script>

</body>
</html>