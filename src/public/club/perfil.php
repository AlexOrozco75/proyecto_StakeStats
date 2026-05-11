<?php
session_start();
require_once '../../admin/config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// 1. DETERMINAR QUÉ PERFIL MOSTRAR
// Si hay un ID en la URL (perfil.php?id=5), mostramos ese. 
// Si no hay ID pero el usuario está logueado, mostramos el suyo.
if (isset($_GET['id'])) {
    $id_perfil = $_GET['id'];
} elseif (isset($_SESSION['id_usuario'])) {
    $id_perfil = $_SESSION['id_usuario'];
} else {
    // Si no hay nada, al Fight Club general
    header("Location: index.php");
    exit;
}

// Saber si el visitante es el dueño del perfil
$es_mi_propio_perfil = (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == $id_perfil);

try {
    // 2. OBTENER DATOS DEL USUARIO DEL PERFIL
    $sql_usuario = "SELECT correo FROM usuario WHERE id_usuario = :id";
    $stmt_us = $sistema->db->prepare($sql_usuario);
    $stmt_us->execute([':id' => $id_perfil]);
    $usuario_perfil = $stmt_us->fetch(PDO::FETCH_ASSOC);

    if (!$usuario_perfil) {
        die("Este peleador no ha sido encontrado en la arena.");
    }

    $nombre_perfil = explode('@', $usuario_perfil['correo'])[0];

    // 3. OBTENER POSTS DE ESTE USUARIO ESPECÍFICO
    $sql_posts = "SELECT * FROM fight_club_posts WHERE id_usuario = :id ORDER BY fecha_creacion DESC";
    $stmt_posts = $sistema->db->prepare($sql_posts);
    $stmt_posts->execute([':id' => $id_perfil]);
    $publicaciones = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la arena: " . $e->getMessage());
}

include '../../includes/public_header.php'; 
?>

<style>
    .profile-cover {
        height: 300px;
        background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%), 
                    url('https://images.unsplash.com/photo-1595079676339-1534801ad6cf?q=80&w=2070&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        border-radius: 0 0 20px 20px;
        position: relative;
    }
    .profile-avatar-container {
        position: absolute;
        bottom: -60px;
        left: 50%;
        transform: translateX(-50%);
    }
    .profile-avatar-big {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid #000;
        background-color: #111;
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(210, 10, 10, 0.3);
    }
    .profile-stats-card {
        background: #111;
        border: 1px solid #2a2a2c;
        border-radius: 12px;
        margin-top: 80px;
    }
    .post-card { 
        background-color: #111; 
        border: 1px solid #222; 
        border-radius: 12px; 
        transition: border-color 0.3s ease;
    }
    .post-card:hover { border-color: #d20a0a; }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="profile-cover">
                <div class="profile-avatar-container">
                    <div class="profile-avatar-big d-flex justify-content-center align-items-center text-secondary">
                        <i class="bi bi-person-fill" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>

            <div class="profile-stats-card p-4 text-center mb-5">
                <h1 class="display-6 fw-bold text-white text-uppercase mt-2" style="font-family: 'Oswald', sans-serif;">
                    <?= htmlspecialchars($nombre_perfil) ?>
                    <i class="bi bi-patch-check-fill text-primary ms-1" title="Peleador Verificado"></i>
                </h1>
                
                <p class="text-white-50 mb-3">Miembro del Fight Club desde <?= date('Y') ?></p>

                <div class="d-flex justify-content-center gap-4 border-top border-dark pt-3 mt-3">
                    <div class="text-center">
                        <h4 class="fw-bold text-white mb-0"><?= count($publicaciones) ?></h4>
                        <small class="text-uppercase text-danger fw-bold" style="font-size: 0.7rem;">GOLPES (POSTS)</small>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-bold text-white mb-0">0</h4>
                        <small class="text-uppercase text-danger fw-bold" style="font-size: 0.7rem;">SEGUIDORES</small>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-bold text-white mb-0">0</h4>
                        <small class="text-uppercase text-danger fw-bold" style="font-size: 0.7rem;">SIGUIENDO</small>
                    </div>
                </div>

                <?php if(!$es_mi_propio_perfil): ?>
                    <button class="btn btn-danger mt-4 px-5 rounded-pill fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i> SEGUIR PELEADOR
                    </button>
                <?php else: ?>
                    <button class="btn btn-outline-secondary mt-4 px-5 rounded-pill fw-bold text-white">
                        <i class="bi bi-gear-fill me-2"></i> EDITAR PERFIL
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="text-white mb-4 text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">
                <i class="bi bi-grid-3x3-gap me-2 text-danger"></i> Publicaciones recientes
            </h3>

            <?php if (count($publicaciones) > 0): ?>
                <?php foreach ($publicaciones as $post): 
                    $fecha = date('d M, Y - H:i', strtotime($post['fecha_creacion']));
                ?>
                    <div class="post-card mb-4">
                        <div class="p-3 border-bottom border-dark d-flex align-items-center gap-3">
                             <div class="bg-dark rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px; border: 1px solid #d20a0a;">
                                <i class="bi bi-person-fill text-secondary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white fw-bold"><?= htmlspecialchars($nombre_perfil) ?></h6>
                                <small class="text-white-50" style="font-size: 0.75rem;"><?= $fecha ?></small>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="fs-5 text-white-50 mb-0"><?= nl2br(htmlspecialchars($post['mensaje'])) ?></p>
                        </div>
                        <div class="p-3 border-top border-dark d-flex gap-4">
                            <span class="text-white-50" style="cursor: pointer;"><i class="bi bi-heart me-1"></i> 0</span>
                            <span class="text-white-50" style="cursor: pointer;"><i class="bi bi-chat-left me-1"></i> 0</span>
                            <span class="text-white-50" style="cursor: pointer;"><i class="bi bi-share me-1"></i></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="post-card p-5 text-center">
                    <i class="bi bi-camera-video-off fs-1 text-secondary mb-3"></i>
                    <p class="text-white-50 fs-5">Este peleador aún no ha compartido nada en la arena.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/public_footer.php'; ?>