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

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Validation function
function validateInput($data) {
    $errors = [];
    
    // Required fields
    $requiredFields = ['marital_status', 'complexion', 'date_of_birth', 'body_type', 'height'];
    
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = 'This field is required';
        }
    }
    
    // Date of birth validation
    if (!empty($data['date_of_birth'])) {
        $dob = new DateTime($data['date_of_birth']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        
        if ($age < 18) {
            $errors['date_of_birth'] = 'You must be at least 18 years old';
        }
        
        if ($age > 100) {
            $errors['date_of_birth'] = 'Please enter a valid date of birth';
        }
    }
    
    // Validate marital status
    $validMaritalStatus = ['never_married', 'divorced', 'widowed'];
    if (!empty($data['marital_status']) && !in_array($data['marital_status'], $validMaritalStatus)) {
        $errors['marital_status'] = 'Invalid marital status';
    }
    
    // Validate complexion
    $validComplexion = ['very_fair', 'fair', 'wheatish', 'dark'];
    if (!empty($data['complexion']) && !in_array($data['complexion'], $validComplexion)) {
        $errors['complexion'] = 'Invalid complexion';
    }
    
    // Validate body type
    $validBodyType = ['slim', 'average', 'athletic', 'heavy'];
    if (!empty($data['body_type']) && !in_array($data['body_type'], $validBodyType)) {
        $errors['body_type'] = 'Invalid body type';
    }
    
    return $errors;
}

// Generate profile ID
function generateProfileId($conn) {
    $year = date('Y');
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_profiles WHERE profile_id LIKE 'B$year%'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'] + 1;
    return 'B' . $year . str_pad($count, 3, '0', STR_PAD_LEFT);
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
    
    // Start transaction
    $conn->beginTransaction();
    
    // Generate profile ID
    $profileId = generateProfileId($conn);
    
    // Insert into user_profiles table
    $sql = "INSERT INTO user_profiles (
        profile_id,
        marital_status,
        complexion,
        date_of_birth,
        body_type,
        height,
        disabilities,
        have_children,
        blood_group,
        zodiac_sign,
        hair_color,
        eye_color,
        created_at,
        updated_at
    ) VALUES (
        :profile_id,
        :marital_status,
        :complexion,
        :date_of_birth,
        :body_type,
        :height,
        :disabilities,
        :have_children,
        :blood_group,
        :zodiac_sign,
        :hair_color,
        :eye_color,
        NOW(),
        NOW()
    )";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':profile_id', $profileId);
    $stmt->bindParam(':marital_status', $input['marital_status']);
    $stmt->bindParam(':complexion', $input['complexion']);
    $stmt->bindParam(':date_of_birth', $input['date_of_birth']);
    $stmt->bindParam(':body_type', $input['body_type']);
    $stmt->bindParam(':height', $input['height']);
    $stmt->bindParam(':disabilities', $input['disabilities']);
    $stmt->bindParam(':have_children', $input['have_children']);
    $stmt->bindParam(':blood_group', $input['blood_group']);
    $stmt->bindParam(':zodiac_sign', $input['zodiac_sign']);
    $stmt->bindParam(':hair_color', $input['hair_color']);
    $stmt->bindParam(':eye_color', $input['eye_color']);
    
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!',
        'profile_id' => $profileId
    ]);
    
} catch(PDOException $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Profile already exists']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

$conn = null;
?>
