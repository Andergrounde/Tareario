<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tareas</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }

        .alta { background-color: #ffb3b3; }
        .normal { background-color: #fff2b3; }
        .baja { background-color: #d6f5d6; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Tareas Registradas</h2>
    <table>
        <tr>
            <th>Asunto</th>
            <th>Descripción</th>
            <th>Prioridad</th>
            <th>Estado</th>
            <th>Vencimiento</th>
        </tr>
        <?php if (!empty($tareas)): ?>
            <?php foreach ($tareas as $tarea): ?>
                <tr class="<?= esc($tarea->prioridad) ?>">
                    <td><?= esc($tarea->asunto) ?></td>
                    <td><?= esc($tarea->descripcion) ?></td>
                    <td><?= esc(ucfirst($tarea->prioridad)) ?></td>
                    <td><?= esc(ucfirst(str_replace('_', ' ', $tarea->estado))) ?></td>
                    <td><?= esc($tarea->fecha_vencimiento) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">No hay tareas registradas.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
