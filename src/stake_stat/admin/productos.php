<?php
// admin/productos.php
require_once 'sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// Obtenemos los productos junto con el nombre de su categoría
try {
    $sql = "SELECT p.*, c.nombre AS categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias_productos c ON p.categoria_id = c.id 
            ORDER BY p.id DESC";
    $stmt = $sistema->db->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

include 'header.php'; // Incluimos tu cabecera admin
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: #2a2a2c !important;">
        <h1 class="h2 text-uppercase text-white m-0" style="font-family: 'Oswald', sans-serif;">
            Gestión de <span class="text-danger">Productos</span>
        </h1>
        <a href="agregar_producto.php" class="btn btn-danger fw-bold" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">
            <i class="bi bi-plus-lg me-1"></i> NUEVO PRODUCTO
        </a>
    </div>

    <div class="card-dash p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle" style="border-color: #2a2a2c;">
                <thead>
                    <tr class="text-uppercase" style="font-family: 'Oswald', sans-serif; color: #888;">
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($productos)): ?>
                        <?php foreach($productos as $prod): ?>
                        <tr>
                            <td><?= $prod['id'] ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($prod['imagen_url']) ?>" alt="Producto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #2a2a2c;">
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($prod['nombre']) ?></td>
                            <td><?= htmlspecialchars($prod['categoria_nombre'] ?? 'Sin Categoría') ?></td>
                            <td class="text-success fw-bold">$<?= number_format($prod['precio'], 2) ?></td>
                            <td><?= $prod['stock'] ?></td>
                            <td class="text-center">
                                <a href="modificar_producto.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-outline-warning me-1" title="Modificar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="eliminar_producto.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto de la tienda?');">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-white-50">No hay productos registrados en la base de datos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>