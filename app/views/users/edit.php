<?php
$title = "Edit User - Regina Hotel";
include INCLUDES_PATH . '/header.php';

// Get form data from session if validation failed, otherwise use user data
$form_data = $_SESSION['form_data'] ?? $user;
unset($_SESSION['form_data']);
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user-edit"></i> Edit User</h1>
            <a href="<?= BASE_URL ?>/users.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php unset($_SESSION['error']);
endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-edit"></i> User Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/users.php?action=edit&id=<?= $user['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name"
                                    value="<?= htmlspecialchars($form_data['name'] ?? $form_data['full_name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= htmlspecialchars($form_data['username'] ?? '') ?>" required>
                                <div class="form-text">Only letters, numbers, and underscores allowed. Minimum 3 characters.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <?php if (SessionManager::getUserRole() === 'Owner'): ?>
                                        <option value="Owner" <?= ($form_data['role_name'] ?? $form_data['role'] ?? '') === 'Owner' ? 'selected' : '' ?>>Owner</option>
                                    <?php endif; ?>
                                    <option value="Admin" <?= ($form_data['role_name'] ?? $form_data['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="Receptionist" <?= ($form_data['role_name'] ?? $form_data['role'] ?? '') === 'Receptionist' ? 'selected' : '' ?>>Receptionist</option>
                                </select>
                                <?php if (SessionManager::getUserRole() !== 'Owner'): ?>
                                    <div class="form-text">Note: Only Owner can set users as Owner.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="1" <?= ($form_data['status'] ?? '') == '1' ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= ($form_data['status'] ?? '') == '0' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Change Password (Optional)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="form-text">Leave blank to keep current password. Minimum 6 characters if changing.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>/users.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> User Details</h5>
            </div>
            <div class="card-body">
                <p><strong>User ID:</strong> <?= $user['id'] ?></p>
                <p><strong>Current Role:</strong>
                    <span class="badge bg-<?= $user['role_name'] === 'Owner' ? 'danger' : ($user['role_name'] === 'Admin' ? 'warning' : 'success') ?>">
                        <?= $user['role_name'] ?>
                    </span>
                </p>
                <p><strong>Current Status:</strong>
                    <span class="badge bg-<?= $user['status'] == 1 ? 'success' : 'secondary' ?>">
                        <?= $user['status'] == 1 ? 'Active' : 'Inactive' ?>
                    </span>
                </p>
                <p><strong>Created:</strong> <?= formatDate($user['created_at']) ?></p>
                <?php if (isset($user['updated_at']) && $user['updated_at']): ?>
                    <p><strong>Last Updated:</strong> <?= formatDate($user['updated_at']) ?></p>
                <?php endif; ?>

                <hr>

                <h6>Security Guidelines:</h6>
                <ul class="list-unstyled">
                    <li>• Use strong passwords</li>
                    <li>• Regular password updates</li>
                    <li>• Monitor user activity</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;

        if (password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    // Username validation
    document.getElementById('username').addEventListener('input', function() {
        const username = this.value;
        const pattern = /^[a-zA-Z0-9_]+$/;

        if (username.length > 0 && !pattern.test(username)) {
            this.setCustomValidity('Username can only contain letters, numbers, and underscores');
        } else if (username.length > 0 && username.length < 3) {
            this.setCustomValidity('Username must be at least 3 characters');
        } else {
            this.setCustomValidity('');
        }
    });

    // Password requirements validation
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const confirmPassword = document.getElementById('confirm_password');

        if (password.length > 0 && password.length < 6) {
            this.setCustomValidity('Password must be at least 6 characters');
        } else {
            this.setCustomValidity('');
        }

        // Trigger confirm password validation
        confirmPassword.dispatchEvent(new Event('input'));
    });
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>