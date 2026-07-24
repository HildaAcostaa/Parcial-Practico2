<?php
$tituloPagina = 'Reporte de Colaboradores — iTECH';
require_once __DIR__ . '/layout/header.php';
?>

<div class="pagina-contenedor">

    <!-- ── Encabezado de sección ── -->
    <div class="seccion-titulo">
        <div class="seccion-titulo-fila">
            <div>
                <h2>Reporte de Colaboradores</h2>
                <p>Historial completo de colaboradores y sus perfiles laborales con auditoría de integridad OpenSSL.</p>
            </div>
            <div class="acciones-reporte">
                <a href="index.php?action=exportar" class="btn btn--exportar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Exportar a Excel
                </a>
            </div>
        </div>

        <!-- Leyenda de integridad -->
        <div class="leyenda-integridad">
            <span class="badge badge--verificado">✔ Integridad Verificada</span>
            <span class="badge badge--corrompido">✘ Datos Corrompidos / Adulterados</span>
            <span class="leyenda-texto">— Firma digital RSA-SHA256 (OpenSSL)</span>
        </div>
    </div>

    <?php if (empty($colaboradores)): ?>
        <!-- Estado vacío -->
        <div class="card estado-vacio">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <h3>No hay colaboradores registrados</h3>
            <p>Use el módulo de <a href="index.php?action=formulario">Registro</a> para agregar colaboradores.</p>
        </div>

    <?php else: ?>

        <!-- Contador resumen -->
        <div class="resumen-bar">
            <span>Total de colaboradores: <strong><?= count($colaboradores) ?></strong></span>
            <?php
                $totalPerfiles = array_sum(array_map(fn($c) => count($c['perfiles']), $colaboradores));
                $totalVerif    = 0;
                $totalCorr     = 0;
                foreach ($colaboradores as $col) {
                    foreach ($col['perfiles'] as $p) {
                        $p['integridad'] ? $totalVerif++ : $totalCorr++;
                    }
                }
            ?>
            <span>Total perfiles: <strong><?= $totalPerfiles ?></strong></span>
            <span class="badge badge--verificado-sm">✔ <?= $totalVerif ?> verificados</span>
            <?php if ($totalCorr > 0): ?>
                <span class="badge badge--corrompido-sm">✘ <?= $totalCorr ?> adulterados</span>
            <?php endif; ?>
        </div>

        <!-- 
             TABLA POR COLABORADOR
         -->
        <?php foreach ($colaboradores as $col): ?>
            <div class="card card--reporte">

                <!-- Cabecera del colaborador -->
                <div class="colaborador-header">
                    <div class="colaborador-avatar">
                        <?= mb_strtoupper(mb_substr($col['nombre'], 0, 1) . mb_substr($col['apellido'], 0, 1)) ?>
                    </div>
                    <div class="colaborador-info">
                        <h3><?= htmlspecialchars($col['nombre'] . ' ' . $col['apellido']) ?></h3>
                        <div class="colaborador-meta">
                            <span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                ID: #<?= $col['id'] ?>
                            </span>
                            <span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <?= htmlspecialchars($col['identidad']) ?>
                            </span>
                            <span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <?= htmlspecialchars($col['correo']) ?>
                            </span>
                            <span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.64 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.81-.81a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17.92z"/></svg>
                                <?= htmlspecialchars($col['celular']) ?>
                            </span>
                            <span>
                                <?= htmlspecialchars($col['edad']) ?> años &bull;
                                <?= htmlspecialchars($col['sexo']) ?> &bull;
                                <?= htmlspecialchars($col['tipo_sangre']) ?> &bull;
                                <?= htmlspecialchars($col['ruta']) ?>
                            </span>
                        </div>
                    </div>
                </div><!-- /colaborador-header -->

                <!-- Perfiles laborales -->
                <?php if (empty($col['perfiles'])): ?>
                    <div class="sin-perfil">Sin perfil laboral asignado.</div>
                <?php else: ?>
                    <div class="tabla-wrapper">
                        <table class="tabla-perfiles">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ocupación</th>
                                    <th>Tipo Planilla</th>
                                    <th>Departamento</th>
                                    <th>Salario</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    <th>Motivo Baja</th>
                                    <th> Integridad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($col['perfiles'] as $p): ?>
                                    <tr class="<?= $p['es_activo'] ? 'fila--activa' : 'fila--inactiva' ?>">
                                        <td class="celda-id"><?= $p['perfil_id'] ?></td>
                                        <td><?= htmlspecialchars($p['ocupacion'] ?? '—') ?></td>
                                        <td>
                                            <span class="tag-planilla"><?= htmlspecialchars($p['tipo_planilla'] ?? '—') ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($p['departamento']) ?></td>
                                        <td class="celda-salario">
                                            $<?= number_format((float)$p['salario'], 2) ?>
                                        </td>
                                        <td><?= htmlspecialchars($p['fecha_inicio']) ?></td>
                                        <td>
                                            <?= $p['fecha_fin']
                                                ? htmlspecialchars($p['fecha_fin'])
                                                : '<span class="texto-gris">—</span>' ?>
                                        </td>
                                        <td>
                                            <?php if ($p['es_activo'] && $p['empleado_activo']): ?>
                                                <span class="estado-badge estado--activo">Activo</span>
                                            <?php elseif ($p['es_activo'] && !$p['empleado_activo']): ?>
                                                <span class="estado-badge estado--baja">Baja</span>
                                            <?php else: ?>
                                                <span class="estado-badge estado--inactivo">Promovido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $p['motivo_baja']
                                                ? '<span class="motivo-texto">' . htmlspecialchars($p['motivo_baja']) . '</span>'
                                                : '<span class="texto-gris">—</span>' ?>
                                        </td>
                                        <td>
                                            <?php if ($p['integridad']): ?>
                                                <span class="badge badge--verificado">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                    Verificada
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge--corrompido">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                    Adulterado
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
