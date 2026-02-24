<?php
// tienda.php
session_start();
require_once '../admin/sistema.class.php';

// --- LÓGICA DEL CARRITO DE COMPRAS ---
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'agregar') {
        $id = $_POST['producto_id'];
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += 1;
        } else {
            $_SESSION['carrito'][$id] = [
                'nombre' => $_POST['nombre'],
                'precio' => $_POST['precio'],
                'imagen' => $_POST['imagen'],
                'cantidad' => 1
            ];
        }
    } elseif ($_POST['accion'] === 'vaciar') {
        $_SESSION['carrito'] = [];
    }
}

$total_articulos = 0;
$total_precio = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_articulos += $item['cantidad'];
    $total_precio += ($item['precio'] * $item['cantidad']);
}
// -------------------------------------

$sistema = new sistema();
$sistema->conectar();

try {
    $sql = "SELECT p.*, c.nombre AS categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias_productos c ON p.categoria_id = c.id";
    $stmt = $sistema->db->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stake Stats | Tienda Oficial</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: var(--negro-puro, #000); }
        .nav-link-animado { transition: color 0.3s ease, transform 0.3s ease; }
        .nav-link-animado:hover { color: var(--rojo-stake) !important; transform: translateY(-2px); }
        .tienda-container { padding-top: 100px; padding-bottom: 60px; }
        
        /* Estilos mejorados para las tarjetas (Minimalista pero estructurado) */
        .card-producto {
            background-color: var(--card-bg, #161618);
            border: 1px solid var(--border, #2a2a2c);
            border-radius: 8px;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .card-producto:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(210, 10, 10, 0.15);
            border-color: var(--rojo-stake, #d20a0a);
        }
        
        /* Contenedor estricto para imágenes */
        .img-wrapper {
            width: 100%;
            aspect-ratio: 1 / 1; /* Fuerza a que sea un cuadrado perfecto */
            overflow: hidden;
            background-color: #0b0b0d;
            position: relative;
        }
        .img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Recorta la imagen sin aplastarla */
            transition: transform 0.6s ease;
        }
        .card-producto:hover .img-wrapper img {
            transform: scale(1.08); /* Efecto zoom al pasar el ratón */
        }
        
        .categoria-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background-color: var(--rojo-stake, #d20a0a);
            color: white;
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 2;
        }

        .producto-desc {
            color: var(--text-muted, #888);
            font-size: 0.85rem;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Limita la descripción a 2 líneas */
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 15px;
        }

        /* Offcanvas (Carrito) */
        .offcanvas.text-bg-dark { background-color: var(--card-bg, #161618) !important; }
        .cart-item { border-bottom: 1px solid var(--border, #2a2a2c); padding-bottom: 15px; margin-bottom: 15px; }
        .cart-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    </style>
</head>

<body class="text-white">

    <nav class="navbar navbar-expand-lg fixed-top shadow-sm py-2" style="background-color: var(--negro-suave, #111); border-bottom: 2px solid var(--rojo-stake, #d20a0a);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="index.php" style="text-decoration: none;">
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
                    <li class="nav-item"><a class="nav-link nav-link-animado text-white fw-bold text-uppercase" style="color: var(--rojo-stake, #d20a0a) !important;" href="tienda.php">Tienda</a></li>
                    
                    <li class="nav-item ms-lg-3">
                        <button class="btn btn-outline-light d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoPanel">
                            <i class="bi bi-cart3"></i>
                            <span class="badge text-bg-danger rounded-pill"><?php echo $total_articulos; ?></span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="tienda-container container">
        
        <div class="text-center mb-5 pb-4" style="border-bottom: 1px solid var(--border, #2a2a2c);">
            <h1 style="font-family: 'Oswald', sans-serif; font-size: 3.5rem; letter-spacing: 2px; margin-bottom: 10px;">TIENDA OFICIAL</h1>
            <p class="text-muted text-uppercase letter-spacing-1">Equipamiento y merchandise exclusivo de combate</p>
        </div>

        <div class="row g-4">
            <?php if(!empty($productos)): ?>
                <?php foreach($productos as $prod): ?>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card-producto h-100 d-flex flex-column">
                        
                        <div class="img-wrapper">
                            <?php if(!empty($prod['categoria_nombre'])): ?>
                                <span class="categoria-badge"><?php echo htmlspecialchars($prod['categoria_nombre']); ?></span>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($prod['imagen_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                                 onerror="this.src='../images/default-product.jpg'">
                        </div>
                        
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h5 class="mb-2" style="font-family: 'Oswald', sans-serif; letter-spacing: 0.5px; text-transform: uppercase;">
                                <?php echo htmlspecialchars($prod['nombre']); ?>
                            </h5>
                            
                            <p class="producto-desc flex-grow-1">
                                <?php echo htmlspecialchars($prod['descripcion']); ?>
                            </p>
                            
                            <form method="POST" action="tienda.php" class="mt-auto pt-3 d-flex justify-content-between align-items-center" style="border-top: 1px solid rgba(255,255,255,0.05);">
                                <span class="fs-5 fw-bold" style="font-family: 'Oswald', sans-serif;">
                                    $<?php echo number_format($prod['precio'], 2); ?>
                                </span>
                                
                                <input type="hidden" name="accion" value="agregar">
                                <input type="hidden" name="producto_id" value="<?php echo $prod['id']; ?>">
                                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($prod['nombre']); ?>">
                                <input type="hidden" name="precio" value="<?php echo $prod['precio']; ?>">
                                <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($prod['imagen_url']); ?>">
                                
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">
                                    AGREGAR
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
                
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                    <h3 class="text-muted mt-3">Próximamente nuevo inventario.</h3>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="carritoPanel">
        <div class="offcanvas-header" style="border-bottom: 1px solid var(--border, #2a2a2c);">
            <h5 class="offcanvas-title" style="font-family: 'Oswald', sans-serif; font-size: 1.5rem;">
                <i class="bi bi-cart3 me-2"></i>TU CARRITO
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            
            <div class="flex-grow-1 overflow-auto">
                <?php if (empty($_SESSION['carrito'])): ?>
                    <div class="text-center text-muted mt-5">
                        <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                        <p class="mt-3">Tu carrito está vacío.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($_SESSION['carrito'] as $id => $item): ?>
                        <div class="cart-item d-flex gap-3 align-items-center">
                            <img src="<?php echo $item['imagen']; ?>" onerror="this.src='../images/default-product.jpg'" alt="<?php echo $item['nombre']; ?>">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif; font-size: 0.95rem;"><?php echo $item['nombre']; ?></h6>
                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.9rem;">
                                    <span>Cant: <?php echo $item['cantidad']; ?></span>
                                    <span class="fw-bold text-white">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-auto pt-3" style="border-top: 1px solid var(--border, #2a2a2c);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-5 text-muted">TOTAL:</span>
                    <span class="fs-3 fw-bold" style="font-family: 'Oswald', sans-serif; color: var(--rojo-stake, #d20a0a);">$<?php echo number_format($total_precio, 2); ?></span>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-light fw-bold py-2" <?php echo empty($_SESSION['carrito']) ? 'disabled' : ''; ?> style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">
                        PAGAR AHORA
                    </button>
                    
                    <?php if (!empty($_SESSION['carrito'])): ?>
                        <form method="POST" action="tienda.php" class="d-grid mt-1">
                            <input type="hidden" name="accion" value="vaciar">
                            <button type="submit" class="btn btn-link text-muted text-decoration-none btn-sm">Vaciar Carrito</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>