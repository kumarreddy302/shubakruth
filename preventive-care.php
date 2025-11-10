<?php include 'header.php'; ?>

<main>
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title">Preventive Care</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> <i class="fas fa-chevron-right"></i>
                <span>Preventive Care</span>
            </nav>
        </div>
    </section>

    <section class="page-content-section">
        <div class="container">
            
            <div class="filter-container">
                <div class="filter-group">
                    <input type="search" id="service-search" placeholder="Search for a service...">
                </div>
                
            </div>

            <?php 
                if (isset($_SESSION['message'])) {
                    echo '<p class="message ' . $_SESSION['message_type'] . '">' . htmlspecialchars($_SESSION['message']) . '</p>';
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
            ?>

            <div class="services-grid" id="services-grid">
                <?php
                include 'db_connect.php';
                $sql = "SELECT id, name, price, tat, image_path, category FROM services where category='Preventive' ORDER BY category, name";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                        <div class="service-card" data-name="<?php echo strtolower(htmlspecialchars($row['name'])); ?>" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                            <div class="service-card-image">
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            </div>
                            <div class="service-card-content">
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <div class="service-card-details">
                                    <span class="price">₹<?php echo number_format($row['price']); ?></span>
                                    <span class="tat"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($row['tat']); ?></span>
                                </div>
                            </div>
                            <div class="service-card-actions">
                                <a href="service-detail.php?id=<?php echo $row['id']; ?>" class="btn-secondary">View Details</a>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form action="cart-logic.php" method="POST" style="margin:0;">
                                        <input type="hidden" name="service_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn-primary">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="btn-primary">Login to Add</a>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>No services found.</p>";
                }
                $conn->close();
                ?>
            </div>
             <p id="no-results-message" class="hidden">No services match your criteria.</p>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>