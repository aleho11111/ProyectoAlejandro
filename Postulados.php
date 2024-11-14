<?php
session_start();
include 'conexion.php';


if (!isset($_SESSION['identificacion'])) {
    header("Location: iniciosesion.php");
    exit();
}


$is_empresa = ($_SESSION['tipoUsuario'] == 'empresa');


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profesional_id']) && $is_empresa) {
    $profesional_id = $_POST['profesional_id'];
    $empresa_id = $_SESSION['identificacion']; 

    $sql = "INSERT INTO notificacionesProfesional (receptor_identificacion, emisor_identificacion, fecha, estado)
            VALUES (?, ?, NOW(), 'pendiente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $profesional_id, $empresa_id);

    if ($stmt->execute()) {
        $mensaje = "La solicitud de contratación ha sido enviada al profesional.";
    } else {
        $mensaje = "Error al enviar la solicitud de contratación.";
    }
    $stmt->close();
}


$busqueda_habilidades = isset($_GET['habilidades']) ? $_GET['habilidades'] : '';
$busqueda_horario = isset($_GET['horario']) ? $_GET['horario'] : '';


$sql = "SELECT u.nombre, p.habilidades, p.contacto, p.horario, p.foto, p.fecha_postulacion, p.profesional_identificacion
        FROM Postulaciones AS p
        JOIN Usuarios AS u ON p.profesional_identificacion = u.identificacion
        WHERE (p.habilidades LIKE ? OR ? = '')
        AND (p.horario LIKE ? OR ? = '')";

$stmt = $conn->prepare($sql);
$busqueda_habilidades_param = "%$busqueda_habilidades%";
$busqueda_horario_param = "%$busqueda_horario%";
$stmt->bind_param("ssss", $busqueda_habilidades_param, $busqueda_habilidades, $busqueda_horario_param, $busqueda_horario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulados</title>
    <link rel="stylesheet" href="Postuladosstyle.css">
</head>
<body>
    <header class="header">
        <div class="menu container">
            <a href="#" class="logo">Logo</a>
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

    <main>
        <h1>Lista de Postulados</h1>

        
        <form method="GET" action="Postulados.php">
            <input type="text" class="controls" name="habilidades" placeholder="Buscar por habilidades" value="<?php echo htmlspecialchars($busqueda_habilidades); ?>">
            <input type="text" class="controls" name="horario" placeholder="Buscar por horario" value="<?php echo htmlspecialchars($busqueda_horario); ?>">
            <button class="boton1" type="submit">Buscar</button>
        </form>

        
        <?php if (isset($mensaje)): ?>
            <p><?php echo $mensaje; ?></p>
        <?php endif; ?>

        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Habilidades</th>
                        <th>Horario</th>
                        <th>Contacto</th>
                        <th>Fecha de Postulación</th>
                        <?php if ($is_empresa): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if ($row['foto']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>" alt="Foto de perfil" width="50" height="50">
                                <?php else: ?>
                                    Sin foto
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['habilidades']); ?></td>
                            <td><?php echo htmlspecialchars($row['horario']); ?></td>
                            <td><?php echo htmlspecialchars($row['contacto']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_postulacion']); ?></td>

                            <?php if ($is_empresa): ?>
                                <td>
                                    <form action="Postulados.php" method="post">
                                        <input type="hidden" name="profesional_id" value="<?php echo $row['profesional_identificacion']; ?>">
                                        <button class="boton2" type="submit">Enviar Solicitud de Contratación</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron postulados con los criterios de búsqueda especificados.</p>
        <?php endif; ?>

        <?php
        
        $stmt->close();
        $conn->close();
        ?>
    </main>
</body>
</html>