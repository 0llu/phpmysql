<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raamat_id = intval($_POST['raamat_id']);
    $paevad = intval($_POST['paevad']);
    $kasutaja_id = $_SESSION['user_id'];
    
    // Kontrolli, kas kasutajal on juba hilinenud laenutusi
    $conn = connectDB();
    $sql = "SELECT COUNT(*) AS hilinenud FROM laenutused
            WHERE kasutaja_id = ? AND tagastatud = FALSE AND lopp_kuupaev < CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kasutaja_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['hilinenud'] > 0) {
        $_SESSION['error'] = "Teil on tagastamata raamatuid. Uusi laenutusi ei saa teha.";
        redirect('index.php');
    }
    
    // Kontrolli, kas raamat on saadaval
    $sql = "SELECT eksemplarid FROM raamatud WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $raamat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $raamat = $result->fetch_assoc();
    
    $sql = "SELECT COUNT(*) AS laenutatud FROM laenutused 
            WHERE raamat_id = ? AND tagastatud = FALSE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $raamat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $laenutus = $result->fetch_assoc();
    
    $saadaval = $raamat['eksemplarid'] - $laenutus['laenutatud'];
    
    if ($saadaval <= 0) {
        $_SESSION['error'] = "See raamat pole praegu saadaval.";
        redirect('index.php');
    }
    
    // Loo laenutus
    $algus = date('Y-m-d');
    $lopp = date('Y-m-d', strtotime("+$paevad days"));
    
    $sql = "INSERT INTO laenutused (kasutaja_id, raamat_id, algus_kuupaev, lopp_kuupaev) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $kasutaja_id, $raamat_id, $algus, $lopp);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Raamat edukalt laenutatud! Tagastamise tähtaeg: $lopp";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['error'] = "Laenutamisel tekkis viga: " . $conn->error;
    }
    
    $conn->close();
}

redirect('index.php');
?>