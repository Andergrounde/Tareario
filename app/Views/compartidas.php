<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Tareas Compartidas Conmigo') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {font-family: Arial, sans-serif; display: flex; flex-direction: column; margin: 0; }
        header {background-color: #333;color: white;padding: 10px 20px;display: flex;justify-content: space-between;align-items: center;}
        header .logo {font-size: 1.5em;font-weight: bold;}
        header .busqueda {flex: 1;margin: 0 20px;}
        header .busqueda input {width: 100%;padding: 5px;}
        header .usuario a {color: white; text-decoration: none;}
        header .usuario a:hover {text-decoration: underline;}
        main {flex: 1;padding: 20px;overflow-y: auto;}
        .tarea {border: 1px solid #ccc; margin-bottom: 20px; padding: 15px; border-radius: 5px; background-color: #fff; position: relative; width: 100%; box-sizing: border-box;}
        .tarea h4 {margin-top: 0;} /* Ajustado para el título de la tarea */
        .subtareas {margin-top: 10px;padding-left: 0;}
        .subtarea {margin-bottom: 5px; border-bottom: 1px dashed #eee; padding-bottom: 5px; font-size: 0.9em; display: flex; align-items: center; justify-content: space-between;}
        .subtarea:last-child { border-bottom: none; }
        .subtask-status-indicator {flex-shrink: 0; margin-left: 8px; display: flex; align-items: center;}
        .subtask-status-indicator form {margin: 0; padding: 0; display: inline-block; margin-left: 5px;}
        .subtask-status-indicator button {padding: 2px 5px; font-size: 0.8em; line-height: 1;}
        .completed-circle {width: 18px; height: 18px; border-radius: 50%; background-color: #28a745; border: 1px solid #28a745; display: inline-block;}
        .subtarea.completed p {text-decoration: line-through; color: #888;}
        .back-link {margin-bottom: 20px; display: inline-block;}
         .task-details p { margin-bottom: 0.5rem; }
         .task-creator-info { font-size: 0.85em; color: #555; margin-top: 10px; border-top: 1px solid #eee; padding-top: 5px; }

    </style>
</head>
<body>

<header>
    <a style="text-decoration: none; color: inherit;" href="<?= site_url('principal') ?>">
        <div class="logo">🗂️ Tareario</div>
    </a>
    <div class="usuario">
        <?php if (isset($usuario) && property_exists($usuario, 'nombre') && $usuario->nombre): ?>
            <a href="<?= site_url('usuario/perfil') ?>">
                Bienvenido, <?= esc($usuario->nombre) ?>
            </a>
        <?php else: ?>
            <a href="<?= site_url('login') ?>">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <section class="container my-4">
        <h2 class="mb-4"><?= esc($title ?? 'Tareas Compartidas Conmigo') ?></h2>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
        <?php endif; ?>

            <?php if (!empty($datosTareasCompartidas)) : ?>
                <?php foreach ($datosTareasCompartidas as $tarea) : ?>
                    <?php
                        if ($tarea['estado_tarea'] === 'completada') {
                            continue; 
                        }
                    ?>
                    <div class="col-md-4 mb-2"> 
                        <article class="tarea p-3 rounded shadow-sm h-100" style="background-color: <?= esc($tarea['color'] ?? '#ffffff') ?>;" data-task-id="<?= esc($tarea['id_tarea'])  ?>">
                            <div class="task-card-header">
                                <h4><?= esc($tarea['asunto_tarea']) ?></h4>
                              
                            </div>
                            <div class="task-details">
                                <p><?= esc($tarea['descripcion_tarea']) ?></p>
                                <?php
                                            $PrioridadFormato = strtoupper($tarea['prioridad_tarea']);
                                            $colorPrioridad = '';
                                            if ($tarea['prioridad_tarea'] === 'alta') { $colorPrioridad = '#8B0000'; }
                                            elseif ($tarea['prioridad_tarea'] === 'normal') { $colorPrioridad = '#004FFF'; }
                                            else { $colorPrioridad = 'black'; }
                                        ?>
                                        <strong>Prior. </strong>
                                        <span class="subtask-status" style="color: <?= $colorPrioridad ?>; font-weight: bold;">
                                            <?= esc($PrioridadFormato) ?>
                                        </span>
    
                                <?php if (!empty($tarea['fecha_vencimiento_tarea']) && $tarea['fecha_vencimiento_tarea'] !== '0000-00-00') : ?>
                                    <br><small class="text-muted">Vence: <?= esc(date('d/m/Y', strtotime($tarea['fecha_vencimiento_tarea']))) ?></small>
                                <?php endif; ?>
                                
                            </div>

                            <div class="subtareas mt-3">
                                <h6>Subtareas que se te asignaron:</h6>
                                <?php if (!empty($tarea['subtareas_colaborador'])) : ?>
                                    <?php foreach ($tarea['subtareas_colaborador'] as $subtarea) : ?>
                                        <div class="subtarea <?= ($subtarea['estado_subtarea'] === 'completada') ? 'completed' : '' ?>" data-subtask-id="<?= esc($subtarea['id_subtarea']) ?>">
                                            <p class="<?= ($subtarea['estado_subtarea'] === 'completada') ? 'completed' : '' ?>">
                                                <?= esc($subtarea['descripcion_subtarea']) ?>
                                                <?php
                                                    $PrioridadFormato = strtoupper($subtarea['prioridad_subtarea']);
                                                    $colorPrioridad = '';
                                                    if ($subtarea['prioridad_subtarea'] === 'alta') { $colorPrioridad = '#8B0000'; }
                                                    elseif ($subtarea['prioridad_subtarea'] === 'normal') { $colorPrioridad = '#004FFF'; }
                                                    else { $colorPrioridad = 'black'; }
                                                ?>
                                                <strong>Prior. </strong>
                                                
                                                <span class="subtask-status" style="color: <?= $colorPrioridad ?>; font-weight: bold;">
                                                    <?= esc($PrioridadFormato) ?>
                                                </span>
                                                <?php
                                                    $estadoFormato = strtoupper($subtarea['estado_subtarea']);
                                                    $colorEstado = '';
                                                    if ($subtarea['estado_subtarea'] === 'en_proceso') { $colorEstado = '#034FFF'; }
                                                    elseif ($subtarea['estado_subtarea'] === 'completada') { $colorEstado = 'darkgreen'; }
                                                    else { $colorEstado = 'grey'; }
                                                ?>
                                                <strong>Estado:</strong>
                                                <span class="subtask-status" style="color: <?= $colorEstado ?>; font-weight: bold;">
                                                    <?= esc($estadoFormato) ?>
                                                </span>
                                                <?php if (!empty($subtarea['fecha_vencimiento_subtarea']) && $subtarea['fecha_vencimiento_subtarea'] !== '0000-00-00') : ?>
                                                    <br><small class="text-muted">Vence: <?= esc(date('d/m/Y', strtotime($subtarea['fecha_vencimiento_subtarea']))) ?></small>
                                                <?php endif; ?>
                                            </p>
                                            <div class="subtask-status-indicator">
                                        
                                                    <form action="<?= site_url('tareas/completeSubtareaEnProcesoCompartida') ?>" method="post" class="ajax-subtask-form">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="subtask_id" value="<?= esc($subtarea['id_subtarea']) ?>">
                                                        <input type="hidden" name="colaboracion_id" value="<?= esc($subtarea['id_colaboracion']) ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Marcar como En Proceso" <?= ($subtarea['estado_subtarea'] === 'en_proceso') ? 'disabled' : '' ?>>
                                                            <i class="fas fa-circle-dot"></i>
                                                        </button>
                                                    </form>
                                                    <form action="<?= site_url('tareas/completeSubtareaCompartida') ?>" method="post" class="ajax-subtask-form">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="subtask_id" value="<?= esc($subtarea['id_subtarea']) ?>">
                                                        <input type="hidden" name="colaboracion_id" value="<?= esc($subtarea['id_colaboracion']) ?>"> 
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como completada">✔</button>
                                                    </form>
                                                
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php elseif ($tarea['comparte_tarea_completa']) : ?>
                                     <p><em>Se te ha compartido solamente la tarea principal.</em></p>
                                    
                                <?php else : ?>
                                    <p><em>No se te han asignado subtareas específicas para esta tarea compartida.</em></p>
                                <?php endif; ?>
                            </div>
                             <div class="task-creator-info">
                                Compartida por: <?= esc($tarea['creador_email'] ?? 'Desconocido') ?> (<?= esc($tarea['creador_nombre'] ?? '') ?>)
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12">
                    <p>No hay tareas compartidas contigo en este momento.</p>
                </div>
            <?php endif; ?>
    
        </div>
    </section>
</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>