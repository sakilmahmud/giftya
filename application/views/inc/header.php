<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $title; ?></title>
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo base_url(); ?>">
    <meta property="og:title" content="Giftya: Custom Prints, Personalized Gifts | T-Shirts, Mugs, Mirrors, Stone & Clock Printing">
    <meta property="og:description" content="Discover unique custom printing gifts at Giftya! Personalize T-shirts, coffee mugs, magic mirrors, stone, and clock prints. Perfect for every occasion and special memories.">
    <meta property="og:image" content="<?php echo base_url('assets/frontend/images/og_image.jpg'); ?>">
    <meta property="fb:app_id" content="YOUR_FACEBOOK_APP_ID"> <!-- Replace with your actual Facebook App ID -->

    <!-- Twitter Card (optional, but good practice) -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo base_url(); ?>">
    <meta property="twitter:title" content="Giftya - Your Perfect Gift Destination | Find Unique & Thoughtful Presents">
    <meta property="twitter:description" content="Discover a wide range of unique and thoughtful gifts for every occasion at Giftya. From personalized items to trending products, make every moment special.">
    <meta property="twitter:image" content="<?php echo base_url('assets/frontend/images/og_image.jpg'); ?>">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/css/animate.min.css') ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/css/aos.css') ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/css/bootstrap.min.css') ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/css/jquery.fancybox.min.css') ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/css/owl.carousel.min.css') ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/css/owl.theme.default.min.css') ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/css/style.css') ?>" />

    <script src="<?php echo base_url('assets/frontend/js/jquery3.7.1.min.js'); ?>"></script>

    <style>
        .nav-item {
            list-style: none;
        }

        .nav-item .sub-menu {
            display: none;
            /* Hide submenu initially */
            padding-left: 15px;
        }



        .search_suggestion_box {
            position: absolute;
            background: #fff;
            z-index: 1000;
            width: 350px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            margin-top: 5px;
            top: 65px;
        }

        .search_suggestion_box ul li {
            padding: 5px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .search_suggestion_box ul li img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 10px;
        }

        .search_suggestion_box ul li:hover {
            background: #f9f9f9;
        }

        @media screen and (max-width: 768px) {
            .search_suggestion_box {
                width: 300px !important;
                top: 110px !important;
            }
        }

        .rating_number {
            margin-left: 5px;
            color: #999;
            font-size: 13px;
        }

        .rating_stars {
            display: flex;
            align-items: center;
        }

        .rating_stars i {
            color: gold;
        }

        .share_items {
            position: absolute;
            top: 0;
            right: 0;
        }

        .delivery_date {
            font-size: 14px;
            color: red;
        }
    </style>
    <script>
        $(document).ready(function() {
            // Function to update cart count in header
            function updateHeaderCartCount() {
                $.ajax({
                    url: '<?php echo base_url('cart/get_count'); ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#cart-item-count').text(response.cart_item_count);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching cart count: " + xhr.responseText);
                    }
                });
            }

            // Call on page load
            updateHeaderCartCount();

            $(".product_search_input").on("keyup", function() {
                let query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: "<?php echo base_url('product/searchProducts'); ?>",
                        method: "POST",
                        data: {
                            keyword: query
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === "success") {
                                let list = "";
                                $.each(res.products, function(i, product) {
                                    let image = product.featured_image ? product.featured_image : 'default.png';
                                    list += `<li class="show_product_modal" data-id="${product.id}">
                                <img src="<?php echo base_url('uploads/products/'); ?>${image}" />
                                <span>${product.name}</span>
                            </li>`;
                                });
                                $(".search_results").html(list);
                                $(".search_suggestion_box").removeClass('d-none');
                            } else {
                                $(".search_results").html('<li>No products found</li>');
                                $(".search_suggestion_box").removeClass('d-none');
                            }
                        },
                    });
                } else {
                    $(".search_suggestion_box").addClass('d-none');
                }
            });

            // Optional: hide on click outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.header_search').length) {
                    $('.search_suggestion_box').addClass('d-none');
                }
            });
        });
    </script>
