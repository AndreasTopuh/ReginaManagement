<?php
class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index()
    {
        requireLogin();

        // Check if user is Owner or Admin
        if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
            $_SESSION['error'] = "Access denied. Only Owner and Admin can manage users.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $users = $this->userModel->getAllUsers();
        include APP_PATH . '/views/users/index.php';
    }

    public function create()
    {
        requireLogin();

        // Check if user is Owner or Admin
        if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
            $_SESSION['error'] = "Access denied. Only Owner and Admin can manage users.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateUser();
        } else {
            include APP_PATH . '/views/users/create.php';
        }
    }

    public function edit($user_id)
    {
        requireLogin();

        // Check if user is Owner or Admin
        if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
            $_SESSION['error'] = "Access denied. Only Owner and Admin can manage users.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        // Prevent non-owners from editing owners
        $target_user = $this->userModel->getUserById($user_id);
        if (!$target_user) {
            $_SESSION['error'] = "User not found.";
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        if ($target_user['role_name'] === 'Owner' && SessionManager::getUserRole() !== 'Owner') {
            $_SESSION['error'] = "Only Owner can edit other Owner accounts.";
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditUser($user_id);
        } else {
            $user = $target_user;
            include APP_PATH . '/views/users/edit.php';
        }
    }

    public function delete($user_id)
    {
        requireLogin();

        // Check if user is Owner or Admin
        if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
            $_SESSION['error'] = "Access denied. Only Owner and Admin can manage users.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        // Only handle POST requests for delete
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Prevent deletion of own account
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['error'] = "You cannot delete your own account.";
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Prevent non-owners from deleting owners
        $target_user = $this->userModel->getUserById($user_id);
        if (!$target_user) {
            $_SESSION['error'] = "User not found.";
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        if ($target_user['role_name'] === 'Owner' && SessionManager::getUserRole() !== 'Owner') {
            $_SESSION['error'] = "Only Owner can delete other Owner accounts.";
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        if ($this->userModel->deleteUser($user_id)) {
            $_SESSION['success'] = "User deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete user.";
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }

    public function toggleStatus($user_id)
    {
        requireLogin();

        // Check if user is Owner or Admin
        if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
            $_SESSION['error'] = "Access denied. Only Owner and Admin can manage users.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        // Only handle POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        try {
            $user = $this->userModel->getUserById($user_id);
            if (!$user) {
                $_SESSION['error'] = 'User not found.';
                header('Location: ' . BASE_URL . '/users');
                exit;
            }

            // Prevent modifying own account status
            if ($user_id == $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot change your own account status.";
                header('Location: ' . BASE_URL . '/users');
                exit;
            }

            // Prevent non-owners from modifying owner status
            if ($user['role_name'] === 'Owner' && SessionManager::getUserRole() !== 'Owner') {
                $_SESSION['error'] = "Only Owner can modify other Owner account status.";
                header('Location: ' . BASE_URL . '/users');
                exit;
            }

            $new_status = $user['status'] ? 0 : 1;
            if ($this->userModel->updateStatus($user_id, $new_status)) {
                $status_text = $new_status ? 'activated' : 'deactivated';
                $_SESSION['success'] = "User successfully $status_text.";
            } else {
                $_SESSION['error'] = 'Failed to update user status.';
            }
        } catch (Exception $e) {
            error_log("Toggle status error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update user status.';
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }

    public function profile()
    {
        requireLogin();

        $user = $this->userModel->getUserById($_SESSION['user_id']);
        if (!$user) {
            $_SESSION['error'] = 'Profile not found.';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        include APP_PATH . '/views/users/profile.php';
    }

    public function updateProfile()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        try {
            $user_id = $_SESSION['user_id'];
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'current_password' => $_POST['current_password'] ?? '',
                'new_password' => $_POST['new_password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? ''
            ];

            // Validate input
            $errors = $this->validateProfileInput($data, $user_id);
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                header('Location: ' . BASE_URL . '/profile');
                exit;
            }

            // Handle photo upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $photo_filename = $this->userModel->uploadPhoto($_FILES['photo'], $user_id);
                } catch (Exception $e) {
                    $_SESSION['error'] = "Failed to upload photo: " . $e->getMessage();
                    header('Location: ' . BASE_URL . '/profile');
                    exit;
                }
            }

            // Update profile data
            $update_data = [
                'name' => $data['name'],
                'username' => $data['username']
            ];

            // If password is being changed
            if (!empty($data['new_password'])) {
                $update_data['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
            }

            if ($this->userModel->update($user_id, $update_data)) {
                // Update session data
                $_SESSION['user_name'] = $data['name'];
                $_SESSION['username'] = $data['username'];

                $_SESSION['success'] = 'Profile updated successfully.';
            } else {
                $_SESSION['error'] = 'Failed to update profile.';
            }
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update profile.';
        }

        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    public function deletePhoto($user_id = null)
    {
        requireLogin();

        // If no user_id provided, assume it's current user's profile
        if ($user_id === null) {
            $user_id = $_SESSION['user_id'];
            $redirect_url = BASE_URL . '/profile';
        } else {
            // Check if user can delete other users' photos
            if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
                $_SESSION['error'] = "Access denied.";
                header('Location: ' . BASE_URL . '/users');
                exit;
            }
            $redirect_url = BASE_URL . '/users/' . $user_id . '/edit';
        }

        try {
            if ($this->userModel->deletePhoto($user_id)) {
                $_SESSION['success'] = 'Photo deleted successfully.';
            } else {
                $_SESSION['error'] = 'No photo to delete or failed to delete photo.';
            }
        } catch (Exception $e) {
            error_log("Delete photo error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete photo.';
        }

        header('Location: ' . $redirect_url);
        exit;
    }

    private function handleCreateUser()
    {
        try {
            $data = [
                'username' => trim($_POST['username']),
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'role' => $_POST['role'],
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password']
            ];

            // Validate input
            $errors = $this->validateUserInput($data);

            // Check if username exists
            if ($this->userModel->getUserByUsername($data['username'])) {
                $errors[] = "Username already exists.";
            }

            // Check if email exists
            if ($this->userModel->getUserByEmail($data['email'])) {
                $errors[] = "Email already exists.";
            }

            // Only Owner can create other Owners
            if ($data['role'] === 'Owner' && SessionManager::getUserRole() !== 'Owner') {
                $errors[] = "Only Owner can create other Owner accounts.";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $data;
                header('Location: ' . BASE_URL . '/users/create');
                exit;
            }

            // Handle photo upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    // Create user first to get user ID
                    if ($this->userModel->createUser($data)) {
                        $user_id = Database::getInstance()->lastInsertId();
                        $photo_filename = $this->userModel->uploadPhoto($_FILES['photo'], $user_id);
                        $_SESSION['success'] = "User created successfully with photo.";
                    } else {
                        $_SESSION['error'] = "Failed to create user.";
                        $_SESSION['form_data'] = $data;
                        header('Location: ' . BASE_URL . '/users/create');
                        exit;
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "User created but failed to upload photo: " . $e->getMessage();
                }
            } else {
                if ($this->userModel->createUser($data)) {
                    $_SESSION['success'] = "User created successfully.";
                } else {
                    $_SESSION['error'] = "Failed to create user.";
                    $_SESSION['form_data'] = $data;
                    header('Location: ' . BASE_URL . '/users/create');
                    exit;
                }
            }

            header('Location: ' . BASE_URL . '/users');
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/users/create');
        }
        exit;
    }

    private function handleEditUser($user_id)
    {
        try {
            $data = [
                'id' => $user_id,
                'username' => trim($_POST['username']),
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'role' => $_POST['role'],
                'status' => $_POST['status']
            ];

            // Add password if provided
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
                $data['confirm_password'] = $_POST['confirm_password'];
            }

            // Validate input
            $errors = $this->validateUserInput($data, true);

            // Check if username exists (excluding current user)
            $existing_user = $this->userModel->getUserByUsername($data['username']);
            if ($existing_user && $existing_user['id'] != $user_id) {
                $errors[] = "Username already exists.";
            }

            // Check if email exists (excluding current user)
            $existing_user = $this->userModel->getUserByEmail($data['email']);
            if ($existing_user && $existing_user['id'] != $user_id) {
                $errors[] = "Email already exists.";
            }

            // Only Owner can change role to Owner
            if ($data['role'] === 'Owner' && SessionManager::getUserRole() !== 'Owner') {
                $errors[] = "Only Owner can set other users as Owner.";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $data;
                header('Location: ' . BASE_URL . '/users/' . $user_id . '/edit');
                exit;
            }

            // Handle photo upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $photo_filename = $this->userModel->uploadPhoto($_FILES['photo'], $user_id);
                    $data['photo'] = $photo_filename;
                } catch (Exception $e) {
                    $_SESSION['error'] = "Failed to upload photo: " . $e->getMessage();
                    $_SESSION['form_data'] = $data;
                    header('Location: ' . BASE_URL . '/users/' . $user_id . '/edit');
                    exit;
                }
            }

            if ($this->userModel->updateUser($data)) {
                $_SESSION['success'] = "User updated successfully.";
                header('Location: ' . BASE_URL . '/users');
            } else {
                $_SESSION['error'] = "Failed to update user.";
                $_SESSION['form_data'] = $data;
                header('Location: ' . BASE_URL . '/users/' . $user_id . '/edit');
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/users/' . $user_id . '/edit');
        }
        exit;
    }

    private function validateUserInput($data, $is_edit = false)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = "Username is required.";
        } elseif (strlen($data['username']) < 3) {
            $errors[] = "Username must be at least 3 characters.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = "Username can only contain letters, numbers, and underscores.";
        }

        if (empty($data['full_name'])) {
            $errors[] = "Full name is required.";
        }

        if (empty($data['email'])) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        if (empty($data['role'])) {
            $errors[] = "Role is required.";
        } elseif (!in_array($data['role'], ['Owner', 'Admin', 'Receptionist'])) {
            $errors[] = "Invalid role selected.";
        }

        // Password validation (required for create, optional for edit)
        if (!$is_edit || !empty($data['password'])) {
            if (empty($data['password'])) {
                $errors[] = "Password is required.";
            } elseif (strlen($data['password']) < 6) {
                $errors[] = "Password must be at least 6 characters.";
            }

            if (empty($data['confirm_password'])) {
                $errors[] = "Confirm password is required.";
            } elseif ($data['password'] !== $data['confirm_password']) {
                $errors[] = "Passwords do not match.";
            }
        }

        return $errors;
    }

    private function validateProfileInput($data, $user_id)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = "Name is required.";
        }

        if (empty($data['username'])) {
            $errors[] = "Username is required.";
        } elseif (strlen($data['username']) < 3) {
            $errors[] = "Username must be at least 3 characters.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = "Username can only contain letters, numbers, and underscores.";
        } else {
            // Check if username already exists (exclude current user)
            $existing_user = $this->userModel->getUserByUsername($data['username']);
            if ($existing_user && $existing_user['id'] != $user_id) {
                $errors[] = "Username already exists.";
            }
        }

        // Password validation (only if changing password)
        if (!empty($data['new_password'])) {
            // Verify current password
            if (empty($data['current_password'])) {
                $errors[] = "Current password is required to change password.";
            } else {
                $current_user = $this->userModel->getUserById($user_id);
                if (!password_verify($data['current_password'], $current_user['password'])) {
                    $errors[] = "Current password is incorrect.";
                }
            }

            if (strlen($data['new_password']) < 6) {
                $errors[] = "New password must be at least 6 characters.";
            }

            if (empty($data['confirm_password'])) {
                $errors[] = "Confirm new password is required.";
            } elseif ($data['new_password'] !== $data['confirm_password']) {
                $errors[] = "New passwords do not match.";
            }
        }

        return $errors;
    }
}
