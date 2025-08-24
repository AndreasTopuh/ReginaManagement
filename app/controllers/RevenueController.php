<?php
class RevenueController
{
    private $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
    }

    public function index()
    {
        requireLogin();

        // Check if user is Owner or Admin
        if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
            $_SESSION['error'] = "Access denied. Only Owner and Admin can view revenue reports.";
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        }

        // Get filter parameters
        $date_from = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
        $date_to = $_GET['date_to'] ?? date('Y-m-d'); // Today
        $period = $_GET['period'] ?? 'monthly';
        $room_type = $_GET['room_type'] ?? '';

        // Auto-adjust dates based on period if not manually set
        if (!isset($_GET['date_from']) && !isset($_GET['date_to'])) {
            if ($period === 'monthly') {
                // For monthly view, show current month (1st to last day)
                $date_from = date('Y-m-01');
                $date_to = date('Y-m-t'); // Last day of current month
            } else {
                // For daily view, show current month up to today
                $date_from = date('Y-m-01');
                $date_to = date('Y-m-d');
            }
        }

        // If period is changed to monthly, ensure we have full month range
        if ($period === 'monthly' && isset($_GET['period'])) {
            // If switching to monthly, adjust to full month range
            $from_date_obj = new DateTime($date_from);
            $to_date_obj = new DateTime($date_to);

            // Set to first day of the from_date month
            $date_from = $from_date_obj->format('Y-m-01');

            // Set to last day of the to_date month
            $date_to = $to_date_obj->format('Y-m-t');
        }

        // Get revenue data
        $revenue_summary = $this->bookingModel->getRevenueSummary($date_from, $date_to);
        $revenue_by_period = $this->bookingModel->getRevenueByPeriod($date_from, $date_to, $period);
        $revenue_by_room_type = $this->bookingModel->getRevenueByRoomType($date_from, $date_to);
        $revenue_by_month = $this->bookingModel->getRevenueByMonth();
        $top_revenue_rooms = $this->bookingModel->getTopRevenueRooms($date_from, $date_to, 10);

        // Get additional statistics
        $booking_statistics = $this->bookingModel->getBookingStatistics($date_from, $date_to);
        $payment_methods = $this->bookingModel->getPaymentMethodStats($date_from, $date_to);

        include APP_PATH . '/views/revenue/index.php';
    }

    public function detailed()
    {
        if (!hasPermission(['Owner', 'Admin'])) {
            setFlashMessage('Access denied!', 'error');
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        }

        // Get filter parameters with smart defaults
        $date_from = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
        $date_to = $_GET['date_to'] ?? date('Y-m-t'); // Last day of current month (for detailed view)
        $status = $_GET['status'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'created_at';
        $sort_order = $_GET['sort_order'] ?? 'DESC';

        // Validate sort parameters
        $allowed_sorts = ['created_at', 'checkin_date', 'grand_total', 'booking_code', 'guest_name'];
        if (!in_array($sort_by, $allowed_sorts)) {
            $sort_by = 'created_at';
        }

        if (!in_array($sort_order, ['ASC', 'DESC'])) {
            $sort_order = 'DESC';
        }

        // Get detailed bookings
        $detailed_bookings = $this->bookingModel->getDetailedRevenue($date_from, $date_to, $status, $sort_by, $sort_order);

        // Calculate totals
        $totals = [
            'total_records' => count($detailed_bookings),
            'total_room_amount' => 0,
            'total_tax' => 0,
            'total_service' => 0,
            'grand_total' => 0
        ];

        foreach ($detailed_bookings as $booking) {
            $totals['total_room_amount'] += $booking['total_room_amount'] ?? 0;
            $totals['total_tax'] += $booking['tax_amount'] ?? 0;
            $totals['total_service'] += $booking['service_amount'] ?? 0;
            $totals['grand_total'] += $booking['grand_total'] ?? 0;
        }

        include APP_PATH . '/views/revenue/detailed.php';
    }

    public function export()
    {
        requireLogin();

        // Check if user is Owner or Admin
        if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
            $_SESSION['error'] = "Access denied. Only Owner and Admin can export reports.";
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        }

        $format = $_GET['format'] ?? 'csv';
        $date_from = $_GET['date_from'] ?? date('Y-m-01');
        $date_to = $_GET['date_to'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? 'summary';

        if ($format === 'csv') {
            $this->exportCSV($type, $date_from, $date_to);
        } elseif ($format === 'pdf') {
            $this->exportPDF($type, $date_from, $date_to);
        }
    }

    private function exportCSV($type, $date_from, $date_to)
    {
        $filename = "revenue_report_{$type}_{$date_from}_to_{$date_to}.csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        if ($type === 'summary') {
            // Export summary data
            fputcsv($output, ['Date Range', 'Total Revenue', 'Total Bookings', 'Average Booking Value', 'Tax Amount', 'Service Amount']);

            $summary = $this->bookingModel->getRevenueSummary($date_from, $date_to);
            fputcsv($output, [
                "$date_from to $date_to",
                $summary['total_revenue'] ?? 0,
                $summary['total_bookings'] ?? 0,
                $summary['average_booking_value'] ?? 0,
                $summary['total_tax'] ?? 0,
                $summary['total_service'] ?? 0
            ]);
        } elseif ($type === 'detailed') {
            // Export detailed bookings
            fputcsv($output, ['Booking Code', 'Guest Name', 'Check In', 'Check Out', 'Rooms', 'Status', 'Room Amount', 'Tax', 'Service', 'Total', 'Created At']);

            $bookings = $this->bookingModel->getDetailedRevenue($date_from, $date_to);
            foreach ($bookings as $booking) {
                fputcsv($output, [
                    $booking['booking_code'],
                    $booking['guest_name'],
                    $booking['checkin_date'],
                    $booking['checkout_date'],
                    $booking['room_numbers'],
                    $booking['status'],
                    $booking['total_room_amount'],
                    $booking['tax_amount'],
                    $booking['service_amount'],
                    $booking['grand_total'],
                    $booking['created_at']
                ]);
            }
        }

        fclose($output);
        exit;
    }

    private function exportPDF($type, $date_from, $date_to)
    {
        // For now, redirect to CSV. PDF export would require additional library like TCPDF or FPDF
        $_SESSION['info'] = "PDF export feature coming soon. CSV export initiated instead.";
        header("Location: " . BASE_URL . "/revenue.php?action=export&format=csv&type=$type&date_from=$date_from&date_to=$date_to");
        exit;
    }

    public function chart_data()
    {
        requireLogin();

        // Check if user is Owner or Admin
        if (!in_array(SessionManager::getUserRole(), ['Owner', 'Admin'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $period = $_GET['period'] ?? 'monthly';
        $date_from = $_GET['date_from'] ?? date('Y-m-01');
        $date_to = $_GET['date_to'] ?? date('Y-m-d');

        $data = $this->bookingModel->getRevenueChartData($period, $date_from, $date_to);

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
