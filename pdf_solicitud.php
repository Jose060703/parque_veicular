<?php
require_once 'dompdf/autoload.inc.php';
require 'configuracionbd.php';

use Dompdf\Dompdf;

session_start();
$usuario_rpe = $_SESSION['id_usuario'] ?? ''; // usa el RPE del usuario logueado

if (empty($usuario_rpe)) {
    die("No hay usuario en sesión.");
}

// 🔹 Ahora buscamos con Usuario_Rpe, no con id
$query = $conn->prepare("
    SELECT t.nombre, t.area AS puesto, t.id_Rpe AS rpe
    FROM trabajadores t
    INNER JOIN usuario_privi u ON u.Usuario_Rpe = t.id_Rpe
    WHERE u.Usuario_Rpe = ?
");
$query->bind_param("s", $usuario_rpe);
$query->execute();
$resultado = $query->get_result();

if ($resultado->num_rows > 0) {
    $trabajador = $resultado->fetch_assoc();
} else {
    die("⚠️ No se encontraron datos del trabajador asociado a '$usuario_rpe'.");
}

// Datos del formulario
$fecha = $_POST['fecha'] ?? date('Y-m-d');
$area = $_POST['area'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$no_economico = $_POST['no_economico'] ?? '';
$tipo_servicio = $_POST['tipo_servicio'] ?? '';
$otro_servicio = $_POST['otro_servicio'] ?? '';

if ($tipo_servicio === 'otros' && !empty($otro_servicio)) {
    $tipo_servicio .= " - " . $otro_servicio;
}

//  HTML del PDF
$html = "
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }
        h1{
            text-align: center;
            color: #000000ff;
              }
        h2 {
            text-align: center;
            color: #000000ff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        td, th {
            border: 1px solid #000;
            padding: 6px;
        }
        .titulo {
            background-color: #382929ff;
            color: white;
            text-align: center;
            font-weight: bold;
        }
        .firma {
            margin-top: 40px;
            text-align: center;
        }
        .firma div {
            border-top: 1px solid #000;
            width: 250px;
            margin: auto;
        }
    </style>
</head>
<body>
    <h1>OFICINAS DIVISIONALES</h1>
    <h2>Solicitud de Mantenimiento Vehicular</h2>
    <table>
        <tr><th class='titulo' colspan='2'>Datos Generales</th></tr>
        <tr><td><b>Fecha:</b></td><td>$fecha</td></tr>
        <tr><td><b>Área:</b></td><td>$area</td></tr>
        <tr><td><b>No. Económico:</b></td><td>$no_economico</td></tr>
    </table>

    <table>
        <tr><th class='titulo' colspan='2'>Descripción del Servicio</th></tr>
        <tr><td colspan='2'>$descripcion</td></tr>
        <tr><td><b>Tipo de Servicio:</b></td><td>$tipo_servicio</td></tr>
    </table>

    <table>
        <tr><th class='titulo' colspan='2'>Datos del Solicitante</th></tr>
        <tr><td><b>Nombre:</b></td><td>{$trabajador['nombre']}</td></tr>
        <tr><td><b>Área:</b></td><td>{$trabajador['puesto']}</td></tr>
        <tr><td><b>ID RPE:</b></td><td>{$trabajador['rpe']}</td></tr>
    </table>

    <div class='firma'>
        <div></div>
        <p>Firma del Solicitante</p>
    </div>
</body>
</html>
";

// Crear y enviar el PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("solicitud_mantenimiento.pdf", ["Attachment" => true]);
?>
