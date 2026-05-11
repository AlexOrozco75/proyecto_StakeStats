<?php
// public/rankings.php
require_once '../admin/config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// 1. Recibir el género por URL (Por defecto 'M')
$genero_seleccionado = (isset($_GET['genero']) && $_GET['genero'] == 'F') ? 'F' : 'M';

try {
    // 2. Obtener las categorías correspondientes al género seleccionado
    $sqlCats = "SELECT id, nombre, limite_peso_lb FROM categorias_peso WHERE genero = :genero ORDER BY limite_peso_lb ASC";
    $stmtCats = $sistema->db->prepare($sqlCats);
    $stmtCats->execute([':genero' => $genero_seleccionado]);
    $categorias = $stmtCats->fetchAll(PDO::FETCH_ASSOC);

    // 3. Obtener los peleadores de ese género
    // NOTA: Aquí los ordenamos por ID a modo de ejemplo. 
    // Si en el futuro agregas una columna "puntos" o "ranking", cambiaríamos el ORDER BY.
    $sqlPeleadores = "SELECT p.id, p.nombre, p.apodo, p.imagen_url, p.categoria_peso_id, pa.codigo_iso
                      FROM peleadores p
                      LEFT JOIN paises pa ON p.pais_id = pa.id
                      WHERE p.genero = :genero 
                      ORDER BY p.id ASC"; 
    
    $stmtPel = $sistema->db->prepare($sqlPeleadores);
    $stmtPel->execute([':genero' => $genero_seleccionado]);
    $peleadores_db = $stmtPel->fetchAll(PDO::FETCH_ASSOC);

    // 4. Agrupar a los peleadores por su categoría de peso para mostrarlos fácilmente
    $rankings = [];
    foreach ($peleadores_db as $peleador) {
        $rankings[$peleador['categoria_peso_id']][] = $peleador;
    }

} catch (PDOException $e) {
    die("Error al cargar los rankings: " . $e->getMessage());
}

// Header público
include '../includes/public_header.php'; 
?>

<style>
    .ranking-card {
        border: 1px solid #2a2a2c;
        border-radius: 8px;
        background-color: #111;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5);
    }
    .ranking-header {
        background: linear-gradient(90deg, #d20a0a 0%, #8b0000 100%);
        color: white;
        padding: 15px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        font-family: 'Oswald', sans-serif;
        text-transform: uppercase;
    }
    .ranking-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .ranking-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #2a2a2c;
        transition: background-color 0.2s;
    }
    .ranking-item:last-child {
        border-bottom: none;
    }
    .ranking-item:hover {
        background-color: #1a1a1c;
    }
    .rank-number {
        font-family: 'Oswald', sans-serif;
        font-size: 1.5rem;
        font-weight: bold;
        color: #555;
        width: 40px;
        text-align: center;
    }
    .rank-champion .rank-number {
        color: #d20a0a;
        font-size: 1.8rem;
    }
    .fighter-mini-img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        object-position: top;
        border: 2px solid #333;
    }
</style>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-uppercase text-white" style="font-family: 'Oswald', sans-serif;">
            Rankings <span class="text-danger">Globales</span>
        </h1>
        <p class="text-white-50 fs-5">Los mejores peleadores libra por libra y por división.</p>
    </div>

    <div class="d-flex justify-content-center mb-5">
        <div class="btn-group shadow-lg" role="group" style="border: 1px solid #d20a0a; border-radius: 6px; overflow: hidden;">
            <a href="rankings.php?genero=M" class="btn btn-<?= $genero_seleccionado == 'M' ? 'danger' : 'dark text-white' ?> px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif;">
                <i class="bi bi-gender-male me-2"></i>Varonil
            </a>
            <a href="rankings.php?genero=F" class="btn btn-<?= $genero_seleccionado == 'F' ? 'danger' : 'dark text-white' ?> px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif;">
                <i class="bi bi-gender-female me-2"></i>Femenil
            </a>
        </div>
    </div>

    <div class="row g-4">
        <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $cat): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="ranking-card h-100">
                        <div class="ranking-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 fw-bold"><?= htmlspecialchars($cat['nombre']) ?></h4>
                            <span class="badge bg-dark text-white border border-light"><?= $cat['limite_peso_lb'] ?> lbs</span>
                        </div>

                        <ul class="ranking-list">
                            <?php 
                            if (isset($rankings[$cat['id']]) && count($rankings[$cat['id']]) > 0): 
                                $posicion = 1;
                                foreach ($rankings[$cat['id']] as $fighter): 
                                    $es_campeon = ($posicion == 1);
                            ?>
                                <li class="ranking-item <?= $es_campeon ? 'rank-champion bg-dark' : '' ?>">
                                    <div class="rank-number">
                                        <?php if ($es_campeon): ?>
                                            <i class="bi bi-trophy-fill" title="Campeón"></i>
                                        <?php else: ?>
                                            <?= $posicion ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mx-3">
                                        <?php if(!empty($fighter['imagen_url'])): ?>
                                            <img src="../uploads/peleadores/<?= htmlspecialchars($fighter['imagen_url']) ?>" class="fighter-mini-img">
                                        <?php else: ?>
                                            <div class="fighter-mini-img d-flex align-items-center justify-content-center bg-secondary">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-grow-1">
                                        <a href="peleador_perfil.php?id=<?= $fighter['id'] ?>" class="text-white text-decoration-none fw-bold" style="font-family: 'Oswald', sans-serif; font-size: 1.1rem;">
                                            <?= htmlspecialchars($fighter['nombre']) ?>
                                        </a>
                                        <?php if(!empty($fighter['apodo'])): ?>
                                            <div class="text-danger" style="font-size: 0.85rem;">"<?= htmlspecialchars($fighter['apodo']) ?>"</div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if(!empty($fighter['codigo_iso'])): ?>
                                        <div>
                                            <img src="https://flagcdn.com/24x18/<?= strtolower($fighter['codigo_iso']) ?>.png" class="rounded shadow-sm">
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php 
                                $posicion++;
                                endforeach; 
                            else: 
                            ?>
                                <li class="text-center text-white-50 py-4">
                                    <i class="bi bi-person-x fs-3 d-block mb-2"></i>
                                    Aún no hay atletas rankeados.
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-white">
                No hay categorías registradas para este género.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
// Footer público
include '../includes/public_footer.php'; 
?>