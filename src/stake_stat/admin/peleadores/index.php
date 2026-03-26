<?php
// admin/peleadores/index.php
require_once '../config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

try {
    // Ajusta los nombres de las llaves primarias (id_pais, id_categoria_peso) si en tu BD son distintos
    $sql = "SELECT p.id, p.nombre, p.apodo, p.imagen_url, 
                   c.nombre AS categoria, pa.nombre AS pais, pa.codigo_iso
            FROM peleadores p
            LEFT JOIN categorias_peso c ON p.categoria_peso_id = c.id
            LEFT JOIN paises pa ON p.pais_id = pa.id
            ORDER BY p.id DESC";
            
    $stmt = $sistema->db->query($sql);
    $peleadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

include '../../includes/admin_header.php'; 
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: #2a2a2c !important;">
        <h1 class="h2 text-uppercase text-white m-0" style="font-family: 'Oswald', sans-serif;">
            Roster de <span class="text-danger">Peleadores</span>
        </h1>
        <a href="crear.php" class="btn btn-danger fw-bold" style="font-family: 'Oswald', sans-serif;">
            <i class="bi bi-plus-lg me-1"></i> NUEVO PELEADOR
        </a>
    </div>

    <div class="card-dash p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle" style="border-color: #2a2a2c;">
                <thead>
                    <tr class="text-uppercase text-muted" style="font-family: 'Oswald', sans-serif;">
                        <th>ID</th>
                        <th>Peleador</th>
                        <th>Categoría</th>
                        <th>País</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($peleadores)): ?>
                        <?php foreach($peleadores as $fighter): ?>
                        <tr>
                            <td><?= $fighter['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; overflow: hidden;">
    <?php if(!empty($fighter['imagen_url'])): ?>
        <img src="../../uploads/peleadores/<?= htmlspecialchars($fighter['imagen_url']) ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
    <?php else: ?>
        <i class="bi bi-person-fill text-white fs-4"></i>
    <?php endif; ?>
</div>
                                    <div>
                                        <div class="fw-bold text-white"><?= htmlspecialchars($fighter['nombre'] ?? '') ?></div>
                                        <?php if(!empty($fighter['apodo'])): ?>
                                            <div class="text-danger small" style="font-family: 'Oswald', sans-serif;">"<?= htmlspecialchars($fighter['apodo']) ?>"</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-dark border border-secondary"><?= htmlspecialchars($fighter['categoria'] ?? 'Sin asignar') ?></span></td>
                            <td>
                                <?php if(!empty($fighter['codigo_iso'])): ?>
                                    <img src="https://flagcdn.com/24x18/<?= strtolower($fighter['codigo_iso']) ?>.png" alt="<?= $fighter['codigo_iso'] ?>" class="me-2">
                                <?php endif; ?>
                                <?= htmlspecialchars($fighter['pais'] ?? 'N/A') ?>
                            </td>
                            <td class="text-center">
                                <a href="editar.php?id=<?= $fighter['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></a>
                                <a href="eliminar.php?id=<?= $fighter['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este peleador?');"><i class="bi bi-trash3-fill"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-white-50">No hay peleadores registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>