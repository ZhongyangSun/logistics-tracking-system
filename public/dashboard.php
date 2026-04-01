<?php
require_once __DIR__ . '/../src/auth.php';
requireLogin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';

$totalShipments = $pdo->query('SELECT COUNT(*) FROM shipments')->fetchColumn();

$pendingShipments = $pdo->query("
    SELECT COUNT(*)
    FROM shipments
    WHERE current_status = 'Pending'
")->fetchColumn();

$inTransitShipments = $pdo->query("
    SELECT COUNT(*)
    FROM shipments
    WHERE current_status = 'In Transit'
")->fetchColumn();

$deliveredShipments = $pdo->query("
    SELECT COUNT(*)
    FROM shipments
    WHERE current_status = 'Delivered'
")->fetchColumn();

$recentSql = "
    SELECT
        s.id,
        s.tracking_number,
        s.sender_name,
        s.receiver_name,
        s.origin,
        s.destination,
        s.current_status,
        s.created_at,
        u.username AS created_by_username
    FROM shipments s
    LEFT JOIN users u ON s.created_by = u.id
    ORDER BY s.created_at DESC, s.id DESC
    LIMIT 5
";
$recentStmt = $pdo->query($recentSql);
$recentShipments = $recentStmt->fetchAll();

require_once __DIR__ . '/../views/partials/header.php';
?>

<h1>Dashboard</h1>

<p>Welcome, <strong><?= e($_SESSION['user_name']) ?></strong>!</p>

<div class="stats-grid">
    <div class="stat-card">
        <h2>Total Shipments</h2>
        <p><?= e((string)$totalShipments) ?></p>
    </div>

    <div class="stat-card">
        <h2>Pending</h2>
        <p><?= e((string)$pendingShipments) ?></p>
    </div>

    <div class="stat-card">
        <h2>In Transit</h2>
        <p><?= e((string)$inTransitShipments) ?></p>
    </div>

    <div class="stat-card">
        <h2>Delivered</h2>
        <p><?= e((string)$deliveredShipments) ?></p>
    </div>
</div>

<h2>Recent Shipments</h2>

<?php
$shipments = $recentShipments;
$showActions = false;
$emptyMessage = 'No recent shipments found.';
require __DIR__ . '/../views/partials/shipments_table.php';
?>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>