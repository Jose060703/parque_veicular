<?php
session_start();
// if ($_SESSION['rol'] != 'trabajador') { header("Location: login.php"); exit; }

$tab = $_GET['tab'] ?? 'entrega';
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
  margin: 0;
  padding: 0;
  color: white;
  font-family: 'Inter', sans-serif;
}

.full-width {
  width: 100%;
  padding: 0 !important;
  margin: 0 !important;
}

.nav-tabs .nav-link.active {
  background-color: #006847 !important;
  color: white !important;
  border: none;
}

.content-wrapper {
  padding: 20px;
  width: 100%;
  background: transparent;
}
</style>
</head>

<body>

<div class="full-width">
  
  <div class="text-center py-3 fw-bold fs-4">
    Panel del Trabajador
  </div>

  <!-- ✅ Tabs con activación según ?tab -->
  <ul class="nav nav-tabs nav-fill" id="formTabs" role="tablist">
    <li class="nav-item">
      <button class="nav-link <?= ($tab == 'entrega') ? 'active' : '' ?>" 
              data-bs-toggle="tab" data-bs-target="#entrega" type="button" role="tab">
        Entrega/Recepción
      </button>
    </li>

    <li class="nav-item">
      <button class="nav-link <?= ($tab == 'mantenimiento') ? 'active' : '' ?>" 
              data-bs-toggle="tab" data-bs-target="#mantenimiento" type="button" role="tab">
        Solicitud de Mantenimiento
      </button>
    </li>
  </ul>

  <div class="tab-content full-width">

    <!-- ✅ PANEL ENTREGA -->
    <div class="tab-pane fade <?= ($tab == 'entrega') ? 'show active' : '' ?> content-wrapper" 
         id="entrega" role="tabpanel">
      <?php include 'entrega_vehiculos.php'; ?>
    </div>

    <!-- ✅ PANEL MANTENIMIENTO -->
    <div class="tab-pane fade <?= ($tab == 'mantenimiento') ? 'show active' : '' ?> content-wrapper" 
         id="mantenimiento" role="tabpanel">
      <?php include 'solicitud_mantenimiento.php'; ?>
    </div>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
