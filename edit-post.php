<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header.php';
include 'db_connect.php';

// Security: Ensure user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied. You do not have permission to view this page.");
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) { die("Invalid Post ID."); }

// Fetch the post details
$stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
if (!$post) { die("Blog post not found."); }
$stmt->close();
?>

<main class="content-area page-content">
    <div class="container">
        <h1 class="page-title">Edit Blog Post</h1>
        <p class="page-intro">Editing post: <strong><?php echo htmlspecialchars($post['title']); ?></strong></p>

        <div class="admin-card">
            <form class="admin-form" action="admin-logic.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_post">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="title">Post Title</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <?php
                            $cat_result = $conn->query("SELECT * FROM blog_categories ORDER BY name");
                            while($cat = $cat_result->fetch_assoc()) {
                                $selected = ($post['category_id'] == $cat['id']) ? 'selected' : '';
                                echo "<option value='{$cat['id']}' {$selected}>" . htmlspecialchars($cat['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Change Featured Image (Optional)</label>
                        <div class="image-preview-wrapper">
                            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Current Image" class="current-image-preview">
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" rows="12" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="admin_dashboard.php" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Update Post</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>