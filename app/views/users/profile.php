<?php
$title = "My Profile - Regina Hotel";
include INCLUDES_PATH . '/header.php';

// Get form data from session if validation failed, otherwise use user data
$form_data = $_SESSION['form_data'] ?? $user;
unset($_SESSION['form_data']);
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user-circle"></i> My Profile</h1>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php unset($_SESSION['success']);
endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php unset($_SESSION['error']);
endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-edit"></i> Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/profile">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                    class="form-control" 
                                    id="name" 
                                    name="name" 
                                    value="<?= htmlspecialchars($form_data['name'] ?? '') ?>" 
                                    required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" 
                                    class="form-control" 
                                    id="username" 
                                    name="username" 
                                    value="<?= htmlspecialchars($form_data['username'] ?? '') ?>" 
                                    required>
                                <div class="form-text">Username can only contain letters, numbers, and underscores.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" 
                                    class="form-control" 
                                    id="role" 
                                    value="<?= htmlspecialchars($user['role_name'] ?? '') ?>" 
                                    readonly>
                                <div class="form-text">Role cannot be changed from profile.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" 
                                    class="form-control" 
                                    id="status" 
                                    value="<?= $user['status'] ? 'Active' : 'Inactive' ?>" 
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <h6 class="mb-3"><i class="fas fa-key"></i> Change Password (Optional)</h6>
                    <p class="text-muted">Leave blank if you don't want to change your password.</p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" 
                                    class="form-control" 
                                    id="current_password" 
                                    name="current_password">
                                <div class="form-text">Required if changing password.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" 
                                    class="form-control" 
                                    id="new_password" 
                                    name="new_password">
                                <div class="form-text">Minimum 6 characters.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" 
                                    class="form-control" 
                                    id="confirm_password" 
                                    name="confirm_password">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-info-circle"></i> Profile Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>User ID:</strong><br>
                    <span class="text-muted">#<?= $user['id'] ?></span>
                </div>
                
                <div class="mb-3">
                    <strong>Member Since:</strong><br>
                    <span class="text-muted">
                        <?php 
                        $created_date = new DateTime($user['created_at'] ?? 'now');
                        echo $created_date->format('F j, Y'); 
                        ?>
                    </span>
                </div>
                
                <div class="mb-3">
                    <strong>Current Role:</strong><br>
                    <span class="badge bg-<?= $user['role_name'] === 'Owner' ? 'danger' : ($user['role_name'] === 'Admin' ? 'warning' : 'info') ?>">
                        <?= htmlspecialchars($user['role_name']) ?>
                    </span>
                </div>
                
                <div class="mb-0">
                    <strong>Account Status:</strong><br>
                    <span class="badge bg-<?= $user['status'] ? 'success' : 'secondary' ?>">
                        <?= $user['status'] ? 'Active' : 'Inactive' ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6><i class="fas fa-shield-alt"></i> Security Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small">
                    <li><i class="fas fa-check text-success"></i> Use a strong password with at least 6 characters</li>
                    <li><i class="fas fa-check text-success"></i> Include numbers and special characters</li>
                    <li><i class="fas fa-check text-success"></i> Don't share your login credentials</li>
                    <li><i class="fas fa-check text-success"></i> Log out when finished using the system</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password validation
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePasswords() {
        // If new password is entered, require current password
        if (newPassword.value && !currentPassword.value) {
            currentPassword.setCustomValidity('Current password is required to change password');
        } else {
            currentPassword.setCustomValidity('');
        }
        
        // Check if passwords match
        if (newPassword.value && confirmPassword.value && newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    newPassword.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    currentPassword.addEventListener('input', validatePasswords);
});
</script>
