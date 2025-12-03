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
$conn->close();

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
        /* Optional: Add CSS to ensure the carousel takes up appropriate space */
        .hero {
            display: flex;
            align-items: center;
            padding-top: 5rem;
            padding-bottom: 5rem;
        }
        .hero-text {
            flex: 1; /* Takes up the left side space */
            padding-right: 30px; /* Space between text and carousel */
        }
        .hero-carousel {
            flex: 1; /* Takes up the right side space */
            max-width: 50%; /* Limit the width of the carousel */
            border-radius: 8px; /* Optional styling */
            overflow: hidden; /* Ensures image corners are rounded */
        }
        .carousel-item img {
            height: 400px; /* Set a fixed height for consistency */
            object-fit: cover; /* Ensures images cover the area without distortion */
        }
    </style>
</head>

<body>

<?php include($navbar_path); ?>


<section class="hero container d-flex align-items-center pt-5">

    <div class="hero-text">
        <h1 class="hero-title">Roha Jobs</h1>
        <p class="hero-subtitle">Tap Into Your Next Opportunity</p>

        <a href="find_jobs.php" class="btn hero-btn mt-3 px-4 py-3" style="color:#AA8B3F">Find Jobs</a>

        <form action="find_jobs.php" method="GET" class="search-box mt-5 p-3 d-flex align-items-center">
            <input type="text" name="query" class="form-control search-input" placeholder="Search jobs"> 
            <button type="submit" class="btn search-btn ms-2 px-4">Search</button>
        </form>

    </div>

    <div class="hero-carousel">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
            
            <div class="carousel-inner">
                <?php 
                // Define your 6 image paths here
                $images = ['slide1.jpg', 'slide2.jpg', 'slide3.jpg', 'slide4.jpg', 'slide5.jpg', 'slide6.jpg'];
                
                foreach ($images as $index => $image): 
                    // The first item MUST have the 'active' class
                    $active_class = ($index === 0) ? 'active' : '';
                ?>
                <div class="carousel-item <?php echo $active_class; ?>">
                    <img src="images/<?php echo htmlspecialchars($image); ?>" class="d-block w-100" alt="Job Slider Image <?php echo $index + 1; ?>">
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
    </div>
    </section>


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

                    <span class="category-tag" style="color: #AA8B3F;"><?php echo htmlspecialchars($job['category']); ?></span>

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
                        <a href="apply_job.php?id=<?php echo $job['id']; ?>" class="btn btn-category btn-apply-now">Apply Now</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">No jobs found.</div>
            <?php endif; ?>
        </div>
        
    </div>
</section>

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


<?php include($footer_path); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>