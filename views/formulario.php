<?php
$tituloPagina = 'Registrar Colaborador — iTECH';
require_once __DIR__ . '/layout/header.php';

/* Valores previos (repoblación si hay error de validación) */
$a = $anterior ?? [];
$e = $errores  ?? [];

function campo(string $key, array $a): string {
    return htmlspecialchars($a[$key] ?? '', ENT_QUOTES, 'UTF-8');
}
function err(string $key, array $e): string {
    return isset($e[$key]) ? '<span class="campo-error">' . htmlspecialchars($e[$key]) . '</span>' : '';
}
function clsErr(string $key, array $e): string {
    return isset($e[$key]) ? ' input--error' : '';
}
?>

<div class="pagina-contenedor">

    <!-- ── Título de sección ── -->
    <div class="seccion-titulo">
        <h2>Registro de Colaborador</h2>
        <p>Complete todos los campos obligatorios (<span class="requerido-marca">*</span>) para registrar al colaborador y su perfil laboral.</p>
    </div>

    <!-- ── Mensaje de éxito / error global ── -->
    <?php if (!empty($mensaje)): ?>
        <div class="alerta alerta--<?= $mensaje['tipo'] === 'exito' ? 'exito' : 'error' ?>">
            <?= $mensaje['texto'] ?>
        </div>
    <?php endif; ?>

    <!-- 
         FORMULARIO
    -->
    <form action="index.php?action=guardar" method="POST" id="frm-colaborador" novalidate>

        <!-- 
             SECCIÓN 1: DATOS DEL COLABORADOR
         -->
        <div class="card">
            <div class="card-cabecera">
                <div class="card-icono">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h3 class="card-titulo">Datos Personales</h3>
            </div>
            <div class="card-cuerpo">

                <div class="grid-2col">
                    <!-- Identidad -->
                    <div class="campo-grupo">
                        <label class="campo-label">Identidad <span class="requerido-marca">*</span></label>
                        <input type="text" name="identidad" id="identidad"
                               class="campo-input<?= clsErr('identidad', $e) ?>"
                               value="<?= campo('identidad', $a) ?>"
                               placeholder="Ej: 8-123-4567" maxlength="20">
                        <?= err('identidad', $e) ?>
                    </div>

                    <!-- Edad -->
                    <div class="campo-grupo">
                        <label class="campo-label">Edad <span class="requerido-marca">*</span></label>
                        <input type="number" name="edad" id="edad"
                               class="campo-input<?= clsErr('edad', $e) ?>"
                               value="<?= campo('edad', $a) ?>"
                               min="18" max="80" placeholder="18 – 80">
                        <?= err('edad', $e) ?>
                    </div>

                    <!-- Nombre -->
                    <div class="campo-grupo">
                        <label class="campo-label">Nombre <span class="requerido-marca">*</span></label>
                        <input type="text" name="nombre" id="nombre"
                               class="campo-input<?= clsErr('nombre', $e) ?>"
                               value="<?= campo('nombre', $a) ?>"
                               placeholder="Ej: Juan Carlos" maxlength="100">
                        <?= err('nombre', $e) ?>
                    </div>

                    <!-- Apellido -->
                    <div class="campo-grupo">
                        <label class="campo-label">Apellido <span class="requerido-marca">*</span></label>
                        <input type="text" name="apellido" id="apellido"
                               class="campo-input<?= clsErr('apellido', $e) ?>"
                               value="<?= campo('apellido', $a) ?>"
                               placeholder="Ej: Pérez González" maxlength="100">
                        <?= err('apellido', $e) ?>
                    </div>

                    <!-- Tipo de Sangre -->
                    <div class="campo-grupo">
                        <label class="campo-label">Tipo de Sangre <span class="requerido-marca">*</span></label>
                        <select name="tipo_sangre" class="campo-input campo-select<?= clsErr('tipo_sangre', $e) ?>">
                            <option value="">— Seleccione —</option>
                            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $ts): ?>
                                <option value="<?= $ts ?>" <?= campo('tipo_sangre', $a) === $ts ? 'selected' : '' ?>><?= $ts ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= err('tipo_sangre', $e) ?>
                    </div>

                    <!-- Sexo -->
                    <div class="campo-grupo">
                        <label class="campo-label">Sexo <span class="requerido-marca">*</span></label>
                        <select name="sexo" class="campo-input campo-select<?= clsErr('sexo', $e) ?>">
                            <option value="">— Seleccione —</option>
                            <?php foreach (['Masculino','Femenino','Otro'] as $sx): ?>
                                <option value="<?= $sx ?>" <?= campo('sexo', $a) === $sx ? 'selected' : '' ?>><?= $sx ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= err('sexo', $e) ?>
                    </div>

                    <!-- Nacionalidad -->
                    <div class="campo-grupo">
                        <label class="campo-label">Nacionalidad <span class="requerido-marca">*</span></label>
                        <input type="text" name="nacionalidad" id="nacionalidad"
                               class="campo-input<?= clsErr('nacionalidad', $e) ?>"
                               value="<?= campo('nacionalidad', $a) ?>"
                               placeholder="Ej: Panameña" maxlength="100">
                        <?= err('nacionalidad', $e) ?>
                    </div>

                    <!-- Ruta -->
                    <div class="campo-grupo">
                        <label class="campo-label">Ruta <span class="requerido-marca">*</span></label>
                        <select name="ruta" class="campo-input campo-select<?= clsErr('ruta', $e) ?>">
                            <option value="">— Seleccione —</option>
                            <?php foreach (['Panamá Este','Panamá Oeste','Panamá Norte'] as $rt): ?>
                                <option value="<?= $rt ?>" <?= campo('ruta', $a) === $rt ? 'selected' : '' ?>><?= $rt ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= err('ruta', $e) ?>
                    </div>

                    <!-- Correo -->
                    <div class="campo-grupo">
                        <label class="campo-label">Correo Electrónico <span class="requerido-marca">*</span></label>
                        <input type="email" name="correo" id="correo"
                               class="campo-input<?= clsErr('correo', $e) ?>"
                               value="<?= campo('correo', $a) ?>"
                               placeholder="ejemplo@correo.com" maxlength="150">
                        <?= err('correo', $e) ?>
                    </div>

                    <!-- Celular -->
                    <div class="campo-grupo">
                        <label class="campo-label">Celular <span class="requerido-marca">*</span></label>
                        <input type="tel" name="celular" id="celular"
                               class="campo-input<?= clsErr('celular', $e) ?>"
                               value="<?= campo('celular', $a) ?>"
                               placeholder="6000-0000" maxlength="20">
                        <?= err('celular', $e) ?>
                    </div>
                </div>

            </div>
        </div>


        <!-- 
             SECCIÓN 2: PERFIL LABORAL
         -->
        <div class="card">
            <div class="card-cabecera">
                <div class="card-icono">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    </svg>
                </div>
                <h3 class="card-titulo">Perfil Laboral</h3>
            </div>
            <div class="card-cuerpo">

                <div class="grid-2col">
                    <!-- Ocupación -->
                    <div class="campo-grupo">
                        <label class="campo-label">Ocupación <span class="requerido-marca">*</span></label>
                        <select name="id_ocupacion" class="campo-input campo-select<?= clsErr('id_ocupacion', $e) ?>">
                            <option value="0">— Seleccione ocupación —</option>
                            <?php foreach ($catalogos['ocupaciones'] as $oc): ?>
                                <option value="<?= $oc['id'] ?>"
                                    <?= (int)campo('id_ocupacion', $a) === (int)$oc['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($oc['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= err('id_ocupacion', $e) ?>
                    </div>

                    <!-- Tipo de Planilla -->
                    <div class="campo-grupo">
                        <label class="campo-label">Tipo de Planilla <span class="requerido-marca">*</span></label>
                        <select name="id_tipo_planilla" class="campo-input campo-select<?= clsErr('id_tipo_planilla', $e) ?>">
                            <option value="0">— Seleccione tipo —</option>
                            <?php foreach ($catalogos['planillas'] as $pl): ?>
                                <option value="<?= $pl['id'] ?>"
                                    <?= (int)campo('id_tipo_planilla', $a) === (int)$pl['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pl['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= err('id_tipo_planilla', $e) ?>
                    </div>

                    <!-- Departamento -->
                    <div class="campo-grupo col-span-2">
                        <label class="campo-label">Departamento / Ubicación en la Empresa <span class="requerido-marca">*</span></label>
                        <input type="text" name="departamento" id="departamento"
                               class="campo-input<?= clsErr('departamento', $e) ?>"
                               value="<?= campo('departamento', $a) ?>"
                               placeholder="Ej: Recursos Humanos — Sede Central" maxlength="150">
                        <?= err('departamento', $e) ?>
                    </div>

                    <!-- Salario -->
                    <div class="campo-grupo">
                        <label class="campo-label">Salario (USD) <span class="requerido-marca">*</span></label>
                        <div class="input-prefijo-wrapper">
                            <span class="input-prefijo">$</span>
                            <input type="number" name="salario" id="salario"
                                   class="campo-input campo-input--prefijo<?= clsErr('salario', $e) ?>"
                                   value="<?= campo('salario', $a) ?>"
                                   min="0" step="0.01" placeholder="0.00">
                        </div>
                        <?= err('salario', $e) ?>
                    </div>

                    <!-- Fecha Inicio -->
                    <div class="campo-grupo">
                        <label class="campo-label">Fecha de Inicio <span class="requerido-marca">*</span></label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                               class="campo-input<?= clsErr('fecha_inicio', $e) ?>"
                               value="<?= campo('fecha_inicio', $a) ?>">
                        <?= err('fecha_inicio', $e) ?>
                    </div>

                    <!-- Fecha Fin -->
                    <div class="campo-grupo">
                        <label class="campo-label">Fecha de Fin
                            <span class="campo-opcional">(Opcional — solo si hay baja)</span>
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin"
                               class="campo-input<?= clsErr('fecha_fin', $e) ?>"
                               value="<?= campo('fecha_fin', $a) ?>">
                        <?= err('fecha_fin', $e) ?>
                    </div>

                    <!-- Motivo de Baja (condicional) -->
                    <div class="campo-grupo col-span-2" id="grupo-motivo-baja" style="display:none">
                        <label class="campo-label">
                            Motivo de Baja <span class="requerido-marca">*</span>
                            <span class="campo-hint">(Requerido cuando se ingresa Fecha Fin)</span>
                        </label>
                        <textarea name="motivo_baja" id="motivo_baja" rows="3"
                                  class="campo-input campo-textarea<?= clsErr('motivo_baja', $e) ?>"
                                  placeholder="Describa el motivo de la desvinculación del colaborador..."
                                  ><?= campo('motivo_baja', $a) ?></textarea>
                        <?= err('motivo_baja', $e) ?>
                    </div>

                </div><!-- /grid-2col -->

                <!-- Info de lógica de promoción -->
                <div class="info-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Si el colaborador ya existe, el perfil anterior se marcará automáticamente como inactivo (promoción). La identidad digital OpenSSL se generará al guardar.</span>
                </div>

            </div><!-- /card-cuerpo -->
        </div><!-- /card -->


        <!-- ───── Botones ───── -->
        <div class="botones-contenedor">
            <button type="reset" class="btn btn--secundario">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.56"/>
                </svg>
                Limpiar Formulario
            </button>
            <button type="submit" class="btn btn--principal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Guardar Registro
            </button>
        </div>

    </form>
</div>

<!-- 
     JAVASCRIPT — Lógica dinámica
 -->
<script>
(function () {
    'use strict';

    const fechaFin      = document.getElementById('fecha_fin');
    const grupoMotivo   = document.getElementById('grupo-motivo-baja');
    const motivoBaja    = document.getElementById('motivo_baja');

    /* Mostrar/ocultar Motivo de Baja según si se ingresa Fecha Fin */
    function toggleMotivoBaja() {
        if (fechaFin.value.trim() !== '') {
            grupoMotivo.style.display = 'block';
            motivoBaja.setAttribute('required', 'required');
        } else {
            grupoMotivo.style.display = 'none';
            motivoBaja.removeAttribute('required');
            motivoBaja.value = '';
        }
    }

    fechaFin.addEventListener('change', toggleMotivoBaja);
    fechaFin.addEventListener('input',  toggleMotivoBaja);

    /* Activar si hubo error previo y había fecha_fin */
    toggleMotivoBaja();

    /* Convertir nombre/apellido a Formato Título en tiempo real */
    ['nombre', 'apellido', 'nacionalidad'].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('blur', function () {
            this.value = this.value
                .toLowerCase()
                .replace(/(?:^|\s)\S/g, c => c.toUpperCase());
        });
    });

    /* Validación cliente antes de enviar */
    document.getElementById('frm-colaborador').addEventListener('submit', function (ev) {
        const fechaIni = document.getElementById('fecha_inicio').value;
        const fechaFinVal = fechaFin.value;

        if (fechaFinVal && fechaFinVal <= fechaIni) {
            ev.preventDefault();
            alert('La Fecha Fin debe ser posterior a la Fecha de Inicio.');
            fechaFin.focus();
        }
    });
})();
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
