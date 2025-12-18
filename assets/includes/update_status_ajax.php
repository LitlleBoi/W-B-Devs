<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit();
}

include 'assets/includes/connectie.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database fout']);
    exit();
}

$panorama_id = isset($_GET['panorama_id']) ? (int) $_GET['panorama_id'] : 0;
$item_id = isset($_GET['item_id']) ? (int) $_GET['item_id'] : 0;
$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';

if ($panorama_id <= 0 || $item_id <= 0 || empty($type) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Ongeldige parameters']);
    exit();
}

if (!in_array($status, ['concept', 'gepubliceerd', 'gearchiveerd'])) {
    echo json_encode(['success' => false, 'message' => 'Ongeldige status']);
    exit();
}

if ($type === 'punt') {
    $stmt = $conn->prepare("UPDATE punten SET status = ? WHERE id = ? AND panorama_id = ?");
    $stmt->bind_param("sii", $status, $item_id, $panorama_id);
} elseif ($type === 'bron') {
    $stmt = $conn->prepare("UPDATE bronnen SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $item_id);
} else {
    echo json_encode(['success' => false, 'message' => 'Ongeldig type']);
    exit();
}

if ($stmt && $stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status bijgewerkt naar ' . $status]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update fout: ' . $conn->error]);
}

if ($stmt)
    $stmt->close();
$conn->close();
?>