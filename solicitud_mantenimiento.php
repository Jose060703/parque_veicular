<?php
include "configuracionbd.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Si no hay sesión activa, redirige al login
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$idUsuario = $_SESSION["id_usuario"]; // debe contener el RPE (Usuario_Rpe)

// --- OBTENER DATOS DEL TRABAJADOR ---
// --- OBTENER DATOS DEL TRABAJADOR ---
$sql = "SELECT 
            t.nombre, 
            t.area AS puesto, 
            t.id_Rpe AS rpe
        FROM trabajadores t
        INNER JOIN usuario_privi u ON u.Usuario_Rpe = t.id_Rpe
        WHERE u.Usuario_Rpe = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$trabajador = $result->fetch_assoc();

if (!$trabajador) {
    die("⚠️ No se encontraron datos del trabajador (verifica que el RPE esté en la tabla trabajadores).");
}


// --- GUARDAR SOLICITUD (cuando se envía el form) ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['guardar_solicitud'])) {
    $area = $_POST["area"] ?? '';
    $descripcion = trim($_POST["descripcion"] ?? '');
    $no_economico = trim($_POST["no_economico"] ?? '');
    $tipo_servicio = $_POST["tipo_servicio"] ?? '';
    $otro_servicio = ($tipo_servicio === "otros") ? trim($_POST["otro_servicio"] ?? '') : null;

    if (empty($area) || empty($descripcion) || empty($no_economico) || empty($tipo_servicio)) {
        $mensaje = "❌ Todos los campos son obligatorios.";
    } else {
        $sqlInsert = "INSERT INTO solicitud_mantenimiento 
            (fecha, area, descripcion, no_economico, tipo_servicio, otro_servicio, solicita_nombre, solicita_puesto, solicita_rpe)
            VALUES (CURRENT_DATE, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtIns = $conn->prepare($sqlInsert);
        $stmtIns->bind_param(
            "ssssssss",
            $area,
            $descripcion,
            $no_economico,
            $tipo_servicio,
            $otro_servicio,
            $trabajador['nombre'],
            $trabajador['puesto'],
            $trabajador['rpe']
        );

        if ($stmtIns->execute()) {
            $mensaje = "✅ Solicitud guardada correctamente.";
        } else {
            $mensaje = "❌ Error al guardar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Solicitud de Mantenimiento - CFE</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #004d33, #006847);
    min-height: 100vh;
    padding: 30px;
    color: white;
}
.card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
.card-header {
    background: #006847;
    color: white;
    text-align: center;
    font-weight: bold;
    font-size: 1.3rem;
}
label {
    font-weight: 600;
}
</style>
</head>
<body>

<div class="container">
  <div class="card">
    <div class="card-header">Solicitud de Mantenimiento Vehicular</div>
    <div class="card-body bg-light text-dark">
      
      <?php if($mensaje): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>

      <!-- Form principal: envía a esta misma página para guardar -->
      <form id="formMantenimiento" method="post" action="panel_trabajador.php?tab=mantenimiento">
        <!-- hidden para distinguir acción guardar -->
        <input type="hidden" name="guardar_solicitud" value="1">

        <!-- Fecha automática -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label>Fecha</label>
            <input type="text" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
          </div>
          <div class="col-md-6">
            <label>Área</label>
            <select name="area" class="form-select" required>
              <option value="">Seleccione un área...</option>
              <option value="Transporte" <?= ($trabajador['area'] === 'Transporte') ? 'selected' : '' ?>>Transporte</option>
              <option value="Mantenimiento" <?= ($trabajador['area'] === 'Mantenimiento') ? 'selected' : '' ?>>Mantenimiento</option>
              <option value="Operaciones" <?= ($trabajador['area'] === 'Operaciones') ? 'selected' : '' ?>>Operaciones</option>
              <option value="Administración" <?= ($trabajador['area'] === 'Administración') ? 'selected' : '' ?>>Administración</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label>Descripción del servicio solicitado</label>
          <textarea name="descripcion" class="form-control" rows="3" required><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
        </div>

        <div class="mb-3">
          <label>No. Económico del Vehículo</label>
          <input type="text" name="no_economico" class="form-control" required value="<?= isset($_POST['no_economico']) ? htmlspecialchars($_POST['no_economico']) : '' ?>">
        </div>

        <div class="mb-3">
          <label>Tipo de Servicio</label>
          <select name="tipo_servicio" id="tipo_servicio" class="form-select" required onchange="mostrarOtro()">
            <option value="">Seleccione un servicio...</option>
            <option value="Afinación">Afinación</option>
            <option value="Hidráulico">Hidráulico</option>
            <option value="Alineación y Balanceo">Alineación y Balanceo</option>
            <option value="Clutch">Clutch</option>
            <option value="Motor">Motor</option>
            <option value="Dirección">Dirección</option>
            <option value="Frenos">Frenos</option>
            <option value="Hojalatería">Hojalatería</option>
            <option value="Pintura">Pintura</option>
            <option value="Sistema Eléctrico">Sistema Eléctrico</option>
            <option value="Sistema de Enfriamiento">Sistema de Enfriamiento</option>
            <option value="Suspensión">Suspensión</option>
            <option value="Llantas">Llantas</option>
            <option value="Transmisión y Diferencial">Transmisión y Diferencial</option>
            <option value="otros">Otros</option>
          </select>
        </div>

        <div class="mb-3" id="otro_servicio_div" style="display:none;">
          <label>Especifique el servicio</label>
          <input type="text" name="otro_servicio" id="otro_servicio" class="form-control" value="<?= isset($_POST['otro_servicio']) ? htmlspecialchars($_POST['otro_servicio']) : '' ?>">
        </div>

        <h5 class="mt-4">Solicita:</h5>
        <div class="row mb-3">
          <div class="col-md-6">
            <label>Nombre</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($trabajador['nombre']) ?>" readonly>
          </div>
          <div class="col-md-6">
            <label>Puesto</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($trabajador['puesto']) ?>" readonly>
          </div>
          <div class="col-md-6 mt-3">
            <label>RPE</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($trabajador['rpe']) ?>" readonly>
          </div>
          <div class="col-md-6 mt-3">
            <label>Firma</label>
            <div style="border: 1px solid #333; height: 50px; border-radius: 5px; background-color: #fff;"></div>
          </div>
        </div>

        <!-- Botones: Guardar (envía a este archivo) y Generar PDF (envía a pdf_solicitud.php en nueva pestaña) -->
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">Guardar Solicitud</button>
            <button type="button" id="btnPDF" class="btn" style="background-color:#0a5d36; color:white;">Generar PDF</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
function mostrarOtro() {
  const tipo = document.getElementById('tipo_servicio').value;
  document.getElementById('otro_servicio_div').style.display = (tipo === 'otros') ? 'block' : 'none';
}

// Envío de PDF: copia campos actuales y abre pdf_solicitud.php en nueva pestaña
document.getElementById('btnPDF').addEventListener('click', function() {
    const form = document.getElementById('formMantenimiento');
    const formData = new FormData(form);

    // Crea un form temporal para enviar al PHP del PDF
    const tempForm = document.createElement('form');
    tempForm.action = 'pdf_solicitud.php';
    tempForm.method = 'POST';
    tempForm.target = '_blank'; // abrirá en nueva pestaña
    tempForm.style.display = 'none';

    // Agrega los campos actuales
    for (const [key, value] of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        tempForm.appendChild(input);
    }

    // Agrega también los datos del trabajador (para el PDF)
    const nombre = <?= json_encode($trabajador['nombre']) ?>;
    const puesto = <?= json_encode($trabajador['puesto']) ?>;
    const rpe = <?= json_encode($trabajador['rpe']) ?>;

    const inNombre = document.createElement('input'); inNombre.type='hidden'; inNombre.name='solicita_nombre'; inNombre.value=nombre; tempForm.appendChild(inNombre);
    const inPuesto = document.createElement('input'); inPuesto.type='hidden'; inPuesto.name='solicita_puesto'; inPuesto.value=puesto; tempForm.appendChild(inPuesto);
    const inRpe = document.createElement('input'); inRpe.type='hidden'; inRpe.name='solicita_rpe'; inRpe.value=rpe; tempForm.appendChild(inRpe);

    document.body.appendChild(tempForm);
    tempForm.submit();
    document.body.removeChild(tempForm);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
