<?php
// admin/index.php

// 1. Incluimos la conexión a la base de datos ANTES del header
require_once 'config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// ==========================================
// 2. CONSULTAS PARA LOS KPI'S (Tarjetas)
// ==========================================
$total_productos = 0;
$total_peleadores = 0;
$total_eventos = 0;
$total_usuarios = 0;

try {
    $stmt = $sistema->db->query("SELECT COUNT(id) FROM productos");
    $total_productos = $stmt->fetchColumn();

    $stmt = $sistema->db->query("SELECT COUNT(id) FROM peleadores");
    $total_peleadores = $stmt->fetchColumn();

    $stmt = $sistema->db->query("SELECT COUNT(id) FROM eventos");
    $total_eventos = $stmt->fetchColumn();

    $stmt = $sistema->db->query("SELECT COUNT(id_usuario) FROM usuario");
    $total_usuarios = $stmt->fetchColumn();
} catch (PDOException $e) {
    // Si alguna tabla no existe, ignoramos el error
}

// ==========================================
// 3. CONSULTAS PARA LAS GRÁFICAS (Chart.js)
// ==========================================
$labels_cat = []; $data_cat = [];
$labels_pais = []; $data_pais = [];

try {
    // Gráfica 1: Peleadores por Categoría de Peso
    $sqlGrafica1 = "SELECT c.nombre, COUNT(p.id) as cantidad 
                    FROM categorias_peso c 
                    LEFT JOIN peleadores p ON c.id = p.categoria_peso_id 
                    GROUP BY c.id, c.nombre";
    $stmtG1 = $sistema->db->query($sqlGrafica1);
    $datosG1 = $stmtG1->fetchAll(PDO::FETCH_ASSOC);

    foreach ($datosG1 as $row) {
        $labels_cat[] = $row['nombre'];
        $data_cat[] = $row['cantidad'];
    }

    // Gráfica 2: Peleadores por País
    $sqlGrafica2 = "SELECT pa.nombre, COUNT(p.id) as cantidad 
                    FROM paises pa 
                    JOIN peleadores p ON pa.id = p.pais_id 
                    GROUP BY pa.id, pa.nombre";
    $stmtG2 = $sistema->db->query($sqlGrafica2);
    $datosG2 = $stmtG2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($datosG2 as $row) {
        $labels_pais[] = $row['nombre'];
        $data_pais[] = $row['cantidad'];
    }
} catch (PDOException $e) {
    // Ignoramos si faltan tablas
}

// ESTA LÍNEA ES VITAL: Es la que trae el diseño oscuro, Bootstrap y el menú
include '../includes/admin_header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: #2a2a2c !important;">
        <h1 class="h2 text-uppercase text-white m-0" style="font-family: 'Oswald', sans-serif;">
            Dashboard <span class="text-white-50 fs-4">/ Resumen General</span>
        </h1>
        <span class="badge bg-danger fs-6 p-2"><i class="bi bi-circle-fill text-white me-2" style="font-size: 8px;"></i>Sistema En Línea</span>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card-dash p-4 text-center h-100 shadow-sm" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <i class="bi bi-box-seam text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;"><?= $total_productos ?></h3>
                <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Productos en Tienda</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card-dash p-4 text-center h-100 shadow-sm" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <i class="bi bi-person-bounding-box text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;"><?= $total_peleadores ?></h3>
                <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Peleadores Activos</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card-dash p-4 text-center h-100 shadow-sm" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <i class="bi bi-calendar-check text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;"><?= $total_eventos ?></h3>
                <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Eventos Creados</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card-dash p-4 text-center h-100 shadow-sm" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <i class="bi bi-people text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;"><?= $total_usuarios ?></h3>
                <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Usuarios Registrados</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card h-100 shadow-sm" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <div class="card-header border-bottom-0 pt-4 pb-0" style="background-color: transparent;">
                    <h5 class="text-white text-uppercase" style="font-family: 'Oswald', sans-serif;"><i class="bi bi-bar-chart-fill text-danger me-2"></i>Roster por Categoría</h5>
                </div>
                <div class="card-body">
                    <canvas id="graficaCategorias"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card h-100 shadow-sm" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <div class="card-header border-bottom-0 pt-4 pb-0" style="background-color: transparent;">
                    <h5 class="text-white text-uppercase" style="font-family: 'Oswald', sans-serif;"><i class="bi bi-globe-americas text-danger me-2"></i>Peleadores por País</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center pb-4">
                    <canvas id="graficaPaises"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Gráfica de Categorías
    const ctxCategorias = document.getElementById('graficaCategorias').getContext('2d');
    new Chart(ctxCategorias, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels_cat) ?>,
            datasets: [{
                label: 'Peleadores',
                data: <?= json_encode($data_cat) ?>,
                backgroundColor: 'rgba(210, 10, 10, 0.8)', // Rojo Stake
                borderColor: '#d20a0a',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { color: '#888', stepSize: 1 }, grid: { color: '#2a2a2c' } },
                x: { ticks: { color: '#888' }, grid: { display: false } }
            }
        }
    });

    // Gráfica de Países
    const ctxPaises = document.getElementById('graficaPaises').getContext('2d');
    new Chart(ctxPaises, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($labels_pais) ?>,
            datasets: [{
                data: <?= json_encode($data_pais) ?>,
                backgroundColor: ['#d20a0a', '#444444', '#ffffff', '#888888', '#ff4d4d'],
                borderColor: '#111', // Borde del mismo color que la tarjeta
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#888', padding: 20 } }
            }
        }
    });
</script>

<?php 
include '../includes/admin_footer.php'; 
?>