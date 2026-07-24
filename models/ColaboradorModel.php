<?php
require_once __DIR__ . '/../config/Conexion.php';

/**
 * Gestiona todas las operaciones de BD sobre la tabla `colaboradores`.
 */
class ColaboradorModel {

    private Conexion $db;

    public function __construct() {
        $this->db = Conexion::getInstancia();
    }

    /*
       LECTURA
   */

    /** Busca un colaborador por número de identidad. */
    public function obtenerPorIdentidad(string $identidad): array|false {
        $stmt = $this->db->consulta(
            'SELECT * FROM colaboradores WHERE identidad = ? LIMIT 1',
            [$identidad]
        );
        return $stmt->fetch();
    }

    /** Busca un colaborador por ID (PK). */
    public function obtenerPorId(int $id): array|false {
        $stmt = $this->db->consulta(
            'SELECT * FROM colaboradores WHERE id = ? LIMIT 1',
            [$id]
        );
        return $stmt->fetch();
    }

    /**
     *colaboradores con todos sus perfiles laborales
     */
    public function obtenerTodosConPerfiles(): array {
        $sql = "
            SELECT
                -- Datos del colaborador
                c.id           AS colaborador_id,
                c.identidad,
                c.nombre,
                c.apellido,
                c.edad,
                c.tipo_sangre,
                c.sexo,
                c.nacionalidad,
                c.ruta,
                c.correo,
                c.celular,
                -- Datos del perfil laboral
                pl.id              AS perfil_id,
                pl.id_empleado,
                pl.id_ocupacion,
                pl.id_tipo_planilla,
                pl.departamento,
                pl.salario,
                pl.fecha_inicio,
                pl.fecha_fin,
                pl.es_activo,
                pl.empleado_activo,
                pl.motivo_baja,
                pl.firma_digital,
                pl.created_at      AS perfil_creado,
                -- Catálogos
                o.nombre           AS ocupacion,
                tp.nombre          AS tipo_planilla
            FROM colaboradores c
            LEFT OUTER JOIN perfiles_laborales pl
                ON pl.id_empleado = c.id
            LEFT OUTER JOIN cat_ocupaciones o
                ON o.id = pl.id_ocupacion
            LEFT OUTER JOIN cat_tipos_planilla tp
                ON tp.id = pl.id_tipo_planilla
            ORDER BY c.id DESC, pl.id DESC
        ";
        $stmt = $this->db->consulta($sql);
        return $stmt->fetchAll();
    }

    /* 
       ESCRITURA
     */

    /**
     * Inserta un colaborador nuevo.
     * @return string ID insertado
     */
    public function crear(array $datos): string {
        return $this->db->insertar(
            'INSERT INTO colaboradores
               (identidad, nombre, apellido, edad, tipo_sangre, sexo, nacionalidad, ruta, correo, celular)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $datos['identidad'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['edad'],
                $datos['tipo_sangre'],
                $datos['sexo'],
                $datos['nacionalidad'],
                $datos['ruta'],
                $datos['correo'],
                $datos['celular'],
            ]
        );
    }
}
