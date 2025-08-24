<?php
$title = "Create Booking - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-plus"></i> Create New Booking</h1>
            <a href="<?= BASE_URL ?>/bookings.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Bookings
            </a>
        </div>
    </div>
</div>

<form method="POST" class="needs-validation" novalidate>
    <div class="row">
        <div class="col-md-8">
            <!-- Guest Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Guest Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="guest_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="guest_name" name="guest_name"
                                    value="<?= htmlspecialchars($_POST['guest_name'] ?? '') ?>" required>
                                <div class="invalid-feedback">Please provide guest's full name.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                    placeholder="+62 xxx-xxxx-xxxx">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                    placeholder="guest@example.com">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="id_type_id" class="form-label">ID Type</label>
                                <select class="form-select" id="id_type_id" name="id_type_id">
                                    <option value="">Select ID Type</option>
                                    <?php foreach ($id_types as $type): ?>
                                        <option value="<?= $type['id'] ?>"
                                            <?= ($_POST['id_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type['type_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="id_number" class="form-label">ID Number</label>
                                <input type="text" class="form-control" id="id_number" name="id_number"
                                    value="<?= htmlspecialchars($_POST['id_number'] ?? '') ?>"
                                    placeholder="ID Number">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-calendar"></i> Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="checkin_date" class="form-label">Check-in Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="checkin_date" name="checkin_date"
                                    value="<?= $_POST['checkin_date'] ?? $_GET['checkin_date'] ?? '' ?>"
                                    min="<?= date('Y-m-d') ?>" required>
                                <div class="invalid-feedback">Please select check-in date.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="checkout_date" class="form-label">Check-out Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="checkout_date" name="checkout_date"
                                    value="<?= $_POST['checkout_date'] ?? $_GET['checkout_date'] ?? '' ?>"
                                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                                <div class="invalid-feedback">Please select check-out date.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="meal_plan" class="form-label">Meal Plan</label>
                                <select class="form-select" id="meal_plan" name="meal_plan">
                                    <option value="NONE" <?= ($_POST['meal_plan'] ?? '') === 'NONE' ? 'selected' : '' ?>>No Meals</option>
                                    <option value="BREAKFAST" <?= ($_POST['meal_plan'] ?? '') === 'BREAKFAST' ? 'selected' : '' ?>>Breakfast Included</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="special_request" class="form-label">Special Requests</label>
                        <textarea class="form-control" id="special_request" name="special_request" rows="2"
                            placeholder="Any special requests or notes..."><?= htmlspecialchars($_POST['special_request'] ?? '') ?></textarea>
                    </div>

                    <?php if (!empty($available_rooms)): ?>
                        <div class="mb-3">
                            <button type="button" id="check-availability" class="btn btn-outline-info">
                                <i class="fas fa-sync-alt"></i> Refresh Room Availability
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <button type="button" id="check-availability" class="btn btn-primary">
                                <i class="fas fa-search"></i> Check Room Availability
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Room Selection -->
            <?php if (!empty($available_rooms)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-bed"></i> Select Rooms</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($available_rooms as $room): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card room-card h-100">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input room-checkbox" type="checkbox"
                                                    name="selected_rooms[]" value="<?= $room['id'] ?>"
                                                    id="room_<?= $room['id'] ?>"
                                                    data-price="<?= $room['price'] ?>">
                                                <label class="form-check-label fw-bold" for="room_<?= $room['id'] ?>">
                                                    Room <?= htmlspecialchars($room['room_number']) ?>
                                                </label>
                                            </div>
                                            <div class="mt-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-info"><?= htmlspecialchars($room['type_name']) ?></span>
                                                    <span class="badge bg-success">Floor <?= $room['floor_number'] ?></span>
                                                </div>
                                                <div class="mt-2">
                                                    <strong><?= formatCurrency($room['price']) ?></strong>
                                                    <small class="text-muted">/night</small>
                                                </div>
                                                <?php if (!empty($room['features'])): ?>
                                                    <div class="mt-1">
                                                        <small class="text-muted"><?= htmlspecialchars($room['features']) ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mb-4">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-info-circle fa-2x text-info mb-3"></i>
                        <h5>Select Dates for Accurate Availability</h5>
                        <p class="text-muted">These are all available rooms. Select check-in and check-out dates to see only rooms available for your specific dates.</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Booking
                </button>
                <a href="<?= BASE_URL ?>/bookings.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top">
                <div class="card-header">
                    <h6><i class="fas fa-calculator"></i> Booking Summary</h6>
                </div>
                <div id="booking-summary">
                    <div class="card-body text-center text-muted">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <p>Select rooms to see pricing summary</p>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-info-circle"></i> Information</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success"></i> Fill in guest information</li>
                        <li><i class="fas fa-check text-success"></i> Select check-in/out dates</li>
                        <li><i class="fas fa-check text-success"></i> Choose available rooms</li>
                        <li><i class="fas fa-check text-success"></i> Review booking summary</li>
                        <li><i class="fas fa-info-circle text-info"></i> Tax (10%) and service (5%) will be added</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include INCLUDES_PATH . '/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate prices when rooms are selected/deselected
        const roomCheckboxes = document.querySelectorAll('.room-checkbox');
        roomCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                calculateBookingSummary();
            });
        });

        // Calculate prices when dates change
        const checkinDate = document.getElementById('checkin_date');
        const checkoutDate = document.getElementById('checkout_date');

        if (checkinDate) {
            checkinDate.addEventListener('change', calculateBookingSummary);
        }
        if (checkoutDate) {
            checkoutDate.addEventListener('change', calculateBookingSummary);
        }

        // Initial calculation
        calculateBookingSummary();

        function calculateBookingSummary() {
            const selectedRooms = document.querySelectorAll('.room-checkbox:checked');
            const checkin = document.getElementById('checkin_date').value;
            const checkout = document.getElementById('checkout_date').value;
            const summaryDiv = document.getElementById('booking-summary');

            if (!checkin || !checkout || selectedRooms.length === 0) {
                summaryDiv.innerHTML = `
                <div class="card-body text-center text-muted">
                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                    <p>Select rooms and dates to see pricing summary</p>
                </div>
            `;
                return;
            }

            const checkinDate = new Date(checkin);
            const checkoutDate = new Date(checkout);
            const nights = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));

            if (nights <= 0) {
                summaryDiv.innerHTML = `
                <div class="card-body text-center text-muted">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Invalid date range</p>
                </div>
            `;
                return;
            }

            let totalRoomPrice = 0;
            selectedRooms.forEach(function(checkbox) {
                const price = parseFloat(checkbox.dataset.price || 0);
                totalRoomPrice += price * nights;
            });

            const tax = totalRoomPrice * 0.1; // 10% tax
            const service = totalRoomPrice * 0.05; // 5% service
            const grandTotal = totalRoomPrice + tax + service;

            summaryDiv.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>${selectedRooms.length} room(s) × ${nights} night(s):</span>
                    <span>Rp ${totalRoomPrice.toLocaleString('id-ID')}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax (10%):</span>
                    <span>Rp ${tax.toLocaleString('id-ID')}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Service (5%):</span>
                    <span>Rp ${service.toLocaleString('id-ID')}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total:</span>
                    <span>Rp ${grandTotal.toLocaleString('id-ID')}</span>
                </div>
            </div>
        `;
        }
    });
</script>