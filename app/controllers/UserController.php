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
            header('Location: ' . BASE_URL . '/dashboard.php');
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
            header('Location: ' . BASE_URL . '/dashboard.php');
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
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        }

        // Prevent non-owners from editing owners
        $target_user = $this->userModel->getUserById($user_id);
        if (!$target_user) {
            $_SESSION['error'] = "User not found.";
            header('Location: ' . BASE_URL . '/users.php');
            exit;
        }

        if ($target_user['role_name'] === 'Owner' && SessionManager::getUserRole() !== 'Owner') {
            $_SESSION['error'] = "Only Owner can edit other Owner accounts.";
            header('Location: ' . BASE_URL . '/users.php');
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
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        }

        // Prevent deletion of own account
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['error'] = "You cannot delete your own account.";
            header('Location: ' . BASE_URL . '/users.php');
            exit;
        }

        // Prevent non-owners from deleting owners
        $target_user = $this->userModel->getUserById($user_id);
        if (!$target_user) {
            $_SESSION['error'] = "User not found.";
            header('Location: ' . BASE_URL . '/users.php');
            exit;
        }

        if ($target_user['role_name'] === 'Owner' && SessionManager::getUserRole() !== 'Owner') {
            $_SESSION['error'] = "Only Owner can delete other Owner accounts.";
            header('Location: ' . BASE_URL . '/users.php');
            exit;
        }

        if ($this->userModel->deleteUser($user_id)) {
            $_SESSION['success'] = "User deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete user.";
        }

        header('Location: ' . BASE_URL . '/users.php');
        exit;
    }

    public function toggleStatus($user_id)
    {
        $this->requireLogin();
        $this->requireRole(['Owner', 'Admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $user = $this->userModel->findById($user_id);
                if (!$user) {
                    $this->flashMessage('error', 'User tidak ditemukan.');
                    $this->redirect('/users');
                }

                $new_status = $user['status'] ? 0 : 1;
                $this->userModel->updateStatus($user_id, $new_status);

                $status_text = $new_status ? 'aktif' : 'nonaktif';
                $this->flashMessage('success', "Status user berhasil diubah menjadi $status_text.");
            } catch (Exception $e) {
                error_log("Toggle status error: " . $e->getMessage());
                $this->flashMessage('error', 'Gagal mengubah status user.');
            }
        }

        $this->redirect('/users');
    }

    public function profile()
    {
        $this->requireLogin();
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$user) {
            $this->flashMessage('error', 'Profile tidak ditemukan.');
            $this->redirect('/dashboard');
        }

        $this->render('users/profile', [
            'title' => 'My Profile - Regina Hotel',
            'user' => $user
        ]);
    }

    public function updateProfile()
    {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
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
                foreach ($errors as $error) {
                    $this->flashMessage('error', $error);
                }
                $this->redirect('/profile');
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

            $this->userModel->update($user_id, $update_data);
            
            // Update session data
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['username'] = $data['username'];

            $this->flashMessage('success', 'Profile berhasil diupdate.');
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            $this->flashMessage('error', 'Gagal mengupdate profile.');
        }

        $this->redirect('/profile');
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
                header('Location: ' . BASE_URL . '/users.php?action=create');
                exit;
            }

            if ($this->userModel->createUser($data)) {
                $_SESSION['success'] = "User created successfully.";
                header('Location: ' . BASE_URL . '/users.php');
            } else {
                $_SESSION['error'] = "Failed to create user.";
                $_SESSION['form_data'] = $data;
                header('Location: ' . BASE_URL . '/users.php?action=create');
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/users.php?action=create');
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
                header('Location: ' . BASE_URL . '/users.php?action=edit&id=' . $user_id);
                exit;
            }

            if ($this->userModel->updateUser($data)) {
                $_SESSION['success'] = "User updated successfully.";
                header('Location: ' . BASE_URL . '/users.php');
            } else {
                $_SESSION['error'] = "Failed to update user.";
                $_SESSION['form_data'] = $data;
                header('Location: ' . BASE_URL . '/users.php?action=edit&id=' . $user_id);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/users.php?action=edit&id=' . $user_id);
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
            $existing_user = $this->userModel->findByUsername($data['username']);
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
                $current_user = $this->userModel->findById($user_id);
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
