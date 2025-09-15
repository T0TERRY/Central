<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'register') {
        // User registration
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }
        
        if ($password !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            exit;
        }
        
        $conn = getDBConnection();
        
        // Check if user already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'User with this email already exists']);
            closeDBConnection($conn);
            exit;
        }
        
        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        
        if ($stmt->execute()) {
            $userId = $conn->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Registration successful',
                'user' => ['id' => $userId, 'name' => $name, 'email' => $email]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
        
        closeDBConnection($conn);
    }
    elseif ($action === 'login') {
        // User login
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required']);
            exit;
        }
        
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful',
                    'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        
        closeDBConnection($conn);
    }
    elseif ($action === 'logout') {
        // User logout
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>