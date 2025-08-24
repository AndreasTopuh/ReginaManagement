<?php
$title = "Rooms - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-bed"></i> Rooms Management</h1>

            <?php if (hasPermission(['Owner', 'Admin'])): ?>
                <a href="<?= BASE_URL ?>/rooms/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Room
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Filter by Status</label>
                        <select name="status" id="status" class="form-select auto-submit">
                            <option value="">All Status</option>
                            <option value="Available" <?= ($_GET['status'] ?? '') === 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Occupied" <?= ($_GET['status'] ?? '') === 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                            <option value="OutOfService" <?= ($_GET['status'] ?? '') === 'OutOfService' ? 'selected' : '' ?>>Out of Service</option>
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                        <a href="<?= BASE_URL ?>/rooms" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Rooms List -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Rooms List</h5>
            </div>
            <div class="card-body">
                <?php if (empty($rooms)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-bed fa-3x mb-3"></i>
                        <h5>No rooms found</h5>
                        <p>No rooms match your current filter criteria.</p>
                        <?php if (hasPermission(['Owner', 'Admin'])): ?>
                            <a href="<?= BASE_URL ?>/rooms/create" class="btn btn-primary">Add First Room</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Floor</th>
                                    <th>Type</th>
                                    <th>Price/Night</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <?php if (hasPermission(['Owner', 'Admin'])): ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rooms as $room): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($room['room_number']) ?></strong>
                                        </td>
                                        <td>Floor <?= $room['floor_number'] ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= htmlspecialchars($room['type_name']) ?></span>
                                        </td>
                                        <td><?= formatCurrency($room['price']) ?></td>
                                        <td>
                                            <?php
                                            $status_class = [
                                                'Available' => 'success',
                                                'Occupied' => 'warning',
                                                'OutOfService' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $status_class[$room['status']] ?>">
                                                <?= $room['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-truncate-2" data-bs-toggle="tooltip"
                                                title="<?= htmlspecialchars($room['description']) ?>">
                                                <?= htmlspecialchars(substr($room['description'], 0, 50)) ?>
                                                <?= strlen($room['description']) > 50 ? '...' : '' ?>
                                            </span>
                                        </td>
                                        <?php if (hasPermission(['Owner', 'Admin'])): ?>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>"
                                                        class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        <?php endif; ?>
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