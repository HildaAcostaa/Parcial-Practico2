<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

$dirClaves = __DIR__ . '/keys/';
$archPriv  = $dirClaves . 'private.pem';
$archPub   = $dirClaves . 'public.pem';

echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
<title>Setup — iTECH Contrataciones</title>
<style>
  body { font-family: -apple-system,sans-serif; max-width:640px; margin:3rem auto; padding:0 1.5rem; color:#2D1B18; }
  h1   { font-size:1.5rem; margin-bottom:1rem; }
  .ok  { background:#D4EDDA; border:1px solid #155724; color:#155724; padding:.75rem 1rem; border-radius:8px; margin-bottom:.75rem; }
  .err { background:#F8D7DA; border:1px solid #721C24; color:#721C24; padding:.75rem 1rem; border-radius:8px; margin-bottom:.75rem; }
  .inf { background:#EEF3FF; border:1px solid #C3D3F7; color:#1E40AF; padding:.75rem 1rem; border-radius:8px; margin-bottom:.75rem; font-size:.875rem; }
  code { background:#F0E6EA; padding:.1rem .4rem; border-radius:4px; font-size:.85rem; }
</style></head><body>';

echo '<h1>🔧 Configuración inicial de iTECH Contrataciones</h1>';

/* ── Verificar extensión OpenSSL ── */
if (!extension_loaded('openssl')) {
    echo '<div class="err"> La extensión <code>openssl</code> de PHP no está habilitada. Habilítela en <code>php.ini</code> y recargue.</div>';
    echo '</body></html>';
    exit;
}
echo '<div class="ok">✔ Extensión OpenSSL disponible.</div>';

/* ── Crear directorio keys/ ── */
if (!is_dir($dirClaves)) {
    mkdir($dirClaves, 0750, true);
    echo '<div class="ok">✔ Directorio <code>keys/</code> creado.</div>';
} else {
    echo '<div class="ok">✔ Directorio <code>keys/</code> existe.</div>';
}

/* ── Generar par de claves ── */
if (file_exists($archPriv) && file_exists($archPub)) {
    echo '<div class="inf">ℹ️ Las claves ya existen. Si desea regenerarlas, elimine <code>keys/private.pem</code> y <code>keys/public.pem</code> y vuelva a ejecutar este script.</div>';
} else {
    $config = [
        'digest_alg'       => 'sha256',
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ];

    $recurso = openssl_pkey_new($config);

    if ($recurso === false) {
        echo '<div class="err"> No se pudo generar la clave RSA: ' . openssl_error_string() . '</div>';
        echo '</body></html>';
        exit;
    }

    openssl_pkey_export($recurso, $clavePrivada);
    $detalles       = openssl_pkey_get_details($recurso);
    $clavePublica   = $detalles['key'];

    file_put_contents($archPriv, $clavePrivada);
    file_put_contents($archPub,  $clavePublica);

    if (PHP_OS_FAMILY !== 'Windows') {
        chmod($archPriv, 0600);
    }

    echo '<div class="ok">✔ Par de claves RSA-2048 generado y guardado en <code>keys/</code>.</div>';
}

/* ── Verificar BD ── */
echo '<hr style="margin:1.5rem 0; border:none; border-top:1px solid #E8D5DA;">';
echo '<h2 style="font-size:1.1rem; margin-bottom:.75rem;">📋 Próximos pasos</h2>';
echo '<ol style="line-height:2; font-size:.9rem;">
  <li>Importe <code>sql/database.sql</code> en <a href="http://localhost/phpmyadmin/" target="_blank">phpMyAdmin</a>.</li>
  <li>Verifique las credenciales de conexión en <code>config/Conexion.php</code>.</li>
  <li>Acceda al sistema en <a href="index.php">index.php</a>.</li>
  <li><strong>Elimine este archivo</strong> (<code>setup.php</code>) del servidor.</li>
</ol>';

echo '<div class="inf" style="margin-top:1rem;">
   <strong>Importante:</strong> La clave privada (<code>keys/private.pem</code>) debe mantenerse segura y nunca ser expuesta públicamente. 
  Agregue <code>keys/</code> a su <code>.htaccess</code> o a las reglas de acceso del servidor web.
</div>';

echo '</body></html>';
