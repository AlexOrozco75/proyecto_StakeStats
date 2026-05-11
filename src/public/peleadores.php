<?php
// peleadores.php (Vista Pública)
require_once '../admin/config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// 1. Recibir parámetros por URL (Género por defecto 'M', y Categoría si existe)
$genero_seleccionado = (isset($_GET['genero']) && $_GET['genero'] == 'F') ? 'F' : 'M';
$categoria_seleccionada = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;

try {
    // 2. Consultar categorías de peso correspondientes AL GÉNERO SELECCIONADO
    $sqlCats = "SELECT id, nombre FROM categorias_peso WHERE genero = :genero ORDER BY limite_peso_lb ASC";
    $stmtCats = $sistema->db->prepare($sqlCats);
    $stmtCats->execute([':genero' => $genero_seleccionado]);
    $categorias = $stmtCats->fetchAll(PDO::FETCH_ASSOC);

    // 3. Armar la consulta de peleadores (Dinámica)
    $sql = "SELECT p.*, 
                   c.nombre AS categoria, 
                   pa.nombre AS pais, pa.codigo_iso
            FROM peleadores p
            LEFT JOIN categorias_peso c ON p.categoria_peso_id = c.id
            LEFT JOIN paises pa ON p.pais_id = pa.id
            WHERE p.genero = :genero";
            
    // Si hay una categoría seleccionada, agregamos el filtro
    if ($categoria_seleccionada) {
        $sql .= " AND p.categoria_peso_id = :cat_id";
    }
    
    $sql .= " ORDER BY p.nombre ASC";
            
    $stmt = $sistema->db->prepare($sql);
    
    // Bind de parámetros
    $stmt->bindParam(':genero', $genero_seleccionado, PDO::PARAM_STR);
    if ($categoria_seleccionada) {
        $stmt->bindParam(':cat_id', $categoria_seleccionada, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $peleadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar el roster: " . $e->getMessage());
}

// Función rápida para calcular la edad
function calcularEdad($fecha_nacimiento) {
    if (!$fecha_nacimiento) return '--';
    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento);
    return $edad->y;
}

// Header público
include '../includes/public_header.php'; 
?>

<style>
    .fighter-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        cursor: pointer;
    }
    
    .fighter-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(210, 10, 10, 0.4) !important;
        border-color: #d20a0a !important;
    }
</style>

