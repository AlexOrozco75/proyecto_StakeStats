<?php
session_start();
// Asegúrate de que la ruta a tu clase sistema sea la correcta desde esta carpeta
require_once '../admin/sistema.class.php'; 

$mensaje_error = '';
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_registrar'])) {
    
    $correo = trim($_POST['correo']);
    $password = $_POST['contraseña'];
    $confirm_password = $_POST['confirmar_contraseña'];

    // 1. Validar que las contraseñas sean iguales
    if ($password !== $confirm_password) {
        $mensaje_error = "Las contraseñas no coinciden.";
    } else {
        $sistema = new sistema();
        $sistema->conectar();

        try {
            // 2. Verificar si el correo ya existe
            $sql_check = "SELECT id_usuario FROM usuario WHERE correo = :correo";
            $stmt_check = $sistema->db->prepare($sql_check);
            $stmt_check->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                $mensaje_error = "Este correo electrónico ya está registrado.";
            } else {
                // 3. Insertar el nuevo usuario
                $hash = password_hash($password, PASSWORD_DEFAULT); // Encriptamos la contraseña
                
                $sql_insert = "INSERT INTO usuario (correo, contraseña) VALUES (:correo, :contrasena)";
                $stmt_insert = $sistema->db->prepare($sql_insert);
                $stmt_insert->bindParam(':correo', $correo, PDO::PARAM_STR);
                $stmt_insert->bindParam(':contrasena', $hash, PDO::PARAM_STR);

                if ($stmt_insert->execute()) {
                    // Obtenemos el ID del usuario que se acaba de crear
                    $id_nuevo_usuario = $sistema->db->lastInsertId();

                    // 4. Le asignamos el rol "usuario" (ID 2 en tu base de datos)
                    $sql_rol = "INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (:id_usuario, 2)";
                    $stmt_rol = $sistema->db->prepare($sql_rol);
                    $stmt_rol->bindParam(':id_usuario', $id_nuevo_usuario, PDO::PARAM_INT);
                    $stmt_rol->execute();

                    $mensaje_exito = "¡Registro exitoso! Ya puedes iniciar sesión.";
                } else {
                    $mensaje_error = "Error al crear la cuenta. Intenta de nuevo.";
                }
            }
        } catch (PDOException $e) {
            $mensaje_error = "Error de conexión: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stake Stats | Crear Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #000; color: #fff; font-family: 'Roboto', sans-serif; }
        .registro-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.9)), url('https://via.placeholder.com/1920x1080') center/cover;
        }
        .registro-card {
            background-color: #111;
            border: 1px solid #333;
            border-top: 4px solid #d20a0a;
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
        }
        .form-control {
            background-color: #e9ecef;
            border: none;
            border-radius: 4px;
            padding: 12px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(210, 10, 10, 0.25);
        }
        .btn-registro {
            background-color: #d20a0a;
            color: white;
            font-family: 'Oswald', sans-serif;
            font-size: 1.2rem;
            letter-spacing: 1px;
            border-radius: 4px;
            padding: 12px;
            transition: 0.3s;
            border: none;
        }
        .btn-registro:hover {
            background-color: #b00808;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="registro-container">
    <div class="registro-card">
        
        <div class="text-center mb-4">
            <h2 style="font-family: 'Oswald', sans-serif; font-weight: bold; letter-spacing: 2px;">
                STAKE <span style="color: #d20a0a;">STATS</span>
            </h2>
            <p class="text-muted">ÚNETE A LA ARENA</p>
        </div>

        <?php if($mensaje_error != ''): ?>
            <div class="alert alert-danger py-2 fw-bold text-center" style="font-size: 0.9rem;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $mensaje_error; ?>
            </div>
        <?php endif; ?>

        <?php if($mensaje_exito != ''): ?>
            <div class="alert alert-success py-2 fw-bold text-center" style="font-size: 0.9rem;">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $mensaje_exito; ?>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div class="mb-3">
                <label for="correo" class="form-label fw-bold" style="font-size: 0.85rem; letter-spacing: 1px; color: #aaa;">CORREO ELECTRÓNICO</label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="tu@correo.com" required>
            </div>
            
            <div class="mb-3">
                <label for="contraseña" class="form-label fw-bold" style="font-size: 0.85rem; letter-spacing: 1px; color: #aaa;">CONTRASEÑA</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="Mínimo 6 caracteres" required>
            </div>

            <div class="mb-4">
                <label for="confirmar_contraseña" class="form-label fw-bold" style="font-size: 0.85rem; letter-spacing: 1px; color: #aaa;">CONFIRMAR CONTRASEÑA</label>
                <input type="password" class="form-control" id="confirmar_contraseña" name="confirmar_contraseña" placeholder="Repite tu contraseña" required>
            </div>

            <button type="submit" name="btn_registrar" class="btn btn-registro w-100 fw-bold">CREAR CUENTA</button>
        </form>

        <div class="text-center mt-4">
            <p style="font-size: 0.9rem; color: #888;">
                ¿Ya tienes una cuenta? <br>
                <a href="login.php" style="color: #d20a0a; text-decoration: none; font-weight: bold;">Inicia sesión aquí</a>
            </p>
        </div>

    </div>
</div>

</body>
</html>