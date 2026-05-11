<?php
session_start();
require_once '../../admin/config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

$usuario_logueado = isset($_SESSION['id_usuario']);
$id_usuario_actual = $usuario_logueado ? $_SESSION['id_usuario'] : 0;

// --- LÓGICA PARA OBTENER EL FEED, LIKES Y COMENTARIOS ---
try {
    // Nota: Asumimos que la llave primaria de la tabla fight_club_posts es 'id_post' o 'id'.
    // Si tu columna se llama diferente (ej. solo 'id'), ajusta "f.id_post" por "f.id" en las consultas.
    
    // 1. Obtenemos los posts
    $sql_feed = "SELECT f.*, u.correo, u.id_usuario as id_autor 
                 FROM fight_club_posts f 
                 JOIN usuario u ON f.id_usuario = u.id_usuario 
                 ORDER BY f.fecha_creacion DESC";
    $stmt_feed = $sistema->db->prepare($sql_feed);
    $stmt_feed->execute();
    $publicaciones = $stmt_feed->fetchAll(PDO::FETCH_ASSOC);

    // Array para guardar todo estructurado
    $feed_completo = [];

    // 2. Por cada post, buscamos sus likes y comentarios
    foreach ($publicaciones as $post) {
        $id_post = isset($post['id_post']) ? $post['id_post'] : $post['id']; // Ajuste de seguridad por el nombre de la columna

        // Buscar total de likes
        $stmt_likes = $sistema->db->prepare("SELECT COUNT(*) as total FROM fight_club_likes WHERE id_post = :post");
        $stmt_likes->execute([':post' => $id_post]);
        $post['total_likes'] = $stmt_likes->fetch(PDO::FETCH_ASSOC)['total'];

        // Saber si YO le di like
        $post['mi_like'] = false;
        if ($usuario_logueado) {
            $stmt_mi_like = $sistema->db->prepare("SELECT 1 FROM fight_club_likes WHERE id_post = :post AND id_usuario = :user");
            $stmt_mi_like->execute([':post' => $id_post, ':user' => $id_usuario_actual]);
            $post['mi_like'] = $stmt_mi_like->fetch() ? true : false;
        }

        // Buscar comentarios de este post
        $sql_comentarios = "SELECT c.*, u.correo 
                            FROM fight_club_comentarios c 
                            JOIN usuario u ON c.id_usuario = u.id_usuario 
                            WHERE c.id_post = :post 
                            ORDER BY c.fecha_comentario ASC";
        $stmt_comentarios = $sistema->db->prepare($sql_comentarios);
        $stmt_comentarios->execute([':post' => $id_post]);
        $post['comentarios'] = $stmt_comentarios->fetchAll(PDO::FETCH_ASSOC);

        $feed_completo[] = $post;
    }

} catch (PDOException $e) {
    die("Error al cargar la arena: " . $e->getMessage());
}

include '../../includes/public_header.php'; 
?>

