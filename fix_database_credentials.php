<?php
// This file will update database credentials in all PHP files

$directory = __DIR__;
$password = ""; // The correct password for your MySQL

echo "<h1>Database Credential Update Tool</h1>";

// Function to process PHP files
function updateDatabaseCredentials($file, $password) {
    $content = file_get_contents($file);
    
    // Check if this file contains database connection code
    if (strpos($content, '$servername = "localhost"') !== false) {
        echo "<div>Processing: " . basename($file) . "... ";
        
        // Replace password line - handle both formats that might exist
        $pattern1 = '/\$password = ".*";(\s*\/\/.*)?/';
        $pattern2 = '/\$password = \'.*\';(\s*\/\/.*)?/';
        
        $replacement = '$password = "' . $password . '"; // Default XAMPP password';
        
        $newContent = preg_replace($pattern1, $replacement, $content);
        $newContent = preg_replace($pattern2, $replacement, $newContent);
        
        // Check if there was a replacement
        if ($newContent != $content) {
            file_put_contents($file, $newContent);
            echo "<span style='color:green'>Updated!</span></div>";
        } else {
            echo "<span style='color:orange'>No changes needed.</span></div>";
        }
    }
}

// Get all PHP files in the directory
$phpFiles = glob($directory . "/*.php");

foreach ($phpFiles as $file) {
    updateDatabaseCredentials($file, $password);
}

echo "<h2>Database Credential Update Complete</h2>";
echo "<p>All PHP files have been checked and updated with password: \"" . ($password === "" ? "empty string" : $password) . "\"</p>";
echo "<p>Next steps:</p>";
echo "<ol>";
echo "<li>Make sure MySQL is running in XAMPP control panel</li>";
echo "<li>Check if your MySQL password is indeed empty (default XAMPP setting)</li>";
echo "<li>Run <a href='test_mysql_connection.php'>test_mysql_connection.php</a> to verify connectivity</li>";
echo "<li>Try logging in again on the main page</li>";
echo "</ol>";
?>
