<?php
session_start();

// Security check: Only logged-in users can manage the cart.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "You must be logged in to manage your cart.";
    $_SESSION['message_type'] = "error";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($service_id > 0) {
        if ($action === 'add') {
            // Add item to cart (if not already there)
            if (!in_array($service_id, $_SESSION['cart'])) {
                $_SESSION['cart'][] = $service_id;
                $_SESSION['message'] = "Service added to cart successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Service is already in your cart.";
                $_SESSION['message_type'] = "error";
            }
        } elseif ($action === 'remove') {
            // Remove item from cart
            if (($key = array_search($service_id, $_SESSION['cart'])) !== false) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['message'] = "Service removed from cart.";
                $_SESSION['message_type'] = "success";
            }
        }
    }
}

// Redirect back to the previous page or a specific page
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'services.php';
header("Location: " . $redirect_url);
exit();
?>