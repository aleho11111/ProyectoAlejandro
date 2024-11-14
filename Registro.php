<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = $_POST['nombre'];
    $identificacion = $_POST['identificacion'];
    $tipoUsuario = $_POST['tipoUsuario'];
    $correo = $_POST['correo'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);

    
    $sqlCheck = "SELECT * FROM Usuarios WHERE identificacion = ? OR correo = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ss", $identificacion, $correo);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        
        $existingUser = $resultCheck->fetch_assoc();
        if ($existingUser['identificacion'] === $identificacion) {
            echo "Error: Ya existe un usuario con esta identificación.";
        } elseif ($existingUser['correo'] === $correo) {
            echo "Error: Ya existe un usuario con este correo electrónico.";
        }
    } else {
        
        $sqlInsert = "INSERT INTO Usuarios (identificacion, nombre, tipoUsuario, correo, contraseña) VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("sssss", $identificacion, $nombre, $tipoUsuario, $correo, $contraseña);

        if ($stmtInsert->execute()) {
            
            if ($tipoUsuario == 'profesional') {
                $sqlPerfil = "INSERT INTO PerfilProfesional (identificacion) VALUES (?)";
            } elseif ($tipoUsuario == 'empresa') {
                $sqlPerfil = "INSERT INTO PerfilEmpresarial (identificacion) VALUES (?)";
            }
            
            if (isset($sqlPerfil)) {
                $stmtPerfil = $conn->prepare($sqlPerfil);
                $stmtPerfil->bind_param("s", $identificacion);
                $stmtPerfil->execute();
            }

            
            header("Location: iniciosesion.php");
            exit();
        } else {
            echo "Error: " . $stmtInsert->error;
        }
    }

    
    $stmtCheck->close();
    $stmtInsert->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    
    <link rel="stylesheet" href="RegistroStyle.css"> 
    <script src="Proyecto.js" defer></script>
</head>
<body>
    <section class="form-register">
        <h4>Registrarse</h4>
        <form method="POST" class="formulario">
            <input type="text" name="nombre" class="controls" placeholder="Nombre" required>
            <input type="text" name="identificacion" class="controls" placeholder="Identificación" required>
            <select name="tipoUsuario" class="controls">
                <option value="profesional">Profesional</option>
                <option value="empresa">Empresa</option>
            </select>
            <input type="email" name="correo" class="controls" placeholder="Correo electrónico" required>
            <input type="password" name="contraseña" class="controls" placeholder="Contraseña" required>
            <input type="submit" value="Registrar" class="buttons">
        </form>
        <p><a href="iniciosesion.php">¿Ya tienes una cuenta?</a></p>
    </section>
</body>
</html>
