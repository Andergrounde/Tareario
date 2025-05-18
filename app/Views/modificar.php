<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador de Tareas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        
    .container {
        max-width: 500px; /* Antes 700px */
        margin: 40px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .form-label {
        font-weight: 500;
        font-size: 14px;
    }

    .form-control,
    .form-select {
        font-size: 14px;
        padding: 6px 10px;
    }

    h2, h4 {
        text-align: center;
        margin-bottom: 25px;
    }

    .btn {
        min-width: 120px;
    }

    form .mb-2:last-child {
        margin-bottom: 0;
    }

    form.border {
        background-color: #f8f9fa;
    }


        body {font-family: Arial, sans-serif; display: flex; flex-direction: column; margin: 0; }
        header {background-color: #333;color: white;padding: 10px 20px;display: flex;justify-content: space-between;align-items: center;}
        header .logo {font-size: 1.5em;font-weight: bold;}
        header .busqueda {flex: 1;margin: 0 20px;}
        header .busqueda input {width: 100%;padding: 5px;}
        header .usuario {white-space: nowrap;}
        /* Style for the user link in header */
        header .usuario a {
            color: white; /* Keep text color white */
            text-decoration: none; /* Remove underline */
            cursor: pointer; /* Indicate it's clickable */
        }
         header .usuario a:hover {
             text-decoration: underline; /* Add underline on hover */
         }

        main {display: flex;height: calc(100vh - 60px);} /* Adjusted height calculation */
        /* aside removed as per user's HTML structure */
        section {flex: 1;padding: 20px;overflow-y: auto;}

        /* Styles for task cards */
        .tarea {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #fff;
            position: relative; /* For color palette positioning */
            width: 100%; /* Ensure it takes full width of its column */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
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
            display: flex; /* Use flex to align multiple buttons/icons */
            align-items: center;
        }
         /* Style for the forms/buttons within the status indicator */
         .subtask-status-indicator form {
             margin: 0; /* Remove default form margin */
             padding: 0;
             display: inline-block; /* Align forms/buttons in a row */
             margin-left: 5px; /* Space between buttons/icons */
         }
         .subtask-status-indicator button {
              padding: 2px 5px; /* Smaller button */
              font-size: 0.8em;
              line-height: 1; /* Adjust line height */
         }


        .subtask-status-indicator .completed-circle {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #28a745;
            border: 1px solid #28a745;
            display: inline-block;
        }
         /* Style for the 'En Proceso' icon/button */
         .subtask-status-indicator .en-proceso-icon {
             color: #007bff; /* Blue color for en proceso */
             font-size: 1.1em; /* Slightly larger icon */
             cursor: pointer;
         }
          .subtask-status-indicator .en-proceso-icon:hover {
              color: #0056b3; /* Darker blue on hover */
          }
         /* Style for the 'Definida' icon/button (optional, using outline circle) */
          .subtask-status-indicator .definida-icon {
              color: #6c757d; /* Grey color for definida */
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

        /* Custom styles for Create Task Card and Archived Tasks Card */
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
            /* Ensure equal height */
            flex-basis: 100%; /* Occupy full width in flex container */
            height: 150px; /* Fixed height to make them equal */
        }
        /* Apply equal height to the parent flex container if needed */
        .row > .col-md-4 {
             display: flex; /* Make column a flex container */
             /* Remove Bootstrap's default padding for gutters */
             padding-left: 0 !important;
             padding-right: 0 !important;
        }
         /* Add margin to the row to create space between columns */
         #taskListContainer .row {
             margin-left: -10px; /* Half of desired gutter */
             margin-right: -10px; /* Half of desired gutter */
         }
         /* Add padding to the task card itself for internal spacing */
         .tarea {
             padding: 15px; /* Keep internal padding */
             margin: 0 10px 20px 10px; /* Add margin for gutter effect */
             border: 1px solid #ccc; /* Keep border */
             border-radius: 5px; /* Keep border radius */
             background-color: #fff; /* Keep background color */
             position: relative; /* Keep position */
             width: calc(100% - 20px); /* Calculate width considering the added margin */
             box-sizing: border-box; /* Include padding and border in the element's total width and height */
         }


        .create-task-card:hover, .archived-tasks-card:hover {
            background-color: #dcdcdc;
        }
        .create-task-card h4, .archived-tasks-card h4 {
            margin: 0;
            color: #007bff;
        }
        .task-form-container {
            display: none; /* Hidden by default */
        }
        .add-subtask-form {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            display: none; /* Hidden by default */
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

        /* Styles for Color Picker */
        .task-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px; /* Added margin for spacing */
        }

        .task-card-actions { /* Wrapper for icons */
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
            margin-right: 5px; /* Space between icons */
        }
        .share-task-btn:hover,
        .edit-color-btn:hover {
            color: #000; /* Darker color on hover */
        }
         .share-task-btn {
             color: #28a745; /* Green color for share icon */
         }
          .share-task-btn:hover {
              color: #218838; /* Darker green on hover */
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

    <div class="container mt-4">
        <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-warning">
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

        <h2>Modificar Tarea</h2>

        <form action="<?= site_url('modificar/guardar/' . esc($tarea['id'])) ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="asunto" class="form-label">Asunto</label>
                <input type="text" name="asunto" id="asunto" class="form-control" value="<?= esc($tarea['asunto']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required><?= esc($tarea['descripcion']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="prioridad" class="form-label">Prioridad</label>
                <select name="prioridad" id="prioridad" class="form-select" required>
                    <option value="baja" <?= $tarea['prioridad'] === 'baja' ? 'selected' : '' ?>>Baja</option>
                    <option value="normal" <?= $tarea['prioridad'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="alta" <?= $tarea['prioridad'] === 'alta' ? 'selected' : '' ?>>Alta</option>
                </select>
            </div>

            <?php if (!empty($tarea['fecha_vencimiento'])): ?>
                <div class="mb-3">
                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="<?= esc($tarea['fecha_vencimiento']) ?>">
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="fecha_recordatorio" class="form-label">Fecha de Recordatorio</label>
                <input type="date" name="fecha_recordatorio" id="fecha_recordatorio" class="form-control" value="<?= esc($tarea['fecha_recordatorio']) ?>">
            </div>
            

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="<?= site_url('/') ?>" class="btn btn-secondary">Cancelar</a>
        </form>

        <?php if (!empty($subtareas)): ?>
            <hr>
            <h4 class="mt-4">Subtareas</h4>

            <?php foreach ($subtareas as $sub): ?>
                <form action="<?= site_url('subtarea/guardar/' . esc($sub['id'])) ?>" method="post" class="border rounded p-3 mb-3">
                    <?= csrf_field() ?>

                    <div class="mb-2">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion" class="form-control" value="<?= esc($sub['descripcion']) ?>" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Prioridad</label>
                        <select name="prioridad" class="form-select">
                            <option value="baja" <?= $sub['prioridad'] === 'baja' ? 'selected' : '' ?>>Baja</option>
                            <option value="normal" <?= $sub['prioridad'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                            <option value="alta" <?= $sub['prioridad'] === 'alta' ? 'selected' : '' ?>>Alta</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control" value="<?= esc($sub['fecha_vencimiento']) ?>">
                    </div>

                    <button type="submit" class="btn btn-sm btn-success">Guardar Subtarea</button>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>