<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eesnimi = sanitizeInput($_POST['eesnimi']);
    $perekonnanimi = sanitizeInput($_POST['perekonnanimi']);
    $isikukood = sanitizeInput($_POST['isikukood']);
    $email = sanitizeInput($_POST['email']);
    $parool = $_POST['parool'];
    $parool_kordus = $_POST['parool_kordus'];
    
    // Valideerimine
    $errors = [];
    
    if (empty($eesnimi)) {
        $errors[] = "Eesnimi on kohustuslik";
    }
    
    if (empty($perekonnanimi)) {
        $errors[] = "Perekonnanimi on kohustuslik";
    }
    
    if (empty($isikukood)) {
        $errors[] = "Isikukood on kohustuslik";
    } elseif (!preg_match('/^[0-9]{11}$/', $isikukood)) {
        $errors[] = "Isikukood peab koosnema 11 numbrist";
    }
    
    if (empty($email)) {
        $errors[] = "E-posti aadress on kohustuslik";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Palun sisestage korrektne e-posti aadress";
    }
    
    if (empty($parool)) {
        $errors[] = "Parool on kohustuslik";
    } elseif (strlen($parool) < 8) {
        $errors[] = "Parool peab olema vähemalt 8 tähemärki pikk";
    } elseif ($parool !== $parool_kordus) {
        $errors[] = "Paroolid ei kattu";
    }
    
    if (empty($errors)) {
        $conn = connectDB();
        
        // Kontrolli, kas e-posti aadress või isikukood on juba kasutusel
        $sql = "SELECT id FROM kasutajad WHERE email = ? OR isikukood = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $isikukood);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Selle e-posti aadressi või isikukoodiga kasutaja on juba olemas";
        } else {
            // Loo uus kasutaja
            $hashed_password = password_hash($parool, PASSWORD_DEFAULT);
            $sql = "INSERT INTO kasutajad (eesnimi, perekonnanimi, isikukood, email, parool) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $eesnimi, $perekonnanimi, $isikukood, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Registreerimine õnnestus! Palun logige sisse.";
                $_SESSION['message_type'] = "success";
                redirect('login.php');
            } else {
                $errors[] = "Registreerimisel tekkis viga: " . $conn->error;
            }
        }
        $conn->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreeri - Raamatukogu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Loo uus konto</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <form action="register.php" method="post" id="registerForm">
                            <div class="mb-3">
                                <label for="eesnimi" class="form-label">Eesnimi</label>
                                <input type="text" class="form-control" id="eesnimi" name="eesnimi" required>
                            </div>
                            <div class="mb-3">
                                <label for="perekonnanimi" class="form-label">Perekonnanimi</label>
                                <input type="text" class="form-control" id="perekonnanimi" name="perekonnanimi" required>
                            </div>
                            <div class="mb-3">
                                <label for="isikukood" class="form-label">Isikukood</label>
                                <input type="text" class="form-control" id="isikukood" name="isikukood" required
                                       pattern="[0-9]{11}" title="Isikukood peab koosnema 11 numbrist">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-posti aadress</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="parool" class="form-label">Parool (vähemalt 8 tähemärki)</label>
                                <input type="password" class="form-control" id="parool" name="parool" minlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label for="parool_kordus" class="form-label">Korda parooli</label>
                                <input type="password" class="form-control" id="parool_kordus" name="parool_kordus" minlength="8" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Registreeri</button>
                        </form>
                        
                        <div class="mt-3">
                            <p>Juba konto olemas? <a href="login.php">Logi sisse</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Kliendipoolne valideerimine
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            let parool = document.getElementById('parool').value;
            let paroolKordus = document.getElementById('parool_kordus').value;
            
            if (parool !== paroolKordus) {
                alert('Paroolid ei kattu!');
                event.preventDefault();
            }
        });
    </script>
</body>
</html>