<?php
// admin/peleadores/editar.php
require_once '../config/sistema.class.php';

$sistema = new sistema();
$sistema->conectar();

// Validamos que venga un ID por la URL
$id_peleador = $_GET['id'] ?? null;

if (!$id_peleador) {
    header("Location: index.php");
    exit();
}

$mensaje_error = '';
$mensaje_exito = '';

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apodo = trim($_POST['apodo']);
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $estatura_cm = !empty($_POST['estatura_cm']) ? $_POST['estatura_cm'] : null;
    $alcance_cm = !empty($_POST['alcance_cm']) ? $_POST['alcance_cm'] : null;
    $postura = $_POST['postura'];
    $pais_id = $_POST['pais_id'];
    $categoria_peso_id = $_POST['categoria_peso_id'];
    
    // Verificamos si subieron una nueva imagen
    $nueva_imagen = false;
    $nombre_foto = $_POST['imagen_actual']; // Conservamos la actual por defecto

    if (isset($_FILES['imagen_url']) && $_FILES['imagen_url']['error'] == 0) {
        $directorio_subida = '../../uploads/peleadores/';
        
        if (!file_exists($directorio_subida)) {
            mkdir($directorio_subida, 0777, true);
        }

        $extension = pathinfo($_FILES['imagen_url']['name'], PATHINFO_EXTENSION);
        $nombre_foto = time() . '_' . preg_replace("/[^a-zA-Z0-9]/", "", $nombre) . '.' . $extension;
        $ruta_final = $directorio_subida . $nombre_foto;

        if (move_uploaded_file($_FILES['imagen_url']['tmp_name'], $ruta_final)) {
            $nueva_imagen = true;
        } else {
            $mensaje_error = "Error al subir la nueva imagen.";
        }
    }

    if (empty($mensaje_error)) {
        try {
            if ($nueva_imagen) {
                $sql = "UPDATE peleadores SET nombre=?, apodo=?, fecha_nacimiento=?, estatura_cm=?, alcance_cm=?, postura=?, imagen_url=?, pais_id=?, categoria_peso_id=? WHERE id=?";
                $stmt = $sistema->db->prepare($sql);
                $stmt->execute([$nombre, $apodo, $fecha_nacimiento, $estatura_cm, $alcance_cm, $postura, $nombre_foto, $pais_id, $categoria_peso_id, $id_peleador]);
            } else {
                $sql = "UPDATE peleadores SET nombre=?, apodo=?, fecha_nacimiento=?, estatura_cm=?, alcance_cm=?, postura=?, pais_id=?, categoria_peso_id=? WHERE id=?";
                $stmt = $sistema->db->prepare($sql);
                $stmt->execute([$nombre, $apodo, $fecha_nacimiento, $estatura_cm, $alcance_cm, $postura, $pais_id, $categoria_peso_id, $id_peleador]);
            }
            
            $mensaje_exito = "Datos del peleador actualizados correctamente.";
            
        } catch (PDOException $e) {
            $mensaje_error = "Error al actualizar: " . $e->getMessage();
        }
    }
}

