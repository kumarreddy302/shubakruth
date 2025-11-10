<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';

// Security: Ensure user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied. You do not have permission to perform this action.");
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// --- ACTION: UPDATE EXISTING SERVICE ---
if ($action === 'update_service') {
    $service_id = $_POST['service_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $tat = $_POST['tat'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // Fetch the current image path first
    $stmt = $conn->prepare("SELECT image_path FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $current_image_path = $stmt->get_result()->fetch_assoc()['image_path'];
    $stmt->close();
    
    $new_image_path = $current_image_path;

    // Handle Image Upload if a new file is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "images/";
        // Create a unique filename to avoid conflicts
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        
        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $new_image_path = $target_file;
            // Optional: Delete the old image if it's not the placeholder
            if ($current_image_path && $current_image_path != 'images/service-placeholder.jpg' && file_exists($current_image_path)) {
                unlink($current_image_path);
            }
        }
    }

    // Update the database with new information
    $stmt = $conn->prepare("UPDATE services SET name=?, price=?, tat=?, description=?, category=?, image_path=? WHERE id=?");
    $stmt->bind_param("sdssssi", $name, $price, $tat, $description, $category, $new_image_path, $service_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Service updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating service: " . $stmt->error;
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
}

// Redirect back to the admin dashboard
header("Location: admin_dashboard.php");
exit();
?>