<?php
function calculateTotalPrice($items, $tax_rate = 0.1) {
    $subtotal = 0;
    
    foreach ($items as $item) {
        if (isset($item['price']) && isset($item['quantity'])) {
            $subtotal += $item['price'] * $item['quantity'];
        }
    }
    
    $tax = $subtotal * $tax_rate;
    $total = $subtotal + $tax;
    
    return [
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total' => $total
    ];
}

$cart = [
    ['name' => 'りんご', 'price' => 100, 'quantity' => 3],
    ['name' => 'バナナ', 'price' => 150, 'quantity' => 2],
];

$result = calculateTotalPrice($cart);
print_r($result);
?>
