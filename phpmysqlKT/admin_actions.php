<?php
require_once 'config.php';
requireAdmin();

header('Content-Type: application/json');

if (!isset($_GET['action'])) {
    $_SESSION['error'] = 'Tegevus puudub';
    header('Location: admin.php');
    exit;
}

$action = $_GET['action'];
$conn = connectDB();

try {
    switch ($action) {
        case 'add_book':
            // Valideeri sisendid
            $required = ['pealkiri', 'autor_id', 'isbn', 'eksemplarid'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception('Kõik väljad peavad olema täidetud');
                }
            }
            
            // Lisa uus raamat
            $stmt = $conn->prepare("INSERT INTO raamatud (pealkiri, autor_id, isbn, aasta, eksemplarid, kirjeldus) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sissss", 
                $_POST['pealkiri'],
                $_POST['autor_id'],
                $_POST['isbn'],
                $_POST['aasta'],
                $_POST['eksemplarid'],
                $_POST['kirjeldus']
            );
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Raamat edukalt lisatud!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        case 'update_book':
            if (empty($_POST['id'])) {
                throw new Exception('ID puudub');
            }
            
            $stmt = $conn->prepare("UPDATE raamatud SET pealkiri = ?, isbn = ?, eksemplarid = ? WHERE id = ?");
            $stmt->bind_param("ssii", 
                $_POST['pealkiri'],
                $_POST['isbn'],
                $_POST['eksemplarid'],
                $_POST['id']
            );
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Raamatu andmed edukalt uuendatud!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        case 'delete_book':
            if (!isset($_GET['id'])) {
                throw new Exception('ID puudub');
            }
            
            $id = intval($_GET['id']);
            
            // Kontrolli, kas raamat on laenutuses
            $sql = "SELECT COUNT(*) AS laenutused FROM laenutused WHERE raamat_id = ? AND tagastatud = FALSE";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['laenutused'] > 0) {
                throw new Exception('Raamat on laenutuses, ei saa kustutada');
            }
            
            $sql = "DELETE FROM raamatud WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Raamat edukalt kustutatud!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        case 'add_author':
            if (empty($_POST['eesnimi']) || empty($_POST['perekonnanimi'])) {
                throw new Exception('Ees- ja perekonnanimi on kohustuslikud');
            }
            
            $stmt = $conn->prepare("INSERT INTO autorid (eesnimi, perekonnanimi, synniaeg, riik) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", 
                $_POST['eesnimi'],
                $_POST['perekonnanimi'],
                $_POST['synniaeg'],
                $_POST['riik']
            );
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Autor edukalt lisatud!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        case 'update_author':
            if (empty($_POST['id'])) {
                throw new Exception('ID puudub');
            }
            
            $stmt = $conn->prepare("UPDATE autorid SET eesnimi = ?, perekonnanimi = ?, synniaeg = ?, riik = ? WHERE id = ?");
            $stmt->bind_param("ssssi", 
                $_POST['eesnimi'],
                $_POST['perekonnanimi'],
                $_POST['synniaeg'],
                $_POST['riik'],
                $_POST['id']
            );
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Autori andmed edukalt uuendatud!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        case 'delete_author':
            if (!isset($_GET['id'])) {
                throw new Exception('ID puudub');
            }
            
            $id = intval($_GET['id']);
            
            // Kontrolli, kas autoril on raamatuid
            $sql = "SELECT COUNT(*) AS raamatud FROM raamatud WHERE autor_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['raamatud'] > 0) {
                throw new Exception('Autoril on seotud raamatuid, ei saa kustutada');
            }
            
            $sql = "DELETE FROM autorid WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Autor edukalt kustutatud!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        case 'update_user':
            if (empty($_POST['id'])) {
                throw new Exception('ID puudub');
            }
            
            $stmt = $conn->prepare("UPDATE kasutajad SET eesnimi = ?, perekonnanimi = ?, isikukood = ?, email = ?, roll = ? WHERE id = ?");
            $stmt->bind_param("sssssi", 
                $_POST['eesnimi'],
                $_POST['perekonnanimi'],
                $_POST['isikukood'],
                $_POST['email'],
                $_POST['roll'],
                $_POST['id']
            );
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Kasutaja andmed edukalt uuendatud!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        case 'delete_user':
            if (!isset($_GET['id'])) {
                throw new Exception('ID puudub');
            }
            
            $id = intval($_GET['id']);
            
            // Ära luba admini enda kustutamist
            if ($id == $_SESSION['user_id']) {
                throw new Exception('Ei saa kustutada enda kontot');
            }
            
            // Kontrolli, kas kasutajal on laenutusi
            $sql = "SELECT COUNT(*) AS laenutused FROM laenutused WHERE kasutaja_id = ? AND tagastatud = FALSE";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['laenutused'] > 0) {
                throw new Exception('Kasutajal on aktiivseid laenutusi, ei saa kustutada');
            }
            
            $sql = "DELETE FROM kasutajad WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Kasutaja edukalt kustutatud!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        case 'return_loan':
            if (!isset($_GET['id'])) {
                throw new Exception('ID puudub');
            }
            
            $id = intval($_GET['id']);
            
            $sql = "UPDATE laenutused SET tagastatud = TRUE, tagastamise_kuupaev = CURDATE() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Laenutus märgitud tagastatuks!';
                $_SESSION['message_type'] = 'success';
            } else {
                throw new Exception('Andmebaasi viga: ' . $conn->error);
            }
            break;
            
        default:
            throw new Exception('Tundmatu tegevus');
    }
    
    // Kui kõik läks hästi, suuna tagasi admin lehele
    header('Location: admin.php');
    exit;
    
} catch (Exception $e) {
    // Kui tekis viga, salvesta see sessiooni ja suuna tagasi
    $_SESSION['error'] = $e->getMessage();
    header('Location: admin.php');
    exit;
} finally {
    $conn->close();
}
?>