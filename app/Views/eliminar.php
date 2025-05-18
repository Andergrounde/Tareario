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

        main {display: flex;height: calc(100vh - 60px);}
        section {flex: 1;padding: 20px;overflow-y: auto;}

        .tarea {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #fff;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }
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

        .create-task-card, .archived-tasks-card {
            border: 1px dashed #007bff;
            margin-bottom: 20px;
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
            flex-basis: 100%;
            height: 150px;
        }
        .row > .col-md-4 {
            display: flex;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        #taskListContainer .row {
            margin-left: -10px;
            margin-right: -10px;
        }
        .tarea {
            padding: 15px;
            margin: 0 10px 20px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            position: relative;
            width: calc(100% - 20px);
            box-sizing: border-box;
        }

        .create-task-card:hover, .archived-tasks-card:hover {
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
            border: 1px dashed #17a2b8;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #e2f3f5;
            cursor: pointer;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: #17a2b8;
            height: 150px;
        }
        .shared-tasks-card:hover {
            background-color: #cceef2;
        }
        .shared-tasks-card h4 {
            margin: 0;
            color: #17a2b8;
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
    
    <div class="container mt-5" style="max-width: 700px;">
        <h2>¿Estás seguro de que deseas eliminar esta tarea?</h2>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="mb-4 p-3 border rounded bg-light">
            <p><strong>Asunto:</strong> <?= esc($tarea['asunto']) ?></p>
            <p><strong>Descripción:</strong> <?= esc($tarea['descripcion']) ?></p>
        </div>

        <?php if (!empty($subtareas)): ?>
            <h4>Subtareas asociadas:</h4>
            <ul class="list-group mb-4">
                <?php foreach ($subtareas as $sub): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= esc($sub['descripcion']) ?>
                        <form action="<?= site_url('eliminar/subtarea/' . $sub['id']) ?>" method="post" onsubmit="return confirm('¿Eliminar esta subtarea?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay subtareas asociadas.</p>
        <?php endif; ?>

        <form action="<?= site_url('eliminar/tarea/' . $tarea['id']) ?>" method="post" onsubmit="return confirm('¿Eliminar esta tarea y todas sus subtareas?')">
            <?= csrf_field() ?>
            <button class="btn btn-danger">Eliminar tarea</button>
            <a href="<?= site_url('/principal') ?>" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    
</body>
</html>
