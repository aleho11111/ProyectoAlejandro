<?php
session_start();
include 'conexion.php';


if (!isset($_SESSION['identificacion'])) {
    header("Location: iniciosesion.php");
    exit();
}

$identificacion = $_SESSION['identificacion'];
$tipoUsuario = $_SESSION['tipoUsuario']; 


if ($tipoUsuario == 'profesional') {
    $sql = "SELECT NP.id, U.nombre AS empresa, P.contacto AS contacto_profesional, E.direccion, NP.estado, U.identificacion AS empresa_id
            FROM NotificacionesProfesional NP
            JOIN Usuarios U ON NP.emisor_identificacion = U.identificacion
            JOIN PerfilEmpresarial E ON E.identificacion = U.identificacion
            JOIN Postulaciones P ON P.profesional_identificacion = NP.receptor_identificacion
            WHERE NP.receptor_identificacion = ? AND NP.estado = 'pendiente'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $identificacion);
    $stmt->execute();
    $notificaciones = $stmt->get_result();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
        $notificacion_id = $_POST['notificacion_id'];
        $accion = $_POST['accion'];
        $estado = ($accion == 'aceptar') ? 'aceptado' : 'rechazado';

        
        $updateSql = "UPDATE NotificacionesProfesional SET estado = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $estado, $notificacion_id);
        $updateStmt->execute();

        
        $empresa_id = $_POST['empresa_id'];
        $mensaje = "El profesional " . $_SESSION['nombre'] . " con el contacto " . $_POST['contacto_profesional'] . " ha " . $estado . " tu solicitud.";
        $insertSql = "INSERT INTO NotificacionesEmpresa (receptor_identificacion, emisor_identificacion, mensaje) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sss", $empresa_id, $identificacion, $mensaje);
        $insertStmt->execute();

        header("Location: Notificaciones.php");
        exit();
    }
}


elseif ($tipoUsuario == 'empresa') {
    $sql = "SELECT NE.mensaje, NE.fecha
            FROM NotificacionesEmpresa NE
            WHERE NE.receptor_identificacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $identificacion);
    $stmt->execute();
    $notificaciones = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <link rel="stylesheet" href="Notificacionstyle.css">
</head>
<body>
<header class="header">
    <div class="menu container">
        <a href="#" class="logo">Logo</a>
        <label for="menu"></label>
        <nav class="navbar">
            <ul>
                <li><a href="InicioPrincipal.php">Inicio</a></li>
                <li><a href="Perfil.php">Perfil</a></li>
                <li><a href="Postulados.php">Postulados</a></li>
                <li><a href="Notificaciones.php">Notificaciones</a></li>
                <li><a href="cerrarsesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="lala">
    <h2>Notificaciones</h2>
    <?php if ($tipoUsuario == 'profesional'): ?>
        <?php if ($notificaciones && $notificaciones->num_rows > 0): ?>
            <?php while ($notif = $notificaciones->fetch_assoc()): ?>
                <div class="notificacion">
                    <h3><?php echo $notif['empresa']; ?> quiere contratarte</h3>
                    <p>Contacto del profesional: <?php echo $notif['contacto_profesional']; ?></p>
                    <p>Dirección: <?php echo $notif['direccion']; ?></p>
                    <form method="POST">
                        <input type="hidden" name="notificacion_id" value="<?php echo $notif['id']; ?>">
                        <input type="hidden" name="empresa_id" value="<?php echo $notif['empresa_id']; ?>">
                        <input type="hidden" name="contacto_profesional" value="<?php echo $notif['contacto_profesional']; ?>">
                        <button type="submit" name="accion" value="aceptar">Aceptar</button>
                        <button type="submit" name="accion" value="rechazar">Rechazar</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No tienes notificaciones pendientes.</p>
        <?php endif; ?>

    <?php elseif ($tipoUsuario == 'empresa'): ?>
        <?php if ($notificaciones && $notificaciones->num_rows > 0): ?>
            <?php while ($notif = $notificaciones->fetch_assoc()): ?>
                <div class="notificacion">
                    <h3><?php echo $notif['mensaje']; ?></h3>
                    <p>Fecha: <?php echo $notif['fecha']; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No tienes respuestas a tus solicitudes.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
