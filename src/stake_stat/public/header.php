<?php 
// Iniciamos la sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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
        /* Animaciones globales y scroll suave */
        html { scroll-behavior: smooth; }
        .btn-animado { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .btn-animado:hover { transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(210, 10, 10, 0.5) !important; }
        .nav-link-animado { transition: color 0.3s ease, transform 0.3s ease; }
        .nav-link-animado:hover { color: var(--rojo-stake, #d20a0a) !important; transform: translateY(-2px); }
        .img-peleador { transition: transform 0.4s ease, filter 0.4s ease; filter: grayscale(100%); }
        .img-peleador:hover { transform: scale(1.1); filter: grayscale(0%); }
        
        /* Botones y utilidades */
        .btn-ufc { border: 1px solid var(--gris-texto, #888); border-radius: 0; color: var(--blanco, #fff); transition: 0.2s; }
        .btn-ufc:hover { border-color: var(--blanco, #fff); background-color: var(--blanco, #fff); color: var(--negro-puro, #000); }
        .btn-login-nav { border: 2px solid var(--rojo-stake, #d20a0a); color: white; transition: all 0.3s ease; }
        .btn-login-nav:hover { background-color: var(--rojo-stake, #d20a0a); color: white; box-shadow: 0 0 15px rgba(210, 10, 10, 0.4); transform: translateY(-2px); }
        
        /* Animación para el icono del carrito */
        .cart-icon { transition: transform 0.3s ease, color 0.3s ease; }
        .cart-icon:hover { color: var(--rojo-stake, #d20a0a) !important; transform: scale(1.1); }
    </style>
</head>

<body id="inicio" class="text-white" style="background-color: var(--negro-puro, #000);">

    <nav class="navbar navbar-expand-lg fixed-top shadow-sm py-2" style="background-color: var(--negro-suave, #111); border-bottom: 2px solid var(--rojo-stake, #d20a0a);">
        <div class="container">
            
            <?php 
                // Detectamos en qué página estamos actualmente
                $pagina_actual = basename($_SERVER['PHP_SELF']);
                // Si estamos en el index, el logo solo sube hacia arriba. Si no, nos lleva al index.
                $link_logo = ($pagina_actual == 'index.php') ? '#inicio' : 'index.php';
            ?>
            <a class="navbar-brand d-flex align-items-center gap-3" href="<?php echo $link_logo; ?>" style="text-decoration: none;">
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
                    
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link position-relative text-white cart-icon" data-bs-toggle="offcanvas" href="#carritoOffcanvas" role="button" style="cursor: pointer;">
                            <i class="bi bi-cart3 fs-4"></i>
                            <?php 
                                // Verificamos si existe el carrito en la sesión y contamos los productos
                                $total_items = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;
                                if ($total_items > 0): 
                            ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem; transform: translate(-30%, 20%);">
                                    <?php echo $total_items; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
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
                                    <li><a class="dropdown-item fw-bold text-warning" href="../admin/index.php"><i class="bi bi-gear-fill me-2"></i>Panel de Control</a></li>
                                    <li><hr class="dropdown-divider" style="border-color: #333;"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item fw-bold text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a href="login.php" class="btn btn-login-nav d-flex align-items-center gap-2 fw-bold px-4 py-2" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px; border-radius: 4px;">
                                <i class="bi bi-person-circle fs-5"></i> INGRESAR
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>