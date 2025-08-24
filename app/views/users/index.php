<?php
$title = "User Management - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-users"></i> User Management</h1>
            <a href="<?= BASE_URL ?>/users.php?action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php unset($_SESSION['success']);
endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php unset($_SESSION['error']);
endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Users List</h5>
            </div>
            <div class="card-body">
                <?php if (empty($users)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No users found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($user['name']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= htmlspecialchars($user['username']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php
                                            $role_colors = [
                                                'Owner' => 'danger',
                                                'Admin' => 'warning',
                                                'Receptionist' => 'success'
                                            ];
                                            $color = $role_colors[$user['role_name']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= $user['role_name'] ?></span>
                                        </td>
                                        <td>
                                            <?php if ($user['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatDate($user['created_at']) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Edit Button -->
                                                <?php if ($user['role_name'] !== 'Owner' || SessionManager::getUserRole() === 'Owner'): ?>
                                                    <a href="<?= BASE_URL ?>/users.php?action=edit&id=<?= $user['id'] ?>"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Toggle Status Button -->
                                                <?php if (
                                                    $user['id'] != $_SESSION['user_id'] &&
                                                    ($user['role_name'] !== 'Owner' || SessionManager::getUserRole() === 'Owner')
                                                ): ?>
                                                    <a href="<?= BASE_URL ?>/users.php?action=toggle-status&id=<?= $user['id'] ?>"
                                                        class="btn btn-sm btn-outline-<?= $user['status'] == 1 ? 'warning' : 'success' ?>"
                                                        title="<?= $user['status'] == 1 ? 'Deactivate' : 'Activate' ?>"
                                                        onclick="return confirm('Are you sure you want to <?= $user['status'] == 1 ? 'deactivate' : 'activate' ?> this user?')">
                                                        <i class="fas fa-<?= $user['status'] == 1 ? 'ban' : 'check' ?>"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Delete Button -->
                                                <?php if (
                                                    $user['id'] != $_SESSION['user_id'] &&
                                                    ($user['role_name'] !== 'Owner' || SessionManager::getUserRole() === 'Owner')
                                                ): ?>
                                                    <a href="<?= BASE_URL ?>/users.php?action=delete&id=<?= $user['id'] ?>"
                                                        class="btn btn-sm btn-outline-danger" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>