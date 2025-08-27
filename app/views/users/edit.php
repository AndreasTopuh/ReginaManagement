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
            <a href="<?= BASE_URL ?>/users" class="btn btn-secondary">
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
                <form method="POST" action="<?= BASE_URL ?>/users/<?= $user['id'] ?>" enctype="multipart/form-data">
                    <!-- Profile Photo Display Section -->
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <div class="profile-photo-display mb-3">
                                <?php if (!empty($user['photo'])): ?>
                                    <img src="<?= BASE_URL ?>/images/imageUsers/<?= htmlspecialchars($user['photo']) ?>"
                                        alt="<?= htmlspecialchars($user['name']) ?>"
                                        class="profile-main-photo"
                                        data-original="<?= BASE_URL ?>/images/imageUsers/<?= htmlspecialchars($user['photo']) ?>"
                                        style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 4px solid #dee2e6; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                <?php else: ?>
                                    <div class="profile-main-photo-placeholder"
                                        style="width: 200px; height: 200px; border-radius: 50%; border: 4px solid #dee2e6; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold; margin: 0 auto;">
                                        <?= strtoupper(substr($user['name'], 0, 2)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <p class="text-muted">
                                <i class="fas fa-camera"></i> Profile Photo
                                <?php if (!empty($user['photo'])): ?>
                                    <span class="badge bg-success ms-2">Has Photo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-2">No Photo</span>
                                <?php endif; ?>
                            </p>
                            <p class="small text-muted">Upload a new photo to replace current image</p>
                        </div>
                    </div>

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
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="photo" class="form-label">Profile Photo</label>
                                <?php if (!empty($user['photo'])): ?>
                                    <div class="current-photo mb-3">
                                        <p class="mb-2">Current Photo:</p>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="photo-display-container">
                                                <img src="<?= BASE_URL ?>/images/imageUsers/<?= htmlspecialchars($user['photo']) ?>"
                                                    alt="Current Photo"
                                                    class="current-photo-image"
                                                    style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid #dee2e6;">
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="deleteCurrentPhoto(<?= $user['id'] ?>)">
                                                    <i class="fas fa-trash"></i> Delete Photo
                                                </button>
                                                <p class="small text-muted mt-2 mb-0">Current profile photo</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="form-text">
                                    Optional. Supported formats: JPEG, PNG, GIF. Maximum size: 5MB.<br>
                                    <strong>Auto-processing:</strong> Images will be automatically resized to 300x300px and optimized.
                                </div>
                                <div id="imagePreview" class="mt-3"></div>
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
                        <a href="<?= BASE_URL ?>/users" class="btn btn-secondary">Cancel</a>
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

    // Photo preview with enhanced display
    document.getElementById('photo').addEventListener('change', function() {
        const file = this.files[0];
        const preview = document.getElementById('imagePreview');
        const mainPhoto = document.querySelector('.profile-main-photo, .profile-main-photo-placeholder');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update the main profile photo display
                if (mainPhoto) {
                    if (mainPhoto.tagName === 'IMG') {
                        mainPhoto.src = e.target.result;
                    } else {
                        // Replace placeholder with actual image
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = 'New Profile Photo';
                        newImg.className = 'profile-main-photo';
                        newImg.style.cssText = 'width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 4px solid #dee2e6; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
                        mainPhoto.parentNode.replaceChild(newImg, mainPhoto);
                    }
                }

                const img = new Image();
                img.onload = function() {
                    // Calculate processing info
                    const size = Math.min(this.width, this.height);
                    const finalSize = size > 300 ? 300 : size;
                    const needsCrop = size < Math.max(this.width, this.height);
                    const needsResize = size > 300;

                    preview.innerHTML = `
                        <div class="photo-preview-container p-3 border rounded bg-light">
                            <h6><i class="fas fa-eye"></i> Photo Preview</h6>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <p class="small mb-2"><strong>Original</strong></p>
                                        <img src="${e.target.result}" alt="Original" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #dee2e6;">
                                        <p class="small text-muted mt-1">${this.width}x${this.height}px</p>
                                    </div>
                                </div>
                                <div class="col-md-1 text-center">
                                    <i class="fas fa-arrow-right text-primary preview-arrow"></i>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <p class="small mb-2"><strong>Processed</strong></p>
                                        <img src="${e.target.result}" alt="Preview" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid #007bff;">
                                        <p class="small text-muted mt-1">${finalSize}x${finalSize}px</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small file-info">
                                        <p class="mb-1"><strong>File:</strong> ${file.name}</p>
                                        <p class="mb-1 text-muted">Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                                        ${needsCrop ? '<span class="badge bg-info processing-indicator crop"><i class="fas fa-crop"></i> Crop</span><br>' : ''}
                                        ${needsResize ? '<span class="badge bg-success processing-indicator resize"><i class="fas fa-compress"></i> Resize</span><br>' : ''}
                                        <span class="badge bg-warning processing-indicator optimize"><i class="fas fa-magic"></i> Optimize</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-photo-action" onclick="clearPhotoPreview()">
                                    <i class="fas fa-times"></i> Remove Preview
                                </button>
                            </div>
                        </div>
                    `;
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });

    // Clear photo preview function
    function clearPhotoPreview() {
        document.getElementById('imagePreview').innerHTML = '';
        document.getElementById('photo').value = '';

        // Reset main photo display
        const mainPhoto = document.querySelector('.profile-main-photo');
        if (mainPhoto && mainPhoto.dataset.original) {
            mainPhoto.src = mainPhoto.dataset.original;
        }
    }

    // Delete current photo function
    function deleteCurrentPhoto(userId) {
        if (confirm('Are you sure you want to delete this photo?')) {
            window.location.href = '<?= BASE_URL ?>/users/' + userId + '/delete-photo';
        }
    }

    // Make functions globally available
    window.clearPhotoPreview = clearPhotoPreview;
    window.deleteCurrentPhoto = deleteCurrentPhoto;
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>