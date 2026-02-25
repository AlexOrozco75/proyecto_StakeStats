<?php
// admin/index.php

// ESTA LÍNEA ES VITAL: Es la que trae el diseño oscuro, Bootstrap y el menú
include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: #2a2a2c !important;">
    <h1 class="h2 text-uppercase text-white m-0" style="font-family: 'Oswald', sans-serif;">
        Dashboard <span class="text-white-50 fs-4">/ Resumen General</span>
    </h1>
    <span class="badge bg-danger fs-6 p-2"><i class="bi bi-circle-fill text-white me-2" style="font-size: 8px;"></i>Sistema En Línea</span>
</div>

<div class="row g-4 mb-5">
    
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card-dash p-4 text-center h-100">
            <i class="bi bi-box-seam text-danger mb-3" style="font-size: 2.5rem;"></i>
            <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;">--</h3>
            <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Productos en Tienda</p>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="card-dash p-4 text-center h-100">
            <i class="bi bi-person-bounding-box text-danger mb-3" style="font-size: 2.5rem;"></i>
            <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;">--</h3>
            <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Peleadores Activos</p>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="card-dash p-4 text-center h-100">
            <i class="bi bi-calendar-check text-danger mb-3" style="font-size: 2.5rem;"></i>
            <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;">--</h3>
            <p class="text-white-50 text-uppercase mb-0 mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Eventos Creados</p>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="card-dash p-4 text-center h-100">
            <i class="bi bi-people text-danger mb-3" style="font-size: 2.5rem;"></i>
            <h3 class="fw-bold m-0 text-white" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem;">--</h3>
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

<?php 
// ESTA LÍNEA TAMBIÉN ES VITAL: Cierra la página correctamente
include 'footer.php'; 
?>