<?php
// tienda.php
require_once 'sistema.class.php'; // Nombre de archivo corregido


try {
    // Consulta para obtener productos y sus categorías
    $sql = "SELECT p.*, c.nombre AS categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias_productos c ON p.categoria_id = c.id";
    $stmt = $pdo->query($sql);
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
    <title>Stake Stats - Tienda</title>
    <link rel="stylesheet" href="css/styles.css"> </head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">STAKE<span>STATS</span></div>
        <ul class="nav-links">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="highlights.php">Highlights</a></li>
            <li><a href="tienda.php" class="active">Tienda</a></li>
        </ul>
    </nav>
</header>



<main class="tienda-container">
    <div class="shop-banner">
        <h1>TIENDA OFICIAL</h1>
        <p>Productos exclusivos para fanáticos del combate</p>
    </div>

    <div class="productos-grid">
        <?php foreach($productos as $prod): ?>
        <div class="producto-card">
            <?php if(!empty($prod['categoria_nombre'])): ?>
                <span class="categoria-tag"><?php echo htmlspecialchars($prod['categoria_nombre']); ?></span>
            <?php endif; ?>
            
            <div class="img-container">
                <img src="<?php echo htmlspecialchars($prod['imagen_url']); ?>" 
                     alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                     onerror="this.src='images/default-product.jpg'">
            </div>
            
            <div class="producto-info">
                <h3><?php echo htmlspecialchars($prod['nombre']); ?></h3>
                <p class="desc"><?php echo htmlspecialchars($prod['descripcion']); ?></p>
                <div class="precio-row">
                    <span class="precio">$<?php echo number_format($prod['precio'], 2); ?></span>
                    <button class="btn-add">Comprar</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>