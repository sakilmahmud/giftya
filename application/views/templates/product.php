<!-- views/templates/product.php -->
<?php
$image_url = ($product['featured_image'] != "") ? base_url('uploads/products/' . $product['featured_image']) : base_url('assets/uploads/no_image.jpeg');
?>
<?php $endpoint = ($product['slug'] != "") ? $product['slug'] : $product['id']; ?>
<div class="card h-100 shadow-sm rounded-lg product-card">
    <div class="single_product_listing_img card-img-top overflow-hidden">
        <a href="<?php echo base_url('products/' . $endpoint); ?>">
            <img
                src="<?php echo $image_url; ?>" class="img-fluid w-100"
                alt="<?php echo $product['name']; ?>" style="height: 200px; object-fit: cover;" />
        </a>
    </div>
    
    <div class="single_product_listing_content card-body d-flex flex-column">
        <h6 class="card-title mb-2"><a href="<?php echo base_url('products/' . $endpoint); ?>" class="text-decoration-none text-dark"><?php echo $product['name']; ?></a></h6>
        <div class="price mt-auto">
            <?php if ($product['sale_price'] > 0 && $product['sale_price'] < $product['regular_price']): ?>
                <p class="strike_price text-muted mb-0"><del>₹<?php echo number_format($product['regular_price'], 2); ?></del></p>
                <p class="main_value text-primary fw-bold fs-5">₹<?php echo number_format($product['sale_price'], 2); ?></p>
            <?php else: ?>
                <p class="main_value text-primary fw-bold fs-5">₹<?php echo number_format($product['regular_price'], 2); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .product-card .card-body {
        padding: 15px;
    }
    .product-card .card-title a {
        font-size: 1rem;
        line-height: 1.3;
        height: 2.6em; /* Limit to 2 lines */
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .product-card .price .main_value {
        color: var(--main-color) !important; /* Ensure main color is applied */
    }
</style>