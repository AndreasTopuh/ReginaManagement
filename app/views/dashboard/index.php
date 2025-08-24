<?php
$title = "Dashboard - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>

            <form method="GET" class="d-flex gap-2">
                <input type="date" name="date_from" class="form-control" value="<?= $date_from ?>" required>
                <input type="date" name="date_to" class="form-control" value="<?= $date_to ?>" required>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $booking_stats['total_bookings'] ?? 0 ?></h4>
                        <p class="mb-0">Total Bookings</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $booking_stats['checked_in_bookings'] ?? 0 ?></h4>
                        <p class="mb-0">Checked In</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-sign-in-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= number_format($occupancy_rate, 1) ?>%</h4>
                        <p class="mb-0">Occupancy Rate</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-pie fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $room_stats['available_rooms'] ?? 0 ?></h4>
                        <p class="mb-0">Available Rooms</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-bed fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Card (Only for Owner & Admin) -->
<?php if (hasPermission(['Owner', 'Admin'])): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h2><?= formatCurrency($booking_stats['total_revenue'] ?? 0) ?></h2>
                            <p class="mb-0">Total Revenue (<?= formatDate($date_from) ?> - <?= formatDate($date_to) ?>)</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Room Status -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bed"></i> Room Status</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <h4 class="text-primary"><?= $room_stats['total_rooms'] ?? 0 ?></h4>
                        <p class="mb-0 small">Total</p>
                    </div>
                    <div class="col-3">
                        <h4 class="text-success"><?= $room_stats['available_rooms'] ?? 0 ?></h4>
                        <p class="mb-0 small">Available</p>
                    </div>
                    <div class="col-3">
                        <h4 class="text-warning"><?= $room_stats['occupied_rooms'] ?? 0 ?></h4>
                        <p class="mb-0 small">Occupied</p>
                    </div>
                    <div class="col-3">
                        <h4 class="text-danger"><?= $room_stats['out_of_service_rooms'] ?? 0 ?></h4>
                        <p class="mb-0 small">Out of Service</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Booking Status</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <h4 class="text-info"><?= $booking_stats['pending_bookings'] ?? 0 ?></h4>
                        <p class="mb-0 small">Pending</p>
                    </div>
                    <div class="col-3">
                        <h4 class="text-success"><?= $booking_stats['checked_in_bookings'] ?? 0 ?></h4>
                        <p class="mb-0 small">Checked In</p>
                    </div>
                    <div class="col-3">
                        <h4 class="text-primary"><?= $booking_stats['checked_out_bookings'] ?? 0 ?></h4>
                        <p class="mb-0 small">Checked Out</p>
                    </div>
                    <div class="col-3">
                        <h4 class="text-danger"><?= $booking_stats['canceled_bookings'] ?? 0 ?></h4>
                        <p class="mb-0 small">Canceled</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-clock"></i> Recent Bookings</h5>
                <a href="<?= BASE_URL ?>/bookings" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_bookings)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No recent bookings found.</p>
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
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= BASE_URL ?>/bookings/<?= $booking['id'] ?>"
                                                class="text-decoration-none">
                                                <?= htmlspecialchars($booking['booking_code']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                                        <td><?= htmlspecialchars($booking['room_numbers']) ?></td>
                                        <td><?= formatDate($booking['checkin_date']) ?></td>
                                        <td><?= formatDate($booking['checkout_date']) ?></td>
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