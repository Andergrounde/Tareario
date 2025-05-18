<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador de Tareas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {font-family: Arial, sans-serif; display: flex; flex-direction: column; margin: 0; }
        header {background-color: #333;color: white;padding: 10px 20px;display: flex;justify-content: space-between;align-items: center;}
        header .logo {font-size: 1.5em;font-weight: bold;}
        header .busqueda {flex: 1;margin: 0 20px;}
        header .busqueda input {width: 100%;padding: 5px;}
        header .usuario {white-space: nowrap;}
        header .usuario a {
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
         header .usuario a:hover {
             text-decoration: underline;
         }

        main {display: flex; flex-direction: column; }
    

        .tarea h3 {margin: 0;}
        .subtareas {margin-top: 10px;padding-left: 0;}
        .subtarea {
            margin-bottom: 5px;
            border-bottom: 1px dashed #eee;
            padding-bottom: 5px;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .subtarea:last-child { border-bottom: none; }
        .subtarea strong { font-size: 0.9em; }

        .subtask-status-indicator {
            flex-shrink: 0;
            margin-left: 8px;
            display: flex;
            align-items: center;
        }
         .subtask-status-indicator form {
             margin: 0;
             padding: 0;
             display: inline-block;
             margin-left: 5px;
         }
         .subtask-status-indicator button {
              padding: 2px 5px;
              font-size: 0.8em;
              line-height: 1;
         }

        .subtask-status-indicator .completed-circle {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #28a745;
            border: 1px solid #28a745;
            display: inline-block;
        }
         .subtask-status-indicator .en-proceso-icon {
             color: #007bff;
             font-size: 1.1em;
             cursor: pointer;
         }
          .subtask-status-indicator .en-proceso-icon:hover {
              color: #0056b3;
          }
          .subtask-status-indicator .definida-icon {
              color: #6c757d;
              font-size: 1.1em;
              cursor: pointer;
          }
           .subtask-status-indicator .definida-icon:hover {
               color: #545b62;
           }

        .subtarea.completed p {
            text-decoration: line-through;
            color: #888;
        }
        .complete-subtask-form, .complete-task-form {
            margin: 0;
            padding: 0;
            display: inline-block;
            margin-left: 10px;
        }
        .complete-subtask-form button, .complete-task-form button {
            padding: 2px 5px;
            font-size: 0.8em;
            line-height: 1;
        }

        .create-task-card, .archived-tasks-card, .shared-tasks-card {
            border: 1px dashed #007bff;
            padding: 15px;
            border-radius: 5px;
            background-color: #e9ecef;
            cursor: pointer;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: #007bff;
            height: 150px;
        }


        .create-task-card:hover, .archived-tasks-card:hover, .shared-tasks-card:hover {
            background-color: #dcdcdc;
        }
        .create-task-card h4, .archived-tasks-card h4 {
            margin: 0;
            color: #007bff;
        }
        .task-form-container {
            display: none;
        }
        .add-subtask-form {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            display: none;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        .add-subtask-form .form-label {
            font-size: 0.9em;
            margin-bottom: 3px;
        }
        .add-subtask-form .form-control,
        .add-subtask-form .form-select {
            font-size: 0.9em;
            padding: 5px;
        }
        .add-subtask-form button {
            padding: 5px 10px;
            font-size: 0.9em;
        }
        .add-subtask-toggle {
            font-size: 0.9em;
            cursor: pointer;
            color: #007bff;
            margin-top: 10px;
            display: block;
        }
        .add-subtask-toggle:hover {
            text-decoration: underline;
        }

        .task-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .task-card-actions {
            display: flex;
            align-items: center;
        }

        .share-task-btn,
        .edit-color-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            padding: 0 5px;
            color: #555;
            margin-right: 5px;
        }
        .share-task-btn:hover,
        .edit-color-btn:hover {
            color: #000;
        }
         .share-task-btn {
             color: #28a745;
         }
          .share-task-btn:hover {
              color: #218838;
          }

        .color-palette {
            position: absolute; 
            top: 40px; 
            right: 0; 
            background-color: white;
            border: 1px solid #ddd;
            padding: 8px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
            z-index: 1050;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            border-radius: 6px;
            display: none;
        }
        .color-swatch {
            width: 22px;
            height: 22px;
            border: 1px solid #eee;
            cursor: pointer;
            border-radius: 4px;
            transition: transform 0.1s ease-in-out;
        }
        .color-swatch:hover {
            transform: scale(1.1);
            border-color: #999;
        }
        .shared-tasks-card {
            background-color: #e2f3f5;
        }
        .shared-tasks-card:hover {
            background-color: #cceef2;
        }
        .shared-tasks-card h4 {
            margin: 0;
            color: #17a2b8;
        }
        .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    width: 400px;
    max-width: 90%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    position: relative;
    animation: aparecer 0.3s ease-out;
}

.cerrar {
    position: absolute;
    right: 12px;
    top: 10px;
    font-size: 20px;
    cursor: pointer;
    color: #888;
}

@keyframes aparecer {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
.notificacion-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999; 
}

.notificacion-contenido {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
    text-align: center;
}

.notificacion-contenido h3 {
    margin-top: 0;
}

.notificacion-contenido button {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background-color: #007bff;
    border: none;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}

.notificacion-contenido button:hover {
    background-color: #0056b3;
}
    </style>
</head>
<body>

<header>
    <a style="text-decoration: none; color: inherit;" href="<?= site_url('principal') ?>">
        <div class="logo">🗂️ Tareario</div>
    </a>
    
    <div class="usuario">
        <?php if (isset($usuario)): ?>
            <a href="<?= site_url('usuario/perfil') ?>" style="color: white; text-decoration: none;">
                Bienvenido, <?= esc($usuario->nombre) ?>
            </a>
        <?php else: ?>
            <a href="<?= site_url('login') ?>" style="color: white; text-decoration: none;">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <section class="container my-4">
        <div class="row"> <div class="col-md-8"> <div class="row mb-3">
                    <div  class="col-md-4 mb-4">
                        <div class="create-task-card h-100" id="createTaskCard">
                            <h4>+ Crear Nueva Tarea</h4>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <a href="<?= site_url('tareas/archivadas') ?>" class="archived-tasks-card h-100">
                            <h4>📦 Tareas Archivadas</h4>
                            <p class="text-muted">Ver tareas completadas</p>
                        </a>
                    </div>
                    <div class="col-md-4 mb-4">
                        <a href="<?= site_url('tareas/compartidasConmigo') ?>" class="shared-tasks-card h-100">
                            <h4>🤝 Tareas Compartidas</h4>
                            <p class="text-muted">Ver tareas compartidas contigo</p>
                        </a>
                    </div>
                </div>

                <div class="row mb-4">
                    <div style="margin-top: 40px;" class="col-12 task-form-container" id="taskFormContainer">
                        <article class="p-4 rounded shadow-sm h-100 bg-light">
                            <h4>Nueva Tarea</h4>
                            <form action="<?= site_url('tareas/store') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label for="asunto" class="form-label">Asunto</label>
                                    <input type="text" class="form-control" id="asunto" name="asunto" required>
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="prioridad" class="form-label">Prioridad</label>
                                    <select class="form-select" id="prioridad" name="prioridad" required>
                                        <option value="baja">Baja</option>
                                        <option value="normal" selected>Normal</option>
                                        <option value="alta">Alta</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                    <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento">
                                    <small class="form-text text-muted">Opcional</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                                <button type="button" class="btn btn-secondary" id="cancelFormBtn">Cancelar</button>
                            </form>
                        </article>
                    </div>
                </div>

                <div id="taskListContainer">
                     <div class="row gx-lg-4 gy-4"> <?php if (!empty($datosTareas)) : ?>
                            <?php foreach ($datosTareas as $tarea) : ?>
                                <div class="col-md-4">
                                    
                                    <article class="tarea h-100 p-3 rounded shadow-sm" style="background-color: <?= esc($tarea['color'] ?? '#ffffff') ?>;" data-task-id="<?= esc($tarea['id']) ?>" id="task-card-<?= esc($tarea['id']) ?>">
                                        <div class="task-card-header">
                                            
                                            <h4><?= esc($tarea['asunto']) ?></h4>
                                            
                                            <div class="task-card-actions">
                                                <a href="<?= site_url('eliminar/inicio/' . esc($tarea['id'])) ?>" class="share-task-btn" title="Eliminar Tarea">
                                                     <i class="fas fa-trash"  style="color: black;"></i>
                                                </a>
                                                <a href="<?= site_url('tareas/compartir/' . esc($tarea['id'])) ?>" class="share-task-btn" title="Compartir Tarea">
                                                     <i class="fas fa-share-alt"></i>
                                                </a>
                                                <button class="edit-color-btn" title="Cambiar color" data-task-id="<?= esc($tarea['id']) ?>">
                                                    <i class="fas fa-palette" style="color: #ff7e70"></i>
                                                </button>
                                                <a href="<?= site_url('modificar/inicio/' . esc($tarea['id'])) ?>" title="Modificar Tarea">
                                                    <i class="fas fa-pencil-alt"> </i>
                                                </a>
                                                <a style="margin-left: 7px;" title="Agregar recordatorio" type="button" onclick="mostrarModalRecordatorio(<?= $tarea['id'] ?>)">
                                                    <i class="fas fa-bell" style="color: #ffc107;"></i>
                                                </a>
                                                <?php
                                                    $taskEstado = is_object($tarea) ? ($tarea->estado ?? null) : ($tarea['estado'] ?? null);
                                                ?>
                                                <?php if (empty($tarea['subtareas']) && $taskEstado !== 'completada') : ?>
                                                    <form action="<?= site_url('tareas/completetask') ?>" method="post" class="complete-task-form">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="task_id" value="<?= esc($tarea['id']) ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar Tarea como completada">✔</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <p><?= esc($tarea['descripcion']) ?></p>
                                        <?php
                                            $PrioridadFormato = strtoupper($tarea['prioridad']);
                                            $colorPrioridad = '';
                                            if ($tarea['prioridad'] === 'alta') { $colorPrioridad = '#8B0000'; }
                                            elseif ($tarea['prioridad'] === 'normal') { $colorPrioridad = '#004FFF'; }
                                            else { $colorPrioridad = 'black'; }
                                        ?>
                                        <strong>Prior. </strong>
                                        <span class="subtask-status" style="color: <?= $colorPrioridad ?>; font-weight: bold;">
                                            <?= esc($PrioridadFormato) ?>
                                        </span>
                                        <?php
                                            $estadoFormato = strtoupper($tarea['estado']);
                                            $colorEstado = '';
                                            if ($tarea['estado'] === 'en_proceso') { $colorEstado = '#034FFF'; }
                                            elseif ($tarea['estado'] === 'completada') { $colorEstado = 'darkgreen'; }
                                            else { $colorEstado = 'grey'; }
                                        ?>
                                        <strong>Estado:</strong>
                                        <span class="subtask-status" style="color: <?= $colorEstado ?>; font-weight: bold;">
                                            <?= esc($estadoFormato) ?>
                                        </span>
                                        <div class="subtareas mt-3">
                                            <h6>Subtareas:</h6>
                                            <?php if (!empty($tarea['subtareas'])) : ?>
                                                <?php foreach ($tarea['subtareas'] as $subtarea) : ?>
                                                    <div class="subtarea <?= ($subtarea['estado'] === 'completada') ? 'completed' : '' ?>" data-subtask-id="<?= esc($subtarea['id']) ?>">
                                                        <p class="<?= ($subtarea['estado'] === 'completada') ? 'completed' : '' ?>">
                                                            <?= esc($subtarea['descripcion']) ?>
                                                            <?php
                                                                $prioridadSubtareaFormato = strtoupper($subtarea['prioridad']);
                                                                $colorPrioridadSub = '';
                                                                if ($subtarea['prioridad'] === 'alta') { $colorPrioridadSub = '#8B0000'; }
                                                                elseif ($subtarea['prioridad'] === 'normal') { $colorPrioridadSub = '#004FFF'; }
                                                                else { $colorPrioridadSub = 'black'; }
                                                            ?>
                                                            <strong>Prior. </strong>
                                                            <span style="color: <?= $colorPrioridadSub ?>; font-weight: bold;">
                                                                <?= esc($prioridadSubtareaFormato) ?>
                                                            </span>
                                                            <?php
                                                                $estadoSubtareaFormato = strtoupper($subtarea['estado']);
                                                                $colorEstadoSub = '';
                                                                if ($subtarea['estado'] === 'en_proceso') { $colorEstadoSub = '#034FFF'; }
                                                                elseif ($subtarea['estado'] === 'completada') { $colorEstadoSub = 'darkgreen'; }
                                                                else { $colorEstadoSub = 'grey'; }
                                                            ?>
                                                            <strong>Estado:</strong>
                                                            <span style="color: <?= $colorEstadoSub ?>; font-weight: bold;">
                                                                <?= esc($estadoSubtareaFormato) ?>
                                                            </span>
                                                            <?php if (!empty($subtarea['fecha_vencimiento']) && $subtarea['fecha_vencimiento'] !== '0000-00-00') : ?>
                                                                <br><small class="text-muted">Vence: <?= esc(date('d/m/Y', strtotime($subtarea['fecha_vencimiento']))) ?></small>
                                                            <?php endif; ?>
                                                        </p>
                                                        <div class="subtask-status-indicator">
                                                            <form action="<?= site_url('tareas/completesubtareaenprocesoInicio') ?>" method="post" class="d-inline-block">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="subtask_id" value="<?= esc($subtarea['id']) ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Marcar como En Proceso" <?= ($subtarea['estado'] === 'en_proceso') ? 'disabled' : '' ?>>
                                                                    <i class="fas fa-circle-dot"></i>
                                                                </button>
                                                            </form>
                                                            <form action="<?= site_url('tareas/completesubtareaInicio') ?>" method="post" class="d-inline-block">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="subtask_id" value="<?= esc($subtarea['id']) ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como completada" <?= ($subtarea['estado'] === 'completada') ? 'disabled' : '' ?>>✔</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <p><em>No hay subtareas para esta tarea.</em></p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($taskEstado !== 'completada') : ?>
                                            <a href="#" class="add-subtask-toggle mt-auto" data-task-id="<?= esc($tarea['id']) ?>">+ Agregar Subtarea</a>
                                        <?php endif; ?>
                                        <div class="add-subtask-form mt-3" id="subtaskForm-<?= esc($tarea['id']) ?>">
                                            <h6>Nueva Subtarea</h6>
                                            <form action="<?= site_url('tareas/storesubtarea') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id_tarea" value="<?= esc($tarea['id']) ?>">
                                                <div class="mb-2">
                                                    <label for="subtask_descripcion_<?= esc($tarea['id']) ?>" class="form-label">Descripción</label>
                                                    <input type="text" class="form-control form-control-sm" id="subtask_descripcion_<?= esc($tarea['id']) ?>" name="descripcion" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="subtask_prioridad_<?= esc($tarea['id']) ?>" class="form-label">Prioridad</label>
                                                    <select class="form-select form-select-sm" id="subtask_prioridad_<?= esc($tarea['id']) ?>" name="prioridad" required>
                                                        <option value="baja">Baja</option>
                                                        <option value="normal" selected>Normal</option>
                                                        <option value="alta">Alta</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="subtask_fecha_vencimiento_<?= esc($tarea['id']) ?>" class="form-label">Fecha Vencimiento</label>
                                                    <input type="date" class="form-control form-control-sm" id="subtask_fecha_vencimiento_<?= esc($tarea['id']) ?>" name="fecha_vencimiento">
                                                    <small class="form-text text-muted">Opcional</small>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-success">Guardar Subtarea</button>
                                                <button type="button" class="btn btn-sm btn-secondary cancel-subtask-btn">Cancelar</button>
                                            </form>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="col-12">
                                <p>No hay tareas cargadas.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div> 
            <div class="col-md-4"> 
                <aside class="sticky-top" style="top: 20px; margin-left: 20px">
                    <h3 class="mt-0 mb-3">🚨 Tareas Vencidas</h3>
                    <?php
                    $tareasVencidas = [];
                    $hoy = new DateTime();

                    if (isset($datosTareas) && is_array($datosTareas)) {
                        $tareasVencidas = array_filter($datosTareas, function($tarea) use ($hoy) {
                            
                            if (!empty($tarea['fecha_vencimiento']) && $tarea['fecha_vencimiento'] !== '0000-00-00') {
                                try {
                                    $fechaVencimiento = new DateTime($tarea['fecha_vencimiento']);
                                  
                                    return $fechaVencimiento < $hoy && ($tarea['estado'] ?? 'definida') !== 'completada';
                                } catch (Exception $e) {
                                    return false;
                                }
                            }
                            return false;
                        });

                         usort($tareasVencidas, function($a, $b) {
                             return strtotime($a['fecha_vencimiento']) - strtotime($b['fecha_vencimiento']);
                         });
                    }
                    ?>

                    <?php if (!empty($tareasVencidas)) : ?>
                        <ul style="list-style: none; padding: 0;">
                            <?php foreach ($tareasVencidas as $tarea) :
                                $fechaVence = new DateTime($tarea['fecha_vencimiento']);
                                $intervalo = $hoy->diff($fechaVence);
                                $diasVencidos = (int)$intervalo->format('%r%a');
                            ?>
                                <li class="overdue-task" style="margin-bottom: 15px; border-left: 5px solid #dc3545; padding-left: 10px;">
                                    <strong><?= esc($tarea['asunto']) ?></strong><br>
                                    <small>Venció el <?= $fechaVence->format('d/m/Y') ?></small><br>
                                    <small>Hace <?= abs($diasVencidos) ?> día(s)</small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p>No hay tareas vencidas.</p>
                    <?php endif; ?>

                    <h3 class="mt-4 mb-3">📅 Vencen pronto</h3>
                    <?php
                    if (isset($datosTareas) && is_array($datosTareas)) {
                    $tareasProximas = array_filter($datosTareas, function($tarea) use ($hoy) {
                        if (!empty($tarea['fecha_vencimiento']) && $tarea['fecha_vencimiento'] !== '0000-00-00') {
                                try {
                                    $fechaVencimiento = new DateTime($tarea['fecha_vencimiento']);
                                    return $fechaVencimiento >= $hoy && ($tarea['estado'] ?? 'definida') !== 'completada';
                                } catch (Exception $e) {
                                    return false;
                                }
                        }
                            return false; 
                        });
                        usort($tareasProximas, function($a, $b) {
                            return strtotime($a['fecha_vencimiento']) - strtotime($b['fecha_vencimiento']);
                        });
                        } else {
                            $tareasProximas = [];
                        }
                        ?>
                        <?php if (!empty($tareasProximas)) : ?>
                            <ul style="list-style: none; padding: 0;">
                                <?php foreach ($tareasProximas as $tarea) :
                                    $fechaVence = new DateTime($tarea['fecha_vencimiento']);
                                    $hoy = new DateTime();
                                    $intervalo = $hoy->diff($fechaVence);
                                    $diasFaltantes = (int)$intervalo->format('%r%a');
                                ?>
                                    <li style="margin-bottom: 15px; border-left: 5px solid <?= esc($tarea['color'] ?? '#6c757d') ?>; padding-left: 10px;">
                                        <strong><?= esc($tarea['asunto']) ?></strong><br>
                                        <small>Vence el <?= $fechaVence->format('d/m/Y') ?></small><br>
                                        <small>
                                            <?php
                                            if ($diasFaltantes == 0) {
                                                echo 'Vence hoy';
                                            } else {
                                                echo 'En ' . $diasFaltantes . ' día(s)';
                                            }
                                            ?>
                                        </small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p>No hay tareas próximas a vencer.</p>
                        <?php endif; ?>
                    </aside>
            </div> </div> </section>
            <div id="modal-recordatorio" class="modal">
    <?php if (isset($tarea)) : ?>            
        <div class="modal-content">
            <span class="cerrar" onclick="ocultarModalRecordatorio()">&times;</span>
            <h2>Nuevo Recordatorio</h2>
            <?= form_open('recordatorios/crearRecordatorio') ?>
                <?= form_hidden('tarea_id', $tarea['id']) ?>
                
                <label for="fecha_recordatorio">Fecha del recordatorio:</label>
                <input type="date" name="fecha_recordatorio" required min="<?= date('Y-m-d') ?>"><br><br>

                <label for="texto_recordatorio">Texto del recordatorio:</label>
                <textarea name="texto_recordatorio" rows="3" cols="40" placeholder="Opcional"></textarea><br><br>

                <button type="submit">Guardar</button>
                <button type="button" onclick="ocultarModalRecordatorio()">Cancelar</button>
            <?= form_close() ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($mostrar_notificacion && !empty($recordatorios)): ?>
<div id="notificacion-recordatorio" class="notificacion-overlay">
    <div class="notificacion-contenido">
        <h3>🔔 Recordatorios</h3>
        <ul>
            <?php foreach ($recordatorios as $rec): ?>
                <li>
                    <strong><?= esc($rec['asunto']) ?></strong>: 
                    <?= esc($rec['texto_recordatorio'] ?? 'Sin descripción') ?> 
                    (<?= esc($rec['fecha_recordatorio']) ?>)
                </li>
            <?php endforeach; ?>
        </ul>
        <button onclick="cerrarNotificacion()">Cerrar</button>
    </div>
</div>
<script>
    function cerrarNotificacion() {
        document.getElementById('notificacion-recordatorio').style.display = 'none';
    }
</script>
<?php endif; ?>

</main>

<div class="color-palette" id="taskColorPalette">
</div>

    

    
<script>

const csrfTokenName = "<?= csrf_token() ?>";
const csrfTokenValue = "<?= csrf_hash() ?>";

const createTaskCard = document.getElementById('createTaskCard');
const taskFormContainer = document.getElementById('taskFormContainer');
const cancelFormBtn = document.getElementById('cancelFormBtn');
const taskListContainer = document.getElementById('taskListContainer'); 
const mainContentArea = document.querySelector('.col-md-8'); 
const actionButtonRows = mainContentArea.querySelectorAll('.row.mb-3'); 

function mostrarModalRecordatorio(tareaId) {
    document.getElementById("modal-recordatorio").style.display = "flex";
}
function ocultarModalRecordatorio() {
    document.getElementById("modal-recordatorio").style.display = "none";
}

if (createTaskCard) {
    createTaskCard.addEventListener('click', function() {
        taskFormContainer.style.display = 'block';
        actionButtonRows.forEach(row => row.style.display = 'none'); 
        if (taskListContainer) {
             taskListContainer.classList.add('d-none');
        }
    });
}

if (cancelFormBtn) {
    cancelFormBtn.addEventListener('click', function() {
        taskFormContainer.style.display = 'none';
        actionButtonRows.forEach(row => row.style.display = 'flex'); 
         if (taskListContainer) { 
             taskListContainer.classList.remove('d-none');
         }
    });
}


document.querySelectorAll('.add-subtask-toggle').forEach(toggleBtn => {
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const taskId = this.getAttribute('data-task-id');
        const subtaskForm = document.getElementById('subtaskForm-' + taskId);
        document.querySelectorAll('.color-palette').forEach(palette => {
             palette.style.display = 'none';
        });
        if (subtaskForm) {
            subtaskForm.style.display = 'block';
            this.style.display = 'none';
        }
    });
});

