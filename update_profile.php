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

if (!$input || !isset($input['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

// Validation function
function validateInput($data) {
    $errors = [];
    
    // Required fields
    $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'religion', 'profession', 'education', 'country', 'division', 'upazila'];
    
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = 'This field is required';
        }
    }
    
    // Email validation
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    // Phone validation
    if (!empty($data['phone']) && !preg_match('/^[0-9+\-\s]+$/', $data['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number';
    }
    
    return $errors;
}

// Validate input
$errors = validateInput($input);

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if email is already used by another user
    $emailStmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
    $emailStmt->bindParam(':email', $input['email']);
    $emailStmt->bindParam(':user_id', $input['user_id']);
    $emailStmt->execute();
    
    if ($emailStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'errors' => ['email' => 'Email address is already in use']]);
        exit;
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    // Update users table
    $sql = "UPDATE users SET 
        first_name = :first_name,
        last_name = :last_name,
        email = :email,
        phone = :phone,
        religion = :religion,
        profession = :profession,
        education = :education,
        country = :country,
        division = :division,
        upazila = :upazila,
        updated_at = NOW()
        WHERE id = :user_id";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':first_name', $input['first_name']);
    $stmt->bindParam(':last_name', $input['last_name']);
    $stmt->bindParam(':email', $input['email']);
    $stmt->bindParam(':phone', $input['phone']);
    $stmt->bindParam(':religion', $input['religion']);
    $stmt->bindParam(':profession', $input['profession']);
    $stmt->bindParam(':education', $input['education']);
    $stmt->bindParam(':country', $input['country']);
    $stmt->bindParam(':division', $input['division']);
    $stmt->bindParam(':upazila', $input['upazila']);
    $stmt->bindParam(':user_id', $input['user_id']);
    
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!'
    ]);
    
} catch(PDOException $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>
