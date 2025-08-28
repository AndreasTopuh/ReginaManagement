<?php
$title = "Rooms - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<style>
    .room-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 20px;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .room-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        cursor: pointer;
    }

    .room-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .room-content {
        padding: 15px;
    }

    .room-title {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .room-details {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
        font-size: 0.9rem;
        color: #666;
    }

    .room-description {
        color: #666;
        margin-bottom: 15px;
        line-height: 1.4;
    }

    .availability-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
    }

    .availability-text {
        font-size: 0.9rem;
        color: #28a745;
        font-weight: 500;
    }

    .room-price {
        font-size: 1.1rem;
        font-weight: bold;
        color: #333;
    }

    .room-detail-panel {
        position: sticky;
        top: 20px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .detail-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .detail-title {
        font-size: 1.4rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .detail-specs {
        margin-bottom: 15px;
    }

    .spec-item {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }

    .features-list {
        margin-bottom: 15px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .feature-item i {
        width: 20px;
        color: #28a745;
        margin-right: 8px;
    }

    .actions-section {
        border-top: 1px solid #eee;
        padding-top: 15px;
    }

    .status-occupied {
        background: #dc3545;
    }

    .status-available {
        background: #28a745;
    }

    .status-out-of-service {
        background: #6c757d;
    }

    .room-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .empty-detail-panel {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #666;
        min-height: 300px;
    }

    @media (max-width: 768px) {
        .room-grid {
            grid-template-columns: 1fr;
        }

        .room-detail-panel {
            position: relative;
            top: 0;
        }
    }
</style>

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

<!-- Rooms Grid -->
<div class="room-grid">
    <!-- Left Side - Room Types List -->
    <div class="room-types-list">
        <?php if (empty($roomTypes)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-bed fa-3x mb-3"></i>
                <h5>No room types found</h5>
                <p>No room types match your current filter criteria.</p>
                <?php if (hasPermission(['Owner', 'Admin'])): ?>
                    <a href="<?= BASE_URL ?>/rooms/create" class="btn btn-primary">Add First Room</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($roomTypes as $roomType): ?>
                <div class="room-card" onclick="showRoomDetail(<?= htmlspecialchars(json_encode($roomType)) ?>)" data-room-type="<?= $roomType['id'] ?>">
                    <?php
                    $imagePath = '';
                    if (!empty($roomType['image_filename'])) {
                        $imagePath = BASE_URL . '/images/imageRooms/' . $roomType['image_filename'];
                    }
                    ?>

                    <?php if ($imagePath): ?>
                        <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($roomType['type_name']) ?>" class="room-image">
                    <?php else: ?>
                        <div class="room-image d-flex align-items-center justify-content-center">
                            <i class="fas fa-bed fa-3x text-white"></i>
                        </div>
                    <?php endif; ?>

                    <div class="room-content">
                        <div class="room-title">
                            <?= htmlspecialchars($roomType['type_name']) ?> Room
                            <?php
                            $status = 'Available';
                            $statusClass = 'success';
                            if ($roomType['available_rooms'] == 0) {
                                $status = 'Occupied';
                                $statusClass = 'danger';
                            }
                            ?>
                            <span class="badge bg-<?= $statusClass ?> ms-2"><?= $status ?></span>
                        </div>

                        <div class="room-details">
                            <span><i class="fas fa-expand-arrows-alt"></i> <?= htmlspecialchars($roomType['room_size'] ?? '20m²') ?></span>
                            <span><i class="fas fa-bed"></i> <?= htmlspecialchars($roomType['bed_size'] ?? 'King Size') ?></span>
                            <span><i class="fas fa-users"></i> <?= $roomType['max_guests'] ?? 2 ?> Guest</span>
                        </div>

                        <div class="room-description">
                            <?php
                            // Get description from features or use default
                            $description = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.";
                            echo htmlspecialchars(substr($description, 0, 120)) . '...';
                            ?>
                        </div>
                    </div>

                    <div class="availability-info">
                        <div class="availability-text">
                            Available Room: <?= $roomType['available_rooms'] ?>/<?= $roomType['total_rooms'] ?>
                        </div>
                        <div class="room-price">
                            <?= formatCurrency($roomType['price']) ?>/night
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Right Side - Room Detail Panel -->
    <div class="room-detail-panel" id="roomDetailPanel">
        <div class="empty-detail-panel">
            <i class="fas fa-mouse-pointer fa-3x mb-3"></i>
            <h5>Room Detail</h5>
            <p>Click on any room type to see detailed information</p>
        </div>
    </div>
</div>

<script>
    function showRoomDetail(roomType) {
        const panel = document.getElementById('roomDetailPanel');

        const imagePath = roomType.image_filename ?
            '<?= BASE_URL ?>/images/imageRooms/' + roomType.image_filename :
            '';

        const imageHtml = imagePath ?
            `<img src="${imagePath}" alt="${roomType.type_name}" class="detail-image">` :
            `<div class="detail-image d-flex align-items-center justify-content-center bg-secondary">
            <i class="fas fa-bed fa-3x text-white"></i>
        </div>`;

        // Parse features if they exist
        let featuresHtml = '';
        if (roomType.features) {
            const features = roomType.features.split(',').map(f => f.trim());
            featuresHtml = features.map(feature =>
                `<div class="feature-item">
                <i class="fas fa-check-circle"></i>
                ${feature}
            </div>`
            ).join('');
        } else {
            // Default features
            const defaultFeatures = ['Air conditioning & balcony/curtains', 'Complimentary high-speed Wi-Fi', 'Daily housekeeping'];
            featuresHtml = defaultFeatures.map(feature =>
                `<div class="feature-item">
                <i class="fas fa-check-circle"></i>
                ${feature}
            </div>`
            ).join('');
        }

        const status = roomType.available_rooms > 0 ? 'Available' : 'Occupied';
        const statusClass = roomType.available_rooms > 0 ? 'success' : 'danger';

        panel.innerHTML = `
        ${imageHtml}
        
        <div class="detail-title">
            ${roomType.type_name} Room
            <span class="badge bg-${statusClass} ms-2">${status}</span>
        </div>
        
        <div class="detail-specs">
            <div class="spec-item">
                <span><i class="fas fa-expand-arrows-alt"></i> Room Size</span>
                <span>${roomType.room_size || '20m²'}</span>
            </div>
            <div class="spec-item">
                <span><i class="fas fa-bed"></i> Bed Type</span>
                <span>${roomType.bed_size || 'King Size bedroom'}</span>
            </div>
            <div class="spec-item">
                <span><i class="fas fa-users"></i> Max Guests</span>
                <span>${roomType.max_guests || 2} Guest</span>
            </div>
            <div class="spec-item">
                <span><i class="fas fa-tag"></i> Price</span>
                <span class="fw-bold">${formatCurrency(roomType.price)}/night</span>
            </div>
        </div>
        
        <div class="features-list">
            <h6><i class="fas fa-star"></i> Features:</h6>
            ${featuresHtml}
        </div>
        
        <div class="availability-info mb-3">
            <div class="availability-text">
                Available Room: ${roomType.available_rooms}/${roomType.total_rooms}
            </div>
        </div>
        
        <?php if (hasPermission(['Owner', 'Admin'])): ?>
        <div class="actions-section">
            <h6><i class="fas fa-cogs"></i> Actions:</h6>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-outline-primary btn-sm" onclick="viewRoomType(${roomType.id})">
                    <i class="fas fa-eye"></i> View Details
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="editRoomType(${roomType.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-outline-warning btn-sm" onclick="setMaintenance(${roomType.id})">
                    <i class="fas fa-tools"></i> Set Maintenance
                </button>
            </div>
        </div>
        <?php endif; ?>
    `;
    }

    function formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function viewRoomType(typeId) {
        // Redirect to room type detail page
        window.location.href = '<?= BASE_URL ?>/rooms/type/' + typeId;
    }

    function editRoomType(typeId) {
        // Redirect to create room with type preset
        window.location.href = '<?= BASE_URL ?>/rooms/create?type=' + typeId;
    }

    function setMaintenance(typeId) {
        if (confirm('Set all rooms of this type for maintenance?')) {
            // Handle maintenance action
            alert('Maintenance feature will be implemented');
        }
    }

    // Auto-submit form when status changes
    document.getElementById('status').addEventListener('change', function() {
        this.form.submit();
    });
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>