<?php
require_once __DIR__ . '/../src/auth.php';
requireLogin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';

$id = $_GET['id'] ?? '';

if (!ctype_digit($id)) {
    die('Invalid shipment ID.');
}

$shipmentSql = "
    SELECT
        s.*,
        u.username AS created_by_username
    FROM shipments s
    LEFT JOIN users u ON s.created_by = u.id
    WHERE s.id = :id
";

$shipmentStmt = $pdo->prepare($shipmentSql);
$shipmentStmt->execute([
    'id' => (int)$id
]);
$shipment = $shipmentStmt->fetch();

if (!$shipment) {
    die('Shipment not found.');
}

$historySql = "
    SELECT
        h.id,
        h.status,
        h.note,
        h.created_at,
        u.username AS updated_by_username
    FROM shipment_status_history h
    LEFT JOIN users u ON h.updated_by = u.id
    WHERE h.shipment_id = :shipment_id
    ORDER BY h.created_at DESC, h.id DESC
";

$historyStmt = $pdo->prepare($historySql);
$historyStmt->execute([
    'shipment_id' => (int)$id
]);
$historyRows = $historyStmt->fetchAll();

require_once __DIR__ . '/../views/partials/header.php';
?>

<h1>Shipment Detail</h1>

<div class="details-grid">
    <section class="card">
        <h2>Shipment Information</h2>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Tracking Number</span>
                <span class="info-value"><?= e($shipment['tracking_number']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Sender</span>
                <span class="info-value"><?= e($shipment['sender_name']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Receiver</span>
                <span class="info-value"><?= e($shipment['receiver_name']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Origin</span>
                <span class="info-value"><?= e($shipment['origin']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Destination</span>
                <span class="info-value"><?= e($shipment['destination']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Current Status</span>
                <span class="status-badge"><?= e($shipment['current_status']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Created By</span>
                <span class="info-value"><?= e($shipment['created_by_username'] ?? 'Unknown') ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Created At</span>
                <span class="info-value"><?= e($shipment['created_at']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Updated At</span>
                <span class="info-value"><?= e($shipment['updated_at']) ?></span>
            </div>
        </div>
    </section>

    <section class="card">
        <h2>Quick Actions</h2>

        <div class="action-stack">
            
            <a href="update_status.php?id=<?= (int)$shipment['id'] ?>" class="primary-button">
                Update Status
            </a>
            <div class="divider"></div>
            <div class="danger-zone">

                <form method="POST" action="delete_shipment.php"
                    onsubmit="return confirm('Are you sure you want to delete this shipment?');">

                    <input type="hidden" name="id" value="<?= (int)$shipment['id'] ?>">

                    <button type="submit" class="danger-button">
                        Delete Shipment
                    </button>
                </form>
            </div>
        </div>

        <p class="muted">
            Use Update Status to keep shipment history consistent.
        </p>
    </section>
</div>

<section class="card section-spacing">
    <h2>Status History</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Note</th>
                <th>Updated By</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($historyRows)): ?>
                <tr>
                    <td colspan="4">No status history found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($historyRows as $row): ?>
                    <tr>
                        <td><?= e($row['status']) ?></td>
                        <td><?= e($row['note']) ?></td>
                        <td><?= e($row['updated_by_username'] ?? 'sys_user') ?></td>
                        <td><?= e($row['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>