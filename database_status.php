<?php
header('Content-Type: text/html');
echo '<!DOCTYPE html>
<html>
<head>
    <title>Database Status Check</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 900px; margin: 0 auto; padding: 20px; }
        h1, h2, h3 { color: #4CAF50; }
        .table-info { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 15px; }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow: auto; }
        .action-btn { 
            display: inline-block;
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Database Status Check</h1>';

// Database configuration for XAMPP
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
        
        // Get tables
        $tableStmt = $dbConn->prepare("SHOW TABLES");
        $tableStmt->execute();
        $tables = $tableStmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo '<h2>Database Tables</h2>';
        echo '<p>Found ' . count($tables) . ' tables in the database</p>';
        
        if (count($tables) == 0) {
            echo '<p class="warning">No tables found. Please run <a href="setup_db_registration.php">setup_db_registration.php</a> to create tables.</p>';
        } else {
            foreach ($tables as $table) {
                echo '<div class="table-info">';
                echo '<h3>Table: ' . $table . '</h3>';
                
                // Get row count
                $countStmt = $dbConn->prepare("SELECT COUNT(*) as count FROM `$table`");
                $countStmt->execute();
                $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
                
                echo '<p>Contains ' . $count . ' records</p>';
                
                // Get structure
                $structureStmt = $dbConn->prepare("DESCRIBE `$table`");
                $structureStmt->execute();
                $columns = $structureStmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo '<h4>Structure:</h4>';
                echo '<table>';
                echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
                
                foreach ($columns as $column) {
                    echo '<tr>';
                    echo '<td>' . $column['Field'] . '</td>';
                    echo '<td>' . $column['Type'] . '</td>';
                    echo '<td>' . $column['Null'] . '</td>';
                    echo '<td>' . $column['Key'] . '</td>';
                    echo '<td>' . ($column['Default'] ?? 'NULL') . '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
                
                // Show sample data
                if ($count > 0) {
                    $sampleStmt = $dbConn->prepare("SELECT * FROM `$table` LIMIT 3");
                    $sampleStmt->execute();
                    $samples = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo '<h4>Sample Data:</h4>';
                    echo '<table>';
                    
                    // Table headers
                    echo '<tr>';
                    foreach (array_keys($samples[0]) as $header) {
                        echo '<th>' . $header . '</th>';
                    }
                    echo '</tr>';
                    
                    // Table data
                    foreach ($samples as $row) {
                        echo '<tr>';
                        foreach ($row as $value) {
                            // Don't show password hashes in full
                            if ($value !== null && strlen($value) > 50 && strpos($header, 'password') !== false) {
                                echo '<td>' . substr($value, 0, 10) . '...[truncated]</td>';
                            } else {
                                echo '<td>' . ($value ?? 'NULL') . '</td>';
                            }
                        }
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                }
                
                echo '</div>';
            }
        }
        
    } else {
        echo '<p class="warning">Database "' . $dbname . '" does not exist. Creating it now...</p>';
        
        // Create database
        $conn->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo '<p class="success">✓ Database created successfully</p>';
        echo '<p>Please run <a href="setup_db_registration.php">setup_db_registration.php</a> to create tables</p>';
    }
    
} catch (PDOException $e) {
    echo '<p class="error">❌ Connection failed: ' . $e->getMessage() . '</p>';
    
    echo '<h2>Troubleshooting</h2>';
    echo '<ul>';
    echo '<li>Make sure MySQL service is running in XAMPP</li>';
    echo '<li>Check MySQL credentials (username: root, password: empty)</li>';
    echo '<li>Try using the <a href="reset_mysql_password.bat">MySQL password reset tool</a></li>';
    echo '<li>Check if any other application is using MySQL port (3306)</li>';
    echo '</ul>';
}

echo '<h2>Next Steps</h2>';
echo '<div>';
echo '<a href="setup_db_registration.php" class="action-btn">Run Database Setup</a>';
echo '<a href="profile_debug.html" class="action-btn">Debug Profile Loading</a>';
echo '<a href="test_mysql_connection.php" class="action-btn">Test MySQL Connection</a>';
echo '<a href="indext.html" class="action-btn">Go to Main Page</a>';
echo '</div>';

echo '</body>
</html>';
?>
