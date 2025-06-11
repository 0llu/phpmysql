<?php
require_once 'config.php';
requireAdmin();
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administraatori paneel - Raamatukogu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <h1>Administraatori paneel</h1>
        
        <ul class="nav nav-tabs mt-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="raamatud-tab" data-bs-toggle="tab" data-bs-target="#raamatud" type="button" role="tab">Raamatud</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="autorid-tab" data-bs-toggle="tab" data-bs-target="#autorid" type="button" role="tab">Autorid</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="laenutused-tab" data-bs-toggle="tab" data-bs-target="#laenutused" type="button" role="tab">Laenutused</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kasutajad-tab" data-bs-toggle="tab" data-bs-target="#kasutajad" type="button" role="tab">Kasutajad</button>
            </li>
        </ul>
        
        <div class="tab-content mt-3" id="adminTabsContent">
            <!-- Raamatud vaheleht -->
            <div class="tab-pane fade show active" id="raamatud" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Raamatud</h3>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#lisaRaamatModal">Lisa uus raamat</button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pealkiri</th>
                                <th>Autor</th>
                                <th>ISBN</th>
                                <th>Eksemplarid</th>
                                <th>Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT r.id, r.pealkiri, r.isbn, r.eksemplarid, 
                                    CONCAT(a.eesnimi, ' ', a.perekonnanimi) AS autor 
                                    FROM raamatud r 
                                    JOIN autorid a ON r.autor_id = a.id 
                                    ORDER BY r.pealkiri";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($row['pealkiri']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['autor']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['isbn']) . '</td>';
                                    echo '<td>' . $row['eksemplarid'] . '</td>';
                                    echo '<td>';
                                    echo '<button class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#muudaRaamatModal" 
                                            data-id="' . $row['id'] . '"
                                            data-pealkiri="' . htmlspecialchars($row['pealkiri']) . '"
                                            data-isbn="' . htmlspecialchars($row['isbn']) . '"
                                            data-eksemplarid="' . $row['eksemplarid'] . '">
                                            Muuda</button>';
                                    echo '<button class="btn btn-sm btn-danger ms-2" 
                                            onclick="kustutaRaamat(' . $row['id'] . ')">
                                            Kustuta</button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6">Raamatuid ei leitud</td></tr>';
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Autorid vaheleht -->
            <div class="tab-pane fade" id="autorid" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Autorid</h3>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#lisaAutorModal">Lisa uus autor</button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nimi</th>
                                <th>Sünniaeg</th>
                                <th>Riik</th>
                                <th>Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT * FROM autorid ORDER BY perekonnanimi, eesnimi";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($row['eesnimi'] . ' ' . $row['perekonnanimi']) . '</td>';
                                    echo '<td>' . ($row['synniaeg'] ? $row['synniaeg'] : '-') . '</td>';
                                    echo '<td>' . ($row['riik'] ? htmlspecialchars($row['riik']) : '-') . '</td>';
                                    echo '<td>';
                                    echo '<button class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#muudaAutorModal" 
                                            data-id="' . $row['id'] . '"
                                            data-eesnimi="' . htmlspecialchars($row['eesnimi']) . '"
                                            data-perekonnanimi="' . htmlspecialchars($row['perekonnanimi']) . '"
                                            data-synniaeg="' . $row['synniaeg'] . '"
                                            data-riik="' . htmlspecialchars($row['riik']) . '">
                                            Muuda</button>';
                                    echo '<button class="btn btn-sm btn-danger ms-2" 
                                            onclick="kustutaAutor(' . $row['id'] . ')">
                                            Kustuta</button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">Autoreid ei leitud</td></tr>';
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Laenutused vaheleht -->
            <div class="tab-pane fade" id="laenutused" role="tabpanel">
                <h3>Laenutused</h3>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kasutaja</th>
                                <th>Raamat</th>
                                <th>Laenutuse algus</th>
                                <th>Tagastamise tähtaeg</th>
                                <th>Staatus</th>
                                <th>Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT l.id, 
                                    CONCAT(k.eesnimi, ' ', k.perekonnanimi) AS kasutaja,
                                    r.pealkiri AS raamat,
                                    l.algus_kuupaev, l.lopp_kuupaev,
                                    l.tagastatud,
                                    CASE 
                                        WHEN l.tagastatud THEN 'Tagastatud'
                                        WHEN l.lopp_kuupaev < CURDATE() THEN 'Hilinenud'
                                        ELSE 'Aktiivne'
                                    END AS staatus
                                    FROM laenutused l
                                    JOIN kasutajad k ON l.kasutaja_id = k.id
                                    JOIN raamatud r ON l.raamat_id = r.id
                                    ORDER BY l.algus_kuupaev DESC";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($row['kasutaja']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['raamat']) . '</td>';
                                    echo '<td>' . $row['algus_kuupaev'] . '</td>';
                                    echo '<td>' . $row['lopp_kuupaev'] . '</td>';
                                    echo '<td>' . $row['staatus'] . '</td>';
                                    echo '<td>';
                                    if (!$row['tagastatud']) {
                                        echo '<button class="btn btn-sm btn-warning" 
                                                onclick="markTagastatuks(' . $row['id'] . ')">
                                                Märgi tagastatuks</button>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7">Laenutusi ei leitud</td></tr>';
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Kasutajad vaheleht -->
            <div class="tab-pane fade" id="kasutajad" role="tabpanel">
                <h3>Kasutajad</h3>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nimi</th>
                                <th>Isikukood</th>
                                <th>E-posti aadress</th>
                                <th>Roll</th>
                                <th>Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT id, eesnimi, perekonnanimi, isikukood, email, roll FROM kasutajad ORDER BY perekonnanimi, eesnimi";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($row['eesnimi'] . ' ' . $row['perekonnanimi']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['isikukood']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                    echo '<td>' . $row['roll'] . '</td>';
                                    echo '<td>';
                                    echo '<button class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#muudaKasutajaModal" 
                                            data-id="' . $row['id'] . '"
                                            data-eesnimi="' . htmlspecialchars($row['eesnimi']) . '"
                                            data-perekonnanimi="' . htmlspecialchars($row['perekonnanimi']) . '"
                                            data-isikukood="' . htmlspecialchars($row['isikukood']) . '"
                                            data-email="' . htmlspecialchars($row['email']) . '"
                                            data-roll="' . $row['roll'] . '">
                                            Muuda</button>';
                                    if ($row['id'] != $_SESSION['user_id']) {
                                        echo '<button class="btn btn-sm btn-danger ms-2" 
                                                onclick="kustutaKasutaja(' . $row['id'] . ')">
                                                Kustuta</button>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6">Kasutajaid ei leitud</td></tr>';
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modalid -->
    <?php include 'admin_modals.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Raamatu muutmise modal
        var muudaRaamatModal = document.getElementById('muudaRaamatModal');
        if (muudaRaamatModal) {
            muudaRaamatModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                document.getElementById('muudaRaamatId').value = button.getAttribute('data-id');
                document.getElementById('muudaPealkiri').value = button.getAttribute('data-pealkiri');
                document.getElementById('muudaIsbn').value = button.getAttribute('data-isbn');
                document.getElementById('muudaEksemplarid').value = button.getAttribute('data-eksemplarid');
            });
        }
        
        // Kustuta raamat
        function kustutaRaamat(id) {
            if (confirm('Kas olete kindel, et soovite selle raamatu kustutada?')) {
                fetch('admin_actions.php?action=delete_book&id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Viga: ' + data.message);
                        }
                    });
            }
        }
        
        // Märgi laenutus tagastatuks
        function markTagastatuks(id) {
            if (confirm('Kas olete kindel, et soovite märkida selle laenutuse tagastatuks?')) {
                fetch('admin_actions.php?action=return_loan&id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Viga: ' + data.message);
                        }
                    });
            }
        }
        
        // Autorite, kasutajate jms funktsioonid sarnaselt
    </script>
</body>
</html>