<?php
/**
 * Limpia y normaliza todas las entradas del sistema antes de guardarlas.
 * Convierte automáticamente Nombres y Apellidos
 *   "HILDA" →  "Hilda"
 */
class Sanitizador {

    
    public static function cadena(mixed $valor): string {
        if (!is_string($valor)) {
            $valor = (string) $valor;
        }
        return htmlspecialchars(strip_tags(trim($valor)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Convierte texto a Formato Título respetando caracteres acentuados.
     */
    public static function formatoTitulo(mixed $valor): string {
        $valor = self::cadena($valor);
        return mb_convert_case(mb_strtolower($valor, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Extrae solo el valor entero de una cadena.
     */
    public static function entero(mixed $valor): int {
        return (int) filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Extrae solo el valor decimal (permite punto flotante).
     */
    public static function decimal(mixed $valor): float {
        $limpio = filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return (float) $limpio;
    }

    /**
     * Sanitiza una dirección de correo electrónico.
     */
    public static function correo(mixed $valor): string {
        return (string) filter_var(trim((string) $valor), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Limpia un número de teléfono dejando solo dígitos, +, -, (, ), espacios.
     */
    public static function telefono(mixed $valor): string {
        return preg_replace('/[^\d\+\-\(\)\s]/', '', (string) $valor);
    }

    /**
     * Sanitiza texto largo (como motivo_baja), preservando saltos de línea.
     */
    public static function textoLargo(mixed $valor): string {
        $valor = strip_tags(trim((string) $valor));
        return htmlspecialchars($valor, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
