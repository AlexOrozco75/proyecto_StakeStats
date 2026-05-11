<?php
// admin/usuarios/eliminar.php
require_once '../config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// Obtenemos el ID del usuario que viene por la URL
$id_usuario = $_GET['id'] ?? null;

if ($id_usuario) {
    try {
        // Empezamos la transacción de seguridad
        $sistema->db->beginTransaction();

        // 1. Primero borramos el registro de la tabla intermedia (su rol)
        $stmtRol = $sistema->db->prepare("DELETE FROM usuario_rol WHERE id_usuario = ?");
        $stmtRol->execute([$id_usuario]);

        // 2. Ahora sí, eliminamos al usuario de la tabla principal
        $stmtUser = $sistema->db->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $stmtUser->execute([$id_usuario]);

        // Confirmamos y guardamos los cambios permanentemente
        $sistema->db->commit();

        // Redirigimos de vuelta al grid de usuarios inmediatamente después de borrar
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        // Si algo sale mal, cancelamos la eliminación
        $sistema->db->rollBack();
        die("Error al eliminar el usuario: " . $e->getMessage());
    }
} else {
    // Si alguien intenta entrar directamente a la página sin un ID, lo regresamos al index
    header("Location: index.php");
    exit();
}