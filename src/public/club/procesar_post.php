<?php
session_start();
require_once '../../admin/config/sistema.class.php';

// Si no hay sesión o no vienen por POST, los pateamos de vuelta
if (!isset($_SESSION['id_usuario']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$sistema = new sistema();
$sistema->conectar();

$id_usuario = $_SESSION['id_usuario'];
$mensaje = trim($_POST['mensaje']);
$media_url = null;
$media_tipo = null;

// LÓGICA DE SUBIDA DE ARCHIVOS MULTIMEDIA
if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['media']['tmp_name'];
    $file_name = $_FILES['media']['name'];
    $file_type = mime_content_type($file_tmp);

    // Tipos permitidos: JPG, PNG, GIF, MP4, WEBM
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];
    
    if (in_array($file_type, $allowed_types)) {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        // Generamos un nombre único para no sobreescribir archivos
        $new_filename = uniqid('arena_') . '.' . $ext; 
        
        // Ruta donde se guardará físicamente
        $upload_dir = '../../uploads/club/'; 
        
        // Si no existe la carpeta, la creamos
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
            $media_url = $new_filename;
            $media_tipo = strpos($file_type, 'image') !== false ? 'imagen' : 'video';
        }
    }
}

// Guardar en la base de datos si hay mensaje O si hay un archivo
if (!empty($mensaje) || $media_url !== null) {
    try {
        $sql = "INSERT INTO fight_club_posts (id_usuario, mensaje, media_url, media_tipo) 
                VALUES (:id, :msg, :url, :tipo)";
        $stmt = $sistema->db->prepare($sql);
        $stmt->execute([
            ':id' => $id_usuario,
            ':msg' => $mensaje,
            ':url' => $media_url,
            ':tipo' => $media_tipo
        ]);
    } catch (PDOException $e) {
        die("Error crítico en la arena: " . $e->getMessage());
    }
}

// Al terminar, volvemos al muro
header("Location: index.php");
exit;