<?php
require_once __DIR__ . '/../src/auth.php';
requireLogin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $sql = "
        SELECT
            s.*,
            u.username AS created_by_username
        FROM shipments s
        LEFT JOIN users u ON s.created_by = u.id
        WHERE s.tracking_number ILIKE :search
        ORDER BY s.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'search' => '%' . $search . '%'
    ]);
    $shipments = $stmt->fetchAll();
} else {
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
}

require_once __DIR__ . '/../views/partials/header.php';
?>

<h1>Shipments</h1>

<form method="GET">
    <input
        type="text"
        name="search"
        placeholder="Search tracking number"
        value="<?= e($search) ?>"
    >
    <button type="submit">Search</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Tracking Number</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($shipments)): ?>
            <tr>
                <td colspan="9">No shipments found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($shipments as $shipment): ?>
                <tr>
                    <td><?= e($shipment['tracking_number']) ?></td>
                    <td><?= e($shipment['sender_name']) ?></td>
                    <td><?= e($shipment['receiver_name']) ?></td>
                    <td><?= e($shipment['origin']) ?></td>
                    <td><?= e($shipment['destination']) ?></td>
                    <td><?= e($shipment['current_status']) ?></td>
                    <td><?= e($shipment['created_by_username'] ?? 'sys_user') ?></td>
                    <td><?= e($shipment['created_at']) ?></td>
                    <td>
                        <a href="shipment_detail.php?id=<?= (int)$shipment['id'] ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>