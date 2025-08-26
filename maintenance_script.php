<?php

/**
 * Script untuk mengupdate status kamar menjadi maintenance
 * Gunakan script ini untuk mengupdate status kamar secara cepat
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'app/models/Room.php';

// Function untuk update status kamar
function updateRoomStatus($roomNumber, $status)
{
    $db = Database::getInstance();
    $roomModel = new Room();

    // Valid status values
    $validStatuses = ['Available', 'Occupied', 'OutOfService'];

    if (!in_array($status, $validStatuses)) {
        return ['success' => false, 'message' => 'Status tidak valid. Gunakan: Available, Occupied, atau OutOfService'];
    }

    try {
        // Cari kamar berdasarkan nomor kamar
        $room = $db->fetchOne("SELECT * FROM rooms WHERE room_number = ?", [$roomNumber]);

        if (!$room) {
            return ['success' => false, 'message' => "Kamar nomor {$roomNumber} tidak ditemukan"];
        }

        // Update status
        $result = $roomModel->updateStatus($room['id'], $status);

        if ($result) {
            $statusLabels = [
                'Available' => 'Tersedia',
                'Occupied' => 'Terisi',
                'OutOfService' => 'Maintenance/Out of Service'
            ];

            return [
                'success' => true,
                'message' => "Status kamar {$roomNumber} berhasil diubah menjadi {$statusLabels[$status]}",
                'old_status' => $room['status'],
                'new_status' => $status
            ];
        } else {
            return ['success' => false, 'message' => 'Gagal mengupdate status kamar'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Function untuk mendapatkan info kamar
function getRoomInfo($roomNumber)
{
    $db = Database::getInstance();

    try {
        $room = $db->fetchOne("
            SELECT r.*, rt.type_name, rt.price, f.floor_number 
            FROM rooms r 
            JOIN room_types rt ON r.type_id = rt.id 
            JOIN floors f ON r.floor_id = f.id 
            WHERE r.room_number = ?
        ", [$roomNumber]);

        return $room;
    } catch (Exception $e) {
        return null;
    }
}

// Jika script dijalankan dari command line
if (php_sapi_name() === 'cli') {
    if ($argc < 3) {
        echo "Usage: php maintenance_script.php <room_number> <status>\n";
        echo "Status options: Available, Occupied, OutOfService\n";
        echo "Example: php maintenance_script.php 102 OutOfService\n";
        exit(1);
    }

    $roomNumber = $argv[1];
    $status = $argv[2];

    echo "Mengupdate status kamar {$roomNumber}...\n";

    $result = updateRoomStatus($roomNumber, $status);

    if ($result['success']) {
        echo "✓ " . $result['message'] . "\n";
        if (isset($result['old_status'])) {
            echo "  Status lama: {$result['old_status']}\n";
            echo "  Status baru: {$result['new_status']}\n";
        }
    } else {
        echo "✗ " . $result['message'] . "\n";
        exit(1);
    }
} else {
    // Jika diakses melalui web browser
    echo "<h2>Maintenance Script untuk Regina Hotel</h2>";

    // Contoh penggunaan untuk kamar 102
    echo "<h3>Contoh: Set Kamar 102 untuk Maintenance</h3>";

    // Tampilkan info kamar 102 saat ini
    $roomInfo = getRoomInfo('102');
    if ($roomInfo) {
        echo "<p><strong>Info Kamar 102:</strong></p>";
        echo "<ul>";
        echo "<li>Nomor Kamar: {$roomInfo['room_number']}</li>";
        echo "<li>Tipe: {$roomInfo['type_name']}</li>";
        echo "<li>Lantai: {$roomInfo['floor_number']}</li>";
        echo "<li>Status Saat Ini: <span style='color: " . ($roomInfo['status'] == 'OutOfService' ? 'red' : 'green') . "'>{$roomInfo['status']}</span></li>";
        echo "</ul>";

        // Tombol untuk update status
        if (isset($_POST['update_status'])) {
            $newStatus = $_POST['status'];
            $result = updateRoomStatus('102', $newStatus);

            if ($result['success']) {
                echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
                echo "✓ " . $result['message'];
                echo "</div>";
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
            } else {
                echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
                echo "✗ " . $result['message'];
                echo "</div>";
            }
        }

        echo "<form method='POST'>";
        echo "<h4>Update Status Kamar 102:</h4>";
        echo "<select name='status' required>";
        echo "<option value=''>Pilih Status</option>";
        echo "<option value='Available'" . ($roomInfo['status'] == 'Available' ? ' selected' : '') . ">Available (Tersedia)</option>";
        echo "<option value='Occupied'" . ($roomInfo['status'] == 'Occupied' ? ' selected' : '') . ">Occupied (Terisi)</option>";
        echo "<option value='OutOfService'" . ($roomInfo['status'] == 'OutOfService' ? ' selected' : '') . ">OutOfService (Maintenance)</option>";
        echo "</select>";
        echo "<button type='submit' name='update_status' onclick='return confirm(\"Yakin ingin mengubah status kamar?\")'>Update Status</button>";
        echo "</form>";
    } else {
        echo "<p style='color: red;'>Kamar 102 tidak ditemukan!</p>";
    }

    echo "<h3>Penggunaan via Command Line:</h3>";
    echo "<pre>php maintenance_script.php 102 OutOfService</pre>";
}