<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="display-4 fw-bold text-uppercase text-white" style="font-family: 'Oswald', sans-serif;">
            Roster <span class="text-danger">Oficial</span>
        </h1>
        <p class="text-white-50 fs-5">Conoce a los atletas de Stake Stats y revisa sus estadísticas.</p>
    </div>

    <div class="d-flex justify-content-center mb-4">
        <div class="btn-group shadow-lg" role="group" style="border: 1px solid #d20a0a; border-radius: 6px; overflow: hidden;">
            <a href="peleadores.php?genero=M" class="btn btn-<?= $genero_seleccionado == 'M' ? 'danger' : 'dark text-white' ?> px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif;">
                <i class="bi bi-gender-male me-2"></i>Varonil
            </a>
            <a href="peleadores.php?genero=F" class="btn btn-<?= $genero_seleccionado == 'F' ? 'danger' : 'dark text-white' ?> px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif;">
                <i class="bi bi-gender-female me-2"></i>Femenil
            </a>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
        <a href="peleadores.php?genero=<?= $genero_seleccionado ?>" class="btn <?= !$categoria_seleccionada ? 'btn-danger' : 'btn-outline-danger' ?> rounded-pill px-4 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif;">
            Todos los Pesos
        </a>
        
        <?php foreach($categorias as $cat): ?>
            <a href="peleadores.php?genero=<?= $genero_seleccionado ?>&categoria=<?= $cat['id'] ?>" 
               class="btn <?= ($categoria_seleccionada === $cat['id']) ? 'btn-danger' : 'btn-outline-danger' ?> rounded-pill px-4 fw-bold text-uppercase" 
               style="font-family: 'Oswald', sans-serif;">
                <?= htmlspecialchars($cat['nombre']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <?php if(!empty($peleadores)): ?>
            <?php foreach($peleadores as $fighter): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    
                    <a href="peleador_perfil.php?id=<?= $fighter['id'] ?>" class="text-decoration-none d-block h-100">
                        
                        <div class="card h-100 bg-dark text-white fighter-card" style="border: 1px solid #2a2a2c; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                            
                            <div style="height: 350px; background-color: #111; position: relative;">
                                <?php if(!empty($fighter['imagen_url'])): ?>
                                    <img src="../uploads/peleadores/<?= htmlspecialchars($fighter['imagen_url']) ?>" 
                                         alt="<?= htmlspecialchars($fighter['nombre']) ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover; object-position: top;">
                                <?php else: ?>
                                    <div class="d-flex justify-content-center align-items-center h-100">
                                        <i class="bi bi-person text-secondary" style="font-size: 8rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="position-absolute top-0 end-0 p-2">
                                    <span class="badge bg-danger text-uppercase p-2 shadow-sm" style="font-family: 'Oswald', sans-serif;">
                                        <?= htmlspecialchars($fighter['categoria'] ?? 'Sin Categoría') ?>
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">
                                <h4 class="card-title fw-bold text-uppercase mb-0 text-white" style="font-family: 'Oswald', sans-serif;">
                                    <?= htmlspecialchars($fighter['nombre']) ?>
                                </h4>
                                <?php if(!empty($fighter['apodo'])): ?>
                                    <h6 class="text-danger text-uppercase mb-3" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">
                                        "<?= htmlspecialchars($fighter['apodo']) ?>"
                                    </h6>
                                <?php else: ?>
                                    <div class="mb-3"></div>
                                <?php endif; ?>

                                <ul class="list-group list-group-flush" style="border-top: 1px solid #2a2a2c;">
                                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-2 border-secondary">
                                        <span class="text-white-50"><i class="bi bi-globe me-2"></i>País</span>
                                        <span>
                                            <?php if(!empty($fighter['codigo_iso'])): ?>
                                                <img src="https://flagcdn.com/20x15/<?= strtolower($fighter['codigo_iso']) ?>.png" class="me-1">
                                            <?php endif; ?>
                                            <?= htmlspecialchars($fighter['pais'] ?? 'N/D') ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-2 border-secondary">
                                        <span class="text-white-50"><i class="bi bi-calendar me-2"></i>Edad</span>
                                        <span><?= calcularEdad($fighter['fecha_nacimiento']) ?> años</span>
                                    </li>
                                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-2 border-secondary">
                                        <span class="text-white-50"><i class="bi bi-arrows-vertical me-2"></i>Altura</span>
                                        <span><?= !empty($fighter['estatura_cm']) ? htmlspecialchars($fighter['estatura_cm']) . ' cm' : '--' ?></span>
                                    </li>
                                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-2 border-secondary">
                                        <span class="text-white-50"><i class="bi bi-arrows-expand me-2"></i>Alcance</span>
                                        <span><?= !empty($fighter['alcance_cm']) ? htmlspecialchars($fighter['alcance_cm']) . ' cm' : '--' ?></span>
                                    </li>
                                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-2 border-transparent">
                                        <span class="text-white-50"><i class="bi bi-person-arms-up me-2"></i>Postura</span>
                                        <span class="text-capitalize"><?= !empty($fighter['postura']) ? htmlspecialchars($fighter['postura']) : '--' ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search text-danger display-1 mb-3"></i>
                <h3 class="text-white">Aún no hay atletas registrados en esta sección.</h3>
                <a href="peleadores.php?genero=<?= $genero_seleccionado ?>" class="btn btn-outline-light mt-3">Ver todos los pesos</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
// Footer público
include '../includes/public_footer.php'; 
?>