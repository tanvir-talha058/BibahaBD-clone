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
    $requiredFields = [
        'profile_created_by', 'looking_for', 'first_name', 'last_name', 
        'religion', 'caste', 'birth_year', 'birth_month', 'birth_day',
        'marital_status', 'education', 'profession', 'country', 'division',
        'district', 'upazila', 'email', 'confirm_email', 'phone', 
        'guardian_phone', 'password', 'confirm_password'
    ];
    
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = 'This field is required';
        }
    }
    
    // Email validation
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    // Email confirmation
    if ($data['email'] !== $data['confirm_email']) {
        $errors['confirm_email'] = 'Email addresses do not match';
    }
    
    // Password validation
    if (!empty($data['password']) && strlen($data['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters long';
    }
    
    // Password confirmation
    if ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    // Date of birth validation
    if (!empty($data['birth_year']) && !empty($data['birth_month']) && !empty($data['birth_day'])) {
        $dob = new DateTime($data['birth_year'] . '-' . $data['birth_month'] . '-' . $data['birth_day']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        
        if ($age < 18) {
            $errors['date_of_birth'] = 'You must be at least 18 years old';
        }
        
        if ($age > 100) {
            $errors['date_of_birth'] = 'Please enter a valid date of birth';
        }
    }
    
    // Phone validation
    if (!empty($data['phone']) && !preg_match('/^[0-9+\-\s]+$/', $data['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number';
    }
    
    if (!empty($data['guardian_phone']) && !preg_match('/^[0-9+\-\s]+$/', $data['guardian_phone'])) {
        $errors['guardian_phone'] = 'Please enter a valid phone number';
    }
    
    // Terms validation
    if (empty($data['terms'])) {
        $errors['terms'] = 'You must agree to the terms and conditions';
    }
    
    return $errors;
}

// Generate profile ID
function generateProfileId($conn) {
    $year = date('Y');
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE profile_id LIKE 'B$year%'");
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
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $input['email']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'errors' => ['email' => 'Email address already exists']]);
        exit;
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    // Generate profile ID
    $profileId = generateProfileId($conn);
    
    // Hash password
    $passwordHash = password_hash($input['password'], PASSWORD_DEFAULT);
    
    // Combine date of birth
    $dateOfBirth = $input['birth_year'] . '-' . $input['birth_month'] . '-' . $input['birth_day'];
    
    // Determine gender based on looking_for
    $gender = ($input['looking_for'] === 'bride') ? 'male' : 'female';
    
    // Insert into users table
    $userSql = "INSERT INTO users (
        profile_id, email, password_hash, first_name, last_name, 
        phone, gender, religion, country, city, occupation, 
        profile_created_by, looking_for, guardian_phone, caste,
        date_of_birth, marital_status, education, division, district, upazila,
        created_at, updated_at
    ) VALUES (
        :profile_id, :email, :password_hash, :first_name, :last_name,
        :phone, :gender, :religion, :country, :upazila, :profession,
        :profile_created_by, :looking_for, :guardian_phone, :caste,
        :date_of_birth, :marital_status, :education, :division, :district, :upazila,
        NOW(), NOW()
    )";
    
    $userStmt = $conn->prepare($userSql);
    
    // Bind parameters for users table
    $userStmt->bindParam(':profile_id', $profileId);
    $userStmt->bindParam(':email', $input['email']);
    $userStmt->bindParam(':password_hash', $passwordHash);
    $userStmt->bindParam(':first_name', $input['first_name']);
    $userStmt->bindParam(':last_name', $input['last_name']);
    $userStmt->bindParam(':phone', $input['phone']);
    $userStmt->bindParam(':gender', $gender);
    $userStmt->bindParam(':religion', $input['religion']);
    $userStmt->bindParam(':country', $input['country']);
    $userStmt->bindParam(':upazila', $input['upazila']);
    $userStmt->bindParam(':profession', $input['profession']);
    $userStmt->bindParam(':profile_created_by', $input['profile_created_by']);
    $userStmt->bindParam(':looking_for', $input['looking_for']);
    $userStmt->bindParam(':guardian_phone', $input['guardian_phone']);
    $userStmt->bindParam(':caste', $input['caste']);
    $userStmt->bindParam(':date_of_birth', $dateOfBirth);
    $userStmt->bindParam(':marital_status', $input['marital_status']);
    $userStmt->bindParam(':education', $input['education']);
    $userStmt->bindParam(':division', $input['division']);
    $userStmt->bindParam(':district', $input['district']);
    
    $userStmt->execute();
    $userId = $conn->lastInsertId();
    
    // Insert into user_profiles table for additional details
    $profileSql = "INSERT INTO user_profiles (
        profile_id, user_id, marital_status, date_of_birth,
        created_at, updated_at
    ) VALUES (
        :profile_id, :user_id, :marital_status, :date_of_birth,
        NOW(), NOW()
    )";
    
    $profileStmt = $conn->prepare($profileSql);
    $profileStmt->bindParam(':profile_id', $profileId);
    $profileStmt->bindParam(':user_id', $userId);
    $profileStmt->bindParam(':marital_status', $input['marital_status']);
    $profileStmt->bindParam(':date_of_birth', $dateOfBirth);
    
    $profileStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile created successfully! Redirecting to login page...',
        'profile_id' => $profileId,
        'user_id' => $userId
    ]);
    
} catch(PDOException $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Email or profile already exists']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

$conn = null;
?>
