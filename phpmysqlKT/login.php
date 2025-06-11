<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $parool = $_POST['parool'];
    $remember = isset($_POST['remember']);
    
    // Valideerimine
    if (empty($email)) {
        $_SESSION['error'] = "Palun sisestage e-posti aadress";
    } elseif (empty($parool)) {
        $_SESSION['error'] = "Palun sisestage parool";
    } else {
        $conn = connectDB();
        $sql = "SELECT id, eesnimi, perekonnanimi, parool, roll FROM kasutajad WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($parool, $user['parool'])) {
                // Autentimine õnnestus
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['eesnimi'] . ' ' . $user['perekonnanimi'];
                $_SESSION['user_role'] = $user['roll'];
                
                if ($remember) {
                    // "Mäleta mind" küpsis 4 tunniks
                    setcookie('remember_user', $user['id'], time() + 14400, '/');
                }
                
                // Suunake kasutaja algsele lehele või avalehele
                $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
                unset($_SESSION['redirect_url']);
                redirect($redirect_url);
            } else {
                $_SESSION['error'] = "Vale parool";
            }
        } else {
            $_SESSION['error'] = "Kasutajat selle e-posti aadressiga ei leitud";
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logi sisse - Raamatukogu</title>
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
                        <h3>Logi sisse</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <form action="login.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-posti aadress</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="parool" class="form-label">Parool</label>
                                <input type="password" class="form-control" id="parool" name="parool" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Mäleta mind</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Logi sisse</button>
                        </form>
                        
                        <div class="mt-3">
                            <p>Pole veel kontot? <a href="register.php">Registreeri siin</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>