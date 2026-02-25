<?php
// admin/header.php
session_start();

// CAPA DE SEGURIDAD: Si no ha iniciado sesión o no es admin, lo mandamos al login
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Stake Stats</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #0b0b0d; color: #fff; font-family: 'Roboto', sans-serif; }
        .navbar-admin { background-color: #161618; border-bottom: 3px solid var(--rojo-stake, #d20a0a); }
        .nav-link.active { color: var(--rojo-stake, #d20a0a) !important; font-weight: bold; }
        .nav-link:hover { color: #d20a0a !important; }
        .admin-title { font-family: 'Oswald', sans-serif; letter-spacing: 1px; }
        .card-dash { background-color: #1a1a1c; border: 1px solid #2a2a2c; border-radius: 8px; transition: transform 0.2s; }
        .card-dash:hover { transform: translateY(-5px); border-color: #d20a0a; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin sticky-top shadow">
        <div class="container-fluid px-4">
            
            <a class="navbar-brand d-flex align-items-center gap-2 admin-title text-uppercase" href="index.php">
                <i class="bi bi-shield-lock-fill text-danger fs-3"></i>
                Stake Stats <span class="text-danger">ADMIN</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-2 text-uppercase admin-title" style="font-size: 0.9rem;">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="productos.php"><i class="bi bi-box-seam me-1"></i> Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="peleadores.php"><i class="bi bi-person-bounding-box me-1"></i> Peleadores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="eventos.php"><i class="bi bi-calendar-event me-1"></i> Eventos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="usuarios.php"><i class="bi bi-people me-1"></i> Usuarios</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <a href="../public/index.php" class="btn btn-outline-light btn-sm admin-title" target="_blank">
                        <i class="bi bi-eye me-1"></i> Ver Sitio
                    </a>
                    <a href="../public/logout.php" class="btn btn-danger btn-sm admin-title">
                        <i class="bi bi-box-arrow-right me-1"></i> Salir
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container-fluid px-4 py-5 flex-grow-1">