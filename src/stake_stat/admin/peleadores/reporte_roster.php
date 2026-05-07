<?php
require_once '../config/sistema.class.php';

// OPICIÓN A: Si usaste Composer, descomenta esta línea y borra la B
require_once '../../vendor/autoload.php'; 


// Importamos las clases de Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Conectar a la base de datos
$sistema = new sistema();
$sistema->conectar();

// 2. Traer los datos de los peleadores
$sql = "SELECT p.nombre, c.nombre AS categoria, pa.nombre AS pais, p.estatura_cm, p.alcance_cm 
        FROM peleadores p
        LEFT JOIN categorias_peso c ON p.categoria_peso_id = c.id
        LEFT JOIN paises pa ON p.pais_id = pa.id
        ORDER BY c.nombre, p.nombre";

$stmt = $sistema->db->prepare($sql);
$stmt->execute();
$peleadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Armar nuestro diseño en HTML (Guardado en una variable)
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Roster Oficial - Stake Stats</title>
    <style>
        body { 
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; 
            color: #333; 
        }
        .header { 
            text-align: center; 
            background-color: #d20a0a; /* Rojo Stake */
            color: white; 
            padding: 20px; 
            border-radius: 5px;
        }
        .header h1 { 
            margin: 0; 
            text-transform: uppercase; 
            font-size: 24px;
        }
        .date-info { 
            text-align: right; 
            font-size: 12px; 
            color: #666; 
            margin-top: 10px; 
            margin-bottom: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th { 
            background-color: #222; /* Negro oscuro */
            color: white; 
            padding: 12px; 
            text-align: left; 
            text-transform: uppercase; 
            font-size: 13px; 
        }
        td { 
            border-bottom: 1px solid #ddd; 
            padding: 10px; 
            font-size: 13px; 
        }
        tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Stake Stats - Roster Oficial</h1>
    </div>
    
    <div class="date-info">
        Reporte generado el: ' . date('d/m/Y H:i') . '
    </div>

    <table>
        <thead>
            <tr>
                <th>Peleador</th>
                <th>Categoría</th>
                <th>País</th>
                <th>Altura</th>
                <th>Alcance</th>
            </tr>
        </thead>
        <tbody>';

// Llenar la tabla dinámicamente
foreach ($peleadores as $fighter) {
    $html .= '<tr>
                <td><strong>' . htmlspecialchars($fighter['nombre']) . '</strong></td>
                <td>' . htmlspecialchars($fighter['categoria'] ?? 'N/D') . '</td>
                <td>' . htmlspecialchars($fighter['pais'] ?? 'N/D') . '</td>
                <td>' . (!empty($fighter['estatura_cm']) ? $fighter['estatura_cm'] . ' cm' : '--') . '</td>
                <td>' . (!empty($fighter['alcance_cm']) ? $fighter['alcance_cm'] . ' cm' : '--') . '</td>
              </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// 4. Configurar e instanciar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Necesario si quieres incluir imágenes por URL en el futuro

$dompdf = new Dompdf($options);

// 5. Cargar el HTML
$dompdf->loadHtml($html);

// 6. Configurar el tamaño del papel y la orientación (A4, vertical)
$dompdf->setPaper('A4', 'portrait');

// 7. Renderizar (Convertir el HTML a PDF)
$dompdf->render();

// 8. Enviar el PDF al navegador
// "Attachment" => false lo abre en la pestaña. Pon "true" si quieres que se descargue directo.
$dompdf->stream("Roster_StakeStats.pdf", array("Attachment" => false));
?>