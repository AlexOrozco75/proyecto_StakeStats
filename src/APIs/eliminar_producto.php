<?php
// 1. Configuramos los encabezados (Nota que ahora permitimos DELETE)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');

// 2. Verificamos que la petición sea efectivamente un DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    // Incluimos tu clase de conexión
    require_once '../admin/config/sistema.class.php';

    // 3. Validamos que nos envíen el ID en la URL
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Falta el ID del producto a eliminar. Ejemplo: ?id=5"]);
        exit;
    }

    try {
        $sistema = new sistema();
        $sistema->conectar();

        // 4. Preparamos la consulta SQL para BORRAR en la tabla productos
        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $sistema->db->prepare($sql);
        
        // Asignamos el ID que viene por la URL
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();

        // 5. Verificamos si realmente se eliminó algo (rowCount nos dice cuántas filas se afectaron)
        if ($stmt->rowCount() > 0) {
            http_response_code(200); // OK
            echo json_encode([
                "mensaje" => "¡Producto eliminado correctamente!",
                "id_eliminado" => $_GET['id']
            ]);
        } else {
            // Si rowCount es 0, significa que el ID no existía en la base de datos
            http_response_code(404); // Not Found
            echo json_encode(["error" => "No se encontró ningún producto con ese ID."]);
        }

    } catch (PDOException $e) {
        // En caso de que el producto esté ligado a otras tablas y no se pueda borrar
        http_response_code(500);
        echo json_encode([
            "error" => "Error al intentar eliminar en la base de datos",
            "detalle" => $e->getMessage()
        ]);
    }

} else {
    // Si intentan usar GET o POST, marcamos error
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Por favor usa DELETE."]);
}
?>