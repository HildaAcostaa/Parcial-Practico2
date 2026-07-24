<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../models/ColaboradorModel.php';
require_once __DIR__ . '/../models/PerfilLaboralModel.php';
require_once __DIR__ . '/../helpers/Validador.php';
require_once __DIR__ . '/../helpers/Sanitizador.php';
require_once __DIR__ . '/../helpers/SeguridadOpenSSL.php';

/**
 * ColaboradorController
 * Maneja el flujo completo del formulario de registro
 */
class ColaboradorController {

    private ColaboradorModel  $colModel;
    private PerfilLaboralModel $perfilModel;

    public function __construct() {
        $this->colModel   = new ColaboradorModel();
        $this->perfilModel = new PerfilLaboralModel();
    }

    /*
       MOSTRAR FORMULARIO
    */

    public function mostrarFormulario(): void {
        $catalogos = $this->perfilModel->obtenerCatalogos();
        $mensaje   = $_SESSION['mensaje'] ?? null;
        $errores   = $_SESSION['errores'] ?? [];
        $anterior  = $_SESSION['datos_anteriores'] ?? [];
        unset($_SESSION['mensaje'], $_SESSION['errores'], $_SESSION['datos_anteriores']);

        require_once __DIR__ . '/../views/formulario.php';
    }

