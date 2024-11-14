<?php
session_start();
include 'conexion.php';


if (!isset($_SESSION['identificacion'])) {
    header("Location: iniciosesion.php");
    exit();
}

$tipoUsuario = $_SESSION['tipoUsuario'];
$identificacion = $_SESSION['identificacion'];

$habilidades = $contacto = $horario = $foto = $direccion = "";


if ($tipoUsuario == 'profesional') {
    $sql = "SELECT habilidades, contacto, horario, foto FROM PerfilProfesional WHERE identificacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $identificacion);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($habilidades, $contacto, $horario, $foto);
    $stmt->fetch();
} else {
    $sql = "SELECT contacto, direccion FROM PerfilEmpresarial WHERE identificacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $identificacion);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($contacto, $direccion);
    $stmt->fetch();
}


$sqlCheckPostulacion = "SELECT * FROM Postulaciones WHERE profesional_identificacion = ?";
$stmtCheck = $conn->prepare($sqlCheckPostulacion);
$stmtCheck->bind_param("s", $identificacion);
$stmtCheck->execute();
$hasPostulacion = $stmtCheck->get_result()->num_rows > 0;
$stmtCheck->close();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['postular']) && !$hasPostulacion && $tipoUsuario == 'profesional') {
    $sql = "INSERT INTO Postulaciones (profesional_identificacion, habilidades, contacto, horario, foto) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $identificacion, $habilidades, $contacto, $horario, $foto);

    if ($stmt->execute()) {
        echo "Postulación realizada correctamente.";
    } else {
        echo "Error al realizar la postulación: " . $stmt->error;
    }

    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_postulacion']) && $hasPostulacion && $tipoUsuario == 'profesional') {
    $sql = "DELETE FROM Postulaciones WHERE profesional_identificacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $identificacion);

    if ($stmt->execute()) {
        echo "Postulación eliminada correctamente.";
    } else {
        echo "Error al eliminar la postulación: " . $stmt->error;
    }

    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    if ($tipoUsuario == 'profesional') {
        $habilidades = $_POST['habilidades'] ?? '';
        $contacto = $_POST['contacto'] ?? '';
        $horario = $_POST['horario'] ?? '';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto = file_get_contents($_FILES['foto']['tmp_name']);
            $sql = "UPDATE PerfilProfesional SET habilidades = ?, contacto = ?, horario = ?, foto = ? WHERE identificacion = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $habilidades, $contacto, $horario, $foto, $identificacion);
        } else {
            $sql = "UPDATE PerfilProfesional SET habilidades = ?, contacto = ?, horario = ? WHERE identificacion = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $habilidades, $contacto, $horario, $identificacion);
        }

        if ($stmt->execute()) {
            echo "Perfil actualizado correctamente.";

            
            if ($hasPostulacion) {
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $sqlUpdatePostulacion = "UPDATE Postulaciones SET habilidades = ?, contacto = ?, horario = ?, foto = ? WHERE profesional_identificacion = ?";
                    $stmtUpdate = $conn->prepare($sqlUpdatePostulacion);
                    $stmtUpdate->bind_param("sssss", $habilidades, $contacto, $horario, $foto, $identificacion);
                } else {
                    $sqlUpdatePostulacion = "UPDATE Postulaciones SET habilidades = ?, contacto = ?, horario = ? WHERE profesional_identificacion = ?";
                    $stmtUpdate = $conn->prepare($sqlUpdatePostulacion);
                    $stmtUpdate->bind_param("ssss", $habilidades, $contacto, $horario, $identificacion);
                }
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }

        } else {
            echo "Error al actualizar el perfil: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $contacto = $_POST['contacto'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $sql = "UPDATE PerfilEmpresarial SET contacto = ?, direccion = ? WHERE identificacion = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $contacto, $direccion, $identificacion);

        if ($stmt->execute()) {
            echo "Perfil actualizado correctamente.";
        } else {
            echo "Error al actualizar el perfil: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="Perfilstyle.css">
    <script src="Proyecto.js" defer></script>
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

    <?php if ($tipoUsuario == 'profesional'): ?>
        <section id="PerfilProfesional" class="form-Perfil">
            <h1>Perfil Profesional</h1>
            <form method="POST" enctype="multipart/form-data">
                <label for="foto" class="controls">Sube tu foto:</label>
                <input class="Profilepic" type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(event)">
                <?php if ($foto): ?>
                    <img class="Profilepic" id="preview" src="data:image/jpeg;base64,<?php echo base64_encode($foto); ?>" alt="Vista previa">
                <?php endif; ?>
                <input class="controls" type="text" name="habilidades" placeholder="Habilidades/Experiencia" value="<?php echo htmlspecialchars($habilidades); ?>">
                <input class="controls" type="text" name="contacto" placeholder="Contacto" value="<?php echo htmlspecialchars($contacto); ?>">
                <input class="controls" type="text" name="horario" placeholder="Horario de disponibilidad" value="<?php echo htmlspecialchars($horario); ?>">
                <input class="buttons" type="submit" name="actualizar" value="Actualizar Perfil">
            </form>

            
            <h2>Postúlate a una oferta</h2>
            <?php if (!$hasPostulacion): ?>
                <form method="POST">
                    <input class="buttons" type="submit" name="postular" value="Postularse">
                </form>
            <?php else: ?>
                <form method="POST">
                    <input class="buttons" type="submit" name="eliminar_postulacion" value="Eliminar Postulación">
                </form>
            <?php endif; ?>
        </section>
    <?php elseif ($tipoUsuario == 'empresa'): ?>
        <section id="PerfilEmpresarial" class="form-Perfil">
            <h1>Perfil de la Empresa</h1>
            <form method="POST">
                <input class="controls" type="text" name="contacto" placeholder="Contacto" value="<?php echo htmlspecialchars($contacto); ?>">
                <input class="controls" type="text" name="direccion" placeholder="Dirección de la Empresa" value="<?php echo htmlspecialchars($direccion); ?>">
                <input class="buttons" type="submit" name="actualizar" value="Actualizar Perfil">
            </form>
        </section>
    <?php endif; ?>
</body>
</html>
