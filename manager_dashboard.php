<?php session_start(); if ($_SESSION['role'] !== 'manager') { die("Access Denied"); } ?>
<h1>Welcome, Manager: <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
<p>This is the Manager Dashboard.</p>
<a href="logout.php">Logout</a>