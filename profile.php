<?php
require_once 'config/config.php';

requireLogin();

$user_model = new User();
$user = $user_model->findById($_SESSION['user_id']);

if (!$user) {
    redirect('/logout.php');
}

$title = "User Profile - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user"></i> User Profile</h1>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Profile Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3" 
                         style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user fa-3x"></i>
                    </div>
                    <h4><?= htmlspecialchars($user['name']) ?></h4>
                    <span class="badge bg-info fs-6"><?= htmlspecialchars($user['role_name']) ?></span>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Full Name:</strong></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Role:</strong></td>
                        <td>
                            <span class="badge bg-info"><?= htmlspecialchars($user['role_name']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <?php if ($user['status']): ?>
                            <span class="badge bg-success">Active</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Account Created:</strong></td>
                        <td><?= formatDateTime($user['created_at']) ?></td>
                    </tr>
                </table>
                
                <div class="text-center mt-4">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Profile updates must be done by Owner or Admin.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>
