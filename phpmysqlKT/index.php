<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raamatukogu Laenutussüsteem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
        
        <h1>Tere tulemast raamatukogu laenutussüsteemi</h1>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <h3>Raamatud</h3>
                <?php
                $conn = connectDB();
                $sql = "SELECT r.*, a.eesnimi AS autor_eesnimi, a.perekonnanimi AS autor_perenimi 
                        FROM raamatud r 
                        JOIN autorid a ON r.autor_id = a.id 
                        ORDER BY r.pealkiri";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    echo '<div class="list-group">';
                    while ($row = $result->fetch_assoc()) {
                        $saadaval = $row['eksemplarid'] - getLaenutatudKogus($conn, $row['id']);
                        echo '<div class="list-group-item">';
                        echo '<h5>' . htmlspecialchars($row['pealkiri']) . '</h5>';
                        echo '<p>Autor: ' . htmlspecialchars($row['autor_eesnimi'] . ' ' . $row['autor_perenimi']) . '</p>';
                        echo '<p>ISBN: ' . htmlspecialchars($row['isbn']) . ' | Aasta: ' . htmlspecialchars($row['aasta']) . '</p>';
                        echo '<p>Saadaval eksemplare: ' . $saadaval . '/' . $row['eksemplarid'] . '</p>';
                        
                        if (isLoggedIn() && $saadaval > 0) {
                            echo '<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#laenutaModal" 
                                  data-raamat-id="' . $row['id'] . '" data-raamat-nimi="' . htmlspecialchars($row['pealkiri']) . '">
                                  Laenuta</button>';
                        }
                        
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p>Raamatuid ei leitud.</p>';
                }
                $conn->close();
                
                function getLaenutatudKogus($conn, $raamat_id) {
                    $sql = "SELECT COUNT(*) AS kogus FROM laenutused 
                            WHERE raamat_id = ? AND tagastatud = FALSE";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $raamat_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    return $row['kogus'];
                }
                ?>
            </div>
            
            <div class="col-md-4">
                <?php if (isLoggedIn()): ?>
                    <div class="card">
                        <div class="card-header">
                            Minu laenutused
                        </div>
                        <div class="card-body">
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT l.*, r.pealkiri 
                                    FROM laenutused l 
                                    JOIN raamatud r ON l.raamat_id = r.id 
                                    WHERE l.kasutaja_id = ? AND l.tagastatud = FALSE 
                                    ORDER BY l.lopp_kuupaev";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                echo '<ul class="list-group">';
                                while ($row = $result->fetch_assoc()) {
                                    echo '<li class="list-group-item">';
                                    echo '<h6>' . htmlspecialchars($row['pealkiri']) . '</h6>';
                                    echo '<p>Laenutatud: ' . $row['algus_kuupaev'] . '</p>';
                                    echo '<p>Tagastamise tähtaeg: ' . $row['lopp_kuupaev'] . '</p>';
                                    echo '<button class="btn btn-warning btn-sm" onclick="tagastaRaamat(' . $row['id'] . ')">Tagasta</button>';
                                    echo '</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>Teil pole aktiivseid laenutusi.</p>';
                            }
                            $conn->close();
                            ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            Sisselogimine
                        </div>
                        <div class="card-body">
                            <p>Logi sisse, et laenutada raamatuid ja jälgida oma laenutusi.</p>
                            <a href="login.php" class="btn btn-primary">Logi sisse</a>
                            <a href="register.php" class="btn btn-secondary">Registreeri</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Laenutamise modal -->
    <div class="modal fade" id="laenutaModal" tabindex="-1" aria-labelledby="laenutaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="laenutaModalLabel">Raamatu laenutamine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="laenuta.php" method="post" id="laenutaForm">
                    <div class="modal-body">
                        <input type="hidden" name="raamat_id" id="modalRaamatId">
                        <div class="mb-3">
                            <label for="raamat_nimi" class="form-label">Raamat:</label>
                            <input type="text" class="form-control" id="raamat_nimi" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="laenutuse_pikkus" class="form-label">Laenutuse pikkus (päevades, maks 14):</label>
                            <input type="number" class="form-control" name="paevad" id="laenutuse_pikkus" min="1" max="14" value="14" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                        <button type="submit" class="btn btn-primary">Kinnita laenutus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Laenutamise modali täitmine
        var laenutaModal = document.getElementById('laenutaModal');
        laenutaModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var raamatId = button.getAttribute('data-raamat-id');
            var raamatNimi = button.getAttribute('data-raamat-nimi');
            
            document.getElementById('modalRaamatId').value = raamatId;
            document.getElementById('raamat_nimi').value = raamatNimi;
        });
        
        // Raamatu tagastamise funktsioon
        function tagastaRaamat(laenutusId) {
            if (confirm('Kas olete kindel, et soovite selle raamatu tagastada?')) {
                fetch('tagasta.php?id=' + laenutusId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Tagastamine ebaõnnestus: ' + data.message);
                        }
                    });
            }
        }
    </script>
</body>
</html>