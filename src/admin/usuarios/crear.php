<?php
// admin/usuarios/crear.php
require_once '../config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

$mensaje_error = '';
$mensaje_exito = '';

// Obtenemos los roles disponibles para llenar el <select>
try {
    $stmtRoles = $sistema->db->query("SELECT id_rol, rol FROM rol ORDER BY rol ASC");
    $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar roles: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $curp = strtoupper(trim($_POST['curp'])); // Convertimos siempre a mayúsculas
    $contrasena = $_POST['contrasena'];
    $id_rol = $_POST['id_rol'];

    // 1. VALIDACIONES ESTRICTAS
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_error = "El formato del correo electrónico no es válido.";
    } 
    // Expresión regular para validar CURP Mexicana (18 caracteres estructurados)
    elseif (!empty($curp) && !preg_match('/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/', $curp)) {
        $mensaje_error = "El formato de la CURP es incorrecto. Verifica los 18 caracteres.";
    }
    elseif (strlen($contrasena) < 6) {
        $mensaje_error = "La contraseña debe tener al menos 6 caracteres por seguridad.";
    } 
    else {
        // Verificamos si el correo ya existe en la base de datos para no duplicarlo
        $stmtCheck = $sistema->db->prepare("SELECT id_usuario FROM usuario WHERE correo = ?");
        $stmtCheck->execute([$correo]);
        
        if ($stmtCheck->rowCount() > 0) {
            $mensaje_error = "Este correo ya está registrado en el sistema.";
        } else {
            // 2. INSERCIÓN SEGURA CON TRANSACCIÓN
            try {
                // Empezamos una transacción: o se guardan las dos tablas, o no se guarda ninguna
                $sistema->db->beginTransaction();

                // Encriptamos la contraseña
                $hash_contrasena = password_hash($contrasena, PASSWORD_DEFAULT);

                // Insertamos en la tabla usuario
                $sqlUser = "INSERT INTO usuario (nombre, correo, curp, contrasena) VALUES (?, ?, ?, ?)";
                $stmtUser = $sistema->db->prepare($sqlUser);
                $stmtUser->execute([$nombre, $correo, $curp, $hash_contrasena]);

                // Obtenemos el ID del usuario que se acaba de crear mágicamente
                $nuevo_id_usuario = $sistema->db->lastInsertId();

                // Insertamos la relación en la tabla usuario_rol
                if (!empty($id_rol)) {
                    $sqlRol = "INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (?, ?)";
                    $stmtRol = $sistema->db->prepare($sqlRol);
                    $stmtRol->execute([$nuevo_id_usuario, $id_rol]);
                }

                // Confirmamos que todo salió bien y guardamos permanentemente
                $sistema->db->commit();
                
                $mensaje_exito = "Usuario/Empleado registrado exitosamente.";
                
                // Limpiamos variables para que el formulario quede vacío
                $nombre = $correo = $curp = '';
                
            } catch (PDOException $e) {
                // Si algo falla, cancelamos todo para no dejar datos a medias
                $sistema->db->rollBack();
                $mensaje_error = "Error al guardar en la base de datos: " . $e->getMessage();
            }
        }
    }
}

include '../../includes/admin_header.php'; 
?>

<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="index.php" class="text-white-50 text-decoration-none"><i class="bi bi-arrow-left"></i> Volver a Usuarios</a>
    </div>

    <div class="card-dash p-4 mx-auto" style="max-width: 800px; background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
        <h2 class="mb-4 text-uppercase" style="font-family: 'Oswald', sans-serif; border-bottom: 2px solid #dc3545; padding-bottom: 10px; color: #fff;">
            Registrar Nuevo <span class="text-danger">Usuario</span>
        </h2>

        <?php if($mensaje_error): ?> 
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $mensaje_error ?></div> 
        <?php endif; ?>
        <?php if($mensaje_exito): ?> 
            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= $mensaje_exito ?></div> 
        <?php endif; ?>

        <form action="crear.php" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-white-50">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" value="<?= htmlspecialchars($correo ?? '') ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50">CURP <small class="text-muted">(18 caracteres)</small></label>
                    <input type="text" name="curp" class="form-control text-white border-secondary text-uppercase" style="background-color: #0b0b0d;" maxlength="18" value="<?= htmlspecialchars($curp ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-white-50">Rol de Sistema</label>
                    <select name="id_rol" class="form-select text-white border-secondary" style="background-color: #0b0b0d;" required>
                        <option value="">Seleccione un puesto...</option>
                        <?php foreach($roles as $rol_db): ?>
                            <option value="<?= $rol_db['id_rol'] ?>"><?= htmlspecialchars(strtoupper($rol_db['rol'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50">Contraseña Temporal</label>
                    <input type="password" name="contrasena" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" required minlength="6">
                    <div class="form-text text-muted">Mínimo 6 caracteres. El usuario podrá cambiarla después.</div>
                </div>
                
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-danger fw-bold px-4" style="font-family: 'Oswald', sans-serif;">
                        <i class="bi bi-save me-1"></i> REGISTRAR USUARIO
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>