<?php
require_once __DIR__ . '/../src/auth.php';
requireLogin();

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: shipments.php');
    exit;
}

$id = $_POST['id'] ?? '';

if (!ctype_digit($id)) {
    die('Invalid shipment ID.');
}

$shipmentSql = "
    SELECT id
    FROM shipments
    WHERE id = :id
";
$shipmentStmt = $pdo->prepare($shipmentSql);
$shipmentStmt->execute([
    'id' => (int)$id
]);
$shipment = $shipmentStmt->fetch();

if (!$shipment) {
    die('Shipment not found.');
}

try {
    $deleteSql = "
        DELETE FROM shipments
        WHERE id = :id
    ";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([
        'id' => (int)$id
    ]);

    header('Location: shipments.php?deleted=1');
    exit;
} catch (Exception $e) {
    die('Failed to delete shipment.');
}