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
    
    // Get profile data from user_profiles table (from registration)
    $stmt = $conn->prepare("SELECT * FROM user_profiles WHERE profile_id = :profile_id");
    $stmt->bindParam(':profile_id', $profileId);
    $stmt->execute();
    
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($profile) {
        echo json_encode([
            'success' => true,
            'profile' => $profile
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
