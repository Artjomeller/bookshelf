<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Register a new user
    public function register($username, $email, $password, $full_name = null) {
        // Check if username or email already exists
        if ($this->usernameExists($username)) {
            return [
                'success' => false,
                'message' => 'Username already exists'
            ];
        }
        
        if ($this->emailExists($email)) {
            return [
                'success' => false,
                'message' => 'Email already exists'
            ];
        }
        
        // Hash the password using bcrypt
        $options = [
            'cost' => 12 // Higher security
        ];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, $options);
        
        // Insert the new user
        $query = "INSERT INTO users (username, email, password, full_name) 
                  VALUES (:username, :email, :password, :full_name)";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashed_password,
                ':full_name' => $full_name
            ]);
            
            return [
                'success' => true,
                'user_id' => $this->pdo->lastInsertId(),
                'message' => 'User registered successfully'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Login a user
    public function login($username, $password) {
        // Find user by username
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        // If user not found, try finding by email
        if (!$user) {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':email' => $username]); // username field might contain email
            $user = $stmt->fetch();
        }
        
        // If user found and password is correct (either through password_verify or hardcoded credentials)
        if ($user && (
            password_verify($password, $user['password']) || 
            ($user['username'] == 'admin' && $password == 'Parool11') || 
            ($user['username'] == 'Kasutaja' && $password == 'Parool12')
        )) {
            // Create user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Check if user is admin (in this case, id=1 is admin)
            $_SESSION['is_admin'] = ($user['id'] == 1);
            
            return [
                'success' => true,
                'user' => $user,
                'message' => 'Login successful'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Invalid username/email or password'
        ];
    }
    
    // Get user by ID
    public function getById($user_id) {
        $query = "SELECT id, username, email, full_name, created_at, updated_at 
                  FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $user_id]);
        
        return $stmt->fetch();
    }
    
    // Update user profile
    public function updateProfile($user_id, $data) {
        // Build the SET part of the query
        $set_fields = [];
        $params = [':id' => $user_id];
        
        // Only update fields that are provided
        if (isset($data['full_name'])) {
            $set_fields[] = "full_name = :full_name";
            $params[':full_name'] = $data['full_name'];
        }
        
        if (isset($data['email'])) {
            // Check if email exists for another user
            $query = "SELECT id FROM users WHERE email = :email AND id != :user_id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':email' => $data['email'],
                ':user_id' => $user_id
            ]);
            
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Email already in use by another account'
                ];
            }
            
            $set_fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        
        // If no fields to update
        if (empty($set_fields)) {
            return [
                'success' => false,
                'message' => 'No fields to update'
            ];
        }
        
        // Build the full query
        $query = "UPDATE users SET " . implode(', ', $set_fields) . " WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            // Update session variables if needed
            if (isset($data['email'])) {
                $_SESSION['email'] = $data['email'];
            }
            
            if (isset($data['full_name'])) {
                $_SESSION['full_name'] = $data['full_name'];
            }
            
            return [
                'success' => true,
                'message' => 'Profile updated successfully'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Change password
    public function changePassword($user_id, $current_password, $new_password) {
        // Get user's current password
        $query = "SELECT password FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();
        
        // Verify current password
        if (!$user || !password_verify($current_password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect'
            ];
        }
        
        // Hash the new password
        $options = [
            'cost' => 12 // Higher security
        ];
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, $options);
        
        // Update the password
        $query = "UPDATE users SET password = :password WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':password' => $hashed_password,
                ':id' => $user_id
            ]);
            
            return [
                'success' => true,
                'message' => 'Password changed successfully'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Password change failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Check if username exists
    public function usernameExists($username) {
        $query = "SELECT id FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':username' => $username]);
        
        return $stmt->fetch() ? true : false;
    }
    
    // Check if email exists
    public function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':email' => $email]);
        
        return $stmt->fetch() ? true : false;
    }
}