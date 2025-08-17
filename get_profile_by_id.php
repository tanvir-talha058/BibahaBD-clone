<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration for XAMPP
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bibahabd_db";

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['profile_id'])) {
    echo json_encode(['success' => false, 'message' => 'Profile ID is required']);
    exit;
}

$profileId = $input['profile_id'];

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get user data by profile_id
    $stmt = $conn->prepare("SELECT * FROM users WHERE profile_id = :profile_id");
    $stmt->bindParam(':profile_id', $profileId);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Remove sensitive data
        unset($user['password_hash']);
        
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        // Try to get from user_profiles table
        $profileStmt = $conn->prepare("SELECT * FROM user_profiles WHERE profile_id = :profile_id");
        $profileStmt->bindParam(':profile_id', $profileId);
        $profileStmt->execute();
        
        $profile = $profileStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($profile) {
            // Create a basic user object from profile data
            $user = [
                'profile_id' => $profile['profile_id'],
                'first_name' => 'New',
                'last_name' => 'User',
                'email' => $profile['profile_id'] . '@bibahabd.com',
                'date_of_birth' => $profile['date_of_birth'],
                'marital_status' => $profile['marital_status'],
                'status' => 'active'
            ];
            
            echo json_encode([
                'success' => true,
                'user' => $user,
                'source' => 'profiles_table'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
