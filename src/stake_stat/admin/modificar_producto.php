<?php
// admin/tienda_admin/modificar_producto.php
require_once 'sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

$mensaje_error = '';
$mensaje_exito = '';

// Obtener el ID del producto (ya sea por GET al cargar la página o por POST al enviar el formulario)
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    header("Location: productos.php");
    exit();
}

// --------------------------------------------------------
// 1. PROCESAR EL FORMULARIO SI SE ENVIÓ POR POST
// --------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria_id = $_POST['categoria_id'];
    $imagen_actual = $_POST['imagen_actual']; // La ruta que ya estaba en la BD
    
    $imagen_url = $imagen_actual; // Por defecto, conservamos la imagen que ya tenía

    // Si el usuario subió una nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $nombre_archivo = time() . "_" . basename($_FILES['imagen']['name']);
        
        // Ruta física para guardar el archivo (subimos dos niveles desde tienda_admin)
        $ruta_fisica_destino = "../../images/" . $nombre_archivo;
        
        // Ruta lógica que guardaremos en la BD (para que la tienda pública lo lea como ../images/...)
        $ruta_bd = "../images/" . $nombre_archivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_fisica_destino)) {
            $imagen_url = $ruta_bd; // Actualizamos la variable con la nueva ruta
            
            // Opcional: Eliminar la imagen anterior del servidor
            $ruta_fisica_anterior = "../" . $imagen_actual; 
            if ($imagen_actual && file_exists($ruta_fisica_anterior) && strpos($imagen_actual, 'default-product.jpg') === false) {
                unlink($ruta_fisica_anterior);
            }
        } else {
            $mensaje_error = "Error al subir la nueva imagen.";
        }
    }

    // Actualizar en la base de datos
    if (empty($mensaje_error)) {
        try {
            $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ?, imagen_url = ? WHERE id = ?";
            $stmt = $sistema->db->prepare($sql);
            if($stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria_id, $imagen_url, $id])) {
                $mensaje_exito = "Producto actualizado correctamente.";
            }
        } catch (PDOException $e) {
            $mensaje_error = "Error DB: " . $e->getMessage();
        }
    }
}

// --------------------------------------------------------
// 2. OBTENER LOS DATOS ACTUALES PARA RELLENAR EL FORMULARIO
// --------------------------------------------------------
try {
    $stmt = $sistema->db->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto) {
        die("Producto no encontrado en la base de datos.");
    }
} catch (PDOException $e) {
    die("Error al obtener producto: " . $e->getMessage());
}

// Obtener categorías para el select
$stmt = $sistema->db->query("SELECT id, nombre FROM categorias_productos");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Incluimos el header (subiendo un nivel hacia la carpeta admin)
include 'header.php'; 
?>

<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="productos.php" class="text-white-50 text-decoration-none"><i class="bi bi-arrow-left"></i> Volver a Productos</a>
    </div>

    <div class="card-dash p-4 mx-auto" style="max-width: 800px; background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
        <h2 class="mb-4 text-uppercase" style="font-family: 'Oswald', sans-serif; border-bottom: 2px solid #ffc107; padding-bottom: 10px; color: #fff;">
            Modificar <span class="text-warning">Producto</span>
        </h2>

        <?php if($mensaje_error): ?> <div class="alert alert-danger"><?= $mensaje_error ?></div> <?php endif; ?>
        <?php if($mensaje_exito): ?> <div class="alert alert-success"><?= $mensaje_exito ?></div> <?php endif; ?>

        <form action="modificar_producto.php?id=<?= $producto['id'] ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
            <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($producto['imagen_url']) ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-white-50">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50">Categoría</label>
                    <select name="categoria_id" class="form-select text-white border-secondary" style="background-color: #0b0b0d;" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($producto['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12">
                    <label class="form-label text-white-50">Descripción</label>
                    <textarea name="descripcion" class="form-control text-white border-secondary" rows="3" style="background-color: #0b0b0d;" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50">Precio ($)</label>
                    <input type="number" step="0.01" name="precio" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" value="<?= htmlspecialchars($producto['precio']) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50">Stock</label>
                    <input type="number" name="stock" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" value="<?= htmlspecialchars($producto['stock']) ?>" required>
                </div>
                
                <div class="col-12 mt-4">
                    <label class="form-label text-white-50">Fotografía Actual</label><br>
                    
                    <?php if (!empty($producto['imagen_url'])): ?>
                        <?php 
                            // Como la BD guarda "../images/foto.jpg" y estamos en admin/tienda_admin/
                            // le agregamos un "../" extra para que el HTML suba los 2 niveles necesarios
                            $ruta_visualizacion = "" . $producto['imagen_url'];
                        ?>
                        <img src="<?= htmlspecialchars($ruta_visualizacion) ?>" alt="Producto actual" class="img-thumbnail mb-3" style="max-width: 200px; max-height: 200px; object-fit: cover; border-color: #333; background-color: #1a1a1a;">
                    <?php else: ?>
                        <div class="alert alert-dark p-2 text-center mb-3" style="max-width: 200px; background-color: #2a2a2a; border-color: #333;">
                            <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0"><small>Sin fotografía actual</small></p>
                        </div>
                    <?php endif; ?>
                    
                    <br>
                    <label class="form-label text-warning"><small>Subir nueva fotografía (Solo si deseas cambiarla)</small></label>
                    <input type="file" name="imagen" class="form-control text-white border-secondary" style="background-color: #0b0b0d;" accept="image/*">
                </div>
                
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-warning fw-bold px-4" style="font-family: 'Oswald', sans-serif;">ACTUALIZAR PRODUCTO</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>