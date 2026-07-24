<?php
/**
 * Genera y verifica firmas digitales HMAC-SHA256 para garantizar
 * la integridad de los datos sensibles de perfiles laborales.
 * Usa hash_hmac() con SHA-256 (función OpenSSL subyacente)
 */
class SeguridadOpenSSL {

    /* Clave secreta para HMAC — cambiar en producción */
    private const CLAVE_SECRETA = 'iTECH_2026_$3cur1ty_K3y_C0ntr4t4c10n3s!_UTP';
    private const ALGORITMO     = 'sha256';
    private const SEPARADOR     = '|';

    /**
     * Genera la firma digital HMAC-SHA256 de los datos del perfil laboral.
     *
     * @param array 
     *                      
     * @return string       
     */
    public static function generarFirma(array $datos): string {
        $cadena = self::construirCadena($datos);
        return hash_hmac(self::ALGORITMO, $cadena, self::CLAVE_SECRETA);
    }

    /**
     * Verifica si los datos actuales coinciden exactamente con la firma almacenada.
     * Usa hash_equals() para prevenir ataques de temporización.
     * @param array  
     * @param string 
     * @return bool           
     */
    public static function verificarFirma(array $datos, string $firma): bool {
        if (empty($firma)) return false;
        $firmaEsperada = self::generarFirma($datos);
        return hash_equals($firmaEsperada, $firma);
    }

    /**
     * Construye la cadena canónica a firmar/verificar.
     * Orden estricto: salario|id_empleado|id_tipo_planilla|id_ocupacion|fecha_inicio
     */
    private static function construirCadena(array $datos): string {
        return implode(self::SEPARADOR, [
            number_format((float) $datos['salario'], 2, '.', ''),
            (string) $datos['id_empleado'],
            (string) $datos['id_tipo_planilla'],
            (string) $datos['id_ocupacion'],
            (string) $datos['fecha_inicio'],
        ]);
    }

    /**
     * Método vacío para compatibilidad — HMAC no necesita inicialización.
     */
    public static function inicializar(): void {}
}