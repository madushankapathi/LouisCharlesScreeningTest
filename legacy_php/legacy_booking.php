<?php
global $con;
/**
 * Refactored Booking System
 * Secure and maintainable version
 */

include_once 'config.php';

// Sanitize and validate GET parameters
$booking_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$site_id = filter_input(INPUT_GET, 'site_id', FILTER_VALIDATE_INT);

if (!$booking_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid booking ID']);
    exit;
}

if (!$site_id) {
    $site_id = 1; // default
}

// Fetch booking securely using prepared statement
$stmt = $con->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

$customer = null;

if ($booking) {
    // Get site-specific DB config
    $db_config = getSiteDbConfig($site_id);

    if ($db_config) {
        // Connect to site database
        $siteDb = new mysqli(
            $db_config['host'],
            $db_config['username'],
            $db_config['password'],
            $db_config['dbname']
        );

        if ($siteDb->connect_error) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to connect to site database']);
            exit;
        }

        // Fetch customer details securely
        $stmt2 = $siteDb->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt2->bind_param("s", $booking['email']);
        $stmt2->execute();
        $customer = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        // Update booking status in main database
        $updateStmt = $con->prepare("UPDATE bookings SET status='confirmed', updated_at=NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $booking_id);
        $updateStmt->execute();
        $updateStmt->close();

        $siteDb->close();
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'booking' => $booking,
    'customer' => $customer
]);
