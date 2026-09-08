<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

header('Content-Type: application/json');

function resolveCartItemImage($slug, $image = '', $snapshot = '', $pdo = null) {
    if (!empty($snapshot)) {
        return $snapshot;
    }
    if (!empty($image)) {
        return $image;
    }
    if (!empty($slug) && $slug !== 'custom' && $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT image_main FROM productos WHERE slug = ? LIMIT 1");
            $stmt->execute([$slug]);
            $row = $stmt->fetch();
            if ($row && !empty($row['image_main'])) {
                return getUploadedImgUrl($row['image_main']);
            }
        } catch (Exception $e) {}
    }
    return 'uploads/carnet_mockup.webp';
}

function calculateCartTotals($cart) {
    $total_units = 0;
    foreach ($cart as $it) {
        $total_units += max(1, (int)($it['qty'] ?? 1));
    }
    return [
        'count' => count($cart),
        'units' => $total_units
    ];
}

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

if ($action === 'add') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
    $snapshot = isset($_POST['snapshot']) ? trim($_POST['snapshot']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';

    $resolvedImage = resolveCartItemImage($slug, $image, $snapshot, $pdo);

    // Verificar si el producto ya está en el carrito (sin personalización canvas) para acumular cantidad
    $existingIndex = -1;
    if (empty($snapshot)) {
        foreach ($_SESSION['cart'] as $idx => $existing) {
            if ($existing['slug'] === $slug && empty($existing['snapshot'])) {
                $existingIndex = $idx;
                break;
            }
        }
    }

    if ($existingIndex >= 0) {
        $_SESSION['cart'][$existingIndex]['qty'] += $qty;
        $_SESSION['cart'][$existingIndex]['image'] = $resolvedImage;
        $_SESSION['cart'][$existingIndex]['subtotal'] = $_SESSION['cart'][$existingIndex]['qty'] * $price;
    } else {
        $item = [
            'name' => $name,
            'slug' => $slug,
            'qty' => $qty,
            'price' => $price,
            'snapshot' => $snapshot,
            'image' => $resolvedImage,
            'subtotal' => $qty * $price
        ];
        $_SESSION['cart'][] = $item;
    }

    $totals = calculateCartTotals($_SESSION['cart']);

    echo json_encode([
        'success' => true,
        'cart_count' => $totals['count'],
        'total_units' => $totals['units'],
        'cart' => $_SESSION['cart']
    ]);
    exit;
}

if ($action === 'add_multiple') {
    $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
    if (is_array($items)) {
        foreach ($items as $itemData) {
            $name = isset($itemData['name']) ? trim($itemData['name']) : '';
            $slug = isset($itemData['slug']) ? trim($itemData['slug']) : '';
            $qty = isset($itemData['qty']) ? max(1, (int)$itemData['qty']) : 1;
            $price = isset($itemData['price']) ? (float)$itemData['price'] : 0.0;
            $snapshot = isset($itemData['snapshot']) ? trim($itemData['snapshot']) : '';
            $image = isset($itemData['image']) ? trim($itemData['image']) : '';

            $resolvedImage = resolveCartItemImage($slug, $image, $snapshot, $pdo);

            $item = [
                'name' => $name,
                'slug' => $slug,
                'qty' => $qty,
                'price' => $price,
                'snapshot' => $snapshot,
                'image' => $resolvedImage,
                'subtotal' => $qty * $price
            ];
            $_SESSION['cart'][] = $item;
        }
    }
    $totals = calculateCartTotals($_SESSION['cart']);
    echo json_encode([
        'success' => true,
        'cart_count' => $totals['count'],
        'total_units' => $totals['units'],
        'cart' => $_SESSION['cart']
    ]);
    exit;
}

if ($action === 'remove') {
    $index = isset($_REQUEST['index']) ? (int)$_REQUEST['index'] : -1;
    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }

    $totals = calculateCartTotals($_SESSION['cart']);
    echo json_encode([
        'success' => true,
        'cart_count' => $totals['count'],
        'total_units' => $totals['units'],
        'cart' => $_SESSION['cart']
    ]);
    exit;
}

if ($action === 'update_qty') {
    $index = isset($_REQUEST['index']) ? (int)$_REQUEST['index'] : -1;
    $qty = isset($_REQUEST['qty']) ? max(1, (int)$_REQUEST['qty']) : 1;
    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['qty'] = $qty;
        $_SESSION['cart'][$index]['subtotal'] = $qty * $_SESSION['cart'][$index]['price'];
    }

    $totals = calculateCartTotals($_SESSION['cart']);
    echo json_encode([
        'success' => true,
        'cart_count' => $totals['count'],
        'total_units' => $totals['units'],
        'cart' => $_SESSION['cart']
    ]);
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    echo json_encode([
        'success' => true,
        'cart_count' => 0,
        'total_units' => 0,
        'cart' => []
    ]);
    exit;
}

if ($action === 'get') {
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$c_item) {
            if (empty($c_item['image'])) {
                $c_item['image'] = resolveCartItemImage($c_item['slug'] ?? '', '', $c_item['snapshot'] ?? '', $pdo);
            }
        }
        unset($c_item);
    }
    $totals = calculateCartTotals($_SESSION['cart']);
    echo json_encode([
        'success' => true,
        'cart' => isset($_SESSION['cart']) ? $_SESSION['cart'] : [],
        'cart_count' => $totals['count'],
        'total_units' => $totals['units']
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Acción no válida'
]);
exit;
 