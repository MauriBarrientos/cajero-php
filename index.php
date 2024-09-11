<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cajero Automático</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Cajero Automático</h1>
        <?php
            session_start();

            // Datos fijos del usuario
            define('USER', 'GENERICO');
            define('PIN', '4326');

            // Inicializar saldo en la primera carga de la sesión
            if (!isset($_SESSION['saldo'])) {
                $_SESSION['saldo'] = 10000; // Saldo inicial
            }

            $mensaje = "";

            // Manejar el inicio de sesión
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
                $usuario = $_POST['usuario'];
                $pin = $_POST['pin'];

                if ($usuario === USER && $pin === PIN) {
                    $_SESSION['autenticado'] = true;
                    $mensaje = "Autenticación exitosa. Bienvenido!";
                } else {
                    $mensaje = "El pin es incorrecto.";
                }
            }

            // Manejar transacciones
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['autenticado'])) {
                if (isset($_POST['accion'])) {
                    $monto = floatval($_POST['monto']);
                    if ($_POST['accion'] === 'extraer') {
                        if ($monto > $_SESSION['saldo']) {
                            $mensaje = "Fondos insuficientes para realizar la extracción.";
                        } else {
                            $_SESSION['saldo'] -= $monto;
                            $mensaje = "Extracción realizada con éxito.";
                        }
                    } elseif ($_POST['accion'] === 'depositar') {
                        $_SESSION['saldo'] += $monto;
                        $mensaje = "Depósito realizado con éxito.";
                    }
                }
            }
        ?>

        <?php if (!isset($_SESSION['autenticado']) || !$_SESSION['autenticado']): ?>
            <!-- Formulario de inicio de sesión -->
            <form method="POST" action="">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
                <label for="pin">PIN:</label>
                <input type="password" id="pin" name="pin" required>
                <button type="submit" name="login">Iniciar Sesión</button>
            </form>
        <?php else: ?>
            <!-- Formulario para realizar operaciones -->
            <p><?php echo $mensaje; ?></p>
            <p>Saldo actual: $<?php echo number_format($_SESSION['saldo'], 2); ?></p>

            <form method="POST" action="">
                <label for="monto">Monto:</label>
                <input type="number" id="monto" name="monto" required>

                <button type="submit" name="accion" value="extraer">Extraer</button>
                <button type="submit" name="accion" value="depositar">Depositar</button>
            </form>
            <form method="POST" action="logout.php">
                <button type="submit" name="logout">Cerrar Sesión</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="script.js"></script>
</body>
</html>
