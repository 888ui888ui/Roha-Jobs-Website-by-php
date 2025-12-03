<?php
// 1. INCLUDE DATABASE CONNECTION (Now using MySQLi, which defines $conn)
require_once('db_config.php');

// Define Paths to common files
$navbar_path = 'includes/navbar.php';
$footer_path = 'includes/footer.php';

// 2. INITIAL DATABASE QUERY LOGIC
$search_query = '';
$search_location = '';

// Check if search parameters were submitted
if (isset($_GET['query'])) {
    $search_query = trim($_GET['query']);
}
if (isset($_GET['location'])) {
    $search_location = trim($_GET['location']);
}

// --- Dynamic Query Construction (Using MySQLi with simple string concatenation) ---
$sql = "SELECT id, title, company, location, job_type, salary, category, description, posted_at, deadline, contact_number
        FROM jobs 
        WHERE 1=1 "; 

$jobs = [];
$db_error_message = null;

// Sanitize inputs for SQL query using real_escape_string (Crucial for MySQLi)
$safe_search_query = $conn->real_escape_string($search_query);
$safe_search_location = $conn->real_escape_string($search_location);

if (!empty($safe_search_query)) {
    $sql .= " AND (title LIKE '%$safe_search_query%' OR category LIKE '%$safe_search_query%' OR company LIKE '%$safe_search_query%' OR description LIKE '%$safe_search_query%') ";
}

if (!empty($safe_search_location)) {
    $sql .= " AND location LIKE '%$safe_search_location%' ";
}

$sql .= " ORDER BY posted_at DESC";

try {
    // 3. EXECUTE QUERY using $conn (MySQLi)
    $result = $conn->query($sql);

    // 4. FETCH DATA
    if ($result) {
        $jobs = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
    } else {
        $db_error_message = '<div class="alert alert-danger container mt-3">MySQL Query Error: ' . $conn->error . '</div>';
    }
} catch (\Exception $e) {
    $jobs = [];
    $db_error_message = '<div class="alert alert-danger container mt-3">General Error: ' . $e->getMessage() . '</div>';
}

// 5. Close MySQLi connection after fetching data (optional)
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Jobs - ZELE Jobs</title>

    <link rel="stylesheet" href="styles.css">

    <link rel="stylesheet" 
              href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?php 
if (file_exists($navbar_path)) {
    include($navbar_path);
}
?>

<section class="py-5 text-center bg-light">
    <h1 class="section-title" style="color:#e9181c;">Find Your Next Job</h1>
    <p>Search for the latest opportunities from trusted employers</p>
</section>

<section class="container py-4">
    <form method="GET" action="find_jobs.php" id="searchForm">
        <div class="search-box p-3 d-flex align-items-center shadow-sm rounded">
            <input type="text" id="searchKeywordInput" name="query" class="form-control" placeholder="Search job title, keyword..." value="<?php echo htmlspecialchars($search_query); ?>">
            
            <input type="text" id="searchLocationInput" name="location" class="form-control mx-2" placeholder="Location" value="<?php echo htmlspecialchars($search_location); ?>">
            
            <button type="submit" id="searchPageButton" class="btn px-4 text-white" style="background:#e9181c;">Search</button>
        </div>
    </form>
</section>


<section class="featured-jobs-section py-5">
    <div id="noResultsMessage" style="display: none; padding: 20px; text-align: center; color: #555; border: 1px solid #ddd; margin-top: 20px;">
        ❌ **No job listings match your search criteria.** Try different keywords or locations.
    </div>

    <?php 
    echo $db_error_message ?? ''; 
    ?>

    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Latest Job Openings (<?php echo count($jobs); ?> found)</h2>
        </div>

        <div class="job-list-container">
            
            <?php if (count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): 
                    $keywords = $job['title'] . ' ' . $job['category'] . ' ' . $job['company'] . ' ' . $job['job_type'] . ' ' . $job['description'];
                    $snippet = substr(htmlspecialchars($job['description']), 0, 150) . '...';
                ?>
                <div class="job-card-image-format attractive-job-card-image job-card" 
                    data-keywords="<?php echo htmlspecialchars($keywords); ?>" 
                    data-location="<?php echo htmlspecialchars($job['location']); ?>" 
                    data-category="<?php echo htmlspecialchars($job['category']); ?>">
            
                    <div class="job-header-top">
                        <div></div> 
                        <div class="top-right-elements">
                            <span class="rohajobs-source"><?php echo htmlspecialchars($job['company'] ?? 'RohaJobs'); ?></span>
                            <div class="bookmark-icon">
                                <a href="#"><i class="far fa-bookmark"></i></a>
                            </div>
                        </div>
                    </div>

                    <span class="category-tag" style="background:#e9181c; color:white;"><?php echo htmlspecialchars($job['category']); ?></span>

                    <div class="job-main-content">
                        <div class="job-details-left">
                            <h3 class="job-title-main"><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p class="job-post-info">
                                <?php echo date('F jS, Y', strtotime($job['posted_at'])); ?> <span style="color: #e9181c;">| By <?php echo htmlspecialchars($job['company']); ?></span> </p>
                            
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
                        <a href="apply_job.php?id=<?php echo $job['id']; ?>" class="btn btn-category btn-apply-now"> Apply</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">No jobs found matching the initial search criteria.</div>
            <?php endif; ?>
        </div>
        
    </div>
</section>


<?php 
if (file_exists($footer_path)) {
    include($footer_path);
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // [JAVASCRIPT REMAINS UNCHANGED]
    function getQueryParameter(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    function filterJobs(keywordQuery, locationQuery) {
        const jobCards = document.querySelectorAll('.job-card');
        const noResultsElement = document.getElementById('noResultsMessage');
        let resultsFound = 0; 
        
        const keywordTerm = keywordQuery ? keywordQuery.toLowerCase().trim() : '';
        const locationTerm = locationQuery ? locationQuery.toLowerCase().trim() : '';
        
        jobCards.forEach(card => {
            const cardKeywords = card.getAttribute('data-keywords').toLowerCase();
            const cardLocation = card.getAttribute('data-location').toLowerCase();
            
            let passesKeywordFilter = true;
            let passesLocationFilter = true;
            
            // The search is already performed server-side on initial load/submit, 
            // but the client-side JS ensures live filtering if the user types into the box
            // without hitting submit.
            
            if (keywordTerm && !cardKeywords.includes(keywordTerm)) {
                passesKeywordFilter = false;
            }

            if (locationTerm && !cardLocation.includes(locationTerm)) {
                passesLocationFilter = false;
            }

            if (passesKeywordFilter && passesLocationFilter) {
                card.style.display = 'block'; 
                resultsFound++; 
            } else {
                card.style.display = 'none'; 
            }
        });

        if (resultsFound === 0) {
            noResultsElement.style.display = 'block';
        } else {
            noResultsElement.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const keywordInput = document.getElementById('searchKeywordInput');
        const locationInput = document.getElementById('searchLocationInput');
        const searchForm = document.getElementById('searchForm');
        
        // Initial filter to respect URL parameters
        function handleLiveSearch() {
            const keyword = keywordInput.value;
            const location = locationInput.value;
            filterJobs(keyword, location);
        }
        
        handleLiveSearch(); 
        
        keywordInput.addEventListener('input', handleLiveSearch);
        locationInput.addEventListener('input', handleLiveSearch);

        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                // Allow the form to submit normally to find_jobs.php
                // e.preventDefault(); is removed here to allow form submission for server processing
            });
        }
    });

</script>

</body>
</html>