<?php
session_start();
include "configuracionbd.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    // 🔹 Buscamos usuario junto con su rol
    $sql = "SELECT u.*, r.descripcion AS rol 
            FROM usuario_privi u
            INNER JOIN roles r ON u.id_rol = r.id_role
            WHERE u.Usuario_Rpe = ? AND u.password = MD5(?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) { 
        // 🔹 Guardamos datos en sesión
        $_SESSION["id_usuario"] = $row["Usuario_Rpe"];
        $_SESSION["usuario"] = $row["nombre"];
        $_SESSION["rol"] = $row["rol"]; // Ej: Administrador, Editor, etc.

        // 🔹 Redirigimos según su rol
        switch (strtolower($row["rol"])) {
            case "administrador":
                header("Location: panel.php");
                break;
            case "editor":
            case "trabajador": // por si lo manejas con ese nombre
                header("Location: panel_trabajador.php");
                break;
            case "mecanico":
                header("Location: panel_mecanico.php");
                break;
            case "jefe":
                header("Location: panel_jefe.php");
                break;
            default:
                header("Location: panel.php");
                break;
        }
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #004d33, #006847);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.login-card {
    width: 380px;
    border-radius: 20px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(15px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    overflow: hidden;
    color: #ffffff;
}
.login-header {
    text-align: center;
    padding: 25px 15px 15px 15px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}
.login-header img {
    width: 80px;
    margin-bottom: 10px;
    filter: drop-shadow(0 0 6px rgba(0,255,128,0.4));
}
.login-header h4 {
    margin: 0;
    font-weight: 700;
}
.login-header p {
    font-size: 0.9rem;
    opacity: 0.8;
}
.card-body {
    padding: 25px 20px;
}
.form-control {
    border-radius: 12px;
    border: none;
    padding: 12px 14px;
    background: rgba(255,255,255,0.15);
    color: white;
    box-shadow: inset 0 2px 6px rgba(0,0,0,0.2);
}
.form-control:focus {
    background: rgba(255,255,255,0.2);
    outline: 2px solid #00ff80;
    box-shadow: 0 0 8px #00ff80;
    color: #fff;
}
.btn-cfe {
    background: linear-gradient(90deg, #00a859, #006847);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 6px 15px rgba(0,168,89,0.5);
}
.btn-cfe:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0,255,128,0.6);
}
.alert {
    font-size: 14px;
    text-align: center;
    border-radius: 10px;
    padding: 8px 10px;
}
h5 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 600;
}
</style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="img/logocfe.png" alt="CFE Logo">
        <h4>Comisión Federal de Electricidad</h4>
        <p>Sistema de Acceso</p>
    </div>
    <div class="card-body">
        <h5>Iniciar Sesión</h5>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Usuario (RPE)</label>
                <input type="text" class="form-control" name="usuario" placeholder="Ingresa tu RPE" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" placeholder="Ingresa tu contraseña" required>
            </div>
            <button type="submit" class="btn btn-cfe w-100 text-white">Ingresar</button>
        </form>
    </div>
</div>

</body>
</html>
