<?php
// 1. Configuramos los encabezados para indicar que la respuesta será JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Permite que otras apps lo consuman
header('Access-Control-Allow-Methods: GET');

// 2. Verificamos que la petición sea efectivamente un GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // 3. Incluimos tu clase de conexión. 
    // NOTA: Revisa que la ruta '../config/sistema.class.php' sea correcta 
    // dependiendo de dónde esté tu carpeta 'api' y tu carpeta 'config'.
    require_once '../admin/config/sistema.class.php';

    try {
        // Instanciamos y conectamos a la BD igual que en tu index
        $sistema = new sistema();
        $sistema->conectar();

        // 4. Tu misma consulta SQL exacta
        $sql = "SELECT p.id, p.nombre, p.apodo, p.imagen_url, 
                       c.nombre AS categoria, pa.nombre AS pais, pa.codigo_iso
                FROM peleadores p
                LEFT JOIN categorias_peso c ON p.categoria_peso_id = c.id
                LEFT JOIN paises pa ON p.pais_id = pa.id
                ORDER BY p.id DESC";
                
        $stmt = $sistema->db->query($sql);
        $peleadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Respondemos con éxito (200) y escupimos los datos en JSON
        http_response_code(200);
        echo json_encode($peleadores);

    } catch (PDOException $e) {
        // Si la base de datos falla, devolvemos un error 500 en formato JSON
        http_response_code(500);
        echo json_encode([
            "error" => "Error interno en el servidor",
            "detalle" => $e->getMessage()
        ]);
    }

} else {
    // Si intentan acceder con POST, DELETE u otro método, marcamos error 405
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Por favor usa GET."]);
}
?>