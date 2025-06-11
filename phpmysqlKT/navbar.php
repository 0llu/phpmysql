<?php
// Navbar fail, mida kasutatakse kõikidel lehtedel
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Raamatukogu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Avaleht</a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="minulaenutused.php">Minu laenutused</a>
                    </li>
                <?php endif; ?>
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Admin paneel</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <span class="nav-link">Tere, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logi välja</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Logi sisse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Registreeri</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>