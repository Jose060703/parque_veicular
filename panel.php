<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION["usuario"];
$rol = $_SESSION["rol"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Control</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card shadow-lg p-4">
    <h3>Bienvenido, <?= $usuario ?> (Rol: <?= $rol ?>)</h3>
    <hr>
    
    <?php if ($rol == "admin"): ?>
      <div class="alert alert-danger">Eres administrador. Puedes gestionar todo el sistema.</div>
    <?php elseif ($rol == "editor"): ?>
      <div class="alert alert-warning">Eres editor. Puedes modificar contenido.</div>
    <?php else: ?>
      <div class="alert alert-info">Eres usuario normal. Acceso limitado.</div>
    <?php endif; ?>

    <a href="logout.php" class="btn btn-secondary mt-3">Cerrar Sesión</a>
  </div>
</div>

</body>
</html>
