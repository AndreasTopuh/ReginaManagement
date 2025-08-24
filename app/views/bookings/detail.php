<?php
$title = "Booking Details - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-eye"></i> Booking Details</h1>
                <p class="text-muted mb-0">Booking Code: <strong><?= htmlspecialchars($booking['booking_code']) ?></strong></p>
            </div>
            <a href="<?= BASE_URL ?>/bookings.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Bookings
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Booking Information -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-info-circle"></i> Booking Information</h5>
                <?php
                $status_class = [
                    'Pending' => 'warning',
                    'CheckedIn' => 'success',
                    'CheckedOut' => 'primary',
                    'Canceled' => 'danger'
                ];
                ?>
                <span class="badge bg-<?= $status_class[$booking['status']] ?> fs-6">
                    <?= $booking['status'] ?>
                </span>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="checkin_date" class="form-label">Check-in Date</label>
                                <input type="datetime-local" class="form-control" id="checkin_date" name="checkin_date"
                                    value="<?= date('Y-m-d\TH:i', strtotime($booking['checkin_date'])) ?>"
                                    <?= $booking['status'] === 'CheckedOut' ? 'readonly' : '' ?>>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="checkout_date" class="form-label">Check-out Date</label>
                                <input type="datetime-local" class="form-control" id="checkout_date" name="checkout_date"
                                    value="<?= date('Y-m-d\TH:i', strtotime($booking['checkout_date'])) ?>"
                                    <?= $booking['status'] === 'CheckedOut' ? 'readonly' : '' ?>>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="meal_plan" class="form-label">Meal Plan</label>
                                <select class="form-select" id="meal_plan" name="meal_plan"
                                    <?= $booking['status'] === 'CheckedOut' ? 'disabled' : '' ?>>
                                    <option value="NONE" <?= $booking['meal_plan'] === 'NONE' ? 'selected' : '' ?>>No Meals</option>
                                    <option value="BREAKFAST" <?= $booking['meal_plan'] === 'BREAKFAST' ? 'selected' : '' ?>>Breakfast Included</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Duration</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-info"><?= $booking['duration_nights'] ?> night(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="special_request" class="form-label">Special Requests</label>
                        <textarea class="form-control" id="special_request" name="special_request" rows="3"
                            <?= $booking['status'] === 'CheckedOut' ? 'readonly' : '' ?>><?= htmlspecialchars($booking['special_request']) ?></textarea>
                    </div>

                    <?php if ($booking['status'] !== 'CheckedOut' && $booking['status'] !== 'Canceled'): ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Booking
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Guest Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-user"></i> Guest Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td><?= htmlspecialchars($booking['guest_phone']) ?: '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?= htmlspecialchars($booking['guest_email']) ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>ID Type:</strong></td>
                                <td><?= htmlspecialchars($booking['guest_id_type']) ?: '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>ID Number:</strong></td>
                                <td><?= htmlspecialchars($booking['guest_id_number']) ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rooms -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-bed"></i> Room(s)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($booking_rooms)): ?>
                    <div class="text-center text-muted py-3">
                        <p>No rooms assigned to this booking.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Type</th>
                                    <th>Rate/Night</th>
                                    <th>Nights</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($booking_rooms as $room): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($room['room_number']) ?></strong></td>
                                        <td><span class="badge bg-info"><?= htmlspecialchars($room['type_name']) ?></span></td>
                                        <td><?= formatCurrency($room['booked_rate_per_night']) ?></td>
                                        <td><?= $room['nights'] ?></td>
                                        <td><?= formatCurrency($room['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6><i class="fas fa-cogs"></i> Actions</h6>
            </div>
            <div class="card-body">
                <?php if ($booking['status'] === 'Pending'): ?>
                    <form method="POST" class="mb-2">
                        <input type="hidden" name="action" value="checkin">
                        <button type="submit" class="btn btn-success w-100 btn-status-change" data-action="checkin">
                            <i class="fas fa-sign-in-alt"></i> Check In
                        </button>
                    </form>
                    <form method="POST" class="mb-2">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn btn-danger w-100 btn-status-change" data-action="cancel">
                            <i class="fas fa-times"></i> Cancel Booking
                        </button>
                    </form>
                <?php elseif ($booking['status'] === 'CheckedIn'): ?>
                    <form method="POST" class="mb-2">
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit" class="btn btn-primary w-100 btn-status-change" data-action="checkout">
                            <i class="fas fa-sign-out-alt"></i> Check Out
                        </button>
                    </form>
                <?php elseif ($booking['status'] === 'CheckedOut'): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> This booking has been completed.
                    </div>
                <?php elseif ($booking['status'] === 'Canceled'): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> This booking has been canceled.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pricing Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h6><i class="fas fa-calculator"></i> Pricing Summary</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>Room Amount:</td>
                        <td class="text-end"><?= formatCurrency($booking['total_room_amount']) ?></td>
                    </tr>
                    <tr>
                        <td>Service Amount:</td>
                        <td class="text-end"><?= formatCurrency($booking['total_service_amount']) ?></td>
                    </tr>
                    <tr>
                        <td>Tax (<?= $booking['tax_rate'] ?>%):</td>
                        <td class="text-end"><?= formatCurrency($booking['total_room_amount'] * $booking['tax_rate'] / 100) ?></td>
                    </tr>
                    <tr>
                        <td>Service Fee (<?= $booking['service_rate'] ?>%):</td>
                        <td class="text-end"><?= formatCurrency($booking['total_room_amount'] * $booking['service_rate'] / 100) ?></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Grand Total:</strong></td>
                        <td class="text-end"><strong><?= formatCurrency($booking['grand_total']) ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Booking History -->
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-history"></i> Booking History</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Booking Created</h6>
                            <p class="timeline-text">Created by <?= htmlspecialchars($booking['created_by_name']) ?></p>
                            <span class="timeline-date"><?= formatDateTime($booking['created_at']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>