<?php
require_once __DIR__ . '/../src/auth.php';
requireLogin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/shipment_helpers.php';

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

$statuses = validShipmentStatuses();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status'] ?? '');
    $note = trim($_POST['note'] ?? '');

    if ($status === '') {
        $error = 'Status is required.';
    } elseif (!in_array($status, $statuses, true)) {
        $error = 'Invalid status selected.';
    } else {
        try {
            $pdo->beginTransaction();

            $updateSql = "
                UPDATE shipments
                SET current_status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ";

            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                'status' => $status,
                'id' => (int)$id
            ]);

            $historySql = "
                INSERT INTO shipment_status_history (
                    shipment_id,
                    status,
                    note,
                    updated_by
                )
                VALUES (
                    :shipment_id,
                    :status,
                    :note,
                    :updated_by
                )
            ";

            $historyStmt = $pdo->prepare($historySql);
            $historyStmt->execute([
                'shipment_id' => (int)$id,
                'status' => $status,
                'note' => $note === '' ? null : $note,
                'updated_by' => $_SESSION['user_id']
            ]);

            $pdo->commit();

            header('Location: shipment_detail.php?id=' . (int)$id);
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Failed to update shipment status.';
        }
    }
}

require_once __DIR__ . '/../views/partials/header.php';
?>

<h1>Update Shipment Status</h1>

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
                <span class="info-value"><?= e($shipment['created_by_username'] ?? 'sys_user') ?></span>
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
        <h2>Update Status</h2>

        <?php if ($error): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label for="status">New Status</label>
                <select name="status" id="status" required>
                    <option value="">Select status</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= e($status) ?>" <?= $shipment['current_status'] === $status ? 'selected' : '' ?>>
                            <?= e($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="note">Note</label>
                <textarea name="note" id="note" placeholder="Add an optional update note"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit">Save Status Update</button>
                <a href="shipment_detail.php?id=<?= (int)$shipment['id'] ?>" class="secondary-button">Cancel</a>
            </div>
        </form>
    </section>
</div>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>