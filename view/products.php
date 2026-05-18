<?php
require_once "header.php";
require_once __DIR__ . "/../model/ProductModel.php";

$gender = $_GET["gender"] ?? "";
$category = $_GET["category"] ?? "";
$products = getProducts("", $category, $gender, 0);
$categories = getCategories();
?>

<h2>Products</h2>
<form method="get" class="filters">
    <input type="text" name="q" placeholder="Search name">
    <select name="gender">
        <option value="">All Gender</option>
        <option value="Men" <?php if ($gender == "Men") echo "selected"; ?>>Men</option>
        <option value="Women" <?php if ($gender == "Women") echo "selected"; ?>>Women</option>
    </select>
    <select name="category">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat) { ?>
            <option value="<?php echo $cat["id"]; ?>" <?php if ($category == $cat["id"]) echo "selected"; ?>><?php echo clean($cat["gender"] . " - " . $cat["name"]); ?></option>
        <?php } ?>
    </select>
    <button type="submit">Filter</button>
</form>

<?php
if (isset($_GET["q"])) {
    $products = getProducts($_GET["q"], $category, $gender, 0);
}
?>

<div class="grid">
    <?php foreach ($products as $product) { ?>
        <div class="card">
            <img class="product-img" src="../<?php echo clean($product["image_path"]); ?>" alt="<?php echo clean($product["name"]); ?>">
            <h3><?php echo clean($product["name"]); ?></h3>
            <p><?php echo clean($product["gender"] . " / " . $product["category_name"]); ?></p>
            <p><?php echo clean(substr($product["description"], 0, 80)); ?></p>
            <p class="price">BDT <?php echo clean($product["price"]); ?></p>
            <a class="btn" href="product_details.php?id=<?php echo $product["id"]; ?>">View Details</a>
        </div>
    <?php } ?>
</div>

<?php require_once "footer.php"; ?>
