<?php
session_start();
include 'conexion.php';


if (!isset($_SESSION['identificacion'])) {
    header("Location: iniciosesion.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="InicioPrincipal.css">
</head>

<body>
    <header class="header">
        <div class="menu container">
            <a href="images/images/com" class="logo">Logo</a>
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
        <div class="header-content container">
            <h1>Bienvenido a nuestra Plataforma de Conexión Profesional</h1>
            <p>
                Nuestra plataforma está diseñada para conectar empresas con profesionales dispuestos a trabajar en
                tareas específicas
                o proyectos de corta duración. Aquí encontrarás una experiencia simple y fácil de usar, donde puedes
                registrarte,
                crear tu perfil y empezar a trabajar o contratar en solo unos pocos pasos.
                Te explicamos cómo funciona y qué puedes hacer en nuestra página
            </p>
        </div>
    </header>
    <section class="content">
        <div class="content-content container">
            <h2>¿Para Quién Es Esta Plataforma?</h2>
            <p class="txt-p">
                Empresas que necesitan realizar trabajos específicos sin comprometerse a largo plazo,
                ideal para tareas puntuales o proyectos cortos.
            </p>
            <p class="txt-p">
                Profesionales que buscan flexibilidad en su trabajo y están abiertos a colaborar
                en diversos proyectos de forma independiente y en horarios variables.
            </p>

        </div>

    </section>
</body>

</html>