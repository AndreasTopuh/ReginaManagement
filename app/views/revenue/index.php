<?php
$title = "Revenue Reports - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-chart-line"></i> Revenue Reports</h1>
            <div class="btn-group">
                <a href="<?= BASE_URL ?>/revenue?action=detailed" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> Detailed Report
                </a>
                <a href="<?= BASE_URL ?>/revenue?action=export&format=csv&type=summary&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>" class="btn btn-outline-success">
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
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $date_from ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $date_to ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="period" class="form-label">Period</label>
                        <select class="form-select" id="period" name="period">
                            <option value="daily" <?= $period === 'daily' ? 'selected' : '' ?>>Daily</option>
                            <option value="monthly" <?= $period === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-adjust dates when period changes
    document.getElementById('period').addEventListener('change', function() {
        const period = this.value;
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        const today = new Date();

        if (period === 'monthly') {
            // Set to first day of current month
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            // Set to last day of current month
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

            dateFromInput.value = firstDay.toISOString().split('T')[0];
            dateToInput.value = lastDay.toISOString().split('T')[0];
        } else if (period === 'daily') {
            // Set to current month range for daily view
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            dateFromInput.value = firstDay.toISOString().split('T')[0];
            dateToInput.value = today.toISOString().split('T')[0];
        }
    });

    // Add quick date range buttons
    document.addEventListener('DOMContentLoaded', function() {
        const filterCard = document.querySelector('.card-body form');
        const quickRangeDiv = document.createElement('div');
        quickRangeDiv.className = 'col-md-12 mt-3';
        quickRangeDiv.innerHTML = `
        <label class="form-label">Quick Date Ranges:</label>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('today')">Today</button>
            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('this_week')">This Week</button>
            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('this_month')">This Month</button>
            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('last_month')">Last Month</button>
            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('this_year')">This Year</button>
        </div>
    `;
        filterCard.appendChild(quickRangeDiv);
    });

    function setDateRange(range) {
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        const periodSelect = document.getElementById('period');
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
                periodSelect.value = 'daily';
                break;
            case 'this_week':
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                fromDate = startOfWeek;
                toDate = today;
                periodSelect.value = 'daily';
                break;
            case 'this_month':
                fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                periodSelect.value = 'monthly';
                break;
            case 'last_month':
                fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                toDate = new Date(today.getFullYear(), today.getMonth(), 0);
                periodSelect.value = 'monthly';
                break;
            case 'this_year':
                fromDate = new Date(today.getFullYear(), 0, 1);
                toDate = today;
                periodSelect.value = 'monthly';
                break;
        }

        dateFromInput.value = fromDate.toISOString().split('T')[0];
        dateToInput.value = toDate.toISOString().split('T')[0];

        // Auto-submit form after setting dates and period
        setTimeout(() => {
            document.querySelector('form').submit();
        }, 100);
    }
</script>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= formatCurrency($revenue_summary['total_revenue'] ?? 0) ?></h4>
                        <p class="mb-0">Total Revenue</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
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
                        <h4><?= $revenue_summary['total_bookings'] ?? 0 ?></h4>
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
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= formatCurrency($revenue_summary['average_booking_value'] ?? 0) ?></h4>
                        <p class="mb-0">Avg Booking Value</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calculator fa-2x"></i>
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
                        <h4><?= $revenue_summary['completed_bookings'] ?? 0 ?></h4>
                        <p class="mb-0">Completed Bookings</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Revenue Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-pie-chart"></i> Booking Status</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Revenue by Room Type -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bed"></i> Revenue by Room Type</h5>
            </div>
            <div class="card-body">
                <?php if (empty($revenue_by_room_type)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <p>No revenue data available for this period.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Room Type</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                    <th>Avg Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revenue_by_room_type as $room_type): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($room_type['type_name']) ?></strong></td>
                                        <td><?= $room_type['total_bookings'] ?></td>
                                        <td><?= formatCurrency($room_type['total_revenue']) ?></td>
                                        <td><?= formatCurrency($room_type['average_rate']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-trophy"></i> Top Revenue Rooms</h5>
            </div>
            <div class="card-body">
                <?php if (empty($top_revenue_rooms)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-medal fa-3x mb-3"></i>
                        <p>No room data available for this period.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Room</th>
                                    <th>Type</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($top_revenue_rooms, 0, 5) as $room): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $room['room_number'] ?></strong>
                                            <small class="text-muted d-block">Floor <?= $room['floor_number'] ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($room['type_name']) ?></td>
                                        <td><?= $room['total_bookings'] ?></td>
                                        <td><?= formatCurrency($room['room_revenue']) ?></td>
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

<!-- Monthly Revenue Trend -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calendar-alt"></i> Monthly Revenue (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($revenue_by_month)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>No monthly data available.</p>
                    </div>
                <?php else: ?>
                    <canvas id="monthlyChart" height="60"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?= json_encode($revenue_by_period) ?>;

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => item.period),
            datasets: [{
                label: 'Revenue',
                data: revenueData.map(item => parseFloat(item.total_revenue)),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Bookings',
                data: revenueData.map(item => parseInt(item.total_bookings)),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                yAxisID: 'y1',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = <?= json_encode($revenue_summary) ?>;

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Active', 'Pending', 'Cancelled'],
            datasets: [{
                data: [
                    parseInt(statusData.completed_bookings || 0),
                    parseInt(statusData.active_bookings || 0),
                    parseInt(statusData.pending_bookings || 0),
                    parseInt(statusData.cancelled_bookings || 0)
                ],
                backgroundColor: [
                    '#28a745',
                    '#007bff',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Monthly Chart
    <?php if (!empty($revenue_by_month)): ?>
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = <?= json_encode(array_reverse($revenue_by_month)) ?>;

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(item => item.month_name + ' ' + item.year),
                datasets: [{
                    label: 'Monthly Revenue',
                    data: monthlyData.map(item => parseFloat(item.total_revenue)),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    <?php endif; ?>
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>