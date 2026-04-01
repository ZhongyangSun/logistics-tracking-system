<?php
require_once __DIR__ . '/../../src/helpers.php';

$showActions = $showActions ?? true;
$emptyMessage = $emptyMessage ?? 'No shipments found.';
?>

<table class="table" id="shipmentsTable">
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
            <?php if ($showActions): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody id="shipmentsTableBody">
        <?php if (empty($shipments)): ?>
        <tr id="emptyShipmentsRow">
            <td colspan="<?= $showActions ? '9' : '8' ?>">
                <div class="empty-state">
                    <p>No recent shipments</p>
                    <a href="create_shipment.php" class="primary-link-button small">
                        Create your first shipment
                    </a>
                </div>
            </td>
        </tr>
        <?php else: ?>
            <?php foreach ($shipments as $shipment): ?>
                <tr
                    class="shipment-row"
                    data-tracking="<?= e(strtolower($shipment['tracking_number'])) ?>"
                >
                    <td><?= e($shipment['tracking_number']) ?></td>
                    <td><strong><?= e($shipment['sender_name']) ?></strong></td>
                    <td><?= e($shipment['receiver_name']) ?></td>
                    <td><?= e($shipment['origin']) ?></td>
                    <td><?= e($shipment['destination']) ?></td>
                    <td>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $shipment['current_status'])) ?>">
                            <?= e($shipment['current_status']) ?>
                        </span>
                    </td>
                    <td><?= e($shipment['created_by_username'] ?? 'sys_user ') ?></td>
                    <td>
                        <?= date('Y-m-d', strtotime($shipment['created_at'])) ?><br>
                        <span class="muted"><?= date('H:i', strtotime($shipment['created_at'])) ?></span>
                    </td>

                    <?php if ($showActions): ?>
                        <td>
                            <div class="actions">
                                <a href="shipment_detail.php?id=<?= (int)$shipment['id'] ?>" class="link-button">
                                    View
                                </a>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

            <tr id="noMatchRow" style="display: none;">
                <td colspan="<?= $showActions ? '9' : '8' ?>">No matching shipments found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>