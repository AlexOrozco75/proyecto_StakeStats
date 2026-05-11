<?php
// admin/usuarios/editar.php
require_once '../config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

$mensaje_error = '';
$mensaje_exito = '';

// Obtener el ID del usuario (ya sea por GET al entrar a la página o por POST al guardar)
$id_usuario = $_GET['id'] ?? $_POST['id_usuario'] ?? null;

if (!$id_usuario) {
    header("Location: index.php");
    exit();
}

// Obtenemos los roles disponibles para llenar el <select>
try {
    $stmtRoles = $sistema->db->query("SELECT id_rol, rol FROM rol ORDER BY rol ASC");
    $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar roles: " . $e->getMessage());
}

// --------------------------------------------------------
// 1. PROCESAR EL FORMULARIO SI SE ENVIÓ
// --------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $curp = strtoupper(trim($_POST['curp']));
    $nueva_contrasena = $_POST['contrasena'];
    $id_rol = $_POST['id_rol'];

    // Validaciones
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_error = "El formato del correo electrónico no es válido.";
    } 
    elseif (!empty($curp) && !preg_match('/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/', $curp)) {
        $mensaje_error = "El formato de la CURP es incorrecto. Verifica los 18 caracteres.";
    } else {
        // Verificar que el correo no lo esté usando OTRO usuario
        $stmtCheck = $sistema->db->prepare("SELECT id_usuario FROM usuario WHERE correo = ? AND id_usuario != ?");
        $stmtCheck->execute([$correo, $id_usuario]);
        
        if ($stmtCheck->rowCount() > 0) {
            $mensaje_error = "Ese correo ya está siendo utilizado por otro usuario.";
        } else {
            try {
                // Empezamos transacción
                $sistema->db->beginTransaction();

                // A. Actualizamos los datos del usuario
                // ¡IMPORTANTE! Cambia 'contrasena' por el nombre real de tu columna
                if (!empty($nueva_contrasena)) {
                    if (strlen($nueva_contrasena) < 6) {
                        throw new Exception("La nueva contraseña debe tener al menos 6 caracteres.");
                    }
                    $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
                    $sqlUser = "UPDATE usuario SET nombre = ?, correo = ?, curp = ?, contrasena = ? WHERE id_usuario = ?";
                    $stmtUser = $sistema->db->prepare($sqlUser);
                    $stmtUser->execute([$nombre, $correo, $curp, $hash, $id_usuario]);
                } else {
                    // Si dejó la contraseña en blanco, no la tocamos
                    $sqlUser = "UPDATE usuario SET nombre = ?, correo = ?, curp = ? WHERE id_usuario = ?";
                    $stmtUser = $sistema->db->prepare($sqlUser);
                    $stmtUser->execute([$nombre, $correo, $curp, $id_usuario]);
                }

                // B. Actualizamos el rol
                // Primero borramos cualquier rol que tuviera antes
                $stmtDelRol = $sistema->db->prepare("DELETE FROM usuario_rol WHERE id_usuario = ?");
                $stmtDelRol->execute([$id_usuario]);

                // Luego le asignamos el nuevo
                if (!empty($id_rol)) {
                    $stmtInsRol = $sistema->db->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (?, ?)");
                    $stmtInsRol->execute([$id_usuario, $id_rol]);
                }

                $sistema->db->commit();
                $mensaje_exito = "Datos del usuario actualizados correctamente.";

            } catch (Exception $e) {
                $sistema->db->rollBack();
                $mensaje_error = "Error al actualizar: " . $e->getMessage();
            }
        }
    }
}

// --------------------------------------------------------
// 2. OBTENER DATOS ACTUALES PARA RELLENAR EL FORMULARIO
// --------------------------------------------------------
try {
    $sql = "SELECT u.*, ur.id_rol 
            FROM usuario u 
            LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario 
            WHERE u.id_usuario = ?";
    $stmt = $sistema->db->prepare($sql);
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuario no encontrado en la base de datos.");
    }
} catch (PDOException $e) {
    die("Error al obtener usuario: " . $e->getMessage());
}

include '../../includes/admin_header.php'; 
?>

<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="index.php" class="text-white-50 text-decoration-none"><i class="bi bi-arrow-left"></i> Volver a Usuarios</a>
    </div>

    <div class="card-dash p-4 mx-auto" style="max-width: 800px; background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
        <h2 class="mb-4 text-uppercase" style="font-family: 'Oswald', sans-serif; border-bottom: 2px solid #ffc107; padding-bottom: 10px; color: #fff;">
            Modificar <span class="text-warning">Usuario</span>
        </h2>

        <?php if($mensaje_error): ?> 
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $mensaje_error ?></div> 
        <?php endif; ?>
        <?php if($mensaje_exito): ?> 
            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= $mensaje_exito ?></div> 
        <?php endif; ?>

        <form action="editar.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-white-50">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50">CURP <small class="text-muted">(18 caracteres)</small></label>
                    <input type="text" name="curp" class="form-control text-white border-secondary text-uppercase" style="background-color: #0b0b0d;" maxlength="18" value="<?= htmlspecialchars($usuario['curp'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-white-50">Rol de Sistema</label>
                    <select name="id_rol" class="form-select text-white border-secondary" style="background-color: #0b0b0d;" required>
                        <option value="">Seleccione un puesto...</option>
                        <?php foreach($roles as $rol_db): ?>
                            <option value="<?= $rol_db['id_rol'] ?>" <?= ($usuario['id_rol'] == $rol_db['id_rol']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(strtoupper($rol_db['rol'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-12 mt-4">
                    <div class="p-3 border border-secondary rounded" style="background-color: #1a1a1c;">
                        <label class="form-label text-warning mb-1"><i class="bi bi-key-fill me-1"></i>Cambiar Contraseña</label>
                        <p class="text-muted small mb-2">Si no deseas cambiar la contraseña de este usuario, deja este campo en blanco.</p>
                        <input type="password" name="contrasena" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" minlength="6" placeholder="Escribe la nueva contraseña aquí...">
                    </div>
                </div>
                
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-warning fw-bold px-4" style="font-family: 'Oswald', sans-serif;">
                        <i class="bi bi-arrow-clockwise me-1"></i> ACTUALIZAR USUARIO
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>