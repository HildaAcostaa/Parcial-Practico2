<?php
/**
 * iTECH Contrataciones — Principal
 * Despacha las peticiones a los controladores según el parámetro `action`.
 */

declare(strict_types=1);

/* Iniciar sesión al comienzo de TODA petición */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Autocargar clases base */
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/helpers/Validador.php';
require_once __DIR__ . '/helpers/Sanitizador.php';
require_once __DIR__ . '/helpers/SeguridadOpenSSL.php';
require_once __DIR__ . '/models/ColaboradorModel.php';
require_once __DIR__ . '/models/PerfilLaboralModel.php';
require_once __DIR__ . '/controllers/ColaboradorController.php';
require_once __DIR__ . '/controllers/ReporteController.php';

/* Determinar acción solicitada (solo letras y guiones para prevenir path traversal) */
$action = preg_replace('/[^a-z_]/', '', strtolower($_GET['action'] ?? 'formulario'));

/* ── Despacho ── */
switch ($action) {

    case 'guardar':
        $controller = new ColaboradorController();
        $controller->guardar();
        break;

    case 'reporte':
        $controller = new ReporteController();
        $controller->mostrarReporte();
        break;

    case 'exportar':
        $controller = new ReporteController();
        $controller->exportarExcel();
        break;

    case 'formulario':
    default:
        $controller = new ColaboradorController();
        $controller->mostrarFormulario();
        break;
}
