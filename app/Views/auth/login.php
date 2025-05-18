<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 320px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button[type="submit"] { width: 100%; background-color: #007bff; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button[type="submit"]:hover { background-color: #0056b3; }
        .text-center { text-align: center; }
        .mt-3 { margin-top: 15px; }
        .link { color: #007bff; text-decoration: none; }
        header .logo {font-size: 1.5em;font-weight: bold;}
        .link:hover { text-decoration: underline; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 0.9em; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .validation-error { color: #dc3545; font-size: 0.8em; margin-top: 3px; }
    </style>
</head>
<body>
   
    
    <div class="container">
        <h2>Iniciar Sesión</h2>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?= form_open('auth/procesarLogin') ?>
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= old('email', '') ?>" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                    <div class="validation-error"><?= esc(session()->getFlashdata('errors')['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
                 <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password'])): ?>
                    <div class="validation-error"><?= esc(session()->getFlashdata('errors')['password']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Ingresar</button>
        <?= form_close() ?>

        <div class="text-center mt-3">
            <p>¿No tienes una cuenta? <a href="<?= site_url('registro') ?>" class="link">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>