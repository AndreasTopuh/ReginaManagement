<?php
$title = "Bookings - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-calendar-check"></i> Bookings Management</h1>
            <a href="<?= BASE_URL ?>/bookings/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Booking
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" id="search-form" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Guest name or booking code..." 
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="checkin_from" class="form-label">Check-in Dari</label>
                        <input type="date" class="form-control" id="checkin_from" name="checkin_from" 
                               value="<?= $_GET['checkin_from'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="checkout_to" class="form-label">Check-out Sampai</label>
                        <input type="date" class="form-control" id="checkout_to" name="checkout_to" 
                               value="<?= $_GET['checkout_to'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="CheckedIn" <?= ($_GET['status'] ?? '') === 'CheckedIn' ? 'selected' : '' ?>>Checked In</option>
                            <option value="CheckedOut" <?= ($_GET['status'] ?? '') === 'CheckedOut' ? 'selected' : '' ?>>Checked Out</option>
                            <option value="Canceled" <?= ($_GET['status'] ?? '') === 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="<?= BASE_URL ?>/bookings" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </form>
                
                <!-- Show active filters -->
                <?php if (!empty($_GET['checkin_from']) || !empty($_GET['checkout_to']) || !empty($_GET['search']) || !empty($_GET['status'])): ?>
                <div class="mt-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="text-muted">Active filters:</span>
                        
                        <?php if (!empty($_GET['search'])): ?>
                        <span class="badge bg-info">
                            Search: "<?= htmlspecialchars($_GET['search']) ?>"
                            <a href="<?= BASE_URL ?>/bookings?<?= http_build_query(array_diff_key($_GET, ['search' => ''])) ?>" 
                               class="text-white ms-1">×</a>
                        </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($_GET['checkin_from']) && !empty($_GET['checkout_to'])): ?>
                        <span class="badge bg-success">
                            Range: Check-in dari <?= formatDate($_GET['checkin_from']) ?>, Check-out sampai <?= formatDate($_GET['checkout_to']) ?>
                            <a href="<?= BASE_URL ?>/bookings?<?= http_build_query(array_diff_key($_GET, ['checkin_from' => '', 'checkout_to' => ''])) ?>" 
                               class="text-white ms-1">×</a>
                        </span>
                        <?php elseif (!empty($_GET['checkin_from'])): ?>
                        <span class="badge bg-success">
                            Check-in dari: <?= formatDate($_GET['checkin_from']) ?>
                            <a href="<?= BASE_URL ?>/bookings?<?= http_build_query(array_diff_key($_GET, ['checkin_from' => ''])) ?>" 
                               class="text-white ms-1">×</a>
                        </span>
                        <?php elseif (!empty($_GET['checkout_to'])): ?>
                        <span class="badge bg-success">
                            Check-out sampai: <?= formatDate($_GET['checkout_to']) ?>
                            <a href="<?= BASE_URL ?>/bookings?<?= http_build_query(array_diff_key($_GET, ['checkout_to' => ''])) ?>" 
                               class="text-white ms-1">×</a>
                        </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($_GET['status'])): ?>
                        <span class="badge bg-warning">
                            Status: <?= htmlspecialchars($_GET['status']) ?>
                            <a href="<?= BASE_URL ?>/bookings?<?= http_build_query(array_diff_key($_GET, ['status' => ''])) ?>" 
                               class="text-white ms-1">×</a>
                        </span>
                        <?php endif; ?>
                        
                        <a href="<?= BASE_URL ?>/bookings" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear All
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bookings List -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Bookings List</h5>
            </div>
            <div class="card-body">
                <?php if (empty($bookings)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <h5>No bookings found</h5>
                    <p>No bookings match your current search criteria.</p>
                    <a href="<?= BASE_URL ?>/bookings/create" class="btn btn-primary">Create First Booking</a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Booking Code</th>
                                <th>Guest Name</th>
                                <th>Room(s)</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Nights</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>
                                    <a href="<?= BASE_URL ?>/bookings/<?= $booking['id'] ?>" 
                                       class="text-decoration-none fw-bold">
                                        <?= htmlspecialchars($booking['booking_code']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($booking['room_numbers']) ?>
                                    </span>
                                </td>
                                <td><?= formatDate($booking['checkin_date']) ?></td>
                                <td><?= formatDate($booking['checkout_date']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= $booking['duration_nights'] ?> night(s)</span>
                                </td>
                                <td>
                                    <?php
                                    $status_class = [
                                        'Pending' => 'warning',
                                        'CheckedIn' => 'success',
                                        'CheckedOut' => 'primary',
                                        'Canceled' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $status_class[$booking['status']] ?>">
                                        <?= $booking['status'] ?>
                                    </span>
                                </td>
                                <td><?= formatCurrency($booking['grand_total']) ?></td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($booking['created_by_name']) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/bookings/<?= $booking['id'] ?>" 
                                           class="btn btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