<style>
    .fight-club-container { max-width: 700px; margin: 0 auto; }
    .post-card { background-color: #111; border: 1px solid #2a2a2c; border-radius: 12px; overflow: hidden; }
    .user-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #d20a0a; }
    .create-post-textarea { background-color: #1a1a1c; border: 1px solid #333; color: white; resize: none; border-radius: 10px; }
    .create-post-textarea:focus { background-color: #222; border-color: #d20a0a; box-shadow: none; color: white; }
    .perfil-link { color: white; text-decoration: none; transition: color 0.2s; }
    .perfil-link:hover { color: #d20a0a; }
    
    /* Estilos Multimedia */
    .post-media-container { background-color: #000; width: 100%; text-align: center; border-top: 1px solid #222; border-bottom: 1px solid #222; }
    .post-img { max-width: 100%; max-height: 500px; object-fit: contain; }
    .post-video { width: 100%; max-height: 500px; }

    /* Estilos Interacciones */
    .btn-interaccion { color: #888; transition: color 0.3s, transform 0.2s; }
    .btn-interaccion:hover { color: #fff; transform: scale(1.05); }
    .liked { color: #d20a0a !important; }
    .comment-box { background-color: #1a1a1c; border-top: 1px solid #2a2a2c; }
    .comment-input { background: transparent; border: none; color: white; border-bottom: 1px solid #444; border-radius: 0; }
    .comment-input:focus { background: transparent; border-color: #d20a0a; box-shadow: none; color: white; }
</style>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-uppercase text-white" style="font-family: 'Oswald', sans-serif;">
            Fight <span class="text-danger">Club</span>
        </h1>
        <p class="text-white-50 fs-5">La red social oficial de los amantes del combate.</p>
    </div>

    <div class="fight-club-container">
        
        <div class="post-card p-4 mb-5 shadow">
            <?php if ($usuario_logueado): ?>
                <form action="procesar_post.php" method="POST" enctype="multipart/form-data">
                    <div class="d-flex gap-3 mb-3">
                        <div class="bg-dark rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 user-avatar">
                            <i class="bi bi-person-fill text-secondary fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <?php $nombre_user = explode('@', $_SESSION['correo'])[0]; ?>
                            <textarea class="form-control create-post-textarea p-3" rows="3" name="mensaje" placeholder="¿Qué tienes en mente, <?= htmlspecialchars(ucfirst($nombre_user)) ?>?"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top border-dark pt-3">
                        <div class="d-flex gap-2">
                            <label class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold" style="cursor: pointer;">
                                <i class="bi bi-camera-fill me-1 text-danger"></i> MULTIMEDIA
                                <input type="file" name="media" accept="image/*,video/mp4,video/webm" style="display: none;">
                            </label>
                        </div>
                        <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill">LANZAR GOLPE</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-2">
                    <p class="text-white-50 mb-3">Inicia sesión para compartir tus predicciones y fotos.</p>
                    <a href="../login.php" class="btn btn-outline-danger fw-bold rounded-pill px-4">INGRESAR A LA ARENA</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="feed-posts">
            <?php if (count($feed_completo) > 0): ?>
                <?php foreach ($feed_completo as $post): 
                    $username = explode('@', $post['correo'])[0];
                    $fecha = date('d M, H:i', strtotime($post['fecha_creacion']));
                    $id_actual_post = isset($post['id_post']) ? $post['id_post'] : $post['id'];
                ?>
                    <div class="post-card mb-4 shadow-sm">
                        
                        <div class="p-3 d-flex align-items-center gap-3">
                            <a href="perfil.php?id=<?= $post['id_autor'] ?>" class="bg-dark rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 user-avatar text-decoration-none">
                                <i class="bi bi-person-fill text-secondary fs-4"></i>
                            </a>
                            <div>
                                <h6 class="mb-0 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif;">
                                    <a href="perfil.php?id=<?= $post['id_autor'] ?>" class="perfil-link">
                                        <?= htmlspecialchars($username) ?>
                                    </a>
                                </h6>
                                <small class="text-white-50" style="font-size: 0.75rem;"><?= $fecha ?></small>
                            </div>
                        </div>
                        
                        <?php if (!empty($post['mensaje'])): ?>
                            <div class="px-3 pb-3 text-white">
                                <p class="mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($post['mensaje']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($post['media_url'])): ?>
                            <div class="post-media-container">
                                <?php if ($post['media_tipo'] === 'imagen'): ?>
                                    <img src="../../uploads/club/<?= $post['media_url'] ?>" class="post-img" alt="Arena Image">
                                <?php elseif ($post['media_tipo'] === 'video'): ?>
                                    <video controls class="post-video"><source src="../../uploads/club/<?= $post['media_url'] ?>" type="video/mp4"></video>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-2 border-top border-dark d-flex justify-content-around">
                            <button class="btn btn-link text-decoration-none fw-bold btn-interaccion btn-like <?= $post['mi_like'] ? 'liked' : '' ?>" 
                                    data-post-id="<?= $id_actual_post ?>"
                                    <?= !$usuario_logueado ? 'disabled title="Inicia sesión para dar like"' : '' ?>>
                                <i class="bi <?= $post['mi_like'] ? 'bi-heart-fill' : 'bi-heart' ?> icon-heart me-1"></i> 
                                <span class="like-count"><?= $post['total_likes'] ?></span> LIKES
                            </button>
                            
                            <button class="btn btn-link text-decoration-none fw-bold btn-interaccion" type="button" data-bs-toggle="collapse" data-bs-target="#comments-<?= $id_actual_post ?>" aria-expanded="false">
                                <i class="bi bi-chat-left-dots me-1"></i> <?= count($post['comentarios']) ?> COMENTARIOS
                            </button>
                        </div>

                        <div class="collapse comment-box" id="comments-<?= $id_actual_post ?>">
                            <div class="p-3">
                                <?php if (count($post['comentarios']) > 0): ?>
                                    <div class="mb-3" style="max-height: 250px; overflow-y: auto;">
                                        <?php foreach ($post['comentarios'] as $comentario): 
                                            $com_user = explode('@', $comentario['correo'])[0];
                                        ?>
                                            <div class="mb-2">
                                                <strong class="text-white" style="font-size: 0.85rem;"><?= htmlspecialchars($com_user) ?>:</strong>
                                                <span class="text-white-50" style="font-size: 0.85rem;"><?= htmlspecialchars($comentario['comentario']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-white-50 mb-3" style="font-size: 0.85rem;">No hay comentarios. Sé el primero.</p>
                                <?php endif; ?>

                                <?php if ($usuario_logueado): ?>
                                    <form action="procesar_comentario.php" method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="id_post" value="<?= $id_actual_post ?>">
                                        <input type="text" name="comentario" class="form-control form-control-sm comment-input" placeholder="Escribe un comentario..." required>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-send"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-white-50 py-5">
                    <i class="bi bi-megaphone fs-1 d-block mb-3"></i>
                    <p class="fs-5">No hay actividad en la arena todavía.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const likeButtons = document.querySelectorAll('.btn-like');

    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.hasAttribute('disabled')) return; // No hacer nada si no está logueado

            const postId = this.getAttribute('data-post-id');
            const icon = this.querySelector('.icon-heart');
            const counter = this.querySelector('.like-count');

            // Hacemos la petición a procesar_like.php
            fetch('procesar_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_post=' + postId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizamos el número
                    counter.textContent = data.total;
                    
                    // Cambiamos el diseño según si se agregó o quitó
                    if (data.accion === 'agregado') {
                        this.classList.add('liked');
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                    } else {
                        this.classList.remove('liked');
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                    }
                } else {
                    console.error("Error al procesar el like:", data.error);
                }
            })
            .catch(error => console.error('Error de red:', error));
        });
    });
});
</script>

<?php include '../../includes/public_footer.php'; ?>