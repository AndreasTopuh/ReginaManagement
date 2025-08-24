<?php
$title = "Floors - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-building"></i> Floor Management</h1>
            <a href="floors.php?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Floor
            </a>
        </div>
    </div>
</div>

<!-- Floors List -->
<div class="row">
    <?php if (empty($floors)): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5>No floors found</h5>
                <p class="text-muted">Start by adding your first floor.</p>
                <a href="floors.php?action=add" class="btn btn-primary">Add First Floor</a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($floors as $floor): ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group"></i> Floor <?= $floor['floor_number'] ?>
                </h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="floors.php?action=detail&id=<?= $floor['id'] ?>">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="floors.php?action=edit&id=<?= $floor['id'] ?>">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger btn-delete" 
                               href="floors.php?action=delete&id=<?= $floor['id'] ?>">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary"><?= $floor['actual_rooms'] ?></h4>
                        <p class="mb-0 small">Total Rooms</p>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success"><?= $floor['available_rooms'] ?></h4>
                        <p class="mb-0 small">Available</p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-warning"><?= $floor['occupied_rooms'] ?></h6>
                        <p class="mb-0 small">Occupied</p>
                    </div>
                    <div class="col-6">
                        <h6 class="text-danger"><?= $floor['out_of_service_rooms'] ?></h6>
                        <p class="mb-0 small">Out of Service</p>
                    </div>
                </div>
                
                <?php if ($floor['actual_rooms'] > 0): ?>
                <div class="mt-3">
                    <?php 
                    $occupancy_rate = ($floor['occupied_rooms'] / $floor['actual_rooms']) * 100;
                    $progress_class = $occupancy_rate <= 50 ? 'success' : ($occupancy_rate <= 80 ? 'warning' : 'danger');
                    ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <small>Occupancy Rate</small>
                        <small><?= number_format($occupancy_rate, 1) ?>%</small>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-<?= $progress_class ?>" 
                             style="width: <?= $occupancy_rate ?>%"></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="floors.php?action=detail&id=<?= $floor['id'] ?>" 
                       class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="floors.php?action=edit&id=<?= $floor['id'] ?>" 
                       class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>
