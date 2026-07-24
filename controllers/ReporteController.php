<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../models/ColaboradorModel.php';
require_once __DIR__ . '/../helpers/SeguridadOpenSSL.php';

/**
 * ReporteController
 * carga la vista con auditoría de integridad OpenSSL.
 *  genera y descarga el archivo CSV.
 */
class ReporteController {

    private ColaboradorModel $colModel;

    public function __construct() {
        $this->colModel = new ColaboradorModel();
        SeguridadOpenSSL::inicializar();
    }

    /*
       VISTA DE REPORTE
    */

    public function mostrarReporte(): void {
        $registros     = $this->colModel->obtenerTodosConPerfiles();
        $colaboradores = $this->procesarRegistros($registros);

        require_once __DIR__ . '/../views/reporte.php';
    }

    /* 
       EXPORTAR A EXCEL
    */

    public function exportarExcel(): void {
        $registros     = $this->colModel->obtenerTodosConPerfiles();
        $colaboradores = $this->procesarRegistros($registros);

        $nombre_archivo = 'reporte_iTECH_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$nombre_archivo}\"");
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        /* BOM UTF-8 para que Excel lo abra correctamente */
        fputs($out, "\xEF\xBB\xBF");

        /* Encabezado */
        fputcsv($out, [
            'ID Empleado', 'Identidad', 'Nombre', 'Apellido', 'Edad',
            'Tipo Sangre', 'Sexo', 'Nacionalidad', 'Ruta', 'Correo', 'Celular',
            'ID Perfil', 'Ocupación', 'Tipo Planilla', 'Departamento',
            'Salario', 'Fecha Inicio', 'Fecha Fin',
            'Perfil Activo', 'Empleado Activo', 'Motivo Baja', 'Integridad',
        ], ';');

        /* Filas */
        foreach ($colaboradores as $col) {
            if (empty($col['perfiles'])) {
                /* Colaborador sin perfil laboral */
                fputcsv($out, [
                    $col['id'],
                    $col['identidad'],
                    $col['nombre'],
                    $col['apellido'],
                    $col['edad'],
                    $col['tipo_sangre'],
                    $col['sexo'],
                    $col['nacionalidad'],
                    $col['ruta'],
                    $col['correo'],
                    $col['celular'],
                    '', '', '', '', '', '', '', '', '', '', 'Sin perfil',
                ], ';');
            } else {
                foreach ($col['perfiles'] as $p) {
                    fputcsv($out, [
                        $col['id'],
                        $col['identidad'],
                        $col['nombre'],
                        $col['apellido'],
                        $col['edad'],
                        $col['tipo_sangre'],
                        $col['sexo'],
                        $col['nacionalidad'],
                        $col['ruta'],
                        $col['correo'],
                        $col['celular'],
                        $p['perfil_id'],
                        $p['ocupacion']     ?? '',
                        $p['tipo_planilla'] ?? '',
                        $p['departamento'],
                        number_format((float)$p['salario'], 2, '.', ''),
                        $p['fecha_inicio'],
                        $p['fecha_fin'] ?? 'N/A',
                        $p['es_activo']       ? 'Activo'  : 'Inactivo',
                        $p['empleado_activo'] ? 'Activo'  : 'Baja',
                        $p['motivo_baja'] ?? '',
                        $p['integridad']      ? 'VERIFICADA' : 'CORROMPIDA',
                    ], ';');
                }
            }
        }

        fclose($out);
        exit;
    }

    /* 
       PROCESAMIENTO INTERNO
    */

    /**
     * Agrupa filas planas por colaborador y verifica la firma digital de
     * cada perfil laboral usando SeguridadOpenSSL.
     */
    private function procesarRegistros(array $filas): array {
        $colaboradores = [];

        foreach ($filas as $fila) {
            $id = (int) $fila['colaborador_id'];

            if (!isset($colaboradores[$id])) {
                $colaboradores[$id] = [
                    'id'           => $id,
                    'identidad'    => $fila['identidad'],
                    'nombre'       => $fila['nombre'],
                    'apellido'     => $fila['apellido'],
                    'edad'         => $fila['edad'],
                    'tipo_sangre'  => $fila['tipo_sangre'],
                    'sexo'         => $fila['sexo'],
                    'nacionalidad' => $fila['nacionalidad'],
                    'ruta'         => $fila['ruta'],
                    'correo'       => $fila['correo'],
                    'celular'      => $fila['celular'],
                    'perfiles'     => [],
                ];
            }

            /* Solo añadir si existe perfil*/
            if ($fila['perfil_id'] !== null) {
                $integridad = false;
                if (!empty($fila['firma_digital'])) {
                    $integridad = SeguridadOpenSSL::verificarFirma([
                        'salario'          => $fila['salario'],
                        'id_empleado'      => $fila['id_empleado'],
                        'id_tipo_planilla' => $fila['id_tipo_planilla'],
                        'id_ocupacion'     => $fila['id_ocupacion'],
                        'fecha_inicio'     => $fila['fecha_inicio'],
                    ], $fila['firma_digital']);
                }

                $colaboradores[$id]['perfiles'][] = [
                    'perfil_id'        => $fila['perfil_id'],
                    'id_empleado'      => $fila['id_empleado'],
                    'id_ocupacion'     => $fila['id_ocupacion'],
                    'id_tipo_planilla' => $fila['id_tipo_planilla'],
                    'ocupacion'        => $fila['ocupacion'],
                    'tipo_planilla'    => $fila['tipo_planilla'],
                    'departamento'     => $fila['departamento'],
                    'salario'          => $fila['salario'],
                    'fecha_inicio'     => $fila['fecha_inicio'],
                    'fecha_fin'        => $fila['fecha_fin'],
                    'es_activo'        => (bool) $fila['es_activo'],
                    'empleado_activo'  => (bool) $fila['empleado_activo'],
                    'motivo_baja'      => $fila['motivo_baja'],
                    'firma_digital'    => $fila['firma_digital'],
                    'perfil_creado'    => $fila['perfil_creado'],
                    'integridad'       => $integridad,
                ];
            }
        }

        return array_values($colaboradores);
    }
}
