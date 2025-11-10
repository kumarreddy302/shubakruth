<?php
include 'header.php';
include 'db_connect.php';

$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($service_id <= 0) { die("Invalid service ID."); }

$stmt = $conn->prepare("SELECT name, price, tat, description, image_path, category FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $service = $result->fetch_assoc();
} else {
    die("Service not found.");
}
$stmt->close();
$conn->close();
?>

<main>
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title"><?php echo htmlspecialchars($service['name']); ?></h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> <i class="fas fa-chevron-right"></i>
                <a href="services.php">Services</a> <i class="fas fa-chevron-right"></i>
                <span><?php echo htmlspecialchars($service['name']); ?></span>
            </nav>
        </div>
    </section>

    <section class="page-content-section">
        <div class="container service-detail-container">
            <div class="service-detail-image">
                <img src="<?php echo htmlspecialchars($service['image_path']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
            </div>
            <div class="service-detail-content">
                <h2>Service Details</h2>
                <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                <div class="service-meta">
                    <div class="meta-item price">
                        <strong>Price:</strong>
                        <span>₹<?php echo number_format($service['price']); ?></span>
                    </div>
                    <div class="meta-item tat">
                        <strong>Turnaround Time:</strong>
                        <span><?php echo htmlspecialchars($service['tat']); ?></span>
                    </div>
                    <div class="meta-item category">
                        <strong>Category:</strong>
                        <span><?php echo htmlspecialchars($service['category']); ?></span>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="cart-logic.php" method="POST" style="margin-top: 30px;">
                        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn-primary btn-book-now">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn-primary btn-book-now" style="margin-top: 30px; text-align:center;">Login to Book Service</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>