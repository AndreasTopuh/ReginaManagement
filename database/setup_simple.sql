USE `regina_hotel`;

-- Table: roles
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Insert default roles
INSERT INTO `roles` (`role_name`) VALUES 
('Owner'), 
('Admin'), 
('Receptionist');

-- Table: users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` boolean DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB;

-- Insert default users (password: admin123)
INSERT INTO `users` (`name`, `username`, `password`, `role_id`) VALUES 
('Owner Regina Hotel', 'owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Admin Hotel', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('Receptionist 1', 'receptionist', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3);

-- Table: id_types
CREATE TABLE `id_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Insert ID types
INSERT INTO `id_types` (`type_name`) VALUES 
('KTP'), 
('Passport'), 
('SIM');

-- Table: guests
CREATE TABLE `guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `id_type_id` int(11) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_type_id`) REFERENCES `id_types`(`id`)
) ENGINE=InnoDB;

-- Table: room_types
CREATE TABLE `room_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL UNIQUE,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Insert room types
INSERT INTO `room_types` (`type_name`, `price`) VALUES 
('Standard', 500000),
('Superior', 750000),
('Deluxe', 1000000),
('Suite', 1500000);

-- Table: floors
CREATE TABLE `floors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `floor_number` int(11) NOT NULL UNIQUE,
  `total_rooms` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Insert sample floors
INSERT INTO `floors` (`floor_number`, `total_rooms`) VALUES 
(1, 0), 
(2, 0), 
(3, 0);

-- Table: rooms
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_number` varchar(10) NOT NULL UNIQUE,
  `type_id` int(11) NOT NULL,
  `floor_id` int(11) NOT NULL,
  `status` enum('Available','Occupied','OutOfService') DEFAULT 'Available',
  `description` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`type_id`) REFERENCES `room_types`(`id`),
  FOREIGN KEY (`floor_id`) REFERENCES `floors`(`id`)
) ENGINE=InnoDB;

-- Insert sample rooms
INSERT INTO `rooms` (`room_number`, `type_id`, `floor_id`, `status`, `description`, `features`) VALUES
('101', 1, 1, 'Available', 'Kamar Standard lantai 1', 'AC, TV, WiFi'),
('102', 1, 1, 'Available', 'Kamar Standard lantai 1', 'AC, TV, WiFi'),
('103', 2, 1, 'Available', 'Kamar Superior lantai 1', 'AC, TV, WiFi, Mini Bar'),
('201', 2, 2, 'Available', 'Kamar Superior lantai 2', 'AC, TV, WiFi, Mini Bar'),
('202', 3, 2, 'Available', 'Kamar Deluxe lantai 2', 'AC, TV, WiFi, Mini Bar, Balcony'),
('301', 4, 3, 'Available', 'Suite lantai 3', 'AC, TV, WiFi, Mini Bar, Balcony, Living Room');

-- Update floors total_rooms
UPDATE `floors` SET `total_rooms` = (
    SELECT COUNT(*) FROM `rooms` WHERE `floor_id` = `floors`.`id`
);

-- Table: bookings
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(20) NOT NULL UNIQUE,
  `guest_id` int(11) NOT NULL,
  `checkin_date` datetime NOT NULL,
  `checkout_date` datetime NOT NULL,
  `duration_nights` int(11) NOT NULL,
  `meal_plan` enum('NONE','BREAKFAST') DEFAULT 'NONE',
  `status` enum('Pending','CheckedIn','CheckedOut','Canceled') DEFAULT 'Pending',
  `special_request` text DEFAULT NULL,
  `total_room_amount` decimal(12,2) DEFAULT 0,
  `total_service_amount` decimal(12,2) DEFAULT 0,
  `tax_rate` decimal(5,2) DEFAULT 10.00,
  `tax_amount` decimal(12,2) DEFAULT 0.00,
  `service_rate` decimal(5,2) DEFAULT 5.00,
  `service_amount` decimal(12,2) DEFAULT 0.00,
  `grand_total` decimal(12,2) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`guest_id`) REFERENCES `guests`(`id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;

-- Table: booking_rooms
CREATE TABLE `booking_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `rate_per_night` decimal(10,2) NOT NULL,
  `nights` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`)
) ENGINE=InnoDB;
