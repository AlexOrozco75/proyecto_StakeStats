<?php
// admin/agregar_producto.php
require_once '../config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

$mensaje_error = '';
$mensaje_exito = '';

// Obtener categorías para el select
$stmt = $sistema->db->query("SELECT id, nombre FROM categorias_productos");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria_id = $_POST['categoria_id'];

    // Lógica del Uploader
    $imagen_url = '../images/default-product.jpg'; // Imagen por defecto
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $nombre_archivo = time() . "_" . basename($_FILES['imagen']['name']);
        $ruta_destino = "../../images/" . $nombre_archivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            $imagen_url = $ruta_destino; // Guardamos la ruta que necesita tienda.php
        } else {
            $mensaje_error = "Error al subir la imagen.";
        }
    }

    if (empty($mensaje_error)) {
        try {
            $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, imagen_url) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $sistema->db->prepare($sql);
            if($stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria_id, $imagen_url])) {
                $mensaje_exito = "Producto agregado correctamente.";
            }
        } catch (PDOException $e) {
            $mensaje_error = "Error DB: " . $e->getMessage();
        }
    }
}

include '../../includes/admin_header.php'; 
?>

<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="productos.php" class="text-white-50 text-decoration-none"><i class="bi bi-arrow-left"></i> Volver a Productos</a>
    </div>

    <div class="card-dash p-4 mx-auto" style="max-width: 800px;">
        <h2 class="mb-4 text-uppercase" style="font-family: 'Oswald', sans-serif; border-bottom: 2px solid #d20a0a; padding-bottom: 10px;">Agregar Nuevo Producto</h2>

        <?php if($mensaje_error): ?> <div class="alert alert-danger"><?= $mensaje_error ?></div> <?php endif; ?>
        <?php if($mensaje_exito): ?> <div class="alert alert-success"><?= $mensaje_exito ?></div> <?php endif; ?>

        <form action="agregar_producto.php" method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-white-50">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white-50">Categoría</label>
                    <select name="categoria_id" class="form-select text-white border-secondary" style="background-color: #0b0b0d;" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label text-white-50">Descripción</label>
                    <textarea name="descripcion" class="form-control text-white border-secondary" rows="3" style="background-color: #0b0b0d;" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white-50">Precio ($)</label>
                    <input type="number" step="0.01" name="precio" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white-50">Stock</label>
                    <input type="number" name="stock" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" required>
                </div>
                <div class="col-12 mt-4">
                    <label class="form-label text-white-50">Fotografía del Producto</label>
                    <input type="file" name="imagen" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" accept="image/*" required>
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-danger fw-bold px-4" style="font-family: 'Oswald', sans-serif;">GUARDAR PRODUCTO</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>