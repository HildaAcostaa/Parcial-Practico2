<?php
require_once __DIR__ . '/../config/Conexion.php';

/**
 * Gestiona la tabla `perfiles_laborales` y los catálogos asociados.
 * Implementa la lógica de promoción: al crear un nuevo perfil,
 * los anteriores se marcan es_activo = 0 automáticamente.
 */
class PerfilLaboralModel {

    private Conexion $db;

    public function __construct() {
        $this->db = Conexion::getInstancia();
    }

    /*
       LÓGICA DE PROMOCIÓN
     */

    public function desactivarPerfilesActivos(int $idEmpleado): bool {
        return $this->db->ejecutar(
            'UPDATE perfiles_laborales
             SET    es_activo = 0
             WHERE  id_empleado = ? AND es_activo = 1',
            [$idEmpleado]
        );
    }

    /*
       ESCRITURA
    */

    /**
     * Inserta un nuevo perfil laboral con firma digital.
     * @return string ID insertado
     */
    public function crear(array $datos): string {
        return $this->db->insertar(
            'INSERT INTO perfiles_laborales
               (id_empleado, id_ocupacion, id_tipo_planilla, departamento,
                salario, fecha_inicio, fecha_fin, es_activo, empleado_activo,
                motivo_baja, firma_digital)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)',
            [
                (int)   $datos['id_empleado'],
                (int)   $datos['id_ocupacion'],
                (int)   $datos['id_tipo_planilla'],
                        $datos['departamento'],
                (float) $datos['salario'],
                        $datos['fecha_inicio'],
                        ($datos['fecha_fin'] !== '' && $datos['fecha_fin'] !== null)
                            ? $datos['fecha_fin'] : null,
                (int)   $datos['empleado_activo'],
                        ($datos['motivo_baja'] !== '' && $datos['motivo_baja'] !== null)
                            ? $datos['motivo_baja'] : null,
                        $datos['firma_digital'],
            ]
        );
    }

    /*
       CATÁLOGOS
    */

    public function obtenerCatalogos(): array {
        $ocupaciones = $this->db
            ->consulta('SELECT * FROM cat_ocupaciones ORDER BY nombre ASC')
            ->fetchAll();

        $planillas = $this->db
            ->consulta('SELECT * FROM cat_tipos_planilla ORDER BY nombre ASC')
            ->fetchAll();

        return [
            'ocupaciones' => $ocupaciones,
            'planillas'   => $planillas,
        ];
    }
}
