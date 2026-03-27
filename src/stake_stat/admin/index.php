<?php
// admin/index.php

// 1. Incluimos la conexión a la base de datos ANTES del header
require_once 'config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// 2. Inicializamos las variables en 0 por defecto
$total_productos = 0;
$total_peleadores = 0;
$total_eventos = 0;
$total_usuarios = 0;

// 3. Hacemos las consultas para contar los registros
try {
    // Cuenta los productos
    $stmt = $sistema->db->query("SELECT COUNT(id) FROM productos");
    $total_productos = $stmt->fetchColumn();

    // Cuenta los peleadores
    $stmt = $sistema->db->query("SELECT COUNT(id) FROM peleadores");
    $total_peleadores = $stmt->fetchColumn();

    // Cuenta los eventos
    $stmt = $sistema->db->query("SELECT COUNT(id) FROM eventos");
    $total_eventos = $stmt->fetchColumn();

    // Cuenta los usuarios (Nota: si tu tabla se llama 'usuario' sin 's', cámbialo aquí)
    $stmt = $sistema->db->query("SELECT COUNT(id_usuario) FROM usuario");
    $total_usuarios = $stmt->fetchColumn();

} catch (PDOException $e) {
    // Si alguna tabla no existe aún, ignoramos el error silenciosamente 
    // y las variables se quedarán con el valor 0 que les dimos arriba.
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
            <div class="card-dash p-4 text-center h-100" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <i class="bi bi-box-seam text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;"><?= $total_productos ?></h3>
                <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Productos en Tienda</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card-dash p-4 text-center h-100" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <i class="bi bi-person-bounding-box text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;"><?= $total_peleadores ?></h3>
                <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Peleadores Activos</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card-dash p-4 text-center h-100" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <i class="bi bi-calendar-check text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;"><?= $total_eventos ?></h3>
                <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Eventos Creados</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card-dash p-4 text-center h-100" style="background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
                <i class="bi bi-people text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;"><?= $total_usuarios ?></h3>
                <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Usuarios Registrados</p>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12 text-center py-5 rounded" style="background-color: rgba(210, 10, 10, 0.05); border: 1px dashed #d20a0a;">
            <i class="bi bi-tools text-danger mb-3" style="font-size: 3rem;"></i>
            <h4 class="text-white" style="font-family: 'Oswald', sans-serif;">BIENVENIDO AL PANEL DE CONTROL</h4>
            <p class="text-white-50">Utiliza el menú superior para navegar y gestionar el contenido de Stake Stats.</p>
        </div>
    </div>
</div>

<?php 
include '../includes/admin_footer.php'; 
?>