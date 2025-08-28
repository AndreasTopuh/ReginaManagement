<?php
$title = "Room Type Detail - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-bed"></i> <?= htmlspecialchars($roomType['type_name']) ?> Rooms</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/rooms">Rooms</a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($roomType['type_name']) ?></li>
                    </ol>
                </nav>
            </div>
            <?php if (hasPermission(['Owner', 'Admin'])): ?>
                <a href="<?= BASE_URL ?>/rooms/create?type=<?= $roomType['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Room
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Room Type Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <?php if (!empty($roomType['image_filename'])): ?>
                    <img src="<?= BASE_URL ?>/images/imageRooms/<?= $roomType['image_filename'] ?>"
                        alt="<?= htmlspecialchars($roomType['type_name']) ?>"
                        class="img-fluid rounded mb-3" style="max-height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                        <i class="fas fa-bed fa-3x text-white"></i>
                    </div>
                <?php endif; ?>

                <h4><?= htmlspecialchars($roomType['type_name']) ?> Room</h4>
                <p class="text-muted">Starting from</p>
                <h3 class="text-primary"><?= formatCurrency($roomType['price']) ?><small>/night</small></h3>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Room Specifications</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="spec-item mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-expand-arrows-alt text-primary me-2"></i>
                                <strong>Room Size:</strong>
                                <span class="ms-2"><?= htmlspecialchars($roomType['room_size'] ?? '20m²') ?></span>
                            </div>
                        </div>
                        <div class="spec-item mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bed text-primary me-2"></i>
                                <strong>Bed Type:</strong>
                                <span class="ms-2"><?= htmlspecialchars($roomType['bed_size'] ?? 'King Size') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="spec-item mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users text-primary me-2"></i>
                                <strong>Max Guests:</strong>
                                <span class="ms-2"><?= $roomType['max_guests'] ?? 2 ?> Guest<?= ($roomType['max_guests'] ?? 2) > 1 ? 's' : '' ?></span>
                            </div>
                        </div>
                        <div class="spec-item mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag text-primary me-2"></i>
                                <strong>Price:</strong>
                                <span class="ms-2"><?= formatCurrency($roomType['price']) ?>/night</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4 text-center">
                        <h6 class="text-muted">Total Rooms</h6>
                        <h4 class="text-info"><?= $roomType['total_rooms'] ?></h4>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="text-muted">Available</h6>
                        <h4 class="text-success"><?= $roomType['available_rooms'] ?></h4>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="text-muted">Occupied</h6>
                        <h4 class="text-warning"><?= $roomType['occupied_rooms'] ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Individual Rooms -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Individual Rooms</h5>
            </div>
            <div class="card-body">
                <?php if (empty($rooms)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-bed fa-3x mb-3"></i>
                        <h5>No rooms found</h5>
                        <p>No rooms of this type have been created yet.</p>
                        <?php if (hasPermission(['Owner', 'Admin'])): ?>
                            <a href="<?= BASE_URL ?>/rooms/create?type=<?= $roomType['id'] ?>" class="btn btn-primary">Add First Room</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Floor</th>
                                    <th>Status</th>
                                    <th>Description</th>
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
                                        <td>Floor <?= $room['floor_number'] ?></td>
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
                                            <span class="text-truncate" style="max-width: 200px;" data-bs-toggle="tooltip"
                                                title="<?= htmlspecialchars($room['description']) ?>">
                                                <?= htmlspecialchars(substr($room['description'], 0, 50)) ?>
                                                <?= strlen($room['description']) > 50 ? '...' : '' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip"
                                                title="<?= htmlspecialchars($room['features']) ?>">
                                                <?= htmlspecialchars(substr($room['features'], 0, 30)) ?>
                                                <?= strlen($room['features']) > 30 ? '...' : '' ?>
                                            </span>
                                        </td>
                                        <?php if (hasPermission(['Owner', 'Admin'])): ?>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>"
                                                        class="btn btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/edit"
                                                        class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-outline-warning dropdown-toggle dropdown-toggle-split"
                                                            data-bs-toggle="dropdown" aria-expanded="false" title="Change Status">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <?php if ($room['status'] !== 'Available'): ?>
                                                                <li>
                                                                    <form method="POST" action="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/update-status" class="d-inline">
                                                                        <input type="hidden" name="status" value="Available">
                                                                        <button type="submit" class="dropdown-item" onclick="return confirm('Set room as Available?')">
                                                                            <i class="fas fa-check text-success"></i> Set Available
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endif; ?>
                                                            <?php if ($room['status'] !== 'OutOfService'): ?>
                                                                <li>
                                                                    <form method="POST" action="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/update-status" class="d-inline">
                                                                        <input type="hidden" name="status" value="OutOfService">
                                                                        <button type="submit" class="dropdown-item" onclick="return confirm('Set room for Maintenance/Out of Service?')">
                                                                            <i class="fas fa-tools text-danger"></i> Set Maintenance
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
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

<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>