<?php
// peleador_perfil.php (Vista Pública)
require_once '../admin/config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// 1. Validamos que venga un ID válido
$id_peleador = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

if (!$id_peleador) {
    // Si no hay ID o intentan jugar con la URL, los regresamos al roster
    header("Location: peleadores.php");
    exit();
}

try {
    // 2. Traemos TODA la información del peleador
    $sql = "SELECT p.*, 
                   c.nombre AS categoria, 
                   pa.nombre AS pais, pa.codigo_iso
            FROM peleadores p
            LEFT JOIN categorias_peso c ON p.categoria_peso_id = c.id
            LEFT JOIN paises pa ON p.pais_id = pa.id
            WHERE p.id = :id";
            
    $stmt = $sistema->db->prepare($sql);
    $stmt->bindParam(':id', $id_peleador, PDO::PARAM_INT);
    $stmt->execute();
    
    $fighter = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si el ID no existe en la base de datos, lo regresamos
    if (!$fighter) {
        header("Location: peleadores.php");
        exit();
    }

} catch (PDOException $e) {
    die("Error al cargar el perfil: " . $e->getMessage());
}

// Calcular la edad
$edad = '--';
if ($fighter['fecha_nacimiento']) {
    $nacimiento = new DateTime($fighter['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;
}

include '../includes/public_header.php'; 
?>

<div class="container py-5">
    
    <a href="peleadores.php" class="text-white-50 text-decoration-none mb-4 d-inline-block">
        <i class="bi bi-arrow-left me-2"></i>Volver al Roster
    </a>

    <div class="row g-5">
        <div class="col-12 col-md-5 col-lg-4">
            <div class="position-relative rounded overflow-hidden shadow-lg" style="border: 2px solid #2a2a2c; background-color: #111;">
                <?php if(!empty($fighter['imagen_url'])): ?>
                    <img src="../uploads/peleadores/<?= htmlspecialchars($fighter['imagen_url']) ?>" 
                         alt="<?= htmlspecialchars($fighter['nombre']) ?>" 
                         class="img-fluid w-100" style="object-fit: cover; object-position: top; min-height: 500px;">
                <?php else: ?>
                    <div class="d-flex justify-content-center align-items-center" style="height: 500px;">
                        <i class="bi bi-person text-secondary" style="font-size: 10rem;"></i>
                    </div>
                <?php endif; ?>
                
                <div class="position-absolute bottom-0 start-0 w-100 bg-danger text-center p-3 text-white fw-bold text-uppercase fs-5" style="font-family: 'Oswald', sans-serif;">
                    <?= htmlspecialchars($fighter['categoria'] ?? 'Sin Categoría') ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-7 col-lg-8 text-white">
            
            <div class="mb-4 border-bottom border-secondary pb-3">
                <?php if(!empty($fighter['apodo'])): ?>
                    <h4 class="text-danger text-uppercase mb-1" style="font-family: 'Oswald', sans-serif; letter-spacing: 2px;">
                        "<?= htmlspecialchars($fighter['apodo']) ?>"
                    </h4>
                <?php endif; ?>
                <h1 class="display-3 fw-bold text-uppercase m-0" style="font-family: 'Oswald', sans-serif;">
                    <?= htmlspecialchars($fighter['nombre']) ?>
                </h1>
                
                <div class="d-flex align-items-center mt-3 fs-5 text-white-50">
                    <?php if(!empty($fighter['codigo_iso'])): ?>
                        <img src="https://flagcdn.com/32x24/<?= strtolower($fighter['codigo_iso']) ?>.png" class="me-2 shadow-sm">
                    <?php endif; ?>
                    <?= htmlspecialchars($fighter['pais'] ?? 'País Desconocido') ?>
                </div>
            </div>

            <h5 class="text-uppercase text-white-50 mb-3" style="font-family: 'Oswald', sans-serif;">Estadísticas Físicas</h5>
            <div class="row g-3 mb-5">
                <div class="col-6 col-md-3">
                    <div class="bg-dark p-3 rounded text-center" style="border: 1px solid #2a2a2c;">
                        <span class="d-block text-white-50 small text-uppercase mb-1">Edad</span>
                        <span class="fs-4 fw-bold"><?= $edad ?></span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-dark p-3 rounded text-center" style="border: 1px solid #2a2a2c;">
                        <span class="d-block text-white-50 small text-uppercase mb-1">Altura</span>
                        <span class="fs-4 fw-bold"><?= !empty($fighter['estatura_cm']) ? htmlspecialchars($fighter['estatura_cm']) : '--' ?></span><span class="text-white-50 ms-1">cm</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-dark p-3 rounded text-center" style="border: 1px solid #2a2a2c;">
                        <span class="d-block text-white-50 small text-uppercase mb-1">Alcance</span>
                        <span class="fs-4 fw-bold"><?= !empty($fighter['alcance_cm']) ? htmlspecialchars($fighter['alcance_cm']) : '--' ?></span><span class="text-white-50 ms-1">cm</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-dark p-3 rounded text-center" style="border: 1px solid #2a2a2c;">
                        <span class="d-block text-white-50 small text-uppercase mb-1">Postura</span>
                        <span class="fs-5 fw-bold text-capitalize"><?= !empty($fighter['postura']) ? htmlspecialchars($fighter['postura']) : '--' ?></span>
                    </div>
                </div>
            </div>

            <h5 class="text-uppercase text-white-50 mb-3" style="font-family: 'Oswald', sans-serif;">Biografía</h5>
            <div class="bg-dark p-4 rounded" style="border: 1px solid #2a2a2c; font-size: 1.1rem; line-height: 1.8;">
                <?php if(!empty($fighter['biografia'])): ?>
                    <p class="m-0 text-light"><?= nl2br(htmlspecialchars($fighter['biografia'])) ?></p>
                <?php else: ?>
                    <p class="text-white-50 text-center m-0 py-3">
                        <i class="bi bi-journal-x d-block mb-2" style="font-size: 2rem;"></i>
                        Aún no hay una biografía registrada para este peleador.
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/public_footer.php'; ?>