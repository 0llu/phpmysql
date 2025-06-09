<?php include("config.php"); ?>

<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HKHK Spordipäev 2025!</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-4">
    <h1 class="mb-4 text-center text-success">Spordipäev 2025</h1>

    <div class="d-flex justify-content-end mb-3">
      <a href="login.php" class="btn btn-warning">Administraatori sisselogimine</a>
    </div>

    <form action="index.php" method="get" class="row g-2 mb-4">
      <div class="col-md-5">
        <input type="text" name="otsi" class="form-control" placeholder="Sisesta otsingusõna...">
      </div>
      <div class="col-md-3">
        <select name="cat" class="form-select">
          <option value="fullname">Nimi</option>
          <option value="category">Spordiala</option>
        </select>
      </div>
      <div class="col-md-2">
        <input type="submit" class="btn btn-success w-100" value="Otsi">
      </div>
    </form>

    <table class="table table-hover">
      <thead class="table-success">
        <tr>
          <th>#</th>
          <th>Nimi</th>
          <th>Email</th>
          <th>Vanus</th>
          <th>Sugu</th>
          <th>Spordiala</th>
          <th>Registreerimise aeg</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $uudiseid_lehel = 50;

          $uudiseid_kokku_paring = "SELECT COUNT('id') FROM sport2025";
          $lehtede_vastus = mysqli_query($yhendus, $uudiseid_kokku_paring);
          $uudiseid_kokku = mysqli_fetch_array($lehtede_vastus)[0];
          $lehti_kokku = ceil($uudiseid_kokku / $uudiseid_lehel);

          $leht = isset($_GET['page']) ? intval($_GET['page']) : 1;
          $start = ($leht - 1) * $uudiseid_lehel;

          if (isset($_GET['otsi']) && !empty($_GET["otsi"])) {
              $s = mysqli_real_escape_string($yhendus, $_GET['otsi']);
              $cat = mysqli_real_escape_string($yhendus, $_GET['cat']);
              echo "<tr><td colspan='7'><strong>Otsingu tulemused fraasile:</strong> " . htmlspecialchars($s) . "</td></tr>";
              $paring = "SELECT * FROM sport2025 WHERE $cat LIKE '%$s%'";
          } else {
              $paring = "SELECT * FROM sport2025 LIMIT $start, $uudiseid_lehel";
          }

          $saada_paring = mysqli_query($yhendus, $paring);

          while ($rida = mysqli_fetch_assoc($saada_paring)) {
              echo "<tr>";
              echo "<td>{$rida['id']}</td>";
              echo "<td>{$rida['fullname']}</td>";
              echo "<td>{$rida['email']}</td>";
              echo "<td>{$rida['age']}</td>";
              echo "<td>{$rida['gender']}</td>";
              echo "<td>{$rida['category']}</td>";
              echo "<td>{$rida['reg_time']}</td>";
              echo "</tr>";
          }

          echo "</tbody></table><div class='d-flex justify-content-center flex-wrap'>";

          $eelmine = $leht - 1;
          $jargmine = $leht + 1;

          if ($leht > 1) {
              echo "<a class='btn btn-outline-success m-1' href='?page=$eelmine'>⟵ Tagasi</a>";
          }

          for ($i = 1; $i <= $lehti_kokku; $i++) {
              $style = $i == $leht ? 'btn-success' : 'btn-outline-success';
              echo "<a class='btn $style m-1' href='?page=$i'>$i</a>";
          }

          if ($leht < $lehti_kokku) {
              echo "<a class='btn btn-outline-success m-1' href='?page=$jargmine'>Edasi ⟶</a>";
          }

          echo "</div>";
        ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
