<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("config.php"); 
session_start();

if (isset($_SESSION['tuvastamine'])) {
    header('Location: admin/');
    exit();
}
?>

<!doctype html>
<html lang="et">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admini sisselogimine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f0f0f0;
      }
      .custom-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        padding: 2rem;
      }
      .btn-login {
        background-color: #222;
        color: white;
      }
      .btn-login:hover {
        background-color: #444;
      }
    </style>
  </head>
  <body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
      <div class="col-md-6">
        <?php
          if (!empty($_POST['user']) && !empty($_POST['password'])) {
              $login = $_POST['user'];
              $str = $_POST['password'];

              $paring = "SELECT * FROM users";
              $saada_paring = mysqli_query($yhendus, $paring);
              $rida = mysqli_fetch_assoc($saada_paring);
              $s = $rida["password"];

              if ($login == 'admin' && password_verify($str, $s)) {
                  $_SESSION['tuvastamine'] = 'authenticated';
                  header('Location: admin/');
                  exit();
              } else {
                  echo "<div class='alert alert-danger'>Vale kasutajanimi või parool</div>";
              }
          }
        ?>

        <div class="custom-card">
          <h3 class="text-center mb-4">Admini sisselogimine</h3>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Kasutajanimi</label>
              <input type="text" name="user" class="form-control" placeholder="Sisesta kasutajanimi">
            </div>
            <div class="mb-3">
              <label class="form-label">Parool</label>
              <input type="password" name="password" class="form-control" placeholder="Sisesta parool">
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" name="remember" id="remember">
              <label class="form-check-label" for="remember">Jäta mind meelde</label>
            </div>
            <button type="submit" class="btn btn-login w-100">Logi sisse</button>
          </form>
        </div>
      </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
