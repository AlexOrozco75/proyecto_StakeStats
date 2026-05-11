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

    // Validar que no nos envíen campos vacíos (Ahora usamos nombre y ubicacion)
    if (!isset($datos['nombre']) || !isset($datos['fecha']) || !isset($datos['ubicacion'])) {
        http_response_code(400); 
        echo json_encode(["error" => "Faltan datos obligatorios (nombre, fecha, ubicacion)."]);
        exit;
    }

    try {
        $sistema = new sistema();
        $sistema->conectar();

        // Consulta SQL con los nombres correctos de tus columnas
        $sql = "INSERT INTO eventos (nombre, fecha, ubicacion) VALUES (:nombre, :fecha, :ubicacion)";
        $stmt = $sistema->db->prepare($sql);
        
        // Asignamos los valores
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':fecha', $datos['fecha']);
        $stmt->bindParam(':ubicacion', $datos['ubicacion']);
        
        // Ejecutamos la consulta
        $stmt->execute();

        // Respondemos con éxito 201
        http_response_code(201); 
        echo json_encode([
            "mensaje" => "¡Evento creado exitosamente!",
            "id_insertado" => $sistema->db->lastInsertId(),
            "evento" => $datos
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