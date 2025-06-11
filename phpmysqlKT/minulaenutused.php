<?php
require_once 'config.php';
requireLogin();
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minu laenutused - Raamatukogu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <h1>Minu laenutused</h1>
        
        <ul class="nav nav-tabs mt-4" id="myLoansTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">Aktiivsed laenutused</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Laenutuste ajalugu</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reservations-tab" data-bs-toggle="tab" data-bs-target="#reservations" type="button" role="tab">Minu broneeringud</button>
            </li>
        </ul>
        
        <div class="tab-content mt-3" id="myLoansTabsContent">
            <!-- Aktiivsed laenutused -->
            <div class="tab-pane fade show active" id="active" role="tabpanel">
                <div class="table-responsive mt-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Raamat</th>
                                <th>Autor</th>
                                <th>Laenutuse algus</th>
                                <th>Tagastamise tähtaeg</th>
                                <th>Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT l.id, r.pealkiri, 
                                    CONCAT(a.eesnimi, ' ', a.perekonnanimi) AS autor,
                                    l.algus_kuupaev, l.lopp_kuupaev
                                    FROM laenutused l
                                    JOIN raamatud r ON l.raamat_id = r.id
                                    JOIN autorid a ON r.autor_id = a.id
                                    WHERE l.kasutaja_id = ? AND l.tagastatud = FALSE
                                    ORDER BY l.lopp_kuupaev";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $today = new DateTime();
                                    $return_date = new DateTime($row['lopp_kuupaev']);
                                    $is_late = $today > $return_date;
                                    
                                    echo '<tr class="' . ($is_late ? 'table-warning' : '') . '">';
                                    echo '<td>' . htmlspecialchars($row['pealkiri']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['autor']) . '</td>';
                                    echo '<td>' . $row['algus_kuupaev'] . '</td>';
                                    echo '<td>' . $row['lopp_kuupaev'] . '</td>';
                                    echo '<td>';
                                    echo '<button class="btn btn-sm btn-warning" onclick="tagastaRaamat(' . $row['id'] . ')">Tagasta</button>';
                                    if ($is_late) {
                                        echo '<span class="badge bg-danger ms-2">Hilinenud</span>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">Teil pole aktiivseid laenutusi</td></tr>';
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Laenutuste ajalugu -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="table-responsive mt-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Raamat</th>
                                <th>Autor</th>
                                <th>Laenutuse algus</th>
                                <th>Tagastamise tähtaeg</th>
                                <th>Tagastatud</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT r.pealkiri, 
                                    CONCAT(a.eesnimi, ' ', a.perekonnanimi) AS autor,
                                    l.algus_kuupaev, l.lopp_kuupaev, l.tagastamise_kuupaev
                                    FROM laenutused l
                                    JOIN raamatud r ON l.raamat_id = r.id
                                    JOIN autorid a ON r.autor_id = a.id
                                    WHERE l.kasutaja_id = ? AND l.tagastatud = TRUE
                                    ORDER BY l.tagastamise_kuupaev DESC
                                    LIMIT 50";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['pealkiri']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['autor']) . '</td>';
                                    echo '<td>' . $row['algus_kuupaev'] . '</td>';
                                    echo '<td>' . $row['lopp_kuupaev'] . '</td>';
                                    echo '<td>' . $row['tagastamise_kuupaev'] . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">Teil pole varasemaid laenutusi</td></tr>';
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Broneeringud -->
            <div class="tab-pane fade" id="reservations" role="tabpanel">
                <div class="table-responsive mt-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Raamat</th>
                                <th>Autor</th>
                                <th>Broneeringu kuupäev</th>
                                <th>Laenutamise tähtaeg</th>
                                <th>Staatus</th>
                                <th>Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT b.id, r.pealkiri, 
                                    CONCAT(a.eesnimi, ' ', a.perekonnanimi) AS autor,
                                    b.broneeringu_kuupaev, b.voimalik_laenutuse_kuupaev, b.staatus
                                    FROM broneeringud b
                                    JOIN raamatud r ON b.raamat_id = r.id
                                    JOIN autorid a ON r.autor_id = a.id
                                    WHERE b.kasutaja_id = ?
                                    ORDER BY b.broneeringu_kuupaev DESC";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['pealkiri']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['autor']) . '</td>';
                                    echo '<td>' . $row['broneeringu_kuupaev'] . '</td>';
                                    echo '<td>' . $row['voimalik_laenutuse_kuupaev'] . '</td>';
                                    echo '<td>';
                                    switch ($row['staatus']) {
                                        case 'ootel':
                                            echo '<span class="badge bg-primary">Ootel</span>';
                                            break;
                                        case 'tühistatud':
                                            echo '<span class="badge bg-secondary">Tühistatud</span>';
                                            break;
                                        case 'täidetud':
                                            echo '<span class="badge bg-success">Täidetud</span>';
                                            break;
                                    }
                                    echo '</td>';
                                    echo '<td>';
                                    if ($row['staatus'] === 'ootel') {
                                        $today = new DateTime();
                                        $reservation_date = new DateTime($row['voimalik_laenutuse_kuupaev']);
                                        $days_diff = $today->diff($reservation_date)->days;
                                        
                                        if ($days_diff >= 3) {
                                            echo '<button class="btn btn-sm btn-danger" onclick="tuhistaBroneering(' . $row['id'] . ')">Tühista</button>';
                                        } else {
                                            echo '<span class="text-muted">Tühistamise tähtaeg on möödas</span>';
                                        }
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6">Teil pole aktiivseid broneeringuid</td></tr>';
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
        
        // Broneeringu tühistamise funktsioon
        function tuhistaBroneering(broneeringId) {
            if (confirm('Kas olete kindel, et soovite selle broneeringu tühistada?')) {
                fetch('tuhista_broneering.php?id=' + broneeringId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Tühistamine ebaõnnestus: ' + data.message);
                        }
                    });
            }
        }
    </script>
</body>
</html>