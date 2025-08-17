<?php
// Test XAMPP connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bibahabd_db";

echo "<h1>Testing Database Connection</h1>";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div style='color:green'>Connection successful!</div>";
    
    // Test query
    $stmt = $conn->prepare("SHOW TABLES");
    $stmt->execute();
    
    echo "<h2>Database Tables</h2>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . $row['Tables_in_' . $dbname] . "</li>";
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<div style='color:red'>Connection failed: " . $e->getMessage() . "</div>";
    
    echo "<h2>Troubleshooting Steps:</h2>";
    echo "<ol>";
    echo "<li>Make sure XAMPP is installed correctly</li>";
    echo "<li>Verify that Apache and MySQL services are running</li>";
    echo "<li>Check if the database 'bibahabd_db' exists</li>";
    echo "<li>Try running setup_database.php to create the database and tables</li>";
    echo "</ol>";
}

$conn = null;
?>
