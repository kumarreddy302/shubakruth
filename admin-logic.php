<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';

// Security: Ensure user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// --- SERVICE MANAGEMENT ---
if ($action === 'add_service') {
    $name = $_POST['name']; $price = $_POST['price']; $tat = $_POST['tat']; $desc = $_POST['description']; $cat = $_POST['category'];
    $image_path = 'images/service-placeholder.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "images/"; $img_name = time().'_'.basename($_FILES["image"]["name"]); $target_file = $target_dir.$img_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) { $image_path = $target_file; }
    }
    $stmt = $conn->prepare("INSERT INTO services (name, price, tat, description, category, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssss", $name, $price, $tat, $desc, $cat, $image_path);
    if($stmt->execute()){ $_SESSION['message'] = "Service added successfully!"; $_SESSION['message_type'] = "success"; } 
    else { $_SESSION['message'] = "Error adding service: ".$stmt->error; $_SESSION['message_type'] = "error"; }
    $stmt->close();
}
if ($action === 'delete_service') {
    $id = $_POST['service_id'];
    // Optional: Get image path before deleting to remove file
    $stmt_img = $conn->prepare("SELECT image_path FROM services WHERE id = ?");
    $stmt_img->bind_param("i", $id); $stmt_img->execute(); $img_res = $stmt_img->get_result(); $img = $img_res->fetch_assoc();
    $stmt_img->close();

    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){ 
        if ($img && $img['image_path'] && $img['image_path'] != 'images/service-placeholder.jpg' && file_exists($img['image_path'])) { unlink($img['image_path']); }
        $_SESSION['message'] = "Service deleted successfully!"; $_SESSION['message_type'] = "success"; 
    } else { $_SESSION['message'] = "Error deleting service: ".$stmt->error; $_SESSION['message_type'] = "error"; }
    $stmt->close();
}

// --- ORDER MANAGEMENT ---
if ($action === 'update_order_status') {
    $id = $_POST['order_id']; $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if($stmt->execute()){ $_SESSION['message'] = "Order status updated successfully!"; $_SESSION['message_type'] = "success"; } 
    else { $_SESSION['message'] = "Error updating order status: ".$stmt->error; $_SESSION['message_type'] = "error"; }
    $stmt->close();
}

// --- BLOG MANAGEMENT ---
if ($action === 'add_post') {
    $title = $_POST['title']; $content = $_POST['content']; $cat_id = $_POST['category_id']; $author_id = $_SESSION['user_id'];
    $image_path = 'images/blog-placeholder.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "images/"; $img_name = time().'_'.basename($_FILES["image"]["name"]); $target_file = $target_dir.$img_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) { $image_path = $target_file; }
    }
    $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, author_id, category_id, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $title, $content, $author_id, $cat_id, $image_path);
    if($stmt->execute()){ $_SESSION['message'] = "Blog post published successfully!"; $_SESSION['message_type'] = "success"; } 
    else { $_SESSION['message'] = "Error publishing post: ".$stmt->error; $_SESSION['message_type'] = "error"; }
    $stmt->close();
}
if ($action === 'update_post') {
    $id = $_POST['post_id']; $title = $_POST['title']; $content = $_POST['content']; $cat_id = $_POST['category_id'];
    $stmt = $conn->prepare("SELECT image_path FROM blog_posts WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute();
    $current_image = $stmt->get_result()->fetch_assoc()['image_path']; $stmt->close();
    $new_image = $current_image;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "images/"; $img_name = time().'_'.basename($_FILES["image"]["name"]); $target_file = $target_dir.$img_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $new_image = $target_file;
            if ($current_image && $current_image != 'images/blog-placeholder.jpg' && file_exists($current_image)) { unlink($current_image); }
        }
    }
    $stmt = $conn->prepare("UPDATE blog_posts SET title=?, content=?, category_id=?, image_path=? WHERE id=?");
    $stmt->bind_param("ssisi", $title, $content, $cat_id, $new_image, $id);
    if($stmt->execute()){ $_SESSION['message'] = "Blog post updated successfully!"; $_SESSION['message_type'] = "success"; } 
    else { $_SESSION['message'] = "Error updating post: ".$stmt->error; $_SESSION['message_type'] = "error"; }
    $stmt->close();
}
if ($action === 'delete_post') {
    $id = $_POST['post_id'];
    $stmt = $conn->prepare("SELECT image_path FROM blog_posts WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute();
    $image = $stmt->get_result()->fetch_assoc()['image_path']; $stmt->close();
    $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($image && $image != 'images/blog-placeholder.jpg' && file_exists($image)) { unlink($image); }
        $_SESSION['message'] = "Blog post deleted successfully!"; $_SESSION['message_type'] = "success";
    } else { $_SESSION['message'] = "Error deleting post: ".$stmt->error; $_SESSION['message_type'] = "error"; }
    $stmt->close();
}

// --- TEAM MANAGEMENT ---
if ($action === 'add_team_member') {
    $name = $_POST['name']; $title = $_POST['title']; $bio = $_POST['bio'];
    $image_path = 'images/team-placeholder.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "images/"; $img_name = time().'_'.basename($_FILES["image"]["name"]); $target_file = $target_dir.$img_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) { $image_path = $target_file; }
    }
    $stmt = $conn->prepare("INSERT INTO team_members (name, title, bio, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $title, $bio, $image_path);
    if($stmt->execute()){ $_SESSION['message'] = "Team member added successfully!"; $_SESSION['message_type'] = "success"; } 
    else { $_SESSION['message'] = "Error adding member: ".$stmt->error; $_SESSION['message_type'] = "error"; }
    $stmt->close();
}
if ($action === 'delete_team_member') {
    $id = $_POST['member_id'];
    $stmt_img = $conn->prepare("SELECT image_path FROM team_members WHERE id = ?"); $stmt_img->bind_param("i", $id); $stmt_img->execute(); $img = $stmt_img->get_result()->fetch_assoc(); $stmt_img->close();
    $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($img && $img['image_path'] && $img['image_path'] != 'images/team-placeholder.jpg' && file_exists($img['image_path'])) { unlink($img['image_path']); }
        $_SESSION['message'] = "Team member deleted successfully!"; $_SESSION['message_type'] = "success";
    } else { $_SESSION['message'] = "Error deleting member: ".$stmt->error; $_SESSION['message_type'] = "error"; }
    $stmt->close();
}

$conn->close(); // Close the connection at the end of the script
header("Location: admin_dashboard.php");
exit();
?>