<?php
// Database setup for Bibahabd.com - XAMPP Compatible
// Run this file once to create the necessary tables

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
    echo "Database '$dbname' created successfully or already exists<br>";
    
    // Now connect to the database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        profile_id VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        phone VARCHAR(20),
        guardian_phone VARCHAR(20),
        date_of_birth DATE,
        gender ENUM('male', 'female') NOT NULL,
        religion VARCHAR(50),
        caste VARCHAR(50),
        nationality VARCHAR(50) DEFAULT 'Bangladeshi',
        country VARCHAR(50),
        division VARCHAR(50),
        district VARCHAR(50),
        upazila VARCHAR(100),
        city VARCHAR(50),
        occupation VARCHAR(100),
        education VARCHAR(100),
        profession VARCHAR(100),
        marital_status ENUM('never_married', 'divorced', 'widowed') DEFAULT 'never_married',
        profile_created_by VARCHAR(50),
        looking_for ENUM('bride', 'groom'),
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        email_verified BOOLEAN DEFAULT FALSE,
        profile_completed BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    )";
    
    $conn->exec($sql);
    echo "Users table created successfully<br>";
    
    // Create user_profiles table for detailed profile information
    $sql = "CREATE TABLE IF NOT EXISTS user_profiles (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        profile_id VARCHAR(50) UNIQUE NOT NULL,
        user_id INT(11) UNSIGNED NULL,
        marital_status ENUM('never_married', 'divorced', 'widowed') DEFAULT 'never_married',
        complexion ENUM('very_fair', 'fair', 'wheatish', 'dark') DEFAULT 'fair',
        date_of_birth DATE NOT NULL,
        body_type ENUM('slim', 'average', 'athletic', 'heavy') DEFAULT 'average',
        height VARCHAR(50) NOT NULL,
        disabilities VARCHAR(100) DEFAULT 'no_disability',
        have_children VARCHAR(100) DEFAULT 'no_children',
        blood_group VARCHAR(20) DEFAULT 'others',
        zodiac_sign VARCHAR(50),
        hair_color VARCHAR(50) DEFAULT 'black',
        eye_color VARCHAR(50) DEFAULT 'black',
        weight VARCHAR(20),
        education VARCHAR(100),
        income VARCHAR(50),
        family_type ENUM('nuclear', 'joint') DEFAULT 'nuclear',
        about_me TEXT,
        partner_preference TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    
    $conn->exec($sql);
    echo "User profiles table created successfully<br>";
    
    // Create partner_preferences table
    $sql = "CREATE TABLE IF NOT EXISTS partner_preferences (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        age_from INT(2) DEFAULT 18,
        age_to INT(2) DEFAULT 35,
        height_from VARCHAR(20),
        height_to VARCHAR(20),
        complexion VARCHAR(50),
        education VARCHAR(100),
        occupation VARCHAR(100),
        income_from VARCHAR(50),
        income_to VARCHAR(50),
        location VARCHAR(100),
        marital_status VARCHAR(50),
        family_type VARCHAR(50),
        about_partner TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    $conn->exec($sql);
    echo "Partner preferences table created successfully<br>";
    
    // Create login_attempts table for security
    $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        success BOOLEAN DEFAULT FALSE,
        INDEX idx_email (email),
        INDEX idx_attempt_time (attempt_time)
    )";
    
    $conn->exec($sql);
    echo "Login attempts table created successfully<br>";
    
    // Create user_photos table
    $sql = "CREATE TABLE IF NOT EXISTS user_photos (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        photo_path VARCHAR(255) NOT NULL,
        is_primary BOOLEAN DEFAULT FALSE,
        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    $conn->exec($sql);
    echo "User photos table created successfully<br>";
    
    // Insert a test user (optional)
    $testEmail = "test@bibahabd.com";
    $testPassword = password_hash("test123", PASSWORD_DEFAULT);
    $testProfileId = "BB" . date('Y') . "001";
    
    $stmt = $conn->prepare("INSERT IGNORE INTO users (profile_id, email, password_hash, first_name, last_name, gender, occupation) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$testProfileId, $testEmail, $testPassword, "Test", "User", "male", "ARCHITECT"]);
    
    if ($stmt->rowCount() > 0) {
        echo "Test user created successfully<br>";
        echo "Email: test@bibahabd.com<br>";
        echo "Password: test123<br>";
        echo "Profile ID: $testProfileId<br>";
        
        // Get the user ID
        $userId = $conn->lastInsertId();
        
        // Insert test profile data
        $profileStmt = $conn->prepare("INSERT IGNORE INTO user_profiles (profile_id, user_id, marital_status, complexion, date_of_birth, body_type, height) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $profileStmt->execute([$testProfileId, $userId, 'never_married', 'fair', '1990-01-01', 'average', '5ft_8in']);
        
        if ($profileStmt->rowCount() > 0) {
            echo "Test profile created successfully<br>";
        }
    }
    
    echo "<br><h3>Database setup completed successfully!</h3>";
    echo "<h4>XAMPP Configuration:</h4>";
    echo "Server: localhost<br>";
    echo "Username: root<br>";
    echo "Password: (empty)<br>";
    echo "Database: bibahabd_db<br><br>";
    
    echo "<h4>Test Login Credentials:</h4>";
    echo "Email: test@bibahabd.com<br>";
    echo "Password: test123<br>";
    echo "Profile ID: $testProfileId<br><br>";
    
    echo "<h4>Next Steps:</h4>";
    echo "1. Make sure XAMPP Apache and MySQL are running<br>";
    echo "2. Access phpMyAdmin at http://localhost/phpmyadmin<br>";
    echo "3. You can now test the registration and login functionality<br>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
