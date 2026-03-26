<?php
// admin/usuarios/index.php
require_once '../config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// Obtenemos los usuarios y cruzamos con usuario_rol y rol para saber qué puesto tienen
try {
    $sql = "SELECT u.id_usuario, u.nombre, u.correo, u.curp, r.rol AS nombre_rol 
            FROM usuario u 
            LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario 
            LEFT JOIN rol r ON ur.id_rol = r.id_rol 
            ORDER BY u.id_usuario DESC";
            
    $stmt = $sistema->db->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

include '../../includes/admin_header.php'; 
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: #2a2a2c !important;">
        <h1 class="h2 text-uppercase text-white m-0" style="font-family: 'Oswald', sans-serif;">
            Gestión de <span class="text-danger">Usuarios</span>
        </h1>
        <a href="crear.php" class="btn btn-danger fw-bold" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">
            <i class="bi bi-person-plus-fill me-1"></i> NUEVO USUARIO
        </a>
    </div>

    <div class="card-dash p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle" style="border-color: #2a2a2c;">
                <thead>
                    <tr class="text-uppercase" style="font-family: 'Oswald', sans-serif; color: #888;">
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>CURP</th>
                        <th>Rol</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($usuarios)): ?>
                        <?php foreach($usuarios as $user): ?>
                        <tr>
                            <td><?= $user['id_usuario'] ?></td>
                            <td class="fw-bold text-white"><?= htmlspecialchars($user['nombre']) ?></td>
                            <td><?= htmlspecialchars($user['correo']) ?></td>
                            <td class="text-white-50"><?= htmlspecialchars($user['curp'] ?? 'N/A') ?></td>
                            <td>
                                <?php if(strtolower($user['nombre_rol'] ?? '') == 'administrador' || strtolower($user['nombre_rol'] ?? '') == 'admin'): ?>
                                    <span class="badge bg-danger"><?= htmlspecialchars($user['nombre_rol']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($user['nombre_rol'] ?? 'Sin Rol') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="editar.php?id=<?= $user['id_usuario'] ?>" class="btn btn-sm btn-outline-warning me-1" title="Modificar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="eliminar.php?id=<?= $user['id_usuario'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-white-50">No hay usuarios registrados en la base de datos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>