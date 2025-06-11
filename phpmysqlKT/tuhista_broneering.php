<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Broneeringu ID puudub']);
    exit;
}

$broneering_id = intval($_GET['id']);
$kasutaja_id = $_SESSION['user_id'];

$conn = connectDB();

// Kontrolli, kas broneering kuulub kasutajale ja on veel ootel
$sql = "UPDATE broneeringud SET staatus = 'tühistatud' 
        WHERE id = ? AND kasutaja_id = ? AND staatus = 'ootel'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $broneering_id, $kasutaja_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Broneeringut ei leitud või seda ei saa tühistada']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Andmebaasi viga: ' . $conn->error]);
}

$conn->close();
?>