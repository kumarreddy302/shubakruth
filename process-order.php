<?php
// Turn on error reporting for debugging.
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');

// Centralized Error Handler
function send_json_error($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit();
}

// Dependency Checks
$autoload_path = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload_path)) { send_json_error("Server Error: Autoloader not found."); }
require $autoload_path;
include 'db_connect.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// Razorpay Keys (Use Test Keys for localhost)
$keyId = 'rzp_test_RUDOZtnhmDWSCV';
$keySecret = 'EBtKpvun4Zd3ITKM3f9NlBRe';

try {
    $api = new Api($keyId, $keySecret);
} catch(Exception $e) {
    send_json_error("Razorpay API Error: " . $e->getMessage());
}

// --- PART 1: CREATE RAZORPAY ORDER ---
$request_body = json_decode(file_get_contents('php://input'), true);
if (isset($request_body['action']) && $request_body['action'] === 'create_order') {
    if (!isset($_SESSION['user_id'])) { send_json_error("You must be logged in.", 403); }
    if (empty($_SESSION['cart'])) { send_json_error("Your cart is empty.", 400); }
    try {
        $cart_items = $_SESSION['cart'];
        $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
        $sql = "SELECT SUM(price) as total FROM services WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('i', count($cart_items)), ...$cart_items);
        $stmt->execute();
        $total_price = $stmt->get_result()->fetch_assoc()['total'];
        if ($total_price <= 0) { send_json_error("Invalid cart total."); }
        $orderData = [ 'receipt' => uniqid(), 'amount' => $total_price * 100, 'currency' => 'INR' ];
        $razorpayOrder = $api->order->create($orderData);
        echo json_encode($razorpayOrder->toArray());
    } catch (Exception $e) {
        send_json_error("Could not create Razorpay order: " . $e->getMessage());
    }
    exit();
}

// --- PART 2: VERIFY PAYMENT & SAVE ORDER ---
if (isset($_POST['action']) && $_POST['action'] === 'verify_payment') {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
        send_json_error("Session expired or cart is empty.", 400);
    }

    try {
        // 1. Verify Signature
        $attributes = ['razorpay_order_id' => $_POST['razorpay_order_id'], 'razorpay_payment_id' => $_POST['razorpay_payment_id'], 'razorpay_signature' => $_POST['razorpay_signature']];
        $api->utility->verifyPaymentSignature($attributes);

        // --- PAYMENT IS VERIFIED ---
        $conn->begin_transaction(); 

        // 2. Get cart details from DB
        $cart_items = $_SESSION['cart'];
        $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
        $sql = "SELECT id, name, price FROM services WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('i', count($cart_items)), ...$cart_items);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_price = 0; $services = [];
        while($row = $result->fetch_assoc()) { $total_price += $row['price']; $services[] = $row; }
        
        // 3. Insert into `orders` table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, address, city, state, zip_code, total_amount, razorpay_payment_id, razorpay_order_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssdss", $_SESSION['user_id'], $_POST['full_name'], $_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip_code'], $total_price, $_POST['razorpay_payment_id'], $_POST['razorpay_order_id']);
        if (!$stmt->execute()) { throw new Exception("Failed to save order details."); }
        $order_id = $stmt->insert_id;
        
        // 4. Insert into `order_items` table
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, service_id, service_name, service_price) VALUES (?, ?, ?, ?)");
        foreach ($services as $service) { 
            $stmt->bind_param("iisd", $order_id, $service['id'], $service['name'], $service['price']);
            if (!$stmt->execute()) { throw new Exception("Failed to save order items."); }
        }
        
        $conn->commit(); 
        
        // 5. Clear ONLY the cart, not the entire session
        // Make sure this line is exactly as written below.
        // DO NOT use session_unset() or session_destroy() here.
        unset($_SESSION['cart']);
        
        echo json_encode(['status' => 'success', 'order_id' => $order_id]);

    } catch(SignatureVerificationError $e) {
        send_json_error('Razorpay Signature Verification Failed: ' . $e->getMessage());
    } catch (Exception $e) {
        $conn->rollback(); 
        send_json_error('An error occurred while saving your order: ' . $e->getMessage());
    }
    exit();
}

send_json_error("Invalid request.", 400);
?>