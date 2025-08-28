<?php
$title = "My Profile - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user-circle"></i> My Profile</h1>
            <div>
                <?php if (in_array($user['role_name'], ['Owner', 'Admin'])): ?>
                    <a href="<?= BASE_URL ?>/profile/edit" class="btn btn-primary me-2">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
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
    <!-- Profile Photo and Main Info -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <!-- Profile Photo -->
                <div class="profile-photo-display mb-4">
                    <?php if (!empty($user['photo'])): ?>
                        <img src="<?= BASE_URL ?>/images/imageUsers/<?= htmlspecialchars($user['photo']) ?>"
                            alt="<?= htmlspecialchars($user['name']) ?>"
                            class="profile-main-photo"
                            style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 4px solid #dee2e6; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <?php else: ?>
                        <div class="profile-main-photo-placeholder"
                            style="width: 200px; height: 200px; border-radius: 50%; border: 4px solid #dee2e6; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold; margin: 0 auto;">
                            <?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Name and Role -->
                <h3 class="mb-2"><?= htmlspecialchars($user['name'] ?? '') ?></h3>
                <p class="text-muted mb-3">@<?= htmlspecialchars($user['username'] ?? '') ?></p>

                <!-- Role Badge -->
                <span class="badge bg-<?= $user['role_name'] === 'Owner' ? 'danger' : ($user['role_name'] === 'Admin' ? 'warning' : 'info') ?> fs-6 mb-3">
                    <i class="fas fa-user-tag"></i> <?= htmlspecialchars($user['role_name'] ?? '') ?>
                </span>

                <!-- Status Badge -->
                <div class="mb-3">
                    <span class="badge bg-<?= $user['status'] ? 'success' : 'secondary' ?> fs-6">
                        <i class="fas fa-circle"></i> <?= $user['status'] ? 'Active' : 'Inactive' ?>
                    </span>
                </div>

                <!-- Edit Button -->
                <?php if (in_array($user['role_name'], ['Owner', 'Admin'])): ?>
                    <div class="d-grid">
                        <a href="<?= BASE_URL ?>/profile/edit" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-grid">
                        <small class="text-muted text-center">
                            <i class="fas fa-info-circle"></i><br>
                            Profile editing is restricted to Admin and Owner roles only.
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-circle"></i> Profile Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group mb-4">
                            <label class="form-label fw-bold text-muted">Full Name</label>
                            <div class="info-value">
                                <i class="fas fa-user text-primary me-2"></i>
                                <?= htmlspecialchars($user['name'] ?? '') ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-group mb-4">
                            <label class="form-label fw-bold text-muted">Username</label>
                            <div class="info-value">
                                <i class="fas fa-at text-primary me-2"></i>
                                <?= htmlspecialchars($user['username'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group mb-4">
                            <label class="form-label fw-bold text-muted">User ID</label>
                            <div class="info-value">
                                <i class="fas fa-hashtag text-primary me-2"></i>
                                #<?= $user['id'] ?? '' ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-group mb-4">
                            <label class="form-label fw-bold text-muted">Role</label>
                            <div class="info-value">
                                <i class="fas fa-shield-alt text-primary me-2"></i>
                                <?= htmlspecialchars($user['role_name'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group mb-4">
                            <label class="form-label fw-bold text-muted">Member Since</label>
                            <div class="info-value">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <?php
                                $created_date = new DateTime($user['created_at'] ?? 'now');
                                echo $created_date->format('F j, Y');
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-group mb-4">
                            <label class="form-label fw-bold text-muted">Account Status</label>
                            <div class="info-value">
                                <i class="fas fa-toggle-<?= $user['status'] ? 'on text-success' : 'off text-secondary' ?> me-2"></i>
                                <?= $user['status'] ? 'Active Account' : 'Inactive Account' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Information Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h6><i class="fas fa-shield-alt"></i> Security & Privacy</h6>
            </div>
            <div class="card-body">
                <?php if (in_array($user['role_name'], ['Owner', 'Admin'])): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="security-item mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-key text-success me-3"></i>
                                    <div>
                                        <strong>Password</strong>
                                        <div class="text-muted small">Last updated recently</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="security-item mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-shield text-info me-3"></i>
                                    <div>
                                        <strong>Account Security</strong>
                                        <div class="text-muted small">Protected profile</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            To change your password or update security settings, use the "Edit Profile" button.
                        </small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fs-4"></i>
                            <div>
                                <strong>Profile Restrictions</strong>
                                <div class="mt-1">
                                    As a <strong><?= htmlspecialchars($user['role_name']) ?></strong>, you have read-only access to your profile information.
                                    Profile editing (name, photo, password) is restricted to Admin and Owner roles only.
                                </div>
                                <div class="mt-2 small">
                                    <strong>Contact your administrator if you need to:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>Change your name or username</li>
                                        <li>Update your profile photo</li>
                                        <li>Reset your password</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>

<style>
    .info-group .info-value {
        font-size: 1.1rem;
        color: #495057;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .profile-main-photo {
        transition: transform 0.3s ease;
    }

    .profile-main-photo:hover {
        transform: scale(1.05);
        cursor: pointer;
    }

    .security-item {
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .badge {
        font-size: 0.875rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click to expand profile photo
        const profilePhoto = document.querySelector('.profile-main-photo');
        if (profilePhoto) {
            profilePhoto.addEventListener('click', function() {
                // Create modal to show larger image
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Profile Photo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="${this.src}" alt="Profile Photo" class="img-fluid" style="max-height: 500px;">
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);

                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();

                // Remove modal after hiding
                modal.addEventListener('hidden.bs.modal', function() {
                    document.body.removeChild(modal);
                });
            });
        }
    });
</script>