<?php
header('Content-Type: text/html');

echo '<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">';
echo '<h1 style="color: #4CAF50;">Database Connection Test</h1>';

// Database configuration for XAMPP
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bibahabd_db";

try {
    // Try connecting to MySQL
    echo '<h2>Step 1: Connecting to MySQL Server</h2>';
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<p style="color: green;">✓ Successfully connected to MySQL server!</p>';
    
    // Try connecting to the database
    echo '<h2>Step 2: Connecting to Database</h2>';
    try {
        $dbConn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo '<p style="color: green;">✓ Successfully connected to database "' . $dbname . '"!</p>';
        
        // Check tables
        echo '<h2>Step 3: Checking Tables</h2>';
        $tables = ['users', 'user_profiles'];
        
        echo '<ul>';
        foreach ($tables as $table) {
            $stmt = $dbConn->prepare("SHOW TABLES LIKE :table");
            $stmt->bindParam(':table', $table);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo '<li style="color: green;">✓ Table "' . $table . '" exists</li>';
                
                // Check row count
                $countStmt = $dbConn->prepare("SELECT COUNT(*) as count FROM $table");
                $countStmt->execute();
                $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
                echo '<ul><li>Contains ' . $count . ' record(s)</li></ul>';
                
                // If it's the users table, show a sample record (for debugging)
                if ($table == 'users' && $count > 0) {
                    $sampleStmt = $dbConn->prepare("SELECT id, email, profile_id, first_name, last_name FROM $table LIMIT 1");
                    $sampleStmt->execute();
                    $sample = $sampleStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($sample) {
                        echo '<ul><li>Sample record: ID=' . $sample['id'] . ', Email=' . $sample['email'] . 
                             ', Profile ID=' . $sample['profile_id'] . ', Name=' . $sample['first_name'] . ' ' . $sample['last_name'] . '</li></ul>';
                    }
                }
            } else {
                echo '<li style="color: red;">✗ Table "' . $table . '" does not exist</li>';
            }
        }
        echo '</ul>';
        
    } catch(PDOException $e) {
        echo '<p style="color: red;">✗ Failed to connect to database: ' . $e->getMessage() . '</p>';
        
        // Try to create the database
        echo '<h2>Attempting to create database</h2>';
        $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        echo '<p style="color: green;">✓ Database created! Please run <a href="setup_db_registration.php">setup_db_registration.php</a> to create tables.</p>';
    }
    
} catch(PDOException $e) {
    echo '<p style="color: red;">✗ Failed to connect to MySQL server: ' . $e->getMessage() . '</p>';
    
    echo '<h2>Troubleshooting</h2>';
    echo '<ol>';
    echo '<li>Make sure MySQL is running in XAMPP Control Panel</li>';
    echo '<li>Check if the username and password are correct</li>';
    echo '<li>Try running <a href="reset_mysql_password.bat">reset_mysql_password.bat</a> to reset MySQL password</li>';
    echo '</ol>';
}

echo '<h2>Next Steps</h2>';
echo '<ol>';
echo '<li><a href="profile_debug.html">Go back to Debug Tool</a></li>';
echo '<li><a href="setup_db_registration.php">Run Database Setup Script</a></li>';
echo '<li><a href="indext.html">Go to Main Page</a></li>';
echo '</ol>';

echo '</div>';
?>
