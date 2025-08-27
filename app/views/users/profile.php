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
                <form method="POST" action="<?= BASE_URL ?>/profile" enctype="multipart/form-data">
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
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="photo" class="form-label">Profile Photo</label>
                                <?php if (!empty($user['photo'])): ?>
                                    <div class="current-photo mb-3">
                                        <p class="mb-2">Current Photo:</p>
                                        <div class="current-photo-display">
                                            <img src="<?= BASE_URL ?>/images/imageUsers/<?= htmlspecialchars($user['photo']) ?>"
                                                alt="Current Photo"
                                                class="photo-preview"
                                                style="width: 150px; height: 150px; object-fit: cover;">
                                            <div class="photo-overlay">
                                                <a href="<?= BASE_URL ?>/profile/delete-photo"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this photo?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="photo-upload-area" style="cursor: pointer;">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-2">Click to upload a photo</p>
                                    <input type="file" class="form-control d-none" id="photo" name="photo" accept="image/*">
                                    <small class="text-muted">
                                        Supported formats: JPEG, PNG, GIF. Maximum size: 5MB.<br>
                                        <strong>Auto-processing:</strong> Images will be automatically resized to 300x300px and optimized.
                                    </small>
                                </div>
                                <div id="imagePreview" class="mt-3"></div>
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

        // Photo preview
        const photoInput = document.getElementById('photo');
        const uploadArea = document.querySelector('.photo-upload-area');
        const preview = document.getElementById('imagePreview');

        // Click to upload
        uploadArea.addEventListener('click', function() {
            photoInput.click();
        });

        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                handlePhotoPreview(file);
            }
        });

        function handlePhotoPreview(file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        // Calculate crop area (center square)
                        const size = Math.min(this.width, this.height);
                        const cropInfo = size < Math.max(this.width, this.height) ?
                            `<small class="text-info"><i class="fas fa-crop"></i> Image will be center-cropped to square</small>` : '';

                        // Calculate final size
                        const finalSize = size > 300 ? 300 : size;
                        const resizeInfo = size > 300 ?
                            `<small class="text-success"><i class="fas fa-compress"></i> Image will be resized to 300x300px</small>` : '';

                        preview.innerHTML = `
                        <div class="photo-preview-container">
                            <h6>Photo Preview:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Original:</strong> ${this.width}x${this.height}px</p>
                                    <img src="${e.target.result}" alt="Original" 
                                         style="width: 120px; height: auto; max-height: 120px; border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>After Processing:</strong> ${finalSize}x${finalSize}px</p>
                                    <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 2px solid #dee2e6;">
                                        <img src="${e.target.result}" alt="Preview" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="mb-1"><strong>File:</strong> ${file.name}</p>
                                <p class="mb-1 text-muted">Original Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                                ${cropInfo}
                                ${resizeInfo}
                                <small class="text-muted"><i class="fas fa-magic"></i> Final file will be optimized to ~100-500KB</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearPhotoPreview()">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    `;
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                alert('Please select a valid image file.');
                photoInput.value = '';
            }
        }

        function clearPhotoPreview() {
            preview.innerHTML = '';
            photoInput.value = '';
        }

        // Make clearPhotoPreview globally available
        window.clearPhotoPreview = clearPhotoPreview;
    });
</script>