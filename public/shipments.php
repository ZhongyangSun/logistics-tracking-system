<?php
require_once __DIR__ . '/../src/auth.php';
requireLogin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';

$sql = "
    SELECT
        s.*,
        u.username AS created_by_username
    FROM shipments s
    LEFT JOIN users u ON s.created_by = u.id
    ORDER BY s.created_at DESC
";

$stmt = $pdo->query($sql);
$shipments = $stmt->fetchAll();

require_once __DIR__ . '/../views/partials/header.php';
?>

<h1>Shipments</h1>

<?php if (isset($_GET['deleted']) && $_GET['deleted'] === '1'): ?>
    <div class="alert">Shipment deleted successfully.</div>
<?php endif; ?>

<div class="search-bar">
    <input
        type="text"
        id="shipmentSearch"
        placeholder="Search tracking number"
        autocomplete="off"
    >
</div>

<?php
$showActions = true;
$emptyMessage = 'No shipments found.';
require __DIR__ . '/../views/partials/shipments_table.php';
?>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>