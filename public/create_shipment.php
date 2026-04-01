<?php
require_once __DIR__ . '/../src/auth.php';
requireLogin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/shipment_helpers.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderName = trim($_POST['sender_name'] ?? '');
    $receiverName = trim($_POST['receiver_name'] ?? '');
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');

    if ($senderName === '' || $receiverName === '' || $origin === '' || $destination === '') {
        $error = 'All fields are required.';
    } else {
        $trackingNumber = generateTrackingNumber();

        try {
            $pdo->beginTransaction();

            $shipmentSql = "
                INSERT INTO shipments (
                    tracking_number,
                    sender_name,
                    receiver_name,
                    origin,
                    destination,
                    current_status,
                    created_by
                )
                VALUES (
                    :tracking_number,
                    :sender_name,
                    :receiver_name,
                    :origin,
                    :destination,
                    :current_status,
                    :created_by
                )
                RETURNING id
            ";

            $shipmentStmt = $pdo->prepare($shipmentSql);
            $shipmentStmt->execute([
                'tracking_number' => $trackingNumber,
                'sender_name' => $senderName,
                'receiver_name' => $receiverName,
                'origin' => $origin,
                'destination' => $destination,
                'current_status' => 'Pending',
                'created_by' => $_SESSION['user_id'],
            ]);

            $shipmentId = $shipmentStmt->fetchColumn();

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
                'shipment_id' => $shipmentId,
                'status' => 'Pending',
                'note' => 'Shipment created',
                'updated_by' => $_SESSION['user_id'],
            ]);

            $pdo->commit();
            $message = 'Shipment created successfully. Tracking Number: ' . $trackingNumber;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Failed to create shipment.';
        }
    }
}

require_once __DIR__ . '/../views/partials/header.php';
?>

<h1>Create Shipment</h1>

<?php if ($message): ?>
    <div class="alert"><?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="sender_name" placeholder="Sender Name" required>
    <input type="text" name="receiver_name" placeholder="Receiver Name" required>
    <input type="text" name="origin" placeholder="Origin" required>
    <input type="text" name="destination" placeholder="Destination" required>
    <button type="submit">Create Shipment</button>
</form>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>