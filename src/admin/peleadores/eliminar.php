<?php
// admin/peleadores/eliminar.php
require_once '../config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// Validamos que venga un ID válido por la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_peleador = $_GET['id'];

    try {
        // 1. Primero, buscamos al peleador para saber cómo se llama su foto
        $stmt = $sistema->db->prepare("SELECT imagen_url FROM peleadores WHERE id = ?");
        $stmt->execute([$id_peleador]);
        $peleador = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el peleador existe...
        if ($peleador) {
            
            // 2. Borramos la imagen física del servidor (si es que tiene una)
            if (!empty($peleador['imagen_url'])) {
                $ruta_imagen = '../../uploads/peleadores/' . $peleador['imagen_url'];
                
                // file_exists comprueba que el archivo esté ahí, y unlink lo elimina
                if (file_exists($ruta_imagen)) {
                    unlink($ruta_imagen); 
                }
            }

            // 3. Borramos el registro de la base de datos
            $stmt_delete = $sistema->db->prepare("DELETE FROM peleadores WHERE id = ?");
            $stmt_delete->execute([$id_peleador]);
        }
        
    } catch (PDOException $e) {
        // Si hay algún error (por ejemplo, si este peleador está ligado a una pelea en otra tabla)
        // se detiene el proceso y te muestra qué pasó.
        die("Error al intentar eliminar el peleador: " . $e->getMessage());
    }
}

// 4. Finalmente, redirigimos de vuelta a la lista de peleadores
header("Location: index.php");
exit();
?>