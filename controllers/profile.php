<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../functions.php';

$config = require __DIR__ . '/../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize auth service
$auth = new Auth($db);

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ' . url('login'));
    exit;
}

// Initialize user model
$userModel = new User($db);

// Get user data
$user = $userModel->getById($_SESSION['user_id']);

// Initialize variables
$errors = [];
$formData = $user;
$passwordUpdated = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Handle profile update
        $formData = [
            'first_name' => $_POST['first_name'] ?? $user['first_name'],
            'last_name' => $_POST['last_name'] ?? $user['last_name'],
            'email' => $_POST['email'] ?? $user['email'],
        ];
        
        // Validate required fields
        if (empty($formData['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }
        
        if (empty($formData['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }
        
        if (empty($formData['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif ($formData['email'] !== $user['email']) {
            // Check if email is already in use
            $existingUser = $userModel->getByEmail($formData['email']);
            if ($existingUser && $existingUser['user_id'] != $user['user_id']) {
                $errors['email'] = 'Email is already in use by another account';
            }
        }
        
        // If no errors, update user profile
        if (empty($errors)) {
            $updated = $userModel->update($user['user_id'], $formData);
            
            if ($updated) {
                // Set success message in session
                $_SESSION['flash_message'] = 'Profile updated successfully';
                $_SESSION['flash_message_type'] = 'success';
                
                // Update session data if needed
                if ($formData['username'] ?? $user['username'] !== $user['username']) {
                    $_SESSION['username'] = $formData['username'] ?? $user['username'];
                }
                
                // Redirect to prevent form resubmission
                header('Location: ' . url('profile'));
                exit;
            } else {
                $errors['general'] = 'Failed to update profile. Please try again.';
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Handle password change
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate password fields
        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required';
        } elseif (!$userModel->verifyPassword($currentPassword, $user['password_hash'])) {
            $errors['current_password'] = 'Current password is incorrect';
        }
        
        if (empty($newPassword)) {
            $errors['new_password'] = 'New password is required';
        } elseif (strlen($newPassword) < 8) {
            $errors['new_password'] = 'Password must be at least 8 characters';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // If no errors, update password
        if (empty($errors)) {
            $updated = $userModel->updatePassword($user['user_id'], $newPassword);
            
            if ($updated) {
                // Set success message in session
                $_SESSION['flash_message'] = 'Password changed successfully';
                $_SESSION['flash_message_type'] = 'success';
                $passwordUpdated = true;
                
                // Redirect to prevent form resubmission
                header('Location: ' . url('profile'));
                exit;
            } else {
                $errors['general'] = 'Failed to change password. Please try again.';
            }
        }
    }
}

// Load view
require __DIR__ . '/../views/profile.view.php';