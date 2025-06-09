<?php
include("../config.php"); 
session_start();
if (!isset($_SESSION['tuvastamine'])) {
    header('Location: ../login.php');
    exit();
}
?>

<!doctype html>
<html lang="et">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Osalejate haldus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f8f9fa;
      }
      .custom-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
      }
      .btn-purple {
        background-color: #6f42c1;
        color: #fff;
      }
      .btn-purple:hover {
        background-color: #5a32a3;
      }
    </style>
  </head>
  <body>
    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Osalejate haldus</h1>
        <a class="btn btn-outline-dark" href="../logout.php?logout">Logi välja</a>
      </div>

      <?php
      if(isset($_GET["muuda"]) && isset($_GET["id"])){
        $id = $_GET["id"];
        $kuvaparing = "SELECT * FROM sport2025 WHERE id=$id";
        $saada_paring = mysqli_query($yhendus, $kuvaparing);
        $rida = mysqli_fetch_assoc($saada_paring);
      }

      if(isset($_GET["salvesta_muudatus"]) && isset($_GET["id"])){
        $id = $_GET["id"];
        $fullname = $_GET["fullname"];
        $email = $_GET["email"];
        $age = $_GET["age"];
        $gender = $_GET["gender"];
        $category = $_GET["category"];

        $muuda_paring = "UPDATE sport2025 SET fullname='$fullname', email='$email', age='$age', gender='$gender', category='$category' WHERE id=$id";
        $saada_paring = mysqli_query($yhendus, $muuda_paring);
        if(mysqli_affected_rows($yhendus) == 1){
          header('Location: index.php?msg=Andmed uuendatud');
        } else {
          echo "<div class='alert alert-warning'>Andmeid ei uuendatud</div>";
        }
      }

      if(isset($_GET['msg'])){
        echo "<div class='alert alert-success'>".$_GET['msg']."</div>";
      }

      if(isset($_GET["salvesta"]) && !empty($_GET["fullname"])){
        $fullname = $_GET["fullname"];
        $email = $_GET["email"];
        $age = $_GET["age"];
        $gender = $_GET["gender"];
        $category = $_GET["category"];

        $lisa_paring = "INSERT INTO sport2025 (fullname, email, age, gender, category) 
                        VALUES ('$fullname', '$email', '$age', '$gender', '$category')";
        mysqli_query($yhendus, $lisa_paring);
        if(mysqli_affected_rows($yhendus) == 1){
          echo "<div class='alert alert-success'>Kirje edukalt lisatud</div>";
        } else {
          echo "<div class='alert alert-danger'>Kirjet ei lisatud</div>";
        }
      }
      ?>

      <div class="custom-card">
        <h5><?php echo isset($_GET['muuda']) ? "Muuda osalejat" : "Lisa uus osaleja"; ?></h5>
        <form action="index.php" method="get">
          <input type="hidden" name="id" value="<?= !empty($rida['id']) ? $rida['id'] : '' ?>">
          <div class="mb-2">
            <label class="form-label">Nimi</label>
            <input type="text" name="fullname" class="form-control" required value="<?= $rida['fullname'] ?? '' ?>">
          </div>
          <div class="mb-2">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" required value="<?= $rida['email'] ?? '' ?>">
          </div>
          <div class="mb-2">
            <label class="form-label">Vanus</label>
            <input type="number" name="age" class="form-control" min="16" max="88" required value="<?= $rida['age'] ?? '' ?>">
          </div>
          <div class="mb-2">
            <label class="form-label">Sugu</label>
            <input type="text" name="gender" class="form-control" required value="<?= $rida['gender'] ?? '' ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Spordiala</label>
            <input type="text" name="category" class="form-control" required value="<?= $rida['category'] ?? '' ?>">
          </div>
          <?php if(isset($_GET["muuda"])){ ?>
            <button type="submit" name="salvesta_muudatus" class="btn btn-success">Salvesta muudatus</button>
          <?php } else { ?>
            <button type="submit" name="salvesta" class="btn btn-purple">Salvesta</button>
          <?php } ?>
        </form>
      </div>

      <form action="index.php" method="get" class="mb-4 d-flex gap-2">
        <input type="text" name="otsi" class="form-control" placeholder="Otsi nime või spordiala järgi...">
        <select name="cat" class="form-select w-auto">
          <option value="fullname">Nimi</option>
          <option value="category">Spordiala</option>
        </select>
        <button type="submit" class="btn btn-outline-secondary">Otsi</button>
      </form>

      <table class="table table-bordered table-hover">
        <thead class="table-secondary">
          <tr>
            <th>ID</th>
            <th>Nimi</th>
            <th>Email</th>
            <th>Vanus</th>
            <th>Sugu</th>
            <th>Spordiala</th>
            <th>Reg aeg</th>
            <th>Muuda</th>
            <th>Kustuta</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if(isset($_GET['kustuta']) && isset($_GET['id'])){
              $id = $_GET['id'];
              $kparing = "DELETE FROM sport2025 WHERE id=$id";
              mysqli_query($yhendus, $kparing);
              if(mysqli_affected_rows($yhendus) == 1){
                header('Location: index.php?msg=Rida kustutatud');
              } else {
                echo "<tr><td colspan='9'>Kirjet ei kustutatud</td></tr>";
              }
            }

            $uudiseid_lehel = 50;
            $lehti_kokku = ceil(mysqli_fetch_array(mysqli_query($yhendus, "SELECT COUNT('id') FROM sport2025"))[0] / $uudiseid_lehel);
            $leht = isset($_GET['page']) ? $_GET['page'] : 1;
            $start = ($leht-1)*$uudiseid_lehel;

            if(isset($_GET['otsi']) && !empty($_GET["otsi"])){
              $s = $_GET['otsi'];
              $cat = $_GET['cat'];
              $paring = "SELECT * FROM sport2025 WHERE $cat LIKE '%$s%'";
              echo "<tr><td colspan='9'>Otsing: <b>$s</b></td></tr>";
            } else {
              $paring = "SELECT * FROM sport2025 LIMIT $start, $uudiseid_lehel";
            }

            $saada_paring = mysqli_query($yhendus, $paring);
            while($rida = mysqli_fetch_assoc($saada_paring)){
              echo "<tr>";
              echo "<td>".$rida['id']."</td>";
              echo "<td>".$rida['fullname']."</td>";
              echo "<td>".$rida['email']."</td>";
              echo "<td>".$rida['age']."</td>";
              echo "<td>".$rida['gender']."</td>";
              echo "<td>".$rida['category']."</td>";
              echo "<td>".$rida['reg_time']."</td>";
              echo "<td><a class='btn btn-sm btn-success' href='?muuda&id=".$rida['id']."'>Muuda</a></td>";
              echo "<td><a class='btn btn-sm btn-danger' href='?kustuta&id=".$rida['id']."'>Kustuta</a></td>";
              echo "</tr>";
            }
          ?>
        </tbody>
      </table>

      <div class="mt-3">
        <?php
          $eelmine = $leht - 1;
          $jargmine = $leht + 1;

          if ($leht > 1) {
            echo "<a class='btn btn-outline-primary me-1' href='?page=$eelmine'>« Eelmine</a>";
          }

          for ($i=1; $i<=$lehti_kokku ; $i++) {
            $active = $i == $leht ? "btn-primary" : "btn-outline-primary";
            echo "<a class='btn $active me-1' href='?page=$i'>$i</a>";
          }

          if ($leht < $lehti_kokku) {
            echo "<a class='btn btn-outline-primary' href='?page=$jargmine'>Järgmine »</a>";
          }
        ?>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
