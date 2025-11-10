<?php
include 'header.php';
include 'db_connect.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
    die("Invalid post ID.");
}

// CORRECTED: The JOINs are now LEFT JOINs to ensure the post always shows up
// COALESCE provides a default author name ('Admin') if the original author is deleted
$sql = "SELECT 
            bp.title, 
            bp.content, 
            bp.image_path, 
            bp.created_at, 
            COALESCE(u.full_name, 'Admin') as author_name, 
            COALESCE(bc.name, 'Uncategorized') as category_name 
        FROM blog_posts bp
        LEFT JOIN users u ON bp.author_id = u.id
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id
        WHERE bp.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    die("Blog post not found.");
}
?>

<main>
    <section class="page-hero" style="background-image: linear-gradient(45deg, rgba(0, 74, 153, 0.9), rgba(243, 112, 33, 0.9)), url('<?php echo htmlspecialchars($post['image_path']); ?>');">
        <div class="container">
            <span class="post-category-tag"><?php echo htmlspecialchars($post['category_name']); ?></span>
            <h1 class="page-title post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="post-meta">
                <span>By <?php echo htmlspecialchars($post['author_name']); ?></span> |
                <span>Published on <?php echo date("F j, Y", strtotime($post['created_at'])); ?></span>
            </div>
        </div>
    </section>

    <section class="page-content-section">
        <div class="container post-container">
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
            <a href="blog.php" class="btn-secondary" style="margin-top: 40px;"><i class="fas fa-arrow-left"></i> Back to Blog</a>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>