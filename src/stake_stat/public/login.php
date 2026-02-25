<?php
session_start();
require_once '../admin/sistema.class.php';

$mensaje_error = '';

// Verificamos si el usuario presionó el botón "INGRESAR"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_ingresar'])) {
    
    $correo = trim($_POST['correo']);
    $password_ingresada = $_POST['contraseña'];

    $sistema = new sistema();
    $sistema->conectar();

    try {
        // Hacemos un JOIN de tus 3 tablas para traer el usuario y su rol de un solo golpe
        $sql = "SELECT u.id_usuario, u.correo, u.contraseña, r.rol 
                FROM usuario u 
                LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario
                LEFT JOIN rol r ON ur.id_rol = r.id_rol
                WHERE u.correo = :correo";
                
        $stmt = $sistema->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        
        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el usuario existe, verificamos la contraseña
        if ($usuario_db && password_verify($password_ingresada, $usuario_db['contraseña'])) {
            
            // ¡Contraseña correcta! Guardamos sus datos en la sesión
            $_SESSION['id_usuario'] = $usuario_db['id_usuario'];
            $_SESSION['correo'] = $usuario_db['correo'];
            $_SESSION['rol'] = $usuario_db['rol']; // Aquí dirá 'admin' o 'usuario'

            // Lo mandamos de regreso a la página principal
            header("Location: index.php");
            exit();

        } else {
            // Falla si el correo no existe o la contraseña está mal
            $mensaje_error = "Correo o contraseña incorrectos.";
        }

    } catch (PDOException $e) {
        $mensaje_error = "Error de conexión: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar | Stake Stats</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/styles.css">

    <style>
        /* Estilos específicos para que el login ocupe toda la pantalla */
        body {
            background-color: var(--bg-dark, #0b0b0d);
            background-image: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.9)), url('https://via.placeholder.com/1920x1080');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background-color: var(--card-bg, #161618);
            border: 1px solid var(--border, #2a2a2c);
            border-top: 4px solid var(--primary, #d20a0a);
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .form-control {
            background-color: #000;
            border: 1px solid var(--border, #2a2a2c);
            color: #fff;
            border-radius: 4px;
            padding: 12px;
        }

        .form-control:focus {
            background-color: #111;
            border-color: var(--primary, #d20a0a);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(210, 10, 10, 0.25);
        }

        .btn-stake {
            background-color: var(--primary, #d20a0a);
            color: white;
            font-family: 'Oswald', sans-serif;
            letter-spacing: 2px;
            border: none;
            border-radius: 4px;
            transition: 0.3s;
            clip-path: polygon(5% 0, 100% 0, 95% 100%, 0% 100%); /* Toque deportivo de tu CSS */
        }

        .btn-stake:hover {
            background-color: #ff0000;
            transform: scale(1.02);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-5">
            <a href="index.php" style="text-decoration: none;">
                <h2 style="font-family: 'Oswald', sans-serif; font-size: 2.5rem; font-weight: bold; letter-spacing: 2px; color: white; margin: 0;">
                    STAKE <span style="color: #d20a0a;">STATS</span>
                </h2>
            </a>
            <p style="color: #888888; letter-spacing: 1px; font-size: 0.9rem; margin-top: 5px;">ACCESO AUTORIZADO</p>
        </div>
        
        <?php if($mensaje_error != ''): ?>
            <div class="alert alert-danger text-center fw-bold" style="border-radius: 4px; padding: 10px; font-size: 0.9rem;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $mensaje_error; ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">

        <form action="login.php" method="POST">
            <div class="mb-4">
                <label for="correo" class="form-label fw-bold text-white text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" required placeholder="tu@correo.com">
            </div>
            
            <div class="mb-5">
                <label for="contraseña" class="form-label fw-bold text-white text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Contraseña</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" required placeholder="••••••••">
            </div>

            <button type="submit" name="btn_ingresar" class="btn btn-stake w-100 py-3 fs-5 fw-bold">INGRESAR</button>
        </form>
        
        <div class="text-center mt-4">
            <a href="#" style="color: var(--primary, #d20a0a); text-decoration: none; font-size: 0.9rem;">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="text-center mt-3">
    <p style="font-size: 0.9rem; color: #888;">
        ¿No tienes cuenta? <a href="registro.php" style="color: var(--rojo-stake, #d20a0a); text-decoration: none; font-weight: bold;">Regístrate aquí</a>
    </p>
</div>
    </div>

</body>
</html>