<?php
include "configuracionbd.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validaciones básicas
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $no_economico = $_POST["no_economico"];
    $placas = $_POST["placas"];
    $modelo = $_POST["modelo"];
    $color = $_POST["color"];
    $km_inicial = (int)$_POST["km_inicial"];
    $km_final = (int)$_POST["km_final"];

    if ($km_final < $km_inicial) {
        $mensaje = "El kilometraje final no puede ser menor al inicial.";
    } else {
        // Prepara el insert dinámico
        $sql = "INSERT INTO entrega_vehiculos 
        (fecha,hora,no_economico,placas,modelo,color,km_inicial,km_final,
        parrilla,calaveras,parabrisas,espejos,tablero,aire,cinturones,llantas,interiores,limpieza,
        refaccion,gato,llave_ruedas,señalamientos,extintor,tarjeta_circ,placas_ok,poliza,verificacion,licencia,anticongelante)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiisssssssssssssssssssss", 
            $fecha,$hora,$no_economico,$placas,$modelo,$color,$km_inicial,$km_final,
            $_POST["parrilla"],$_POST["calaveras"],$_POST["parabrisas"],$_POST["espejos"],$_POST["tablero"],$_POST["aire"],
            $_POST["cinturones"],$_POST["llantas"],$_POST["interiores"],$_POST["limpieza"],
            $_POST["refaccion"],$_POST["gato"],$_POST["llave_ruedas"],$_POST["señalamientos"],$_POST["extintor"],
            $_POST["tarjeta_circ"],$_POST["placas_ok"],$_POST["poliza"],$_POST["verificacion"],$_POST["licencia"],$_POST["anticongelante"]
        );

        if ($stmt->execute()) {
            $mensaje = "Formato guardado correctamente.";
        } else {
            $mensaje = "Error al guardar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formato de Entrega/Recepción - CFE</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #004d33, #006847);
    min-height: 100vh;
    padding: 20px;
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
    font-size: 1.2rem;
}
.table-check th, .table-check td {
    text-align: center;
    vertical-align: middle;
}
</style>
</head>
<body>

<div class="container">
  <div class="card">
    <div class="card-header">Formato de Entrega / Recepción de Vehículos</div>
    <div class="card-body bg-light text-dark">
      
      <?php if($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
      <?php endif; ?>

      <form method="POST">
        <h5 class="mt-3">Datos del Vehículo</h5>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Fecha de entrega</label>
            <input type="date" name="fecha" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Hora de entrega</label>
            <input type="time" name="hora" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>No. Económico</label>
            <input type="text" name="no_economico" class="form-control" 
       value="<?= isset($_POST['no_economico']) ? htmlspecialchars($_POST['no_economico']) : '' ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Placas</label>
            <input type="text" name="placas" class="form-control" 
       value="<?= isset($_POST['placas']) ? htmlspecialchars($_POST['placas']) : '' ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Modelo</label>
            <input type="text" name="modelo" class="form-control" 
       value="<?= isset($_POST['modelo']) ? htmlspecialchars($_POST['modelo']) : '' ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Color</label>
            <input type="text" name="color" class="form-control" 
       value="<?= isset($_POST['color']) ? htmlspecialchars($_POST['color']) : '' ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Kilometraje Inicial</label>
            <input type="number" name="km_inicial" class="form-control" 
       value="<?= isset($_POST['km_inicial']) ? htmlspecialchars($_POST['no_economico']) : '' ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Kilometraje Final</label>
            <input type="number" name="km_final" class="form-control" 
       value="<?= isset($_POST['km_final']) ? htmlspecialchars($_POST['km_final']) : '' ?>" required>
          </div>
        </div>

        <h5 class="mt-4">Estado</h5>
        <table class="table table-bordered table-check">
          <thead>
            <tr>
              <th></th>
              <th>B</th>
              <th>R</th>
              <th>M</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $items = ["parrilla","calaveras","parabrisas","espejos","tablero","aire","cinturones","llantas","interiores","limpieza"];
              foreach ($items as $item) {
                echo "<tr><td>".ucfirst($item)."</td>";
                foreach(["B","R","M"] as $estado){
                  echo "<td><input type='radio' name='$item' value='$estado' required></td>";
                }
                echo "</tr>";
              }
            ?>
          </tbody>
        </table>

        <h5 class="mt-4">elementos</h5>
        <table class="table table-bordered table-check">
          <thead>
            <tr>
              <th>Elemento</th>
              <th>Sí</th>
              <th>No</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $elementos = ["refaccion"=>"Llanta de refacción","gato"=>"Gato","llave_ruedas"=>"Llave de ruedas",
              "señalamientos"=>"Señalamientos","extintor"=>"Extintor vigente","tarjeta_circ"=>"Tarjeta de circulación",
              "placas_ok"=>"Placas","poliza"=>"Póliza de seguro","verificacion"=>"Verificación",
              "licencia"=>"Licencia vigente","anticongelante"=>"Anticongelante"];
              foreach ($elementos as $campo=>$label) {
                echo "<tr><td>$label</td>";
                foreach(["Si","No"] as $opt){
                  echo "<td><input type='radio' name='$campo' value='$opt' required></td>";
                }
                echo "</tr>";
              }
            ?>
          </tbody>
        </table>

        <h5 class="mt-4">Señala partes dañadas en el vehículo</h5>
<div class="text-center mb-3">
  <div style="position: relative; display: inline-block;">
    <img id="autoImg" src="img/auto.jpg" alt="Vehículo" style="width: 400px; border: 1px solid #ccc;">
    <canvas id="autoCanvas" width="400" height="300" 
            style="position:absolute; top:0; left:0; cursor: crosshair;"></canvas>
  </div>
</div>
<div class="text-center mb-3">
  <button type="button" id="resetCanvas" class="btn btn-danger btn-sm mt-2">
    Borrar marcas
  </button>
</div>

<input type="hidden" name="danios" id="danios">


        <button type="submit" class="btn btn-success w-100">Guardar Formato</button>
      </form>
    </div>
  </div>
</div>
<script>
const canvas = document.getElementById('autoCanvas');
const ctx = canvas.getContext('2d');
const puntos = [];

function dibujarPuntos() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.fillStyle = "red";
  puntos.forEach(p => {
    ctx.beginPath();
    ctx.arc(p.x, p.y, 5, 0, 2 * Math.PI);
    ctx.fill();
  });
  document.getElementById('danios').value = JSON.stringify(puntos);
}

// Agregar punto
canvas.addEventListener('click', function(e) {
  const rect = canvas.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;
  puntos.push({x, y});
  dibujarPuntos();
});

// Quitar punto 
canvas.addEventListener('contextmenu', function(e) {
  e.preventDefault(); // evita el menú del navegador (no editar)
  const rect = canvas.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;

  // Busca el punto más cercano al clic
  let indiceEliminar = -1;
  let distanciaMin = 9999;
  puntos.forEach((p, i) => {
    const distancia = Math.sqrt((p.x - x)**2 + (p.y - y)**2);
    if (distancia < 10 && distancia < distanciaMin) { // tolerancia de 10px
      distanciaMin = distancia;
      indiceEliminar = i;
    }
  });

  // eliminar PUNTO
  if (indiceEliminar !== -1) {
    puntos.splice(indiceEliminar, 1);
    dibujarPuntos();
  }
});

// Botón para borrar todos los puntossss
document.getElementById('resetCanvas').addEventListener('click', function() {
  puntos.length = 0;
  dibujarPuntos();
});
</script>


</body>
</html>
