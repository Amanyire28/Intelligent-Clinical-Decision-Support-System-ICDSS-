<?php
/**
 * USER MODEL
 * ============================================================================
 * Handles user authentication and user-related database operations
 * ============================================================================
 */

class User {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Authenticate user by username/email and password
     * Returns user data if successful, null otherwise
     */
    public function authenticate($username, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, role, password_hash, is_active 
                FROM users 
                WHERE username = :username AND is_active = TRUE
            ");
            
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();
            
            // If not found by username, try by email
            if (!$user) {
                $stmt = $this->db->prepare("
                    SELECT id, username, email, full_name, role, password_hash, is_active 
                    FROM users 
                    WHERE email = :username AND is_active = TRUE
                ");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();
            }
            
            if ($user && password_verify($password, $user['password_hash'])) {
                return $user;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Authentication Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new user (used during registration/admin setup)
     */
    public function createUser($username, $email, $password, $full_name, $role = 'doctor', $specialization = null, $license_number = null) {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO users (username, email, password_hash, full_name, role, specialization, license_number)
                VALUES (:username, :email, :password_hash, :full_name, :role, :specialization, :license_number)
            ");
            
            $result = $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $password_hash,
                ':full_name' => $full_name,
                ':role' => $role,
                ':specialization' => $specialization,
                ':license_number' => $license_number
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("User Creation Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, role, specialization, created_at 
                FROM users 
                WHERE id = :id AND is_active = TRUE
            ");
            
            $stmt->execute([':id' => $user_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all doctors (for admin view)
     */
    public function getAllDoctors() {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, specialization, license_number, created_at, is_active
                FROM users 
                WHERE role = 'doctor'
                ORDER BY full_name ASC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Doctors Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Deactivate user account
     */
    public function deactivateUser($user_id) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET is_active = FALSE WHERE id = :id");
            return $stmt->execute([':id' => $user_id]);
        } catch (PDOException $e) {
            error_log("Deactivate User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Activate user account
     */
    public function activateUser($user_id) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET is_active = TRUE WHERE id = :id");
            return $stmt->execute([':id' => $user_id]);
        } catch (PDOException $e) {
            error_log("Activate User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user information
     */
    public function updateUser($user_id, $full_name, $email, $specialization = null, $license_number = null) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET full_name = :full_name, 
                    email = :email, 
                    specialization = :specialization, 
                    license_number = :license_number
                WHERE id = :id
            ");

            return $stmt->execute([
                ':id' => $user_id,
                ':full_name' => $full_name,
                ':email' => $email,
                ':specialization' => $specialization,
                ':license_number' => $license_number
            ]);
        } catch (PDOException $e) {
            error_log("Update User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user account
     */
    public function deleteUser($user_id) {
        try {
            // Don't allow deleting the system admin (ID 1)
            if ($user_id == 1) {
                return false;
            }
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => $user_id]);
        } catch (PDOException $e) {
            error_log("Delete User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Change user password
     */
    public function changePassword($user_id, $new_password) {
        try {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
            return $stmt->execute([
                ':id' => $user_id,
                ':password_hash' => $password_hash
            ]);
        } catch (PDOException $e) {
            error_log("Change Password Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all admins
     */
    public function getAllAdmins() {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, created_at, is_active
                FROM users 
                WHERE role = 'admin'
                ORDER BY full_name ASC
            ");

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Admins Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if username already exists
     */
    public function usernameExists($username, $exclude_user_id = null) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE username = :username" . ($exclude_user_id ? " AND id != :exclude_id" : ""));
            
            $params = [':username' => $username];
            if ($exclude_user_id) {
                $params[':exclude_id'] = $exclude_user_id;
            }
            
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Check Username Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email already exists
     */
    public function emailExists($email, $exclude_user_id = null) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE email = :email" . ($exclude_user_id ? " AND id != :exclude_id" : ""));
            
            $params = [':email' => $email];
            if ($exclude_user_id) {
                $params[':exclude_id'] = $exclude_user_id;
            }
            
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Check Email Error: " . $e->getMessage());
            return false;
        }
    }
}
