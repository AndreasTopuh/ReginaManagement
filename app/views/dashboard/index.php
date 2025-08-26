<?php
$title = "Dashboard";
include INCLUDES_PATH . '/header.php';
?>

<!-- Dashboard Header dengan Filter -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-muted">Dashboard</h3>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="date" name="date_from" class="form-control" value="<?= $date_from ?>" required>
        <span class="align-self-center">to</span>
        <input type="date" name="date_to" class="form-control" value="<?= $date_to ?>" required>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<!-- Statistics Cards Row -->
<div class="row g-3 mb-4">
    <!-- New Bookings -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-plus fa-lg text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">New Bookings</h6>
                        <h3 class="mb-0"><?= $booking_stats['total_bookings'] ?? 0 ?></h3>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 8.2% from last week
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Check In -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-sign-in-alt fa-lg text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Check In</h6>
                        <h3 class="mb-0"><?= $booking_stats['checked_in_bookings'] ?? 0 ?></h3>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 9.7% from last week
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Check Out -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-sign-out-alt fa-lg text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Check Out</h6>
                        <h3 class="mb-0"><?= $booking_stats['checked_out_bookings'] ?? 0 ?></h3>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 2.1% from last week
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-money-bill-wave fa-lg text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Revenue</h6>
                        <h3 class="mb-0"><?= formatCurrency($booking_stats['total_revenue'] ?? 0) ?></h3>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 8.7% from last week
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overall Rating Card (Additional metric) -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-star fa-lg text-secondary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Overall Rating</h6>
                        <h3 class="mb-0">4.5</h3>
                        <div class="mt-1">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 90%"></div>
                            </div>
                        </div>
                        <div class="mt-2 small">
                            <span class="text-muted">Facilities: </span><span class="fw-bold">4.1</span><br>
                            <span class="text-muted">Services: </span><span class="fw-bold">4.8</span><br>
                            <span class="text-muted">Comfort: </span><span class="fw-bold">4.5</span><br>
                            <span class="text-muted">Location: </span><span class="fw-bold">4.9</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Charts and Analytics Row -->
<div class="row g-3 mb-4">
    <!-- Reservation By Months Chart -->
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Reservation</h6>
                    <small class="text-muted">By months</small>
                </div>
                <div class="mt-2">
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i> Trending up by 5.2% this month
                    </small>
                    <br>
                    <small class="text-muted">Showing total visitors for the last 6 months</small>
                </div>
            </div>
            <div class="card-body">
                <!-- Simplified chart representation -->
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-end" style="height: 200px;">
                        <div class="chart-bar bg-light" style="width: 15%; height: 70%;">
                            <div class="text-center mt-auto small">Jan<br>186</div>
                        </div>
                        <div class="chart-bar bg-light" style="width: 15%; height: 85%;">
                            <div class="text-center mt-auto small">Feb<br>305</div>
                        </div>
                        <div class="chart-bar bg-warning" style="width: 15%; height: 60%;">
                            <div class="text-center mt-auto small">Mar<br>237</div>
                        </div>
                        <div class="chart-bar bg-light" style="width: 15%; height: 30%;">
                            <div class="text-center mt-auto small">Apr<br>73</div>
                        </div>
                        <div class="chart-bar bg-light" style="width: 15%; height: 75%;">
                            <div class="text-center mt-auto small">May<br>209</div>
                        </div>
                        <div class="chart-bar bg-light" style="width: 15%; height: 90%;">
                            <div class="text-center mt-auto small">Jun<br>214</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking By Platform Pie Chart -->
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Booking</h6>
                    <small class="text-muted">By Platform</small>
                </div>
                <div class="mt-2">
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i> Trending up by 5.2% this month
                    </small>
                    <br>
                    <small class="text-muted">Showing total visitors for the last 6 months</small>
                </div>
            </div>
            <div class="card-body">
                <!-- Pie chart representation -->
                <div class="row">
                    <div class="col-6">
                        <div class="position-relative mx-auto" style="width: 150px; height: 150px;">
                            <!-- Simplified pie chart using CSS -->
                            <div class="position-absolute w-100 h-100 rounded-circle"
                                style="background: conic-gradient(#8B4513 0deg 130deg, #D2B48C 130deg 180deg, #654321 180deg 230deg, #A0522D 230deg 280deg, #DEB887 280deg 360deg);">
                            </div>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <strong>279</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="legend">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge me-2" style="background-color: #8B4513;">&nbsp;</span>
                                <small>Direct Booking</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge me-2" style="background-color: #D2B48C;">&nbsp;</span>
                                <small>Booking.com</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge me-2" style="background-color: #654321;">&nbsp;</span>
                                <small>Airbnb</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge me-2" style="background-color: #A0522D;">&nbsp;</span>
                                <small>Others</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge me-2" style="background-color: #DEB887;">&nbsp;</span>
                                <small>Agoda</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Room Availability Section -->