</head>

<body>
    <div class="top-slide-bar">
        <span class="font-medium">Free Shipping & Cash on Delivery | 7602855329</span>
    </div>
    <header class="web_header">
        <div class="container">
            <div class="header_top">
                <a class="navbar-brand" href="<?php echo base_url(); ?>">
                    <img src="<?php echo getSetting('admin_logo') ? base_url(getSetting('admin_logo')) : base_url('assets/frontend/images/logo.png') ?>" class="img-fluid" alt="logo" /></a>
                <div class="header_search">
                    <input type="text" placeholder="Search here..." />
                    <div class="header_search_btn">
                        <img src="<?php echo base_url('assets/frontend/images/search.png') ?>" class="img-fluid" alt="search" />
                    </div>
                </div>
                <div class="header_links">
                    <ul>
                        <li>
                            <a href="<?php echo base_url('login'); ?>"><img src="<?php echo base_url('assets/frontend/images/user.png') ?>" alt="user" />Account</a>
                        </li>
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/frontend/images/heart.png') ?>" alt="heart" />Wihlist</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('cart'); ?>" id="cart-link" data-cart-count="0"><img src="<?php echo base_url('assets/frontend/images/cart.png') ?>" alt="cart" />Cart <span id="cart-item-count" class="badge rounded-pill ms-1" style="background-color: var(--main-color); color: #fff;">0</span></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="header_bottom">
                <nav class="navbar navbar-expand-lg bg-body-tertiary">
                    <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($this->uri->segment(1) == '') ? 'active' : ''; ?>" aria-current="page" href="<?php echo base_url(); ?>">Home</a>
                            </li>
                            <?php
                            $get_product_types = get_product_types();
                            $current_slug = $this->uri->segment(2);

                            if (!empty($get_product_types)) :
                                foreach ($get_product_types as $get_product_type):
                                    $active_class = ($this->uri->segment(1) == 'product-type' && $current_slug == $get_product_type['slug']) ? 'active' : '';

                                    // Display the parent category
                                    echo '<li class="nav-item"><a class="nav-link ' . $active_class . '" href="' . base_url('product-type/') . $get_product_type['slug'] . '">' . $get_product_type['product_type_name'] . '</a>';

                                    // Check if there are child categories and display them as a dropdown or submenu
                                    if (!empty($get_product_type['children'])) {
                                        echo '<ul class="sub-menu">'; // Sub-menu class for child categories
                                        foreach ($get_product_type['children'] as $child) {
                                            $child_active_class = ($current_slug == $child['slug']) ? 'active' : '';
                                            echo '<li class="nav-item"><a class="nav-link ' . $child_active_class . '" href="' . base_url('product-type/') . $child['slug'] . '">' . $child['product_type_name'] . '</a></li>';
                                        }
                                        echo '</ul>';
                                    }

                                    echo '</li>';
                                endforeach;
                            endif;
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url('category/custom-t-shirts'); ?>">Custom T-Shirts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url('category/personalized-mugs'); ?>">Personalized Mugs</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url('category/magic-mirrors'); ?>">Magic Mirrors</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url('category/photo-gifts'); ?>">Photo Gifts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url('gifts-by-occasion'); ?>">Gifts by Occasion</a>
                            </li>

                            <li class="nav-item">
                                <a href="<?php echo base_url('contact'); ?>" class="nav-link <?php echo ($this->uri->segment(1) == 'contact') ? 'active' : ''; ?>">Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <header class="mobile_header">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <a class="navbar-brand" href="<?php echo base_url(); ?>">
                            <img src="<?php echo getSetting('admin_logo') ? base_url(getSetting('admin_logo')) : base_url('assets/frontend/images/logo.png') ?>" class="img-fluid" alt="logo" />
                        </a>
                    </div>
                    <div class="col-2">
                        <button
                            class="navbar-toggler"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent"
                            aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                    <div class="col-10">
                        <div class="header_search">
                            <input type="text" class="form-control product_search_input" placeholder="Search here..." autocomplete="off" />
                            <div class="search_suggestion_box d-none">
                                <ul class="search_results list-unstyled"></ul>
                            </div>

                            <div class="header_search_btn">
                                <img
                                    src="<?php echo base_url('assets/frontend/images/search.png') ?>"
                                    class="img-fluid"
                                    alt="search" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="header_bottom">
                            <div
                                class="collapse navbar-collapse"
                                id="navbarSupportedContent">
                                <ul class="navbar-nav mb-2 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo ($this->uri->segment(1) == '') ? 'active' : ''; ?>" aria-current="page" href="<?php echo base_url(); ?>">Home</a>
                                    </li>
                                    <?php
                                    $get_product_types = get_product_types();
                                    $current_slug = $this->uri->segment(2);

                                    if (!empty($get_product_types)) :
                                        foreach ($get_product_types as $get_product_type):
                                            $active_class = ($this->uri->segment(1) == 'product-type' && $current_slug == $get_product_type['slug']) ? 'active' : '';

                                            // Display the parent category
                                            echo '<li class="nav-item"><a class="nav-link dropdown-toggle' . $active_class . '" data-bs-toggle="dropdown" aria-expanded="false" href="' . base_url('product-type/') . $get_product_type['slug'] . '">' . $get_product_type['product_type_name'] . '</a>';

                                            // Check if there are child categories and display them as a dropdown or submenu
                                            if (!empty($get_product_type['children'])) {
                                                echo '<ul class="sub-menu dropdown-menu">'; // Sub-menu class for child categories
                                                foreach ($get_product_type['children'] as $child) {
                                                    $child_active_class = ($current_slug == $child['slug']) ? 'active' : '';
                                                    echo '<li><a class=" dropdown-item' . $child_active_class . '" href="' . base_url('product-type/') . $child['slug'] . '">' . $child['product_type_name'] . '</a></li>';
                                                }
                                                echo '</ul>';
                                            }

                                            echo '</li>';
                                        endforeach;
                                    endif;
                                    ?>

                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo base_url('category/custom-t-shirts'); ?>">Custom T-Shirts</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo base_url('category/personalized-mugs'); ?>">Personalized Mugs</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo base_url('category/magic-mirrors'); ?>">Magic Mirrors</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo base_url('category/photo-gifts'); ?>">Photo Gifts</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo base_url('gifts-by-occasion'); ?>">Gifts by Occasion</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="<?php echo base_url('contact'); ?>" class="nav-link <?php echo ($this->uri->segment(1) == 'contact') ? 'active' : ''; ?>">Contact Us</a>
                                    </li>
                                </ul>

                            </div>

                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <script>
        $(document).ready(function() {
            $(".product_search_input").on("keyup", function() {
                let query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: "<?php echo base_url('product/searchProducts'); ?>",
                        method: "POST",
                        data: {
                            keyword: query
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === "success") {
                                let list = "";
                                $.each(res.products, function(i, product) {
                                    let image = product.featured_image ? product.featured_image : 'default.png';
                                    list += `<li class="show_product_modal" data-id="${product.id}">
                                <img src="<?php echo base_url('uploads/products/'); ?>${image}" />
                                <span>${product.name}</span>
                            </li>`;
                                });
                                $(".search_results").html(list);
                                $(".search_suggestion_box").removeClass('d-none');
                            } else {
                                $(".search_results").html('<li>No products found</li>');
                                $(".search_suggestion_box").removeClass('d-none');
                            }
                        },
                    });
                } else {
                    $(".search_suggestion_box").addClass('d-none');
                }
            });

            // Optional: hide on click outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.header_search').length) {
                    $('.search_suggestion_box').addClass('d-none');
                }
            });
        });
    </script>