<?php
include 'header.php';
include 'db_connect.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$services = [];
$total_price = 0;

if (!empty($cart_items)) {
    // Create placeholders for the IN clause to prevent SQL injection
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
    $types = str_repeat('i', count($cart_items));
    
    $sql = "SELECT id, name, price, image_path FROM services WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$cart_items);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        $services[] = $row;
        $total_price += $row['price'];
    }
    $stmt->close();
}
$conn->close();
?>

<main>
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title">Your Shopping Cart</h1>
        </div>
    </section>
    
    <section class="page-content-section">
        <div class="container">
            <?php 
                if (isset($_SESSION['message'])) {
                    echo '<p class="message ' . $_SESSION['message_type'] . '">' . htmlspecialchars($_SESSION['message']) . '</p>';
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
            ?>
            <?php if (empty($services)): ?>
                <div class="cart-empty">
                    <p>Your cart is currently empty.</p>
                    <a href="services.php" class="btn-primary">Browse Services</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <div class="cart-items">
                        <?php foreach ($services as $service): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($service['image_path']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" class="cart-item-image">
                            <div class="cart-item-details">
                                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                                <span class="cart-item-price">₹<?php echo number_format($service['price']); ?></span>
                            </div>
                            <form action="cart-logic.php" method="POST">
                                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="cart-item-remove" aria-label="Remove item">&times;</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span>₹<?php echo number_format($total_price); ?></span>
                        </div>
                        <div class="summary-line total">
                            <span>Total</span>
                            <span>₹<?php echo number_format($total_price); ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="checkout.php" class="btn-primary btn-checkout" style="text-align:center;">Proceed to Checkout</a>
                        <?php else: ?>
                            <a href="login.php" class="btn-primary btn-checkout" style="text-align:center;">Login to Checkout</a>
                            <p style="text-align: center; margin-top: 15px; color: var(--text-light); font-size: 0.9em;">You must be logged in to complete your order.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>