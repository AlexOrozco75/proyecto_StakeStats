<?php
session_start();
require_once '../../admin/config/sistema.class.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_post'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado o datos incompletos']);
    exit;
}

$sistema = new sistema();
$sistema->conectar();

$id_usuario = $_SESSION['id_usuario'];
$id_post = (int)$_POST['id_post'];

try {
    // 1. Verificar si el usuario ya le dio like a este post
    $sql_check = "SELECT id_like FROM fight_club_likes WHERE id_post = :post AND id_usuario = :user";
    $stmt_check = $sistema->db->prepare($sql_check);
    $stmt_check->execute([':post' => $id_post, ':user' => $id_usuario]);
    $existe_like = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($existe_like) {
        // Si ya le dio like, se lo quitamos (Dislike)
        $sql_delete = "DELETE FROM fight_club_likes WHERE id_post = :post AND id_usuario = :user";
        $stmt_del = $sistema->db->prepare($sql_delete);
        $stmt_del->execute([':post' => $id_post, ':user' => $id_usuario]);
        $accion = 'quitado';
    } else {
        // Si no le ha dado like, se lo agregamos
        $sql_insert = "INSERT INTO fight_club_likes (id_post, id_usuario) VALUES (:post, :user)";
        $stmt_ins = $sistema->db->prepare($sql_insert);
        $stmt_ins->execute([':post' => $id_post, ':user' => $id_usuario]);
        $accion = 'agregado';
    }

    // 2. Contar el total de likes actualizados para este post
    $sql_count = "SELECT COUNT(*) as total FROM fight_club_likes WHERE id_post = :post";
    $stmt_count = $sistema->db->prepare($sql_count);
    $stmt_count->execute([':post' => $id_post]);
    $total_likes = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode(['success' => true, 'accion' => $accion, 'total' => $total_likes]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}