<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Laenutuse ID puudub']);
    exit;
}

$laenutus_id = intval($_GET['id']);
$kasutaja_id = $_SESSION['user_id'];

$conn = connectDB();

// Kontrolli, kas laenutus kuulub kasutajale või kasutaja on admin
if (isAdmin()) {
    $sql = "UPDATE laenutused SET tagastatud = TRUE, tagastamise_kuupaev = CURDATE() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $laenutus_id);
} else {
    $sql = "UPDATE laenutused SET tagastatud = TRUE, tagastamise_kuupaev = CURDATE() 
            WHERE id = ? AND kasutaja_id = ? AND tagastatud = FALSE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $laenutus_id, $kasutaja_id);
}

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Laenutust ei leitud või see on juba tagastatud']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Andmebaasi viga: ' . $conn->error]);
}

$conn->close();
?>