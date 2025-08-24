<?php
$title = "Edit Floor - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-edit"></i> Edit Floor <?= $floor['floor_number'] ?></h1>
            <a href="<?= BASE_URL ?>/floors.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Floors
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Edit Floor Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="floor_number" class="form-label">Floor Number <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="floor_number" name="floor_number" 
                               value="<?= htmlspecialchars($_POST['floor_number'] ?? $floor['floor_number']) ?>" 
                               min="1" max="50" required>
                        <div class="form-text">Enter the floor number (e.g., 1, 2, 3, etc.)</div>
                        <div class="invalid-feedback">
                            Please provide a valid floor number (1-50).
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Current floor has <?= $floor['total_rooms'] ?> rooms.</strong>
                        Changing the floor number will not affect existing rooms.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Floor
                        </button>
                        <a href="<?= BASE_URL ?>/floors.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>
