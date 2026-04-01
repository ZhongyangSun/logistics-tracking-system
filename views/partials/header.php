<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../src/helpers.php';

$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = $_SESSION['user_name'] ?? '';
$currentRole = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics Tracking System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <nav class="nav">
        <div class="nav-left">
            <a href="<?= $isLoggedIn ? 'dashboard.php' : 'login.php' ?>" class="brand">
                Logistics Tracking System
            </a>

            <?php if ($isLoggedIn): ?>
                <div class="nav-links">

                    <a href="shipments.php">Shipments</a>
                    <a href="create_shipment.php">Create Shipment</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="nav-right">
            <?php if ($isLoggedIn): ?>
                <span class="user-badge">
                    <?= e($currentUser) ?>
                    <span class="user-role">(<?= e($currentRole) ?>)</span>
                </span>
                <a href="logout.php" class="logout-link">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="page-wrap">
    <div class="container">