<html>
<head><title>Registro Exitoso</title></head>
<body>
    <h2>Usuario registrado con éxito</h2>
    <p>Nombre: <?= esc($nombre) ?></p>
    <p>Correo: <?= esc($email) ?></p>
    <p>Contraseña: <?= esc($password) ?></p> 

    <p><?= anchor('form', 'Intentar nuevamente') ?></p>
</body>
</html>
