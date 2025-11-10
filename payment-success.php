<?php
include 'header.php';
include 'db_connect.php';

// Security check: ensure order ID is provided and user is logged in
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0 || !isset($_SESSION['user_id'])) { 
    die("Invalid access. Please go to your dashboard to see order history."); 
}

// Fetch the order details, ensuring it belongs to the logged-in user
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) { 
    die("Order not found or you do not have permission to view it."); 
}
?>

<main class="content-area page-content">
    <div class="container" style="text-align: center;">
        <div class="success-icon"><i class="fas fa-check-circle"></i></div>
        <h1 class="page-title">Payment Successful!</h1>
        <p class="page-intro">
            Thank you for your order, <?php echo htmlspecialchars($order['full_name']); ?>. Your order has been placed successfully.
        </p>
        <div class="order-summary-box">
            <h3>Order Summary</h3>
            <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount']); ?></p>
            <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($order['razorpay_payment_id']); ?></p>
            <p><strong>Date:</strong> <?php echo date("d M Y, h:i A", strtotime($order['ordered_at'])); ?></p>
        </div>
        <a href="user_dashboard.php" class="btn-primary" style="margin-top:30px;">View Order History</a>
    </div>
</main>

<?php include 'footer.php'; ?>