<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?? 'iTECH Contrataciones' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

<!-- ══════════ HEADER ══════════ -->
<header class="header-principal">
    <div class="header-contenedor">
        <div class="header-logo">
            <div class="logo-icono">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <h1 class="logo-titulo">iTECH Contrataciones</h1>
                <p class="logo-subtitulo">Sistema de Gestión de Colaboradores</p>
            </div>
        </div>

        <nav class="header-nav">
            <?php
                $accionActual = $_GET['action'] ?? 'formulario';
            ?>
            <a href="index.php?action=formulario"
               class="nav-link <?= $accionActual === 'formulario' ? 'nav-link--activo' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
                Registrar
            </a>
            <a href="index.php?action=reporte"
               class="nav-link <?= $accionActual === 'reporte' ? 'nav-link--activo' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                </svg>
                Reporte
            </a>
        </nav>
    </div>
</header>

<!-- ══════════ MAIN ══════════ -->
<main class="contenido-principal">
