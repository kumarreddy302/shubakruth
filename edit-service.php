<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header.php';
include 'db_connect.php';

// Security: Ensure user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied. You do not have permission to view this page.");
}

$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($service_id <= 0) { die("Invalid Service ID."); }

// Fetch the service details
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$service = $stmt->get_result()->fetch_assoc();
if (!$service) { die("Service not found."); }
$stmt->close();
$conn->close();
?>

<main class="content-area page-content">
    <div class="container">
        <h1 class="page-title">Edit Service</h1>
        <p class="page-intro">Editing details for: <strong><?php echo htmlspecialchars($service['name']); ?></strong></p>

        <div class="admin-card">
            <form class="admin-form" action="admin-logic-edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_service">
                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Service Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (₹)</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($service['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tat">Turnaround Time</label>
                        <input type="text" id="tat" name="tat" value="<?php echo htmlspecialchars($service['tat']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="Personalized Medicine" <?php if($service['category'] == 'Personalized Medicine') echo 'selected'; ?>>Personalized Medicine</option>
                            <option value="Preventive" <?php if($service['category'] == 'Preventive') echo 'selected'; ?>>Preventive</option>
                            <option value="Clinical Specific" <?php if($service['category'] == 'Clinical Specific') echo 'selected'; ?>>Clinical Specific</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="image">Change Service Image (Optional)</label>
                        <div class="image-preview-wrapper">
                            <img src="<?php echo htmlspecialchars($service['image_path']); ?>" alt="Current Image" class="current-image-preview">
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        <small>Current Image: <?php echo basename($service['image_path']); ?>. Upload a new file to replace it.</small>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="admin_dashboard.php" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Update Service</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>