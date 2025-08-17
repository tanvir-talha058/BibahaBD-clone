<?php
// Simple script to check if bibahabd_db exists and create it if not

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bibahabd_db";

echo "<h1>Database Check Tool</h1>";

try {
    // First, connect without database
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $conn->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :dbname");
    $stmt->bindParam(':dbname', $dbname);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "<div style='color: green; font-size: 18px;'>✓ Database '$dbname' exists!</div>";
        
        // Connect to the database to check tables
        $dbConn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if user_profiles table exists
        $tableStmt = $dbConn->prepare("SHOW TABLES LIKE 'user_profiles'");
        $tableStmt->execute();
        
        if ($tableStmt->rowCount() > 0) {
            echo "<div style='color: green; margin-top: 10px;'>✓ Table 'user_profiles' exists!</div>";
            
            // Count profiles
            $countStmt = $dbConn->prepare("SELECT COUNT(*) as total FROM user_profiles");
            $countStmt->execute();
            $count = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            echo "<div style='margin-top: 10px;'>Found <b>$count</b> profile(s) in the database.</div>";
        } else {
            echo "<div style='color: orange; margin-top: 10px;'>⚠ Table 'user_profiles' does not exist!</div>";
            echo "<div style='margin-top: 10px;'>Please run <a href='setup_db_registration.php'>setup_db_registration.php</a> to create it.</div>";
        }
    } else {
        echo "<div style='color: orange; font-size: 18px;'>⚠ Database '$dbname' does not exist!</div>";
        echo "<div style='margin-top: 10px;'>Creating database...</div>";
        
        // Create the database
        $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<div style='color: green; margin-top: 10px;'>✓ Database '$dbname' created successfully!</div>";
        
        echo "<div style='margin-top: 10px;'>Please run <a href='setup_db_registration.php'>setup_db_registration.php</a> to set up tables.</div>";
    }
    
    echo "<h2 style='margin-top: 30px;'>XAMPP Configuration</h2>";
    echo "<ul>";
    echo "<li>Server: $servername</li>";
    echo "<li>Username: $username</li>";
    echo "<li>Password: [empty]</li>";
    echo "<li>Database: $dbname</li>";
    echo "</ul>";
    
    echo "<h2>Next Steps</h2>";
    echo "<ol>";
    echo "<li>Make sure XAMPP Apache and MySQL services are running</li>";
    echo "<li>If tables need to be created, run <a href='setup_db_registration.php'>setup_db_registration.php</a></li>";
    echo "<li>Go to <a href='register.html'>registration page</a> to test the form</li>";
    echo "<li>Check <a href='README.md'>README.md</a> for more information</li>";
    echo "</ol>";
    
} catch(PDOException $e) {
    echo "<div style='color: red; font-size: 18px;'>❌ Error: " . $e->getMessage() . "</div>";
    
    echo "<h2>Troubleshooting</h2>";
    echo "<ol>";
    echo "<li>Make sure XAMPP is installed and running</li>";
    echo "<li>Check if MySQL service is running in XAMPP Control Panel</li>";
    echo "<li>Verify there are no port conflicts</li>";
    echo "<li>Try restarting XAMPP services</li>";
    echo "</ol>";
}
?>