// Cargar los datos actuales del peleador
try {
    $stmt = $sistema->db->prepare("SELECT * FROM peleadores WHERE id = ?");
    $stmt->execute([$id_peleador]);
    $peleador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$peleador) {
        die("Peleador no encontrado.");
    }

    // Cargar catálogos
    $paises = $sistema->db->query("SELECT id, nombre FROM paises ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $categorias = $sistema->db->query("SELECT id, nombre, limite_peso_lb FROM categorias_peso ORDER BY limite_peso_lb ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

include '../../includes/admin_header.php'; 
?>

<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="index.php" class="text-white-50 text-decoration-none"><i class="bi bi-arrow-left"></i> Volver al Roster</a>
    </div>

    <div class="card-dash p-4 mx-auto" style="max-width: 900px; background-color: #111; border: 1px solid #2a2a2c; border-radius: 8px;">
        <h2 class="mb-4 text-uppercase" style="font-family: 'Oswald', sans-serif; border-bottom: 2px solid #ffc107; padding-bottom: 10px; color: #fff;">
            Editar <span class="text-warning">Peleador</span>
        </h2>

        <?php if($mensaje_error): ?> <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $mensaje_error ?></div> <?php endif; ?>
        <?php if($mensaje_exito): ?> <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= $mensaje_exito ?></div> <?php endif; ?>

        <form action="editar.php?id=<?= $peleador['id'] ?>" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($peleador['imagen_url'] ?? '') ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-white-50">Nombre Completo *</label>
                    <input type="text" name="nombre" class="form-control text-white bg-dark border-secondary" value="<?= htmlspecialchars($peleador['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white-50">Apodo</label>
                    <input type="text" name="apodo" class="form-control text-white bg-dark border-secondary" value="<?= htmlspecialchars($peleador['apodo'] ?? '') ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label text-white-50">País de Origen *</label>
                    <select name="pais_id" class="form-select text-white bg-dark border-secondary" required>
                        <option value="">Selecciona...</option>
                        <?php foreach($paises as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($peleador['pais_id'] == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white-50">Categoría de Peso *</label>
                    <select name="categoria_peso_id" class="form-select text-white bg-dark border-secondary" required>
                        <option value="">Selecciona...</option>
                        <?php foreach($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($peleador['categoria_peso_id'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nombre']) ?> (<?= $c['limite_peso_lb'] ?> lbs)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
    <label class="form-label text-white-50">Postura *</label>
    <select name="postura" class="form-select text-white bg-dark border-secondary" required>
        <option value="">Selecciona...</option>
        <?php 
            // Guardamos la postura limpia en una variable para que el código quede más legible
            $postura_actual = isset($peleador['postura']) ? strtolower(trim($peleador['postura'])) : ''; 
        ?>
        <option value="ortodoxo" <?= ($postura_actual === 'ortodoxo') ? 'selected' : '' ?>>Ortodoxa (Diestro)</option>
        <option value="zurdo" <?= ($postura_actual === 'zurdo') ? 'selected' : '' ?>>Zurda (Southpaw)</option>
        <option value="ambidextro" <?= ($postura_actual === 'ambidextro') ? 'selected' : '' ?>>Ambidextra (Switch)</option>
    </select>
</div>

                <div class="col-md-4">
                    <label class="form-label text-white-50">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control text-white bg-dark border-secondary" value="<?= htmlspecialchars($peleador['fecha_nacimiento'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white-50">Altura (cm)</label>
                    <input type="number" step="0.01" name="estatura_cm" class="form-control text-white bg-dark border-secondary" value="<?= htmlspecialchars($peleador['estatura_cm'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white-50">Alcance (cm)</label>
                    <input type="number" step="0.01" name="alcance_cm" class="form-control text-white bg-dark border-secondary" value="<?= htmlspecialchars($peleador['alcance_cm'] ?? '') ?>">
                </div>

                <div class="col-12 mt-4">
                    <label class="form-label text-white-50">Fotografía Actual</label>
                    <div class="mb-2">
                        <?php if(!empty($peleador['imagen_url'])): ?>
                            <img src="../../uploads/peleadores/<?= htmlspecialchars($peleador['imagen_url']) ?>" alt="Foto" class="img-thumbnail bg-dark border-secondary" style="max-height: 150px;">
                        <?php else: ?>
                            <div class="text-muted fst-italic">No hay imagen registrada.</div>
                        <?php endif; ?>
                    </div>
                    <label class="form-label text-white-50">Actualizar Fotografía (Opcional)</label>
                    <input type="file" name="imagen_url" class="form-control text-white bg-dark border-secondary" accept="image/jpeg, image/png, image/webp">
                    <div class="form-text text-muted">Sube una nueva imagen solo si deseas reemplazar la actual.</div>
                </div>

                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-warning fw-bold px-5 text-dark" style="font-family: 'Oswald', sans-serif;">
                        <i class="bi bi-arrow-clockwise me-1"></i> ACTUALIZAR PELEADOR
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>