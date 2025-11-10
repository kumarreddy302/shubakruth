<?php 
session_start(); 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') { die("Access Denied"); } 
include 'header.php';
include 'db_connect.php';
?>
<main class="content-area page-content">
    <div class="container">
        <h1 class="page-title">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
        <p>This is your user dashboard.</p>
        
        <h2>My Previous Orders</h2>
        <div class="order-history">
            <?php
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY ordered_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while($order = $result->fetch_assoc()) {
                    echo "<div class='order-item'>";
                    echo "<h4>Order #" . $order['id'] . " - " . date("d M Y", strtotime($order['ordered_at'])) . "</h4>";
                    echo "<p><strong>Total:</strong> ₹" . number_format($order['total_amount']) . "</p>";
                    echo "<p><strong>Status:</strong> " . htmlspecialchars($order['order_status']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>You have no previous orders.</p>";
            }
            $stmt->close();
            ?>
        </div>
        <a href="logout.php">Logout</a>
    </div>
</main>
<?php include 'footer.php'; ?>