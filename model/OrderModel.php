<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/CartModel.php";

function createOrderFromCart($userId, $paymentMethod, $transactionId)
{
    global $conn;

    $userId = (int) $userId;
    $paymentMethod = mysqli_real_escape_string($conn, $paymentMethod);
    $transactionId = mysqli_real_escape_string($conn, $transactionId);
    $items = getCartItems($userId);

    if (count($items) == 0) {
        return 0;
    }

    $total = 0;
    foreach ($items as $item) {
        if ((int) $item["quantity"] > (int) $item["stock"]) {
            return 0;
        }
        $total += ((float) $item["price"] * (int) $item["quantity"]);
    }

    mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, status) VALUES ($userId, $total, 'pending')");
    $orderId = mysqli_insert_id($conn);

    foreach ($items as $item) {
        $productId = (int) $item["product_id"];
        $quantity = (int) $item["quantity"];
        $unitPrice = (float) $item["price"];
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                             VALUES ($orderId, $productId, $quantity, $unitPrice)");
        mysqli_query($conn, "UPDATE products SET stock = stock - $quantity WHERE id = $productId");
    }

    mysqli_query($conn, "INSERT INTO payments (order_id, amount, payment_method, transaction_id)
                         VALUES ($orderId, $total, '$paymentMethod', '$transactionId')");
    clearCart($userId);

    return $orderId;
}

function getAllOrders()
{
    global $conn;

    $query = "SELECT orders.*, users.name AS customer_name, users.email
              FROM orders
              INNER JOIN users ON orders.user_id = users.id
              ORDER BY orders.id DESC";
    $result = mysqli_query($conn, $query);
    $orders = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    return $orders;
}

function updateOrderStatus($orderId, $status)
{
    global $conn;

    $orderId = (int) $orderId;
    $status = mysqli_real_escape_string($conn, $status);

    return mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $orderId");
}

function getUserOrders($userId)
{
    global $conn;

    $userId = (int) $userId;
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC");
    $orders = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    return $orders;
}

function getOrderItems($orderId)
{
    global $conn;

    $orderId = (int) $orderId;
    $query = "SELECT order_items.*, products.name, products.image_path
              FROM order_items
              INNER JOIN products ON order_items.product_id = products.id
              WHERE order_items.order_id = $orderId";
    $result = mysqli_query($conn, $query);
    $items = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }

    return $items;
}

function getAllPurchaseHistory()
{
    global $conn;

    $query = "SELECT orders.*, users.name AS customer_name, users.email
              FROM orders
              INNER JOIN users ON orders.user_id = users.id
              ORDER BY orders.id DESC";
    $result = mysqli_query($conn, $query);
    $orders = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    return $orders;
}

function getDashboardCounts()
{
    global $conn;

    $counts = array();
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
    $counts["products"] = mysqli_fetch_assoc($result)["total"];
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'");
    $counts["customers"] = mysqli_fetch_assoc($result)["total"];
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
    $counts["orders"] = mysqli_fetch_assoc($result)["total"];
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE status = 'pending'");
    $counts["pending"] = mysqli_fetch_assoc($result)["total"];

    return $counts;
}
?>
