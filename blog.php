<?php include 'header.php'; ?>
<?php include 'db_connect.php'; ?>

<main>
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title">Our Blog</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> <i class="fas fa-chevron-right"></i>
                <span>Blog</span>
            </nav>
        </div>
    </section>

    <section class="page-content-section">
        <div class="container blog-container">
            <div class="blog-grid">
                <?php
                // CORRECTED: The JOIN is now a LEFT JOIN to ensure posts always show up.
                $sql = "SELECT 
                            bp.id, 
                            bp.title, 
                            LEFT(bp.content, 150) as excerpt, 
                            bp.image_path, 
                            bp.created_at, 
                            COALESCE(u.full_name, 'Admin') as author_name, 
                            bc.name as category_name 
                        FROM blog_posts bp
                        LEFT JOIN users u ON bp.author_id = u.id
                        LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                        ORDER BY bp.created_at DESC";
                
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($post = $result->fetch_assoc()) {
                ?>
                <a href="blog-post.php?id=<?php echo $post['id']; ?>" class="blog-card">
                    <div class="blog-card-image">
                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                    <div class="blog-card-content">
                        <span class="blog-card-category"><?php echo htmlspecialchars($post['category_name']); ?></span>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo htmlspecialchars($post['excerpt']); ?>...</p>
                        <div class="blog-card-meta">
                            <span>By <?php echo htmlspecialchars($post['author_name']); ?></span>
                            <span><?php echo date("d M Y", strtotime($post['created_at'])); ?></span>
                        </div>
                    </div>
                </a>
                <?php 
                    }
                } else {
                    echo "<p>No blog posts found. An administrator can add posts from the admin panel.</p>";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>