<?php
require_once "header.php";
require_once __DIR__ . "/../model/ProductModel.php";

$featured = getProducts("", "", "", 6);
$menCategories = getCategories("Men");
$womenCategories = getCategories("Women");
?>

<section class="hero">
    <div>
        <h1>DeshiDrip Clothing</h1>
        <p>Browse simple, clean clothing collections for men and women. Guests can explore products, while customers can add items to cart and place orders.</p>
        <a class="btn" href="products.php">Browse Products</a>
    </div>
    <div class="hero-box">
        <h3>Shop By Gender</h3>
        <p><a class="btn secondary" href="products.php?gender=Men">Men</a> <a class="btn secondary" href="products.php?gender=Women">Women</a></p>
    </div>
</section>

<h2 class="section-title">Categories</h2>
<div class="two-col">
    <div class="card">
        <h3>Men</h3>
        <?php foreach ($menCategories as $category) { ?>
            <a class="btn secondary" href="products.php?gender=Men&category=<?php echo $category["id"]; ?>"><?php echo clean($category["name"]); ?></a>
        <?php } ?>
    </div>
    <div class="card">
        <h3>Women</h3>
        <?php foreach ($womenCategories as $category) { ?>
            <a class="btn secondary" href="products.php?gender=Women&category=<?php echo $category["id"]; ?>"><?php echo clean($category["name"]); ?></a>
        <?php } ?>
    </div>
</div>

<h2 class="section-title">Search Products</h2>
<div class="filters">
    <input type="text" id="searchText" placeholder="Search by product name">
    <select id="genderFilter">
        <option value="">All Gender</option>
        <option value="Men">Men</option>
        <option value="Women">Women</option>
    </select>
    <select id="categoryFilter">
        <option value="">All Categories</option>
        <?php foreach (getCategories() as $category) { ?>
            <option value="<?php echo $category["id"]; ?>"><?php echo clean($category["gender"] . " - " . $category["name"]); ?></option>
        <?php } ?>
    </select>
    <button type="button" onclick="searchProducts()">Search</button>
</div>
<div id="searchResult" class="grid"></div>

<h2 class="section-title">Featured Products</h2>
<div class="grid">
    <?php foreach ($featured as $product) { ?>
        <div class="card">
            <img class="product-img" src="../<?php echo clean($product["image_path"]); ?>" alt="<?php echo clean($product["name"]); ?>">
            <h3><?php echo clean($product["name"]); ?></h3>
            <p><?php echo clean($product["gender"] . " / " . $product["category_name"]); ?></p>
            <p class="price">BDT <?php echo clean($product["price"]); ?></p>
            <a class="btn" href="product_details.php?id=<?php echo $product["id"]; ?>">View Details</a>
        </div>
    <?php } ?>
</div>

<script>
function searchProducts() {
    let q = document.getElementById("searchText").value;
    let gender = document.getElementById("genderFilter").value;
    let category = document.getElementById("categoryFilter").value;

    fetch("../ajax/product_search.php?q=" + encodeURIComponent(q) + "&gender=" + encodeURIComponent(gender) + "&category=" + encodeURIComponent(category))
    .then(res => res.json())
    .then(data => {
        let html = "";
        data.products.forEach(function(product) {
            html += `<div class="card">
                <img class="product-img" src="../${product.image_path}" alt="">
                <h3>${product.name}</h3>
                <p>${product.gender} / ${product.category_name}</p>
                <p class="price">BDT ${product.price}</p>
                <a class="btn" href="product_details.php?id=${product.id}">View Details</a>
            </div>`;
        });
        document.getElementById("searchResult").innerHTML = html || "<p>No products found.</p>";
    });
}
</script>

<?php require_once "footer.php"; ?>
