<?php
class DashboardController {
    private $bookingModel;
    private $roomModel;
    
    public function __construct() {
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
    }
    
    public function index() {
        requireLogin();
        
        // Get date range (default: this week)
        $date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-7 days'));
        $date_to = $_GET['date_to'] ?? date('Y-m-d');
        
        // Get statistics
        $booking_stats = $this->bookingModel->getStatistics($date_from, $date_to);
        $room_stats = $this->roomModel->getStatistics();
        
        // Get recent bookings
        $recent_bookings = $this->bookingModel->getRecentBookings(10);
        
        // Calculate occupancy rate
        $occupancy_rate = $room_stats['total_rooms'] > 0 ? 
            ($room_stats['occupied_rooms'] / $room_stats['total_rooms']) * 100 : 0;
        
        include APP_PATH . '/views/dashboard/index.php';
    }
}
