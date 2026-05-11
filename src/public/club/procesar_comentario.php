<?php
// Activamos la visualización de errores para saber qué está fallando
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../admin/config/sistema.class.php';

// Si no hay sesión o no es POST, lo regresamos a index
if (!isset($_SESSION['id_usuario']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$sistema = new sistema();
$sistema->conectar();

$id_usuario = $_SESSION['id_usuario'];
$id_post = (int)$_POST['id_post'];
$comentario = trim($_POST['comentario']);

if (!empty($comentario) && $id_post > 0) {
    try {
        $sql = "INSERT INTO fight_club_comentarios (id_post, id_usuario, comentario) VALUES (:post, :user, :comentario)";
        $stmt = $sistema->db->prepare($sql);
        $stmt->execute([
            ':post' => $id_post,
            ':user' => $id_usuario,
            ':comentario' => $comentario
        ]);
    } catch (PDOException $e) {
        // Si hay error en la base de datos, lo mostramos claramente
        die("<div style='background: #333; color: #fff; padding: 20px; font-family: sans-serif;'>
                <h2>Error en la Base de Datos</h2>
                <p><strong>Detalle:</strong> " . $e->getMessage() . "</p>
                <a href='index.php' style='color: #d20a0a;'>Volver a la arena</a>
             </div>");
    }
}

// Redirección segura usando JavaScript como respaldo por si fallan los headers
if (!headers_sent()) {
    header("Location: index.php");
    exit;
} else {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}
?>