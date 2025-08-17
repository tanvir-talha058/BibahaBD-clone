<?php
header('Content-Type: text/html');
echo '<!DOCTYPE html>
<html>
<head>
    <title>Profile Login Fix</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 900px; margin: 0 auto; padding: 20px; }
        h1, h2, h3 { color: #4CAF50; }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        .card { border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 20px; }
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
    <h1>Profile Login Fix Tool</h1>';

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bibahabd_db";

// Get profile ID from query string or form
$profileId = $_GET['profile_id'] ?? '';

try {
    // Connect to database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<p class="success">✓ Connected to database</p>';
    
    // Show form if no profile ID provided
    if (!$profileId) {
        echo '<div class="card">
            <h2>Find Profiles Without User Accounts</h2>';
        
        // Check for orphaned profiles
        $stmt = $conn->prepare("
            SELECT p.* FROM user_profiles p
            LEFT JOIN users u ON p.profile_id = u.profile_id
            WHERE u.id IS NULL
        ");
        $stmt->execute();
        $orphanedProfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($orphanedProfiles) > 0) {
            echo '<p>Found ' . count($orphanedProfiles) . ' profiles without user accounts:</p>';
            echo '<ul>';
            foreach ($orphanedProfiles as $profile) {
                echo '<li>Profile ID: ' . $profile['profile_id'] . ' - <a href="?profile_id=' . $profile['profile_id'] . '">Create user account</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>All profiles have associated user accounts.</p>';
        }
        
        echo '</div>';
        
        echo '<div class="card">
            <h2>Enter Profile ID Manually</h2>
            <form method="get">
                <div>
                    <label for="profile_id">Profile ID:</label>
                    <input type="text" id="profile_id" name="profile_id" required>
                </div>
                <div style="margin-top: 10px;">
                    <button type="submit" class="action-btn">Create User Account</button>
                </div>
            </form>
        </div>';
    } else {
        echo '<div class="card">';
        echo '<h2>Creating User Account for Profile ID: ' . htmlspecialchars($profileId) . '</h2>';
        
        // Check if profile exists
        $profileStmt = $conn->prepare("SELECT * FROM user_profiles WHERE profile_id = ?");
        $profileStmt->execute([$profileId]);
        $profile = $profileStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$profile) {
            echo '<p class="error">Profile not found!</p>';
        } else {
            echo '<p>Found profile with ID: ' . $profile['profile_id'] . '</p>';
            
            // Check if user already exists
            $userStmt = $conn->prepare("SELECT * FROM users WHERE profile_id = ?");
            $userStmt->execute([$profileId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo '<p class="warning">User account already exists with this profile ID.</p>';
                echo '<pre>';
                print_r($user);
                echo '</pre>';
                echo '<p>You can use these credentials to log in:</p>';
                echo '<ul>';
                echo '<li><strong>Email/Profile ID:</strong> ' . $user['email'] . ' or ' . $user['profile_id'] . '</li>';
                echo '<li><strong>Password:</strong> welcome123 (default password)</li>';
                echo '</ul>';
            } else {
                // Create user account
                $email = strtolower($profileId) . "@bibahabd.com";
                $passwordHash = password_hash("welcome123", PASSWORD_DEFAULT);
                
                $insertStmt = $conn->prepare("
                    INSERT INTO users (
                        profile_id, email, password_hash, first_name, last_name, 
                        date_of_birth, marital_status, profile_created_by, looking_for,
                        status, created_at, updated_at
                    ) VALUES (
                        ?, ?, ?, 'New', 'User', ?, ?, 'Self', 'bride', 'active', NOW(), NOW()
                    )
                ");
                
                $insertStmt->execute([
                    $profileId,
                    $email,
                    $passwordHash,
                    $profile['date_of_birth'],
                    $profile['marital_status']
                ]);
                
                $userId = $conn->lastInsertId();
                
                echo '<p class="success">✓ User account created successfully!</p>';
                echo '<p>User ID: ' . $userId . '</p>';
                echo '<p>You can now log in with these credentials:</p>';
                echo '<ul>';
                echo '<li><strong>Email/Profile ID:</strong> ' . $email . ' or ' . $profileId . '</li>';
                echo '<li><strong>Password:</strong> welcome123</li>';
                echo '</ul>';
                
                // Update user_profiles table with user_id if needed
                if ($profile['user_id'] === null) {
                    $updateStmt = $conn->prepare("UPDATE user_profiles SET user_id = ? WHERE profile_id = ?");
                    $updateStmt->execute([$userId, $profileId]);
                    echo '<p class="success">✓ Updated user_profiles table with user ID</p>';
                }
            }
        }
        echo '</div>';
    }
    
} catch (PDOException $e) {
    echo '<p class="error">Database error: ' . $e->getMessage() . '</p>';
}

echo '<h2>Next Steps</h2>';
echo '<div>';
echo '<a href="indext.html" class="action-btn">Go to Login Page</a>';
echo '<a href="database_status.php" class="action-btn">Check Database Status</a>';
echo '<a href="profile_debug.html" class="action-btn">Debug Profile Loading</a>';
echo '</div>';

echo '</body>
</html>';
?>
