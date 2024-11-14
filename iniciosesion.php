<?php
include 'conexion.php';
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    
    $sql = "SELECT nombre, identificacion, tipoUsuario, contraseña FROM Usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($nombre, $identificacion, $tipoUsuario, $hash);
        $stmt->fetch();

        
        if (password_verify($contraseña, $hash)) {
            
            $_SESSION['identificacion'] = $identificacion;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['tipoUsuario'] = $tipoUsuario;

            
            header("Location: Perfil.php");
            exit(); 
        } else {
            echo "Credenciales incorrectas.";
        }
    } else {
        echo "Credenciales incorrectas.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inicio de Sesión</title>
    <link rel="stylesheet" href="iniciosesionstyle.css">
    <script src="Proyecto.js" defer></script>
</head>
<body>
    <section class="form-login">
        <h5>Iniciar Sesión</h5>
        <form method="POST" action="">
            <input class="controls" type="email" name="correo" placeholder="Correo" required>
            <input class="controls" type="password" name="contraseña" placeholder="Contraseña" required>
            <input class="buttons" type="submit" value="Iniciar Sesión">
        </form>
        <p><a href="Registro.php">¿No estás registrado?</a></p>
    </section>
    <script src="Proyecto.js"></script>
</body>
</html>
