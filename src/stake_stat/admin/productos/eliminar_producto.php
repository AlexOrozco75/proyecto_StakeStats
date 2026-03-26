<?php
// admin/eliminar_producto.php
require_once '../config/sistema.class.php';

session_start();
// Verificación de seguridad
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $sistema = new sistema();
    $sistema->conectar();

    // 1. Obtener la ruta de la imagen actual
    $stmt = $sistema->db->prepare("SELECT imagen_url FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // 2. Si existe la imagen físicamente (y no es un default link), la borramos del servidor
        $ruta_imagen = $producto['imagen_url'];
        if ($ruta_imagen && file_exists($ruta_imagen) && strpos($ruta_imagen, 'default-product.jpg') === false) {
            unlink($ruta_imagen);
        }

        // 3. Eliminar de la base de datos
        $stmt_delete = $sistema->db->prepare("DELETE FROM productos WHERE id = ?");
        $stmt_delete->execute([$id]);
    }
}

// Redirigir de regreso a la lista
header("Location: productos.php");
exit();
?>