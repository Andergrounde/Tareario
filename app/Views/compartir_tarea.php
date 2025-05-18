<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir Tarea: <?= esc($tarea['asunto'] ?? 'Tarea') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding-top: 60px; }
        header {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000; 
        }
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


        .container {
            margin-top: 20px;
        }

        .task-card-large {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 25px; 
            border-radius: 8px;
            background-color: #fff;
            position: relative;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .task-card-large h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.8em; 
        }
        .task-card-large p {
            font-size: 1.1em; 
            margin-bottom: 10px;
        }
         .task-card-large strong {
             font-weight: bold;
         }


        .subtareas-list {
            margin-top: 20px;
            padding-left: 0;
            list-style: none; 
        }
        .subtarea-item {
            border: 1px solid #eee;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
         .subtarea-item label {
             margin-bottom: 0;
             flex-grow: 1; 
             margin-right: 10px; 
             cursor: pointer; 
         }
         .subtarea-item input[type="checkbox"] {
             flex-shrink: 0; 
         }


        .email-input-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .email-input-group input[type="email"] {
            flex-grow: 1;
            margin-right: 10px;
        }
        .add-email-btn {
            flex-shrink: 0;
        }

        .form-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
         .form-section h4 {
             margin-top: 0;
             margin-bottom: 15px;
             border-bottom: 1px solid #eee;
             padding-bottom: 10px;
         }

         .back-link {
             margin-bottom: 20px;
             display: inline-block;
             text-decoration: none; 
             color: #007bff; 
         }
          .back-link:hover {
              text-decoration: underline;
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
            <a href="<?= site_url('usuario/perfil') ?>">
                Bienvenido, <?= esc($usuario->nombre) ?>
            </a>
        <?php else: ?>
            <a href="<?= site_url('login') ?>" style="color: white;">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <section class="container">
    
        <h2 class="mb-4">Compartir Tarea</h2>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
         <?php if (session()->getFlashdata('success')): ?>
             <div class="alert alert-success">
                 <?= esc(session()->getFlashdata('success')) ?>
             </div>
         <?php endif; ?>
         <?php if (session()->getFlashdata('warning')): ?>
             <div class="alert alert-warning">
                 <?= esc(session()->getFlashdata('warning')) ?>
             </div>
         <?php endif; ?>


        <?php if (isset($tarea)): ?>
            <div class="task-card-large" style="background-color: <?= esc($tarea['color'] ?? '#ffffff') ?>;">
                <h3><?= esc($tarea['asunto']) ?></h3>
                <p><strong>Descripción:</strong> <?= esc($tarea['descripcion']) ?></p>
                <p><strong>Prioridad:</strong> <?= esc($tarea['prioridad']) ?></p>
                <p><strong>Estado:</strong> <?= esc($tarea['estado']) ?></p>
                <?php if (!empty($tarea['fecha_vencimiento'])): ?>
                    <p><strong>Vence:</strong> <?= esc($tarea['fecha_vencimiento']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-section">
                <h4>Seleccionar Subtareas para Compartir</h4>
                <form action="<?= site_url('tareas/procesarCompartir') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="task_id" value="<?= esc($tarea['id']) ?>">

                    <?php if (!empty($subtareas)): ?>
                        <ul class="subtareas-list">
                            <?php foreach ($subtareas as $subtarea): ?>
                                <li class="subtarea-item">
                                    <label for="subtask_<?= esc($subtarea['id']) ?>">
                                        <?= esc($subtarea['descripcion']) ?>
                                        (Prioridad: <strong><?= esc($subtarea['prioridad']) ?></strong>,
                                        Estado: <?= esc($subtarea['estado']) ?>)
                                    </label>
                                    <input type="checkbox" name="selected_subtasks[]" id="subtask_<?= esc($subtarea['id']) ?>" value="<?= esc($subtarea['id']) ?>">
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Esta tarea no tiene subtareas.</p>
                    <?php endif; ?>

                    <div class="form-section mt-4">
                        <h4>Compartir con Usuarios (por Email)</h4>
                        <div id="emails-container">
                            <div class="email-input-group">
                                <input type="email" name="emails[]" class="form-control" placeholder="Email del colaborador" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm mt-2" id="add-email-btn">+ Agregar Otro Email</button>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Compartir Tarea</button>
                </form>
            </div>

        <?php else: ?>
            <div class="alert alert-danger">Tarea no encontrada.</div>
        <?php endif; ?>

    </section>
</main>

<script>

    document.getElementById('add-email-btn').addEventListener('click', function() {
        const emailsContainer = document.getElementById('emails-container');
        const newInputGroup = document.createElement('div');
        newInputGroup.classList.add('email-input-group');
        newInputGroup.innerHTML = `
            <input type="email" name="emails[]" class="form-control" placeholder="Email del colaborador" required>
            <button type="button" class="btn btn-danger btn-sm remove-email-btn">X</button>
        `;
        emailsContainer.appendChild(newInputGroup);

        newInputGroup.querySelector('.remove-email-btn').addEventListener('click', function() {
            emailsContainer.removeChild(newInputGroup);
        });
    });

    document.querySelectorAll('.remove-email-btn').forEach(button => {
        button.addEventListener('click', function() {
            button.closest('.email-input-group').remove();
        });
    });
    const usuarioEmail = "<?= esc($usuario->email ?? '') ?>";

    document.querySelector('form').addEventListener('submit', function(event) {
        const emailInputs = document.querySelectorAll('input[name="emails[]"]');
        let mismoEmail = false;

        emailInputs.forEach(input => {
            if (input.value.trim().toLowerCase() === usuarioEmail.toLowerCase()) {
                mismoEmail = true;
            }
        });

        if (mismoEmail) {
            event.preventDefault();

            const existingAlert = document.querySelector('.alert.alert-warning');
            if (!existingAlert) {
                const alert = document.createElement('div');
                alert.classList.add('alert', 'alert-warning');
                alert.innerText = 'No podés compartirte tareas a vos mismo.';
                document.querySelector('.container').prepend(alert);
            }
        }
    });

</script>

</body>
</html>
