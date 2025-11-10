<?php
include 'header.php';
include 'db_connect.php';

// Security: Redirect if user is not logged in or cart is empty
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: login.php");
    exit();
}

$cart_items = $_SESSION['cart'];
$total_price = 0;
// Recalculate total from database for security
if (!empty($cart_items)) {
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
    $sql = "SELECT SUM(price) as total FROM services WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($cart_items)), ...$cart_items);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $total_price = $result['total'];
}
?>

<main>
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title">Checkout</h1>
        </div>
    </section>
    
    <section class="page-content-section">
        <div class="container checkout-container">
            <div class="checkout-form">
                <h3>Shipping & Billing Address</h3>
                <form id="checkout-form">
                    <div class="form-group"><label for="full_name">Full Name</label><input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" required></div>
                    <div class="form-group"><label for="address">Street Address</label><input type="text" id="address" name="address" required></div>
                    <div class="form-group"><label for="city">City</label><input type="text" id="city" name="city" required></div>
                    <div class="form-group"><label for="state">State</label><input type="text" id="state" name="state" required></div>
                    <div class="form-group"><label for="zip_code">ZIP Code</label><input type="text" id="zip_code" name="zip_code" required></div>
                </form>
            </div>
            
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-line total">
                    <span>Total Amount</span>
                    <span>₹<?php echo number_format($total_price); ?></span>
                </div>
                <button id="rzp-button" class="btn-primary btn-checkout">Place Order & Pay</button>
            </div>
        </div>
    </section>
</main>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('rzp-button').onclick = async function (e) {
    e.preventDefault();
    const form = document.getElementById('checkout-form');
    if (!form.reportValidity()) { return; }

    const response = await fetch('process-order.php', { method: 'POST', body: JSON.stringify({ action: 'create_order' }) });
    const orderData = await response.json();
    if (!orderData.id) { alert('Could not create order. ' + (orderData.message || 'Please check the console for errors.')); return; }

    var options = {
        "key": "rzp_test_RUDOZtnhmDWSCV", // <-- REPLACE WITH YOUR TEST KEY ID
        "amount": orderData.amount,
        "currency": "INR",
        "name": "Shubhakruth Genetics",
        "order_id": orderData.id,
        "handler": function (response){
            const formData = new FormData(form);
            formData.append('razorpay_payment_id', response.razorpay_payment_id);
            formData.append('razorpay_order_id', response.razorpay_order_id);
            formData.append('razorpay_signature', response.razorpay_signature);
            formData.append('action', 'verify_payment');

            fetch('process-order.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        window.location.href = 'payment-success.php?order_id=' + data.order_id;
                    } else {
                        // This will show the specific error from the backend
                        alert('Payment verification failed: ' + data.message);
                    }
                })
                .catch(error => {
                    // ADDED: This will catch network errors or if the response isn't JSON
                    console.error('Fetch Error:', error);
                    alert('A critical error occurred. Please check the console for details.');
                });
        },
        "prefill": { "name": "<?php echo htmlspecialchars($_SESSION['full_name']); ?>", "email": "<?php echo htmlspecialchars($_SESSION['email']); ?>" },
        "theme": { "color": "#f37021" }
    };
    var rzp1 = new Razorpay(options);
    rzp1.on('payment.failed', function (response){ alert("Payment Failed: " + response.error.description); });
    rzp1.open();
}
</script>

<?php include 'footer.php'; ?>