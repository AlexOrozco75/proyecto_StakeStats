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

// 1. INCLUIMOS EL HEADER (Después de la lógica PHP para que el contador se actualice)
include 'header.php'; 
?>

<style>
    .tienda-container { padding-top: 100px; padding-bottom: 60px; }
    
    /* Estilos mejorados para las tarjetas */
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
        aspect-ratio: 1 / 1; 
        overflow: hidden;
        background-color: #0b0b0d;
        position: relative;
    }
    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover; 
        transition: transform 0.6s ease;
    }
    .card-producto:hover .img-wrapper img {
        transform: scale(1.08); 
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
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 15px;
    }

    /* Offcanvas (Carrito) */
    .offcanvas.text-bg-dark { background-color: var(--card-bg, #161618) !important; }
    .cart-item { border-bottom: 1px solid var(--border, #2a2a2c); padding-bottom: 15px; margin-bottom: 15px; }
    .cart-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
</style>

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

<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="carritoOffcanvas">
    <div class="offcanvas-header" style="border-bottom: 1px solid var(--border, #2a2a2c);">
        <h5 class="offcanvas-title" style="font-family: 'Oswald', sans-serif; font-size: 1.5rem;">
            <i class="bi bi-cart3 me-2 text-danger"></i>TU CARRITO
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

<?php include 'footer.php'; ?>