<?php
// Optimize PHP settings for file upload
ini_set('upload_max_filesize', '3M');
ini_set('post_max_size', '4M');
ini_set('max_execution_time', 30);
ini_set('memory_limit', '128M');

$title = "Edit My Profile - Regina Hotel";
include INCLUDES_PATH . '/header.php';

// Get form data from session if validation failed, otherwise use user data
$form_data = $_SESSION['form_data'] ?? $user;
unset($_SESSION['form_data']);
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user-edit"></i> Edit My Profile</h1>
            <div>
                <a href="<?= BASE_URL ?>/profile" class="btn btn-secondary me-2">
                    <i class="fas fa-eye"></i> View Profile
                </a>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

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
                <h5><i class="fas fa-user-edit"></i> Edit Profile Information</h5>
            </div>
            <div class="card-body">
                <form id="profileForm" method="POST" action="<?= BASE_URL ?>/profile/update" enctype="multipart/form-data">
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

                                <!-- File Size Info -->
                                <div class="alert alert-info py-2 mb-3">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Upload Requirements:</strong> Maximum 3MB • JPEG, PNG, or GIF • Will be automatically resized to 300x300px for optimal performance
                                    </small>
                                </div>

                                <?php if (!empty($user['photo'])): ?>
                                    <div class="current-photo mb-3">
                                        <p class="mb-2">Current Photo:</p>
                                        <div class="current-photo-display">
                                            <img src="<?= BASE_URL ?>/images/imageUsers/<?= htmlspecialchars($user['photo']) ?>"
                                                alt="Current Photo"
                                                class="photo-preview"
                                                style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px;">
                                            <div class="photo-overlay mt-2">
                                                <a href="<?= BASE_URL ?>/profile/delete-photo"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this photo?')">
                                                    <i class="fas fa-trash"></i> Delete Photo
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="photo-upload-area border rounded p-4 text-center" style="cursor: pointer; background: #f8f9fa;">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-2">Click to upload a new photo</p>
                                    <input type="file" class="form-control d-none" id="photo" name="photo" accept="image/*">
                                    <small class="text-muted">
                                        Maximum file size: 3MB<br>
                                        Supported formats: JPEG, PNG, GIF
                                    </small>
                                </div>

                                <!-- Upload Progress -->
                                <div id="uploadProgress" class="mt-3" style="display: none;">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Processing image...</small>
                                </div>

                                <!-- Image Preview -->
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

                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <a href="<?= BASE_URL ?>/profile" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Current Profile Preview -->
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-eye"></i> Current Profile</h6>
            </div>
            <div class="card-body text-center">
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?= BASE_URL ?>/images/imageUsers/<?= htmlspecialchars($user['photo']) ?>"
                        alt="<?= htmlspecialchars($user['name']) ?>"
                        class="profile-current-photo mb-3"
                        style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid #dee2e6;">
                <?php else: ?>
                    <div class="profile-current-photo-placeholder mb-3"
                        style="width: 120px; height: 120px; border-radius: 50%; border: 3px solid #dee2e6; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold; margin: 0 auto;">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?>
                    </div>
                <?php endif; ?>

                <h6><?= htmlspecialchars($user['name'] ?? '') ?></h6>
                <p class="text-muted small">@<?= htmlspecialchars($user['username'] ?? '') ?></p>
                <span class="badge bg-<?= $user['role_name'] === 'Owner' ? 'danger' : ($user['role_name'] === 'Admin' ? 'warning' : 'info') ?>">
                    <?= htmlspecialchars($user['role_name'] ?? '') ?>
                </span>
            </div>
        </div>

        <!-- Security Tips -->
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
        const form = document.getElementById('profileForm');
        const photoInput = document.getElementById('photo');
        const uploadArea = document.querySelector('.photo-upload-area');
        const preview = document.getElementById('imagePreview');
        const uploadProgress = document.getElementById('uploadProgress');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        // Password validation
        const currentPassword = document.getElementById('current_password');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');

        function validatePasswords() {
            if (newPassword.value && !currentPassword.value) {
                currentPassword.setCustomValidity('Current password is required to change password');
            } else {
                currentPassword.setCustomValidity('');
            }

            if (newPassword.value && confirmPassword.value && newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }

        if (newPassword) newPassword.addEventListener('input', validatePasswords);
        if (confirmPassword) confirmPassword.addEventListener('input', validatePasswords);
        if (currentPassword) currentPassword.addEventListener('input', validatePasswords);

        // Photo upload handling
        uploadArea.addEventListener('click', function() {
            photoInput.click();
        });

        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (3MB limit)
                const maxSize = 3 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File size too large. Maximum 3MB allowed.');
                    this.value = '';
                    preview.innerHTML = '';
                    return;
                }

                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPEG, PNG, and GIF are allowed.');
                    this.value = '';
                    preview.innerHTML = '';
                    return;
                }

                handlePhotoPreview(file);
            }
        });

        function handlePhotoPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const size = Math.min(this.width, this.height);
                    const finalSize = size > 300 ? 300 : size;
                    const estimatedFinalSize = Math.round((file.size * 0.3) / (1024 * 1024) * 100) / 100; // Rough estimate

                    preview.innerHTML = `
                        <div class="alert alert-success">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="${e.target.result}" alt="Preview" 
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                                </div>
                                <div class="col-md-9">
                                    <h6 class="mb-1"><i class="fas fa-check-circle text-success"></i> Image Ready for Upload</h6>
                                    <small class="text-muted">
                                        <strong>File:</strong> ${file.name}<br>
                                        <strong>Original:</strong> ${this.width}x${this.height}px (${(file.size / 1024 / 1024).toFixed(2)}MB)<br>
                                        <strong>After processing:</strong> 300x300px (~${estimatedFinalSize}MB)
                                    </small>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPhotoPreview()">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Form submission with progress
        form.addEventListener('submit', function(e) {
            const hasPhoto = photoInput.files.length > 0;

            if (hasPhoto) {
                // Show progress for photo upload
                uploadProgress.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Processing Photo...
                `;

                // Simulate progress (since we can't track real upload progress easily)
                let progress = 0;
                const progressBar = uploadProgress.querySelector('.progress-bar');
                const interval = setInterval(() => {
                    progress += Math.random() * 30;
                    if (progress > 90) progress = 90;
                    progressBar.style.width = progress + '%';
                }, 200);

                // Clear interval after form submission
                setTimeout(() => clearInterval(interval), 100);
            } else {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Updating...
                `;
            }

            // Restore button after timeout (safety net)
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                uploadProgress.style.display = 'none';
            }, 30000);
        });

        // Global function for removing photo preview
        window.clearPhotoPreview = function() {
            preview.innerHTML = '';
            photoInput.value = '';
            uploadProgress.style.display = 'none';
        };
    });
</script>