document.querySelectorAll('.cancel-subtask-btn').forEach(cancelBtn => {
    cancelBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('.add-subtask-form');
        if (form) {
            form.style.display = 'none';
            const taskId = form.id.replace('subtaskForm-', '');
            const toggleBtn = document.querySelector(`.add-subtask-toggle[data-task-id="${taskId}"]`);
            if (toggleBtn) {
                toggleBtn.style.display = 'block';
            }
        }
    });
});

const colorPalette = document.getElementById('taskColorPalette');
const rainbowColors = [
    '#FFCDD2', '#FFDAC8', '#FFE0B2', '#FFECB3', '#FFF9C4',
    '#F0F4C3', '#DCEDC8', '#C8E6C9', '#B2DFDB', '#B2EBF2',
    '#B3E5FC', '#C5CAE9', '#D1C4E9', '#E1BEE7', '#FCE4EC'
];

    rainbowColors.forEach(color => {
        const swatch = document.createElement('div');
        swatch.classList.add('color-swatch');
        swatch.style.backgroundColor = color;
        swatch.dataset.color = color;
        swatch.addEventListener('click', function() {
            const selectedColor = this.dataset.color;
            const currentTaskId = colorPalette.dataset.currentTaskId;
            const taskCard = document.getElementById('task-card-' + currentTaskId);

            if (taskCard) {
                taskCard.style.backgroundColor = selectedColor;

                const formData = new URLSearchParams();
                formData.append(csrfTokenName, csrfTokenValue);
                formData.append('task_id', currentTaskId);
                formData.append('color', selectedColor);

                fetch('<?= site_url('tareas/updateColor') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData.toString()
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Color updated successfully for task ' + currentTaskId);
                    } else {
                        console.error('Failed to update color:', data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error updating color:', error);
                });
            }
            colorPalette.style.display = 'none';
        });
        colorPalette.appendChild(swatch);
    });


    document.querySelectorAll('.edit-color-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();
            const taskId = this.dataset.taskId;
            colorPalette.dataset.currentTaskId = taskId;

            document.querySelectorAll('.add-subtask-form').forEach(form => {
                 form.style.display = 'none';
                 const formTaskId = form.id.replace('subtaskForm-', '');
                 const formToggleBtn = document.querySelector(`.add-subtask-toggle[data-task-id="${formTaskId}"]`);
                 if (formToggleBtn) {
                     formToggleBtn.style.display = 'block';
                 }
            });

            const buttonRect = this.getBoundingClientRect();
            
            colorPalette.style.position = 'fixed'; 
            let topPosition = buttonRect.bottom + 5;
            let leftPosition = buttonRect.left;

            colorPalette.style.top = `${topPosition}px`;
            colorPalette.style.left = `${leftPosition}px`;
            colorPalette.style.right = 'auto';
            colorPalette.style.bottom = 'auto';
            colorPalette.style.display = 'grid'; 

            const paletteRect = colorPalette.getBoundingClientRect();

            if (paletteRect.right > window.innerWidth) {
                 colorPalette.style.left = `${buttonRect.right - paletteRect.width}px`;
            }
            if (paletteRect.bottom > window.innerHeight) {
                 colorPalette.style.top = `${buttonRect.top - paletteRect.height - 5}px`;
            }
        });
    });

    document.addEventListener('click', function(event) {
        if (colorPalette.style.display === 'grid' && !colorPalette.contains(event.target) && !event.target.closest('.edit-color-btn')) {
            colorPalette.style.display = 'none';
        }
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous"></script>


</body>
</html>