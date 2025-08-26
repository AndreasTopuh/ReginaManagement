<?php
$title = "Room Details - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-bed"></i> Room <?= htmlspecialchars($room['room_number']) ?></h1>
            <div>
                <?php if (hasPermission(['Owner', 'Admin'])): ?>
                    <a href="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/edit" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit Room
                    </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/rooms" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Rooms
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Room Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Room Number:</strong></td>
                                <td><?= htmlspecialchars($room['room_number']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($room['type_name']) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Floor:</strong></td>
                                <td>
                                    <span class="badge bg-success">Floor <?= htmlspecialchars($room['floor_number']) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php
                                    $badgeClass = match ($room['status']) {
                                        'Available' => 'bg-success',
                                        'Occupied' => 'bg-warning',
                                        'OutOfService' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($room['status']) ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Rate per Night:</strong></td>
                                <td><strong class="text-primary">Rp <?= number_format($room['price'], 0, ',', '.') ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Features:</strong></td>
                                <td><?= htmlspecialchars($room['features']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td><?= htmlspecialchars($room['description']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-chart-line"></i> Room Statistics</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <i class="fas fa-bed fa-3x text-primary mb-3"></i>
                    <h4><?= htmlspecialchars($room['room_number']) ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($room['type_name']) ?> Room</p>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Room ID:</span>
                    <span class="badge bg-secondary">#<?= $room['id'] ?></span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Floor Level:</span>
                    <span><?= htmlspecialchars($room['floor_number']) ?></span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Current Status:</span>
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($room['status']) ?></span>
                </div>
            </div>
        </div>

        <?php if (hasPermission(['Owner', 'Admin'])): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-cogs"></i> Room Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/edit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Room Details
                        </a>

                        <?php if ($room['status'] === 'Available'): ?>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Delete Room
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (hasPermission(['Owner', 'Admin']) && $room['status'] === 'Available'): ?>
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete Room <?= htmlspecialchars($room['room_number']) ?>?</p>
                    <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/delete" class="d-inline">
                        <button type="submit" class="btn btn-danger">Delete Room</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include INCLUDES_PATH . '/footer.php'; ?>