    /*
       PROCESAR FORMULARIO
    */

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('formulario');
        }

        /* ──  Sanitizar ── */
        $identidad    = Sanitizador::cadena($_POST['identidad']    ?? '');
        $nombre       = Sanitizador::formatoTitulo($_POST['nombre']  ?? '');
        $apellido     = Sanitizador::formatoTitulo($_POST['apellido'] ?? '');
        $edad         = Sanitizador::entero($_POST['edad']         ?? 0);
        $tipo_sangre  = Sanitizador::cadena($_POST['tipo_sangre']  ?? '');
        $sexo         = Sanitizador::cadena($_POST['sexo']         ?? '');
        $nacionalidad = Sanitizador::formatoTitulo($_POST['nacionalidad'] ?? '');
        $ruta         = Sanitizador::cadena($_POST['ruta']         ?? '');
        $correo       = Sanitizador::correo($_POST['correo']       ?? '');
        $celular      = Sanitizador::telefono($_POST['celular']    ?? '');

        $id_ocupacion     = Sanitizador::entero($_POST['id_ocupacion']     ?? 0);
        $departamento     = Sanitizador::cadena($_POST['departamento']     ?? '');
        $id_tipo_planilla = Sanitizador::entero($_POST['id_tipo_planilla'] ?? 0);
        $salario          = Sanitizador::decimal($_POST['salario']         ?? '0');
        $fecha_inicio     = Sanitizador::cadena($_POST['fecha_inicio']     ?? '');
        $fecha_fin        = Sanitizador::cadena($_POST['fecha_fin']        ?? '');
        $motivo_baja      = Sanitizador::textoLargo($_POST['motivo_baja'] ?? '');

        /* ──  Validar ── */
        Validador::resetear();

        Validador::requerido($identidad,    'identidad',    'Identidad');
        Validador::requerido($nombre,       'nombre',       'Nombre');
        Validador::requerido($apellido,     'apellido',     'Apellido');
        Validador::edad($edad);
        Validador::requerido($tipo_sangre,  'tipo_sangre',  'Tipo de Sangre');
        Validador::requerido($sexo,         'sexo',         'Sexo');
        Validador::requerido($nacionalidad, 'nacionalidad', 'Nacionalidad');
        Validador::requerido($ruta,         'ruta',         'Ruta');
        Validador::correo($correo);
        Validador::celular($celular);
        Validador::catalogoId($id_ocupacion,     'id_ocupacion');
        Validador::requerido($departamento,  'departamento', 'Departamento');
        Validador::catalogoId($id_tipo_planilla, 'id_tipo_planilla');
        Validador::salario($salario);
        Validador::fecha($fecha_inicio, 'fecha_inicio');

        if ($fecha_fin !== '') {
            Validador::fecha($fecha_fin, 'fecha_fin');
            if (!Validador::hayErrores() || empty(Validador::getErrores()['fecha_fin'])) {
                Validador::rangoFechas($fecha_inicio, $fecha_fin);
            }
            if ($fecha_fin !== '' && $motivo_baja === '') {
                Validador::requerido('', 'motivo_baja', 'Motivo de Baja');
            }
        }

        if (Validador::hayErrores()) {
            $_SESSION['errores']         = Validador::getErrores();
            $_SESSION['datos_anteriores'] = $_POST;
            $_SESSION['mensaje']         = [
                'tipo'  => 'error',
                'texto' => 'Por favor corrija los errores marcados en el formulario.',
            ];
            $this->redirigir('formulario');
        }

        /* ──  Persistir ── */
        try {
            /* si el colaborador ya existe reutilizamos su ID */
            $colaborador = $this->colModel->obtenerPorIdentidad($identidad);

            if (!$colaborador) {
                $idEmpleado = (int) $this->colModel->crear([
                    'identidad'    => $identidad,
                    'nombre'       => $nombre,
                    'apellido'     => $apellido,
                    'edad'         => $edad,
                    'tipo_sangre'  => $tipo_sangre,
                    'sexo'         => $sexo,
                    'nacionalidad' => $nacionalidad,
                    'ruta'         => $ruta,
                    'correo'       => $correo,
                    'celular'      => $celular,
                ]);
            } else {
                $idEmpleado = (int) $colaborador['id'];
            }

            /* Lógica de Promoción: desactivar perfiles anteriores */
            $this->perfilModel->desactivarPerfilesActivos($idEmpleado);

            /* Lógica de empleado_activo: 0 si tiene fecha_fin */
            $empleadoActivo = ($fecha_fin === '') ? 1 : 0;

            /* Generar firma digital OpenSSL */
            SeguridadOpenSSL::inicializar();
            $firma = SeguridadOpenSSL::generarFirma([
                'salario'          => $salario,
                'id_empleado'      => $idEmpleado,
                'id_tipo_planilla' => $id_tipo_planilla,
                'id_ocupacion'     => $id_ocupacion,
                'fecha_inicio'     => $fecha_inicio,
            ]);

            /* Crear nuevo perfil laboral */
            $this->perfilModel->crear([
                'id_empleado'      => $idEmpleado,
                'id_ocupacion'     => $id_ocupacion,
                'id_tipo_planilla' => $id_tipo_planilla,
                'departamento'     => $departamento,
                'salario'          => $salario,
                'fecha_inicio'     => $fecha_inicio,
                'fecha_fin'        => $fecha_fin,
                'empleado_activo'  => $empleadoActivo,
                'motivo_baja'      => $motivo_baja,
                'firma_digital'    => $firma,
            ]);

            $accion = $colaborador ? 'actualizado (promoción)' : 'registrado';
            $_SESSION['mensaje'] = [
                'tipo'  => 'exito',
                'texto' => "✅ Colaborador <strong>{$nombre} {$apellido}</strong> {$accion} exitosamente.",
            ];

        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'Duplicate') !== false && strpos($msg, 'correo') !== false) {
                $msg = 'El correo electrónico ya está registrado en el sistema.';
            } elseif (strpos($msg, 'Duplicate') !== false && strpos($msg, 'identidad') !== false) {
                $msg = 'Ya existe un colaborador con esa identidad registrada.';
            }
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Error de base de datos: ' . $msg];
        } catch (Exception $e) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Error inesperado: ' . $e->getMessage()];
        }

        $this->redirigir('formulario');
    }

    /* ── Utilidad de redirección (patrón PRG) ── */
    private function redirigir(string $action): never {
        header("Location: index.php?action={$action}");
        exit;
    }
}
