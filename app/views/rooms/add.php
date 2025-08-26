<?php
$title = "Add Room - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-plus"></i> Add New Room</h1>
            <a href="<?= BASE_URL ?>/rooms" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Rooms
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Room Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/rooms" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="room_number" name="room_number"
                                    value="<?= htmlspecialchars($_POST['room_number'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Please provide a valid room number.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_id" class="form-label">Room Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type_id" name="type_id" required>
                                    <option value="">Select Room Type</option>
                                    <?php foreach ($room_types as $type): ?>
                                        <option value="<?= $type['id'] ?>"
                                            <?= ($_POST['type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type['type_name']) ?> - <?= formatCurrency($type['price']) ?>/night
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a room type.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="floor_id" class="form-label">Floor <span class="text-danger">*</span></label>
                                <select class="form-select" id="floor_id" name="floor_id" required>
                                    <option value="">Select Floor</option>
                                    <?php foreach ($floors as $floor): ?>
                                        <option value="<?= $floor['id'] ?>"
                                            <?= ($_POST['floor_id'] ?? '') == $floor['id'] ? 'selected' : '' ?>>
                                            Floor <?= $floor['floor_number'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a floor.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Enter room description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="features" class="form-label">Features</label>
                        <textarea class="form-control" id="features" name="features" rows="3"
                            placeholder="e.g., AC, TV, WiFi, Mini Bar, Balcony..."><?= htmlspecialchars($_POST['features'] ?? '') ?></textarea>
                        <div class="form-text">List the amenities and features available in this room.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Room
                        </button>
                        <a href="<?= BASE_URL ?>/rooms" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-info-circle"></i> Information</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Room number must be unique</li>
                    <li><i class="fas fa-check text-success"></i> Choose appropriate room type</li>
                    <li><i class="fas fa-check text-success"></i> Select the correct floor</li>
                    <li><i class="fas fa-check text-success"></i> Add detailed description</li>
                    <li><i class="fas fa-check text-success"></i> List all room features</li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6><i class="fas fa-list"></i> Available Room Types</h6>
            </div>
            <div class="card-body">
                <?php foreach ($room_types as $type): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-info"><?= htmlspecialchars($type['type_name']) ?></span>
                        <small><?= formatCurrency($type['price']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>