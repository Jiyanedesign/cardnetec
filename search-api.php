<?php
/**
 * search-api.php - Endpoint AJAX para búsqueda de productos en tiempo real
 * CardNet.ec
 */
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once 'db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 20) : 8;

if (empty($query) || mb_strlen($query) < 2) {
    echo json_encode([
        'success' => true,
        'count' => 0,
        'query' => $query,
        'results' => []
    ]);
    exit;
}

try {
    $q_lower = mb_strtolower($query);
    $terms = [$query];
    
    // Sinónimos inteligentes del taller de personalización CardNet
    $synonyms_map = [
        'carnet' => ['credencial', 'portacredencial', 'pvc'],
        'carnets' => ['credencial', 'portacredencial', 'pvc'],
        'gafete' => ['credencial', 'portacredencial'],
        'gafetes' => ['credencial', 'portacredencial'],
        'fotocheck' => ['credencial', 'portacredencial'],
        'lanyard' => ['cinta', 'cordon'],
        'lanyards' => ['cinta', 'cordon'],
        'colgante' => ['cinta', 'cordon'],
        'tagua' => ['aretes', 'collar'],
        'placas' => ['placa'],
        'boligrafo' => ['esfero'],
        'lapicero' => ['esfero'],
        'llaveros' => ['llavero'],
        'cajas' => ['caja']
    ];

    foreach ($synonyms_map as $trigger => $syns) {
        if (str_contains($q_lower, $trigger)) {
            foreach ($syns as $syn) {
                if (!in_array($syn, $terms)) {
                    $terms[] = $syn;
                }
            }
        }
    }

    $whereParts = [];
    $params = [];
    foreach ($terms as $t) {
        $st = '%' . $t . '%';
        $whereParts[] = "(p.name LIKE ? OR p.description_short LIKE ? OR p.description_long LIKE ? OR p.sku LIKE ? OR p.category LIKE ? OR c.name LIKE ? OR p.slug LIKE ?)";
        for ($i = 0; $i < 7; $i++) {
            $params[] = $st;
        }
    }
    $whereSql = implode(' OR ', $whereParts);

    $startTerm = $query . '%';
    $mainTerm = '%' . $query . '%';

    $sql = "SELECT p.id, p.name, p.slug, p.price, p.image_main, p.description_short, p.sku,
                   COALESCE(c.name, p.category, 'Personalizado') as category_name,
                   COALESCE(c.slug, 'general') as category_slug
            FROM productos p
            LEFT JOIN categorias c ON p.category_id = c.id
            WHERE p.is_active = 1
              AND ($whereSql)
            ORDER BY 
              CASE 
                WHEN p.name LIKE ? THEN 1
                WHEN p.name LIKE ? THEN 2
                WHEN c.name LIKE ? THEN 3
                ELSE 4
              END,
              CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC,
              p.id DESC
            LIMIT " . (int)$limit;

    $orderParams = [$startTerm, $mainTerm, $mainTerm];
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($params, $orderParams));

    $items = $stmt->fetchAll();
    $results = [];

    foreach ($items as $item) {
        $imgUrl = getUploadedImgUrl($item['image_main'] ?? '', 'uploads/carnet_mockup.webp');
        $results[] = [
            'id' => (int)$item['id'],
            'name' => $item['name'],
            'slug' => $item['slug'],
            'sku' => $item['sku'] ?: '',
            'price' => number_format((float)$item['price'], 2),
            'category' => $item['category_name'],
            'category_slug' => $item['category_slug'],
            'desc' => mb_substr(strip_tags($item['description_short'] ?? ''), 0, 70),
            'image' => $imgUrl,
            'url' => 'producto.php?slug=' . urlencode($item['slug'])
        ];
    }

    echo json_encode([
        'success' => true,
        'count' => count($results),
        'query' => $query,
        'results' => $results
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en la búsqueda',
        'results' => []
    ]);
}
