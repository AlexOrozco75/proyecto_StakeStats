<?php
// Configuramos los encabezados
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Incluimos tu clase de conexión
    require_once '../admin/config/sistema.class.php';

    // Leer los datos JSON 
    $datos = json_decode(file_get_contents("php://input"), true);

    // Validar que nos envíen los campos obligatorios
    if (!isset($datos['nombre']) || !isset($datos['precio']) || !isset($datos['stock'])) {
        http_response_code(400); 
        echo json_encode(["error" => "Faltan datos obligatorios (nombre, precio, stock)."]);
        exit;
    }

    try {
        $sistema = new sistema();
        $sistema->conectar();

        // Consulta SQL ajustada a tu tabla 'productos' y 'categoria_id'
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) 
                VALUES (:nombre, :descripcion, :precio, :stock, :categoria_id)";
        $stmt = $sistema->db->prepare($sql);
        
        // Valores por defecto si no los envían
        $descripcion = isset($datos['descripcion']) ? $datos['descripcion'] : 'Sin descripción';
        $categoria_id = isset($datos['categoria_id']) ? $datos['categoria_id'] : 1; // Asumimos la categoría ID 1 por defecto

        // Asignamos los valores
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $datos['precio']);
        $stmt->bindParam(':stock', $datos['stock']);
        $stmt->bindParam(':categoria_id', $categoria_id);
        
        // Ejecutamos la consulta
        $stmt->execute();

        // Respondemos con éxito 201
        http_response_code(201); 
        echo json_encode([
            "mensaje" => "¡Producto agregado a la tienda exitosamente!",
            "id_producto" => $sistema->db->lastInsertId(),
            "producto" => $datos
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "error" => "Error al guardar en la base de datos",
            "detalle" => $e->getMessage()
        ]);
    }

} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Por favor usa POST."]);
}
?>