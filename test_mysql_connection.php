<?php
// Database connection test script
echo "<h1>Database Connection Test</h1>";

// Try with empty password (default XAMPP setting)
echo "<h2>Testing with empty password:</h2>";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bibahabd_db";

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div style='color:green'>Connection to MySQL server successful!</div>";
    
    // Try to connect to specific database
    try {
        $dbConn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div style='color:green'>Connection to database '$dbname' successful!</div>";
        
        // Check if users table exists
        $stmt = $dbConn->prepare("SHOW TABLES LIKE 'users'");
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            echo "<div style='color:green'>Table 'users' exists!</div>";
        } else {
            echo "<div style='color:red'>Table 'users' does not exist!</div>";
        }
        
    } catch(PDOException $e) {
        echo "<div style='color:red'>Connection to database failed: " . $e->getMessage() . "</div>";
        echo "<div>Will try to create the database...</div>";
        
        try {
            $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
            echo "<div style='color:green'>Database created successfully!</div>";
            echo "<div>Please run setup_db_registration.php to create the tables</div>";
        } catch(PDOException $e) {
            echo "<div style='color:red'>Failed to create database: " . $e->getMessage() . "</div>";
        }
    }
    
} catch(PDOException $e) {
    echo "<div style='color:red'>Connection failed with empty password: " . $e->getMessage() . "</div>";
    
    // Try with other common passwords
    echo "<h2>Testing with standard password:</h2>";
    try {
        $password = "password";
        $conn = new PDO("mysql:host=$servername", $username, $password);
        echo "<div style='color:green'>Connection successful with password 'password'!</div>";
        echo "<div>Please update all your PHP files to use this password</div>";
    } catch(PDOException $e) {
        echo "<div style='color:red'>Connection failed with 'password': " . $e->getMessage() . "</div>";
    }
}

echo "<h2>XAMPP MySQL Troubleshooting</h2>";
echo "<ol>";
echo "<li>Make sure MySQL service is running in XAMPP Control Panel</li>";
echo "<li>Check MySQL error log at C:\\xampp\\mysql\\data\\mysql_error.log</li>";
echo "<li>Try resetting the MySQL root password:
    <ol>
        <li>Stop the MySQL server from XAMPP Control Panel</li>
        <li>Open Command Prompt as Administrator</li>
        <li>Navigate to XAMPP MySQL bin directory: <code>cd C:\\xampp\\mysql\\bin</code></li>
        <li>Start MySQL with skip-grant-tables: <code>mysqld --skip-grant-tables</code></li>
        <li>Open another Command Prompt and enter: <code>mysql -u root</code></li>
        <li>Run: <code>USE mysql;</code></li>
        <li>Run: <code>UPDATE user SET password=PASSWORD('') WHERE User='root';</code></li>
        <li>Run: <code>FLUSH PRIVILEGES;</code></li>
        <li>Run: <code>EXIT;</code></li>
        <li>Stop and restart MySQL from XAMPP Control Panel</li>
    </ol>
</li>";
echo "</ol>";

echo "<h2>PHP Files Configuration</h2>";
echo "Make sure all your PHP files have these settings:<br>";
echo "<pre>
\$servername = \"localhost\";
\$username = \"root\";
\$password = \"\";  // Empty string for default XAMPP
\$dbname = \"bibahabd_db\";
</pre>";
?>
