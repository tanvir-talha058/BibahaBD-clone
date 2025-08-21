<?php
header('Content-Type: text/html');
echo '<!DOCTYPE html>
<html>
<head>
    <title>Database Status Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #4CAF50; }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        .action-btn { 
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Database Status Check</h1>';

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bibahabd_db";

try {
    // Connect to MySQL
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<p class="success">✓ Successfully connected to MySQL server</p>';
    
    // Check if database exists
    $stmt = $conn->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
    $stmt->execute([$dbname]);
    
    if ($stmt->rowCount() > 0) {
        echo '<p class="success">✓ Database "' . $dbname . '" exists</p>';
        
        // Connect to the database
        $dbConn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get tables count
        $tableStmt = $dbConn->prepare("SHOW TABLES");
        $tableStmt->execute();
        $tables = $tableStmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo '<p class="success">✓ Found ' . count($tables) . ' tables in the database</p>';
    } else {
        echo '<p class="warning">Database "' . $dbname . '" does not exist.</p>';
    }
    
} catch (PDOException $e) {
    echo '<p class="error">❌ Connection failed: ' . $e->getMessage() . '</p>';
}

echo '<div>';
echo '<a href="indext.html" class="action-btn">Back to Main Page</a>';
echo '</div>';

echo '</body>
</html>';
?>
