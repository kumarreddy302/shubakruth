<?php
include 'header.php';
include 'db_connect.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
?>

<main>
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title">Search Results</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> <i class="fas fa-chevron-right"></i>
                <span>Search</span>
            </nav>
        </div>
    </section>

    <section class="page-content-section">
        <div class="container">
            
            <h2 class="section-title" style="text-align:left; margin-bottom: 20px;">
                Showing results for: "<?php echo htmlspecialchars($query); ?>"
            </h2>

            <div class="services-grid" id="services-grid">
                <?php
                if (!empty($query)) {
                    $search_param = "%" . $query . "%";
                    $sql = "SELECT id, name, price, tat, image_path, category FROM services WHERE name LIKE ? OR description LIKE ? ORDER BY name";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $search_param, $search_param);
                    $stmt->execute();
                    $result = $stmt->get_result();

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
                                    <form action="cart-logic.php" method="POST" style="margin:0;"></form>
                                </div>
                            </div>
                <?php
                        }
                    } else {
                        echo "<p>No services found matching your search. Please try a different term.</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p>Please enter a search term.</p>";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>