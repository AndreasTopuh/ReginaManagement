<?php
$title = "Floor Details - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-building"></i> Floor <?= $floor['floor_number'] ?> Details</h1>
            <div>
                <a href="floors.php?action=edit&id=<?= $floor['id'] ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Floor
                </a>
                <a href="<?= BASE_URL ?>/floors.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Floors
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Floor Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3><?= count($rooms) ?></h3>
                <p class="mb-0">Total Rooms</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <?php $available = array_filter($rooms, fn($r) => $r['status'] === 'Available'); ?>
                <h3><?= count($available) ?></h3>
                <p class="mb-0">Available</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <?php $occupied = array_filter($rooms, fn($r) => $r['status'] === 'Occupied'); ?>
                <h3><?= count($occupied) ?></h3>
                <p class="mb-0">Occupied</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <?php $outOfService = array_filter($rooms, fn($r) => $r['status'] === 'OutOfService'); ?>
                <h3><?= count($outOfService) ?></h3>
                <p class="mb-0">Out of Service</p>
            </div>
        </div>
    </div>
</div>

<!-- Rooms on this Floor -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Rooms on Floor <?= $floor['floor_number'] ?></h5>
                <?php if (hasPermission(['Owner', 'Admin'])): ?>
                <a href="<?= BASE_URL ?>/rooms.php?action=add&floor_id=<?= $floor['id'] ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add Room
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($rooms)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-bed fa-3x mb-3"></i>
                    <h5>No rooms on this floor</h5>
                    <p>This floor doesn't have any rooms yet.</p>
                    <?php if (hasPermission(['Owner', 'Admin'])): ?>
                    <a href="<?= BASE_URL ?>/rooms.php?action=add&floor_id=<?= $floor['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Room
                    </a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Type</th>
                                <th>Price/Night</th>
                                <th>Status</th>
                                <th>Features</th>
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
                                    <?php if (!empty($room['features'])): ?>
                                    <small class="text-muted"><?= htmlspecialchars($room['features']) ?></small>
                                    <?php else: ?>
                                    <small class="text-muted">No features listed</small>
                                    <?php endif; ?>
                                </td>
                                <?php if (hasPermission(['Owner', 'Admin'])): ?>
                                <td>
                                    <a href="<?= BASE_URL ?>/rooms.php?action=edit&id=<?= $room['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
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
