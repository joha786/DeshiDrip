<?php
require_once "header.php";
require_once __DIR__ . "/../model/ProductModel.php";

$product = getProductById($_GET["id"] ?? 0);

if (!$product) {
    echo "<div class='message'>Product not found.</div>";
    require_once "footer.php";
    exit;
}
?>

<div class="two-col">
    <div class="card">
        <img class="product-img" style="height:360px" src="../<?php echo clean($product["image_path"]); ?>" alt="<?php echo clean($product["name"]); ?>">
    </div>
    <div class="card">
        <h2><?php echo clean($product["name"]); ?></h2>
        <p><?php echo clean($product["gender"] . " / " . $product["category_name"]); ?></p>
        <p><?php echo clean($product["description"]); ?></p>
        <h3>Size Chart</h3>
        <p><?php echo nl2br(clean($product["size_chart"])); ?></p>
        <p class="price">BDT <?php echo clean($product["price"]); ?></p>
        <p>Stock: <?php echo clean($product["stock"]); ?></p>

        <?php if (isCustomer()) { ?>
            <label>Quantity</label>
            <input type="number" id="quantity" value="1" min="1" max="<?php echo $product["stock"]; ?>">
            <button type="button" onclick="addToCart(<?php echo $product["id"]; ?>)">Add To Cart</button>
            <div id="cartMsg"></div>
        <?php } elseif (!isLoggedIn()) { ?>
            <a class="btn" href="login.php">Login to Buy</a>
        <?php } ?>
    </div>
</div>

<script>
function addToCart(productId) {
    let quantity = parseInt(document.getElementById("quantity").value);
    if (!quantity || quantity < 1) {
        document.getElementById("cartMsg").innerHTML = "<p class='message'>Enter a valid quantity.</p>";
        return;
    }

    let data = new FormData();
    data.append("action", "add");
    data.append("product_id", productId);
    data.append("quantity", quantity);

    fetch("../ajax/cart_handler.php", { method: "POST", body: data })
    .then(res => res.json())
    .then(data => {
        document.getElementById("cartMsg").innerHTML = "<p class='message success'>" + data.message + "</p>";
        if (data.count !== undefined) {
            document.getElementById("cartCount").innerText = data.count;
        }
    });
}
</script>

<?php require_once "footer.php"; ?>
