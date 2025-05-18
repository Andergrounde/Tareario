<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tareas Archivadas</title>
    <style>
        body {font-family: Arial, sans-serif; display: flex; flex-direction: column; margin: 0; }
        header {background-color: #333;color: white;padding: 10px 20px;display: flex;justify-content: space-between;align-items: center;}
        header .logo {font-size: 1.5em;font-weight: bold;}

        header .usuario {white-space: nowrap;}
        header .usuario a {
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
         header .usuario a:hover {
             text-decoration: underline;
         }
        main {flex: 1;padding: 20px;overflow-y: auto;}
        .tarea {border: 1px solid #ccc;margin-bottom: 20px;padding: 15px;    border-radius: 5px;background-color: #fff;}
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
         .subtarea.completed p {
             text-decoration: line-through;
             color: #888;
         }
        .completed-circle {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #28a745;
            border: 1px solid #28a745;
            display: inline-block;
            flex-shrink: 0;
            margin-left: 8px;
        }
         .back-link {
             margin-bottom: 20px;
             display: inline-block;
         }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>

<header>
    <a style="text-decoration: none; color: inherit;" href="<?= site_url('principal') ?>">
        <div class="logo">🗂️ Tareario</div>
    </a>

    <div class="usuario">
        <?php if (isset($usuario)): ?>
            <a href="<?= site_url('usuario/perfil') ?>">
                Bienvenido, <?= esc($usuario->nombre) ?>
            </a>
        <?php else: ?>
            <a href="<?= site_url('login') ?>" style="color: white;">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <section class="container my-4">
        <h2 class="mb-4">Tareas Archivadas</h2>

        <div class="row">
            <?php if (!empty($datosTareasArchivadas)) : ?>
                <?php foreach ($datosTareasArchivadas as $tarea) : ?>
                    <div class="col-md-4 mb-4">
                        <article class="p-3 rounded shadow-sm h-100" style="background-color: <?= esc($tarea['color'] ?? '#ffffff') ?>;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4><?= esc($tarea['asunto']) ?></h4>
                                </div>

                            <p><?= esc($tarea['descripcion']) ?></p>
                            <?PHP 
                                $archivadaEstado = strtoupper($tarea['estado']);
                                $color = '';

                                if ($tarea['estado'] === 'completada') {
                                        $color = 'darkgreen';
                                } else{
                                        $color = 'dark';
                                }               
                            ?>
                            <p style="color: <?= $color ?>; font-weight: bold;"><strong>Estado:</strong> <?= esc($archivadaEstado) ?></p> 

                            <div class="subtareas mt-3">
                
                                <h6>Subtareas:</h6>
                                <?php if (!empty($tarea['subtareas'])) : ?>
                                    <?php foreach ($tarea['subtareas'] as $subtarea) : ?>
                                        <div class="subtarea <?= ($subtarea['estado'] === 'completada') ? 'completed' : '' ?>"> <p class="<?= ($subtarea['estado'] === 'completada') ? 'completed' : '' ?>"> <?= esc($subtarea['descripcion']) ?>
                                                <?php if (!($subtarea['fecha_vencimiento']) === 0000-00-00) : ?>
                                                    <br><small class="text-muted">Vence: <?= esc($subtarea['fecha_vencimiento_tarea']) ?></small>
                                                <?php endif; ?>
                                            </p>
                                            <div class="subtask-status-indicator">
                                
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p><em>No hay subtareas para esta tarea.</em></p>
                                <?php endif; ?>
                            </div>

                            </article>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12">
                     <p>No hay tareas archivadas.</p>
                </div>
            <?php endif; ?>
         </div>
    </section>
</main>

</body>
</html>
