<?php
// 1. Configuramos los encabezados
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // 2. Incluimos la clase de conexión
    require_once '../admin/config/sistema.class.php';

    // 3. Validamos que nos envíen un ID en la URL
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400); // 400 Bad Request
        echo json_encode(["error" => "Falta el ID. Debes proporcionarlo en la URL. Ejemplo: ?id=1"]);
        exit;
    }

    try {
        $sistema = new sistema();
        $sistema->conectar();

        // 4. Preparamos la consulta SQL agregando la condición WHERE p.id = :id
        $sql = "SELECT p.id, p.nombre, p.apodo, p.imagen_url, 
                       c.nombre AS categoria, pa.nombre AS pais, pa.codigo_iso
                FROM peleadores p
                LEFT JOIN categorias_peso c ON p.categoria_peso_id = c.id
                LEFT JOIN paises pa ON p.pais_id = pa.id
                WHERE p.id = :id";
                
        $stmt = $sistema->db->prepare($sql);
        
        // Asignamos el ID que viene por la URL
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();

        // 5. Usamos fetch() en lugar de fetchAll() porque solo queremos UN registro
        $peleador = $stmt->fetch(PDO::FETCH_ASSOC);

        // 6. Verificamos si realmente se encontró un peleador con ese ID
        if ($peleador) {
            http_response_code(200); // OK
            echo json_encode($peleador);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "No se encontró ningún peleador con el ID proporcionado."]);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "error" => "Error interno en el servidor",
            "detalle" => $e->getMessage()
        ]);
    }

} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Por favor usa GET."]);
}
?>