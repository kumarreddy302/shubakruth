<?php
/**
 * popup_submit.php
 * Handles popup form submission via AJAX.
 * Inserts into contact_message (mobile optional).
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request']); exit;
    }

    $name   = trim($_POST['name']   ?? '');
    $email  = trim($_POST['email']  ?? '');
    $mobile = trim($_POST['mobile'] ?? '');

    if ($name === '' || $email === '') {
        echo json_encode(['success' => false, 'message' => 'Name and Email are required.']); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Enter a valid email address.']); exit;
    }

    // Clean up mobile (optional)
    $mobileParam = null;
    if ($mobile !== '') {
        $digits = preg_replace('/\D+/', '', $mobile);
        if (strlen($digits) < 7 || strlen($digits) > 15) {
            echo json_encode(['success' => false, 'message' => 'Invalid phone number']); exit;
        }
        $mobileParam = $digits;
    }

    // Check if 'mobile' column exists
    $res = $conn->query("SHOW COLUMNS FROM contact_message");
    $cols = [];
    while ($row = $res->fetch_assoc()) { $cols[] = strtolower($row['Field']); }
    $res->free();
    $hasMobile = in_array('mobile', $cols, true);

    if ($hasMobile) {
        $stmt = $conn->prepare("INSERT INTO contact_message (name, email, mobile) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $mobileParam);
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_message (name, email) VALUES (?, ?)");
        $stmt->bind_param('ss', $name, $email);
    }

    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => $ok, 'message' => $ok ? 'Saved successfully' : 'Failed to save']); exit;

} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']); exit;
}
?>
