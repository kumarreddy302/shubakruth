<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { die("Access Denied. Please <a href='login.php'>login</a> as an admin."); } 
include 'header.php';
include 'db_connect.php';
?>

<main class="content-area page-content">
    <div class="container">
        <div class="dashboard-header">
            <h1 class="page-title">Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Manage your site's content and operations.</p>
        </div>

        <?php 
            if (isset($_SESSION['message'])) {
                echo '<p class="message ' . $_SESSION['message_type'] . '">' . htmlspecialchars($_SESSION['message']) . '</p>';
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
        ?>
        
        <div class="admin-tabs">
            <button class="tab-link" data-tab="orders"><i class="fas fa-receipt"></i> Orders</button>
            <button class="tab-link" data-tab="services"><i class="fas fa-concierge-bell"></i> Services</button>
            <button class="tab-link" data-tab="blog"><i class="fas fa-blog"></i> Blog</button>
            <button class="tab-link" data-tab="messages"><i class="fas fa-envelope-open-text"></i> Messages</button>
            <button class="tab-link" data-tab="team"><i class="fas fa-users"></i> Team</button>
        </div>

        <div id="orders" class="tab-content">
            <div class="admin-card">
                <h2>All Customer Orders</h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php $result = $conn->query("SELECT * FROM orders ORDER BY ordered_at DESC"); if ($result->num_rows > 0) { while($order = $result->fetch_assoc()) { ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo date("d M Y", strtotime($order['ordered_at'])); ?></td>
                                <td>₹<?php echo number_format($order['total_amount']); ?></td>
                                <td><span class="status-badge status-<?php echo strtolower(htmlspecialchars($order['order_status'])); ?>"><?php echo htmlspecialchars($order['order_status']); ?></span></td>
                                <td>
                                    <form action="admin-logic.php" method="POST" class="status-form">
                                        <input type="hidden" name="action" value="update_order_status"><input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status"><option value="Placed" <?php if($order['order_status'] == 'Placed') echo 'selected'; ?>>Placed</option><option value="Processing" <?php if($order['order_status'] == 'Processing') echo 'selected'; ?>>Processing</option><option value="Completed" <?php if($order['order_status'] == 'Completed') echo 'selected'; ?>>Completed</option><option value="Cancelled" <?php if($order['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option></select>
                                        <button type="submit" class="btn-update">Update</button>
                                    </form>
                                </td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='6'>No orders found.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="services" class="tab-content">
            <div class="admin-card">
                <h2>Add New Service</h2>
                <form class="admin-form" action="admin-logic.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_service">
                    <div class="form-grid">
                        <div class="form-group"><label>Service Name</label><input type="text" name="name" required></div>
                        <div class="form-group"><label>Price (₹)</label><input type="number" step="0.01" name="price" required></div>
                        <div class="form-group"><label>Turnaround Time</label><input type="text" name="tat" required></div>
                        <div class="form-group"><label>Category</label><select name="category" required><option>Personalized Medicine</option><option>Preventive</option><option>Clinical Specific</option></select></div>
                        <div class="form-group full-width"><label>Description</label><textarea name="description" rows="4" required></textarea></div>
                        <div class="form-group full-width"><label>Service Image</label><input type="file" name="image" accept="image/*"></div>
                    </div>
                    <button type="submit" class="btn-primary">Add Service</button>
                </form>
            </div>
            <div class="admin-card">
                <h2>Existing Services</h2>
                <div class="admin-table-wrapper">
                     <table class="admin-table">
                        <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php $result = $conn->query("SELECT id, name, price, category FROM services ORDER BY id DESC"); if ($result->num_rows > 0) { while($service = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $service['id']; ?></td><td><?php echo htmlspecialchars($service['name']); ?></td><td>₹<?php echo number_format($service['price']); ?></td><td><?php echo htmlspecialchars($service['category']); ?></td>
                                <td class="action-buttons">
                                    <a href="edit-service.php?id=<?php echo $service['id']; ?>" class="btn-edit">Edit</a>
                                    <form action="admin-logic.php" method="POST" onsubmit="return confirm('Delete this service?');"><input type="hidden" name="action" value="delete_service"><input type="hidden" name="service_id" value="<?php echo $service['id']; ?>"><button type="submit" class="btn-delete">Delete</button></form>
                                </td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='5'>No services found.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div id="blog" class="tab-content">
            <div class="admin-card">
                <h2>Add New Blog Post</h2>
                <form class="admin-form" action="admin-logic.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_post">
                    <div class="form-grid">
                        <div class="form-group full-width"><label>Post Title</label><input type="text" name="title" required></div>
                        <div class="form-group"><label>Category</label>
                            <select name="category_id" required>
                                <?php $cat_result = $conn->query("SELECT * FROM blog_categories ORDER BY name"); while($cat = $cat_result->fetch_assoc()) { echo "<option value='{$cat['id']}'>" . htmlspecialchars($cat['name']) . "</option>"; } ?>
                            </select>
                        </div>
                        <div class="form-group"><label>Featured Image</label><input type="file" name="image" accept="image/*"></div>
                        <div class="form-group full-width"><label>Content</label><textarea name="content" rows="10" required></textarea></div>
                    </div>
                    <button type="submit" class="btn-primary">Publish Post</button>
                </form>
            </div>
            <div class="admin-card">
                <h2>Existing Blog Posts</h2>
                <div class="admin-table-wrapper">
                     <table class="admin-table">
                        <thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Author</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php $sql = "SELECT bp.id, bp.title, bc.name as cat_name, u.full_name as author FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id LEFT JOIN users u ON bp.author_id = u.id ORDER BY bp.id DESC";
                            $result = $conn->query($sql); if ($result->num_rows > 0) { while($post = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $post['id']; ?></td><td><?php echo htmlspecialchars($post['title']); ?></td><td><?php echo htmlspecialchars($post['cat_name']); ?></td><td><?php echo htmlspecialchars($post['author']); ?></td>
                                <td class="action-buttons">
                                    <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn-edit">Edit</a>
                                    <form action="admin-logic.php" method="POST" onsubmit="return confirm('Delete this post?');"><input type="hidden" name="action" value="delete_post"><input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"><button type="submit" class="btn-delete">Delete</button></form>
                                </td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='5'>No blog posts found.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div id="messages" class="tab-content">
            <div class="admin-card">
                <h2>Contact Form Messages</h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>From</th><th>Email</th><th>Subject</th><th>Received</th><th>Message</th></tr></thead>
                        <tbody>
                            <?php $sql = "SELECT * FROM contact_messages ORDER BY received_at DESC"; $result = $conn->query($sql); if ($result->num_rows > 0) { while($msg = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                <td><?php echo date("d M Y, h:i A", strtotime($msg['received_at'])); ?></td>
                                <td><button class="btn-view-message" data-message="<?php echo htmlspecialchars($msg['message']); ?>">View</button></td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='5'>No messages found.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="team" class="tab-content">
            <div class="admin-card">
                <h2>Add New Team Member</h2>
                <form class="admin-form" action="admin-logic.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_team_member">
                    <div class="form-grid">
                        <div class="form-group"><label>Full Name</label><input type="text" name="name" required></div>
                        <div class="form-group"><label>Title / Designation</label><input type="text" name="title" required></div>
                        <div class="form-group full-width"><label>Short Bio</label><textarea name="bio" rows="5"></textarea></div>
                        <div class="form-group full-width"><label>Profile Image</label><input type="file" name="image" accept="image/*"></div>
                    </div>
                    <button type="submit" class="btn-primary">Add Member</button>
                </form>
            </div>
            <div class="admin-card">
                <h2>Existing Team Members</h2>
                <div class="admin-table-wrapper">
                     <table class="admin-table">
                        <thead><tr><th>ID</th><th>Name</th><th>Title</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php $sql = "SELECT id, name, title FROM team_members ORDER BY id DESC"; $result = $conn->query($sql); if ($result->num_rows > 0) { while($member = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $member['id']; ?></td><td><?php echo htmlspecialchars($member['name']); ?></td><td><?php echo htmlspecialchars($member['title']); ?></td>
                                <td class="action-buttons">
                                    <form action="admin-logic.php" method="POST" onsubmit="return confirm('Delete this member?');"><input type="hidden" name="action" value="delete_team_member"><input type="hidden" name="member_id" value="<?php echo $member['id']; ?>"><button type="submit" class="btn-delete">Delete</button></form>
                                </td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='4'>No team members found.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>