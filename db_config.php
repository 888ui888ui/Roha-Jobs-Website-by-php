<?php
// DB credentials for standard XAMPP setup
//$host = 'rohajobs.great-site.net'; 
$host = 'localhost'; 


// Use the database name you confirmed (rohajobs)
$dbname = 'rohajobs'; 

// Standard XAMPP default username
//$user = 'if0_40590905'; 
$user = 'root'; 

// Standard XAMPP default password (empty string)
$pass = ''; 

// Attempt to establish a connection using the MySQLi Object-Oriented style
// The connection object will be stored in $conn (replacing the old $pdo)
$conn = new mysqli($host, $user, $pass, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    // If connection fails, stop the script and display a clear error message
    die("<h1>❌ Database Connection Failed:</h1>
         <p>Please check your XAMPP services and DB credentials.</p>
         <p>Error: " . $conn->connect_error . "</p>");
}

// If connection is successful, the script continues and $conn is ready to use.
?>