<div class="row g-3 mb-4">
    <div class="col-xl-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Room Availability</h6>
                    <small class="text-muted">Recent</small>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="p-3">
                            <h3 class="text-primary mb-1"><?= $room_stats['total_rooms'] ?? 0 ?></h3>
                            <p class="text-muted mb-0">Occupied</p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3">
                            <h3 class="text-success mb-1"><?= $room_stats['available_rooms'] ?? 0 ?></h3>
                            <p class="text-muted mb-0">Available</p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3">
                            <h3 class="text-warning mb-1"><?= $room_stats['occupied_rooms'] ?? 0 ?></h3>
                            <p class="text-muted mb-0">Available</p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3">
                            <h3 class="text-danger mb-1"><?= $room_stats['out_of_service_rooms'] ?? 0 ?></h3>
                            <p class="text-muted mb-0">Not Available</p>
                        </div>
                    </div>
                </div>

                <!-- Visual representation of room status -->
                <div class="mt-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 rounded" style="background-color: #8B4513; height: 80px;"></div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background-color: #D2B48C; height: 80px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking List and Tasks Row -->
<div class="row g-3">
    <!-- Booking List -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Booking List</h6>
                <a href="<?= BASE_URL ?>/bookings" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_bookings)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No recent bookings found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Guest Name</th>
                                    <th>Room Type</th>
                                    <th>Room Number</th>
                                    <th>Duration</th>
                                    <th>Check-In & Check-Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($recent_bookings, 0, 4) as $booking): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= BASE_URL ?>/bookings/<?= $booking['id'] ?>"
                                                class="text-decoration-none text-primary fw-bold">
                                                <?= htmlspecialchars($booking['booking_code']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark">Standard</span>
                                        </td>
                                        <td><?= htmlspecialchars($booking['room_numbers']) ?></td>
                                        <td>
                                            <?php
                                            $checkin = new DateTime($booking['checkin_date']);
                                            $checkout = new DateTime($booking['checkout_date']);
                                            $duration = $checkin->diff($checkout)->days;
                                            ?>
                                            <?= $duration ?> Nights
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('M j, Y', strtotime($booking['checkin_date'])) ?> -
                                                <?= date('M j, Y', strtotime($booking['checkout_date'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $status_classes = [
                                                'Pending' => 'warning',
                                                'CheckedIn' => 'success',
                                                'CheckedOut' => 'primary',
                                                'Canceled' => 'danger'
                                            ];
                                            $status_text = [
                                                'Pending' => 'Booked',
                                                'CheckedIn' => 'Checked-In',
                                                'CheckedOut' => 'Checked-Out',
                                                'Canceled' => 'Canceled'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $status_classes[$booking['status']] ?>">
                                                <?= $status_text[$booking['status']] ?>
                                            </span>
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

    <!-- Tasks Section -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Task</h6>
                <button class="btn btn-sm btn-link">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="card-body">
                <!-- Sample Tasks -->
                <div class="task-item mb-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">May 20th 2025</h6>
                            <p class="text-muted small mb-0">
                                Sorem ipsum dolor amet, consectetur adipiscing elit.
                                Etiam eu turpis molestie, dictum est a, mattis tellus.
                            </p>
                        </div>
                        <button class="btn btn-sm btn-link text-muted">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                </div>

                <div class="task-item mb-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">May 20th 2025</h6>
                            <p class="text-muted small mb-0">
                                Sorem ipsum dolor amet, consectetur adipiscing elit.
                                Etiam eu turpis molestie, dictum est a, mattis tellus.
                            </p>
                        </div>
                        <button class="btn btn-sm btn-link text-muted">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button class="btn btn-outline-primary btn-sm">View All Tasks</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .chart-container {
        margin: 20px 0;
    }

    .chart-bar {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        margin: 0 2px;
        border-radius: 4px 4px 0 0;
        transition: all 0.3s ease;
    }

    .chart-bar:hover {
        opacity: 0.8;
    }

    .task-item:hover {
        background-color: #e9ecef !important;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>