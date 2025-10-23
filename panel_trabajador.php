<?php
// Puedes validar sesión aquí si ya tienes login real
// session_start();
// if ($_SESSION['rol'] != 'trabajador') { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel del Trabajador - CFE</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #004d33, #006847);
  min-height: 100vh;
  color: white;
  font-family: 'Inter', sans-serif;
}
.nav-tabs .nav-link.active {
  background-color: #006847 !important;
  color: white !important;
  border: none;
}
.card {
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
iframe {
  width: 100%;
  height: 80vh;
  border: none;
  border-radius: 0 0 12px 12px;
}
</style>
</head>
<body>

<div class="container mt-4">
  <div class="card">
    <div class="card-header text-center fw-bold">
      Panel del Trabajador
    </div>
    <div class="card-body p-0">
      <ul class="nav nav-tabs nav-fill" id="formTabs" role="tablist">
        <li class="nav-item">
          <button class="nav-link active" id="entrega-tab" data-bs-toggle="tab" data-bs-target="#entrega" type="button" role="tab">Entrega/Recepción</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" id="mantenimiento-tab" data-bs-toggle="tab" data-bs-target="#mantenimiento" type="button" role="tab">Solicitud de Mantenimiento</button>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="entrega" role="tabpanel">
          <iframe src="entrega_vehiculos.php"></iframe>
        </div>
        <div class="tab-pane fade" id="mantenimiento" role="tabpanel">
          <iframe src="solicitud_mantenimiento.php"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
