<?php
$title = "Detailed Revenue Report - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-list"></i> Detailed Revenue Report</h1>
            <div class="btn-group">
                <a href="<?= BASE_URL ?>/revenue.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Summary
                </a>
                <a href="<?= BASE_URL ?>/revenue.php?action=export&format=csv&type=detailed&date_from=<?= $date_from ?>&date_to=<?= $date_to ?><?= !empty($status) ? '&status=' . $status : '' ?><?= !empty($room_type) ? '&room_type=' . $room_type : '' ?>" class="btn btn-outline-success">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="action" value="detailed">
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $date_from ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $date_to ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="CheckedIn" <?= $status === 'CheckedIn' ? 'selected' : '' ?>>Checked In</option>
                            <option value="CheckedOut" <?= $status === 'CheckedOut' ? 'selected' : '' ?>>Checked Out</option>
                            <option value="Cancelled" <?= $status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" <?= $sort_by === 'created_at' ? 'selected' : '' ?>>Created Date</option>
                            <option value="checkin_date" <?= $sort_by === 'checkin_date' ? 'selected' : '' ?>>Check-in Date</option>
                            <option value="grand_total" <?= $sort_by === 'grand_total' ? 'selected' : '' ?>>Total Amount</option>
                            <option value="booking_code" <?= $sort_by === 'booking_code' ? 'selected' : '' ?>>Booking Code</option>
                            <option value="guest_name" <?= $sort_by === 'guest_name' ? 'selected' : '' ?>>Guest Name</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort_order" class="form-label">Order</label>
                        <select class="form-select" id="sort_order" name="sort_order">
                            <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Descending</option>
                            <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Quick Date Range Buttons -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label">Quick Date Ranges:</label>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="setDetailedDateRange('today')">Today</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDetailedDateRange('this_week')">This Week</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDetailedDateRange('this_month')">This Month</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDetailedDateRange('last_month')">Last Month</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDetailedDateRange('this_year')">This Year</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setDetailedDateRange(range) {
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        const today = new Date();

        let fromDate, toDate;

        // Remove active class from all buttons
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.replace('btn-primary', 'btn-outline-secondary');
        });

        // Add active class to clicked button
        event.target.classList.add('active');
        event.target.classList.replace('btn-outline-secondary', 'btn-primary');

        switch (range) {
            case 'today':
                fromDate = today;
                toDate = today;
                break;
            case 'this_week':
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                fromDate = startOfWeek;
                toDate = today;
                break;
            case 'this_month':
                fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                toDate = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'this_year':
                fromDate = new Date(today.getFullYear(), 0, 1);
                toDate = today;
                break;
        }

        dateFromInput.value = fromDate.toISOString().split('T')[0];
        dateToInput.value = toDate.toISOString().split('T')[0];
        
        // Auto-submit form after setting dates
        setTimeout(() => {
            document.querySelector('form').submit();
        }, 100);
    }
</script>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4><?= $totals['total_records'] ?? 0 ?></h4>
                <p class="mb-0">Total Bookings</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4><?= formatCurrency($totals['total_room_amount'] ?? 0) ?></h4>
                <p class="mb-0">Room Revenue</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4><?= formatCurrency(($totals['total_tax'] ?? 0) + ($totals['total_service'] ?? 0)) ?></h4>
                <p class="mb-0">Tax + Service</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4><?= formatCurrency($totals['grand_total'] ?? 0) ?></h4>
                <p class="mb-0">Grand Total</p>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Bookings Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-table"></i> Booking Details</h5>
            </div>
            <div class="card-body">
                <?php if (empty($detailed_bookings)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No bookings found for the selected criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Booking Code</th>
                                    <th>Guest Name</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Rooms</th>
                                    <th>Status</th>
                                    <th>Room Amount</th>
                                    <th>Tax</th>
                                    <th>Service</th>
                                    <th>Total</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detailed_bookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $booking['booking_code'] ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                                        <td><?= formatDate($booking['checkin_date']) ?></td>
                                        <td><?= formatDate($booking['checkout_date']) ?></td>
                                        <td>
                                            <small><?= htmlspecialchars($booking['room_numbers']) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $status_colors = [
                                                'Pending' => 'warning',
                                                'CheckedIn' => 'success',
                                                'CheckedOut' => 'primary',
                                                'Cancelled' => 'danger'
                                            ];
                                            $color = $status_colors[$booking['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= $booking['status'] ?></span>
                                        </td>
                                        <td><?= formatCurrency($booking['total_room_amount'] ?? 0) ?></td>
                                        <td><?= formatCurrency($booking['tax_amount'] ?? 0) ?></td>
                                        <td><?= formatCurrency($booking['service_amount'] ?? 0) ?></td>
                                        <td>
                                            <strong><?= formatCurrency($booking['grand_total'] ?? 0) ?></strong>
                                        </td>
                                        <td><?= formatDate($booking['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="6">TOTALS</th>
                                    <th><?= formatCurrency($totals['total_room_amount'] ?? 0) ?></th>
                                    <th><?= formatCurrency($totals['total_tax'] ?? 0) ?></th>
                                    <th><?= formatCurrency($totals['total_service'] ?? 0) ?></th>
                                    <th><strong><?= formatCurrency($totals['grand_total'] ?? 0) ?></strong></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>