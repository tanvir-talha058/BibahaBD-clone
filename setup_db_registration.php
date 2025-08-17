<?php
// Database setup specifically for the registration page
$servername = "localhost";
$username = "root";        // Default XAMPP username
$password = "";            // Default XAMPP password (empty)
$dbname = "bibahabd_db";

try {
    // First, connect without database to create it
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    echo "<div style='color: green; font-weight: bold;'>Database '$dbname' created successfully!</div>";
    
    // Now connect to the database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create user_profiles table specifically for the registration form
    $sql = "CREATE TABLE IF NOT EXISTS user_profiles (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        profile_id VARCHAR(50) UNIQUE NOT NULL,
        marital_status ENUM('never_married', 'divorced', 'widowed') DEFAULT 'never_married',
        complexion ENUM('very_fair', 'fair', 'wheatish', 'dark') DEFAULT 'fair',
        date_of_birth DATE NOT NULL,
        body_type ENUM('slim', 'average', 'athletic', 'heavy') DEFAULT 'average',
        height VARCHAR(50) NOT NULL,
        disabilities VARCHAR(100) DEFAULT 'no_disability',
        have_children VARCHAR(100) DEFAULT 'no_children',
        blood_group VARCHAR(20) DEFAULT NULL,
        zodiac_sign VARCHAR(50) DEFAULT NULL,
        hair_color VARCHAR(50) DEFAULT 'black',
        eye_color VARCHAR(50) DEFAULT 'black',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "<div style='color: green;'>User profiles table created successfully!</div>";
    
    // Insert a test profile
    $testProfileId = "B" . date('Y') . "001";
    
    $stmt = $conn->prepare("INSERT IGNORE INTO user_profiles 
        (profile_id, marital_status, complexion, date_of_birth, body_type, height) 
        VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $testProfileId, 
        'never_married', 
        'fair', 
        '1990-01-01', 
        'average', 
        '5ft_8in'
    ]);
    
    if ($stmt->rowCount() > 0) {
        echo "<div style='color: green;'>Test profile created successfully!</div>";
        echo "<div>Profile ID: $testProfileId</div>";
    } else {
        echo "<div style='color: blue;'>Test profile already exists or could not be created.</div>";
    }
    
    echo "<h2 style='color: green;'>Database setup completed successfully!</h2>";
    echo "<h3>XAMPP Configuration</h3>";
    echo "<ul>";
    echo "<li>Server: localhost</li>";
    echo "<li>Username: root</li>";
    echo "<li>Password: (empty)</li>";
    echo "<li>Database: bibahabd_db</li>";
    echo "</ul>";
    
    echo "<h3>Next Steps</h3>";
    echo "<ol>";
    echo "<li>Make sure XAMPP Apache and MySQL are running</li>";
    echo "<li>Go to <a href='register.html'>Register Page</a> to test the form</li>";
    echo "</ol>";
    
} catch(PDOException $e) {
    echo "<div style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</div>";
    
    echo "<h3>Troubleshooting Steps</h3>";
    echo "<ol>";
    echo "<li>Make sure XAMPP is installed correctly</li>";
    echo "<li>Verify that Apache and MySQL services are running</li>";
    echo "<li>Check for any conflicting services on ports 80 and 3306</li>";
    echo "</ol>";
}

$conn = null;
?>
