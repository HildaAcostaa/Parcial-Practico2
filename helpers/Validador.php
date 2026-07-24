<?php

class Validador {

    private static array $errores = [];

    /** Limpia el registro de errores antes de una nueva validación. */
    public static function resetear(): void {
        self::$errores = [];
    }

    /** Retorna todos los errores acumulados. */
    public static function getErrores(): array {
        return self::$errores;
    }

    /** ¿Hubo algún error? */
    public static function hayErrores(): bool {
        return !empty(self::$errores);
    }

    /* 
       VALIDACIONES GENERALES
    */

    /**
     * Campo requerido (no vacío).
     */
    public static function requerido(string $valor, string $campo, string $etiqueta = ''): bool {
        if (trim($valor) === '') {
            $label = $etiqueta ?: ucfirst($campo);
            self::$errores[$campo] = "El campo «{$label}» es obligatorio.";
            return false;
        }
        return true;
    }

    /**
     * Valida formato de correo electrónico.
     */
    public static function correo(string $correo, string $campo = 'correo'): bool {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            self::$errores[$campo] = 'El correo electrónico no tiene un formato válido (ejemplo@dominio.com).';
            return false;
        }
        return true;
    }

    /**
     * Valida número de celular (7–15 dígitos; admite espacios, guiones y paréntesis).
     */
    public static function celular(string $celular, string $campo = 'celular'): bool {
        $limpio = preg_replace('/[\s\-\+\(\)]/', '', $celular);
        if (!preg_match('/^\d{7,15}$/', $limpio)) {
            self::$errores[$campo] = 'El número de celular debe contener entre 7 y 15 dígitos.';
            return false;
        }
        return true;
    }

    /**
     * Valida que la edad sea un número entero entre 18 y 80.
     */
    public static function edad(mixed $edad, string $campo = 'edad'): bool {
        if (!is_numeric($edad) || (int)$edad < 18 || (int)$edad > 80) {
            self::$errores[$campo] = 'La edad debe ser un número entero entre 18 y 80 años.';
            return false;
        }
        return true;
    }

    /**
     * Valida que el salario sea un número positivo.
     */
    public static function salario(mixed $salario, string $campo = 'salario'): bool {
        if (!is_numeric($salario) || (float)$salario < 0) {
            self::$errores[$campo] = 'El salario debe ser un valor numérico positivo.';
            return false;
        }
        return true;
    }

    /**
     * Valida formato de fecha ISO (YYYY-MM-DD).
     */
    public static function fecha(string $fecha, string $campo = 'fecha'): bool {
        $dt = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$dt || $dt->format('Y-m-d') !== $fecha) {
            self::$errores[$campo] = "La fecha «{$campo}» debe tener formato YYYY-MM-DD.";
            return false;
        }
        return true;
    }

    /**
     * Valida que fecha_fin sea posterior a fecha_inicio.
     */
    public static function rangoFechas(string $fechaInicio, string $fechaFin): bool {
        if ($fechaFin <= $fechaInicio) {
            self::$errores['fecha_fin'] = 'La Fecha Fin debe ser posterior a la Fecha Inicio.';
            return false;
        }
        return true;
    }

    /**
     * Valida que un entero provenga de un catálogo (> 0).
     */
    public static function catalogoId(mixed $id, string $campo): bool {
        if (!is_numeric($id) || (int)$id <= 0) {
            self::$errores[$campo] = "Debe seleccionar una opción válida para «{$campo}».";
            return false;
        }
        return true;
    }

    /**
     * Valida longitud máxima de una cadena.
     */
    public static function longitud(string $valor, int $max, string $campo): bool {
        if (mb_strlen($valor, 'UTF-8') > $max) {
            self::$errores[$campo] = "El campo «{$campo}» no puede superar {$max} caracteres.";
            return false;
        }
        return true;
    }
}
