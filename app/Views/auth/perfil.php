<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
     <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
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

        main { flex: 1; display: flex; justify-content: center; align-items: flex-start; padding: 20px; } /* Adjusted alignment */
        .container { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; } /* Added max-width */
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="text"], input[type="email"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button[type="submit"] { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px;} /* Adjusted button styles */
        button[type="submit"]:hover { background-color: #0056b3; }
         .logout-button {
             background-color: #dc3545; /* Bootstrap danger color */
             color: white;
             padding: 10px 15px;
             border: none;
             border-radius: 4px;
             cursor: pointer;
             font-size: 16px;
             text-decoration: none; /* For the link version */
             display: inline-block; /* For the link version */
             text-align: center;
         }
         .logout-button:hover {
             background-color: #c82333;
         }
         .button-group {
             margin-top: 20px;
             text-align: center; /* Center buttons */
         }
         .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 0.9em; }
         .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
         .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
         .validation-error { color: #dc3545; font-size: 0.8em; margin-top: 3px; }

    </style>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>

<header>
    <a style="text-decoration: none; color: inherit;" href="<?= site_url('principal') ?>">
        <div class="logo">🗂️ Tareario</div>
    </a>
    <div class="busqueda">
        <input type="text" placeholder="Buscar tareas...">
    </div>

    <div class="usuario">
        <?php if (isset($usuario)): ?>
             <a href="<?= site_url('usuario/perfil') ?>">
                Bienvenido, <?= esc($usuario['nombre'] ?? $usuario->nombre ?? '') ?> </a>
        <?php else: ?>
            <a href="<?= site_url('login') ?>" style="color: white;">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <div class="container">
        <h2>Perfil de Usuario</h2>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>
         <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>
         <?php if (session()->getFlashdata('errors')): ?>
             <div class="alert alert-danger">
                 <ul>
                     <?php foreach (session()->getFlashdata('errors') as $error): ?>
                         <li><?= esc($error) ?></li>
                     <?php endforeach; ?>
                 </ul>
             </div>
         <?php endif; ?>


        <form action="<?= site_url('usuario/actualizar') ?>"" method="post"> <?= csrf_field() ?>
             <input type="hidden" name="id" value="<?= esc($usuario['id'] ?? $usuario->id ?? '') ?>"> <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?= esc($usuario['nombre'] ?? $usuario->nombre ?? '') ?>" required>
                 </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= esc($usuario['email'] ?? $usuario->email ?? '') ?>" required>
                 </div>

             <div class="button-group">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>

                <a href="<?= site_url('logout') ?>" class="logout-button">Cerrar Sesión</a>
            </div>
            <div class="button-group">
            <button type="button" class="btn btn-secondary" onclick="togglePasswordForm()">Cambiar Contraseña</button>
            </div>

            <div id="password-form" style="display: none; margin-top: 20px;">
                <div class="form-group">
                    <label for="password_actual">Contraseña Actual:</label>
                    <input type="password" name="password_actual" id="password_actual" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password_nueva">Nueva Contraseña:</label>
                    <input type="password" name="password_nueva" id="password_nueva" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password_confirmar">Confirmar Nueva Contraseña:</label>
                    <input type="password" name="password_confirmar" id="password_confirmar" class="form-control">
                </div>
            </div>
        </form>
    </div>
</main>


<script>
    function togglePasswordForm() {
        var form = document.getElementById('password-form');
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>
</body>
</html>
