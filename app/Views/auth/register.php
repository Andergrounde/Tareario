<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px 0; }
        .container { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button[type="submit"] { width: 100%; background-color: #28a745; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button[type="submit"]:hover { background-color: #218838; }
        .text-center { text-align: center; }
        .mt-3 { margin-top: 15px; }
        .link { color: #007bff; text-decoration: none; }
        .link:hover { text-decoration: underline; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 0.9em; }
        .alert-danger ul { margin: 0; padding-left: 20px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .validation-error { color: #dc3545; font-size: 0.8em; margin-top: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Crear Cuenta</h2>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <p>Por favor, corrige los siguientes errores:</p>
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <?= form_open('auth/procesarRegistro') ?>
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="nombre">Nombre de Usuario:</label>
                <input type="text" name="nombre" id="nombre" value="<?= old('nombre', '') ?>" required>
                 <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nombre'])): ?>
                    <div class="validation-error"><?= esc(session()->getFlashdata('errors')['nombre']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= old('email', '') ?>" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                    <div class="validation-error"><?= esc(session()->getFlashdata('errors')['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Contraseña (mínimo 6 caracteres):</label>
                <input type="password" name="password" id="password" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password'])): ?>
                    <div class="validation-error"><?= esc(session()->getFlashdata('errors')['password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Contraseña:</label>
                <input type="password" name="password_confirm" id="password_confirm" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password_confirm'])): ?>
                    <div class="validation-error"><?= esc(session()->getFlashdata('errors')['password_confirm']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Registrarse</button>
        <?= form_close() ?>

        <div class="text-center mt-3">
            <p>¿Ya tienes una cuenta? <a href="<?= site_url('login') ?>" class="link">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>