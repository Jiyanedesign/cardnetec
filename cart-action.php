<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

header('Content-Type: application/json');

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

if ($action === 'add') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
    $snapshot = isset($_POST['snapshot']) ? trim($_POST['snapshot']) : '';

    $item = [
        'name' => $name,
        'slug' => $slug,
        'qty' => $qty,
        'price' => $price,
        'snapshot' => $snapshot,
        'subtotal' => $qty * $price
    ];

    $_SESSION['cart'][] = $item;

    echo json_encode([
        'success' => true,
        'cart_count' => count($_SESSION['cart'])
    ]);
    exit;
}

if ($action === 'add_multiple') {
    $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
    if (is_array($items)) {
        foreach ($items as $itemData) {
            $name = isset($itemData['name']) ? trim($itemData['name']) : '';
            $slug = isset($itemData['slug']) ? trim($itemData['slug']) : '';
            $qty = isset($itemData['qty']) ? (int)$itemData['qty'] : 1;
            $price = isset($itemData['price']) ? (float)$itemData['price'] : 0.0;
            $snapshot = isset($itemData['snapshot']) ? trim($itemData['snapshot']) : '';

            $item = [
                'name' => $name,
                'slug' => $slug,
                'qty' => $qty,
                'price' => $price,
                'snapshot' => $snapshot,
                'subtotal' => $qty * $price
            ];
            $_SESSION['cart'][] = $item;
        }
    }
    echo json_encode([
        'success' => true,
        'cart_count' => count($_SESSION['cart'])
    ]);
    exit;
}


if ($action === 'remove') {
    $index = isset($_REQUEST['index']) ? (int)$_REQUEST['index'] : -1;
    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }

    echo json_encode([
        'success' => true,
        'cart_count' => count($_SESSION['cart'])
    ]);
    exit;
}

if ($action === 'update_qty') {
    $index = isset($_REQUEST['index']) ? (int)$_REQUEST['index'] : -1;
    $qty = isset($_REQUEST['qty']) ? (int)$_REQUEST['qty'] : 1;
    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['qty'] = $qty;
        $_SESSION['cart'][$index]['subtotal'] = $qty * $_SESSION['cart'][$index]['price'];
    }

    echo json_encode([
        'success' => true,
        'cart_count' => count($_SESSION['cart'])
    ]);
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    echo json_encode([
        'success' => true,
        'cart_count' => 0
    ]);
    exit;
}

if ($action === 'get') {
    echo json_encode([
        'success' => true,
        'cart' => isset($_SESSION['cart']) ? $_SESSION['cart'] : []
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Acción no válida'
]);
exit;
