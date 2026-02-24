<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stake Stats | Estadísticas de Combate</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Animaciones */
        .btn-animado { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .btn-animado:hover { transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(210, 10, 10, 0.5) !important; }
        .nav-link-animado { transition: color 0.3s ease, transform 0.3s ease; }
        .nav-link-animado:hover { color: var(--rojo-stake, #d20a0a) !important; transform: translateY(-2px); }
        .img-peleador { transition: transform 0.4s ease, filter 0.4s ease; filter: grayscale(100%); }
        .img-peleador:hover { transform: scale(1.1); filter: grayscale(0%); }
        
        /* Animación suave al hacer scroll */
        html { scroll-behavior: smooth; }

        /* Botones tipo UFC */
        .btn-ufc { border: 1px solid var(--gris-texto, #888); border-radius: 0; color: var(--blanco, #fff); transition: 0.2s; }
        .btn-ufc:hover { border-color: var(--blanco, #fff); background-color: var(--blanco, #fff); color: var(--negro-puro, #000); }
        .event-item { transition: background-color 0.3s ease; }
        .event-item:hover { background-color: rgba(255, 255, 255, 0.05); }

        /* Botón de Login Especial */
        .btn-login-nav {
            border: 2px solid var(--rojo-stake, #d20a0a);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login-nav:hover {
            background-color: var(--rojo-stake, #d20a0a);
            color: white;
            box-shadow: 0 0 15px rgba(210, 10, 10, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>

<body id="inicio" class="text-white" style="background-color: var(--negro-puro, #000);">

    <nav class="navbar navbar-expand-lg fixed-top shadow-sm py-2" style="background-color: var(--negro-suave, #111); border-bottom: 2px solid var(--rojo-stake, #d20a0a);">
        <div class="container">
            
            <a class="navbar-brand d-flex align-items-center gap-3" href="#inicio" style="text-decoration: none;">
                <img src="../images/logo.png" alt="Logo Stake" height="50">
                <span class="text-white" style="font-family: 'Oswald', sans-serif; font-size: 1.8rem; font-weight: bold; letter-spacing: 2px;">
                    STAKE <span style="color: var(--rojo-stake, #d20a0a);">STATS</span>
                </span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" style="border-color: var(--rojo-stake, #d20a0a);">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-4 align-items-center">
                    <li class="nav-item"><a class="nav-link nav-link-animado text-white fw-bold text-uppercase" href="index.php">Eventos</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-animado text-white fw-bold text-uppercase" href="rankings.php">Rankings</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-animado text-white fw-bold text-uppercase" href="peleadores.php">Peleadores</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-animado text-white fw-bold text-uppercase" href="highlights.php">Highlights</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-animado text-white fw-bold text-uppercase" href="tienda.php">Tienda</a></li>
                    
                    <?php if(isset($_SESSION['id_usuario'])): ?>
                        <li class="nav-item ms-lg-3 dropdown">
                            <a class="btn btn-login-nav d-flex align-items-center gap-2 fw-bold px-4 py-2 dropdown-toggle" href="#" id="menuUsuario" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px; border-radius: 4px;">
                                <i class="bi bi-person-circle fs-5"></i>
                                <?php 
                                    $nombre_usuario = explode('@', $_SESSION['correo'])[0];
                                    echo strtoupper($nombre_usuario); 
                                ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="menuUsuario" style="border: 1px solid var(--rojo-stake, #d20a0a);">
                                
                                <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                                    <li><a class="dropdown-item fw-bold text-warning" href="admin/index.php"><i class="bi bi-gear-fill me-2"></i>Panel de Control</a></li>
                                    <li><hr class="dropdown-divider" style="border-color: #333;"></li>
                                <?php endif; ?>

                                <li><a class="dropdown-item fw-bold text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a href="login.php" class="btn btn-login-nav d-flex align-items-center gap-2 fw-bold px-4 py-2" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px; border-radius: 4px;">
                                <i class="bi bi-person-circle fs-5"></i>
                                INGRESAR
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </nav>

    <section class="main-event position-relative" style="height: 100vh; background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.6)), url('https://via.placeholder.com/1920x1080'); background-size: cover; background-position: center; padding-top: 80px;">
        <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-center gap-5 h-100">
            
            <div class="fighter-card text-center order-2 order-lg-1">
                <img src="../images/g.png" alt="Gaethje" class="img-fluid img-peleador" style="border-bottom: 5px solid var(--rojo-stake, #d20a0a); max-height: 400px;">
                <h2 class="mt-3 fw-bold" style="font-family: 'Oswald', sans-serif;">CAMPEÓN</h2>
            </div>
            
            <div class="event-details text-center order-1 order-lg-2 z-3">
                <span class="badge mb-3 d-inline-flex align-items-center gap-2 px-3 py-2 shadow" style="background-color: var(--rojo-stake, #d20a0a); font-size: 1rem; border-radius: 4px;">
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    EN VIVO
                </span>
                <h1 style="font-family: 'Oswald', sans-serif; font-size: 4.5rem; text-shadow: 2px 2px 10px rgba(0,0,0,0.8);">STAKE FIGHT NIGHT</h1>
                <p class="matchup mb-2" style="color: var(--rojo-stake, #d20a0a); font-size: 2.2rem; font-weight: bold;">GAETHJE vs TOPURIA</p>
                <p class="date fs-5 text-light mb-4">Sábado, 20 de Mayo | Las Vegas, NV</p>
                
                <a href="#cartelera" class="btn btn-lg text-white fw-bold px-5 py-3 btn-animado" style="background-color: var(--rojo-stake, #d20a0a); border: none; font-family: 'Oswald', sans-serif; letter-spacing: 1px; border-radius: 4px;">
                    VER CARTELERA
                </a>
            </div>

            <div class="fighter-card text-center order-3 order-lg-3">
                <img src="../images/t.png" alt="Topuria" class="img-fluid img-peleador" style="border-bottom: 5px solid var(--rojo-stake, #d20a0a); max-height: 400px;">
                <h2 class="mt-3 fw-bold" style="font-family: 'Oswald', sans-serif;">CONTENDIENTE</h2>
            </div>
            
        </div>
    </section>

    <section id="cartelera" class="container py-5 mt-4">
        <h2 class="text-center mb-5" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem; letter-spacing: 2px;">CARTELERA DE EVENTOS</h2>

        <div class="row align-items-center border-bottom border-secondary py-4 event-item">
            <div class="col-12 col-md-2 text-center text-md-start mb-4 mb-md-0">
                <h5 class="fw-bold text-uppercase fst-italic" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">STAKE<br><span style="color: var(--rojo-stake, #d20a0a);">FIGHT NIGHT</span></h5>
            </div>
            
            <div class="col-12 col-md-5 d-flex justify-content-center align-items-end mb-4 mb-md-0 gap-4">
                <img src="../images/g.png" alt="Gaethje" style="height: 180px; width: auto; object-fit: cover; object-position: top;">
                <span class="fs-4 fw-bold mb-4" style="color: var(--gris-texto, #888);">VS</span>
                <img src="../images/t.png" alt="Topuria" style="height: 180px; width: auto; object-fit: cover; object-position: top;">
            </div>
            
            <div class="col-12 col-md-5 text-center text-md-start">
                <h4 class="fw-bold text-uppercase mb-2" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">GAETHJE VS TOPURIA</h4>
                <p class="mb-1" style="color: var(--gris-texto, #888); font-size: 0.9rem;">Sáb, Mayo 20 / 8:00 PM CST / Cartelera Estelar</p>
                <p class="mb-3" style="color: var(--gris-texto, #888); font-size: 0.9rem;">T-Mobile Arena<br>Las Vegas, NV United States</p>
                
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-md-start">
                    <button class="btn btn-ufc px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">CÓMO VER</button>
                    <button class="btn btn-ufc px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">BOLETOS</button>
                </div>
            </div>
        </div>

        <div class="row align-items-center border-bottom border-secondary py-4 event-item">
            <div class="col-12 col-md-2 text-center text-md-start mb-4 mb-md-0">
                <h5 class="fw-bold text-uppercase fst-italic" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">STAKE<br><span style="color: var(--rojo-stake, #d20a0a);">FIGHT NIGHT</span></h5>
            </div>
            
            <div class="col-12 col-md-5 d-flex justify-content-center align-items-end mb-4 mb-md-0 gap-4">
                <img src="../images/g.png" alt="Peleador 3" style="height: 180px; width: auto; object-fit: cover; object-position: top; filter: brightness(0.7);">
                <span class="fs-4 fw-bold mb-4" style="color: var(--gris-texto, #888);">VS</span>
                <img src="../images/t.png" alt="Peleador 4" style="height: 180px; width: auto; object-fit: cover; object-position: top; filter: brightness(0.7);">
            </div>
            
            <div class="col-12 col-md-5 text-center text-md-start">
                <h4 class="fw-bold text-uppercase mb-2" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">GARCÍA VS HERNÁNDEZ</h4>
                <p class="mb-1" style="color: var(--gris-texto, #888); font-size: 0.9rem;">Sáb, Jun 15 / 7:00 PM CST / Cartelera Estelar</p>
                <p class="mb-3" style="color: var(--gris-texto, #888); font-size: 0.9rem;">Toyota Center<br>Houston, TX United States</p>
                
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-md-start">
                    <button class="btn btn-ufc px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">CÓMO VER</button>
                    <button class="btn btn-ufc px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">BOLETOS</button>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>