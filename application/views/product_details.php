<link rel="stylesheet" href="<?php echo base_url(); ?>assets/frontend/css/lightslider.min.css" />
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="demo">
                    <ul id="lightSlider">
                        <?php
                        $image_url = ($product['featured_image'] != "") ? base_url('uploads/products/' . $product['featured_image']) : base_url('assets/uploads/no_image.jpeg');

                        //$image = '<img src="' . $image_url . '" alt="' . $product['name'] . '" width="100">';
                        ?>
                        <li data-thumb="<?php echo $image_url; ?>">
                            <img
                                class="slide_img"
                                src="<?php echo $image_url; ?>" />
                        </li>
                        <?php
                        if (!empty($product['gallery_images'])) :
                            $gallery_images = json_decode($product['gallery_images']);
                            foreach ($gallery_images as $gallery_image) :
                                $image_thumb_url = ($gallery_image) ? base_url('uploads/products/' . $gallery_image) : base_url('assets/uploads/no_image.jpeg');
                                echo '<li data-thumb="' . $image_thumb_url . '"><img class="slide_img" src="' . $image_thumb_url . '" /></li>';
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <div class="listing">
                <div class="container ps-0">
                    <div class="breadcrumb_area pt-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo base_url('products'); ?>">Products</a></li>
                            <li class="breadcrumb-item current"><?php echo $product['name']; ?></li>
                        </ol>
                        <div class="share_items">
                            <i class="fa-solid fa-share-nodes"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="single_product_content_box">
                <h4 class="single_product_heading">
                    <?php echo $product['name']; ?>
                </h4>

                <div class="product_rating_display">
                    <div class="rating_stars">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star-half-alt"></i>
                        <span class="rating_number"><span class="rate-color">4.5</span> (120 ratings)</span><br><br>
                    </div>

                </div>

                <div class="single_product_col mt-3">
                    <h5 class="price_single_product">
                        <?php
                        if ($product['sale_price'] > 0) {
                            $regular_price = $product['regular_price'];
                            $sale_price = $product['sale_price'];
                            $discount_percentage = (($regular_price - $sale_price) / $regular_price) * 100;
                            echo '<del>₹' . number_format($regular_price, 2) . '</del> <span class="sale-price" style="color: var(--main-color);font-weight: bolder;">₹' . number_format($sale_price, 2) . '</span>';
                            echo '&nbsp;&nbsp;<span class="discount_percentage" style="background:var(--main-color);; color:#fff; padding:2px 6px; border-radius:4px; font-size:13px; position:absolute;"> (' . round($discount_percentage) . '% off)</span>';
                        } else {
                            echo '₹' . number_format($product['regular_price'], 2);
                        }
                        ?>
                    </h5>
                    <!-- <img src="<?php echo base_url(); ?>assets/frontend/images/lineheart.png" class="img-fluid heart_clickable" alt=""> -->
                </div>
                <p class="tax_data">Inclusive of all taxes</p>
                <div class="delivery_estimation mt-3">
                    <div class="custom_input_section w-100 d-flex gap-3">
                        <input type="text" id="custom_text_input" class="form-control" placeholder="Enter Pincode">
                        <button class="btn btn-sm" style="background-color: var(--main-color); color: #fff;">Submit</button>
                    </div>
                    <p class="delivery_date mt-2">Estimated Delivery: 29th Aug - 30th Aug</p>
                </div>


                <div class="upload_photo_option">
                    <h6>Upload 2 Photos</h6>
                    <div class=" border border-border rounded border-2">
                        <label for="product_photos" class="custom-upload ">📁 Upload Files</label>
                        <input type="file" id="product_photos" name="product_photos[]" multiple accept=".jpg,.jpeg,.png,.webp,.gif,.pdf" required class="file-input">
                    </div>
                    <small style="font-size: 10px;">Accepted formats: JPG, PNG, WEBP, GIF, PDF</small>
                    <div id="photo-previews" class="d-flex flex-wrap mt-2"></div> <!-- Container for previews -->
                </div>
                <div class="underline"></div>

                <div class="custom_message_option" style="margin-top: 15px;">
                    <h6>Custom Message (Optional)</h6>
                    <textarea id="custom_message" name="custom_message" rows="2" placeholder="Write your custom message here..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                </div>
                <div class="underline"></div>

                <div class="single_pro_button" style="margin-top: 20px; text-align: center;">
                    <div class="single_pro_quantity">
                        <!-- <h6>Quantity</h6> -->
                        <form id="add_to_cart_form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div
                                class="value-button"
                                id="decrease"
                                onclick="decreaseValue()"
                                value="Decrease Value">
                                <i class="fa-solid fa-minus"></i>
                            </div>
                            <input type="number" id="number" name="quantity" value="1" />
                            <div
                                class="value-button"
                                id="increase"
                                onclick="increaseValue()"
                                value="Increase Value">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                        </form>
                    </div>
                    <a href="#" id="add_to_cart_btn" class="add_to_cart_btn" style="color: var(--main-color); background: white; border: 1px solid;">Add to Cart</a>
                    <a href="#" class="buy_now_btn" style="background-color: var(--main-color); color: #fff;">Buy Now</a>
                </div>

                <div class="whatsapp_button_container" style="margin-top: 20px; text-align: center;">
                    <a href="https://wa.me/7602855329" class="btn btn-success" style="background-color: #25D366; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                        <i class="fa-brands fa-whatsapp"></i> Questions? Ask on Whatsapp
                    </a>
                </div>

                <!-- <div class="single_pro_data">
                    <h6>Product Details</h6>
                    <?php echo $product['highlight_text']; ?>
                </div> -->
                <!-- <div class="underline"></div> -->


            </div>
        </div>
    </div>

    <div class="single_pro_description">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 m-auto">
                    <div class="wrapper">
                        <input type="radio" name="slider" checked id="desp" />
                        <input type="radio" name="slider" id="fab" />
                        <input type="radio" name="slider" id="return" />
                        <input type="radio" name="slider" id="help" />
                        <nav>
                            <label for="desp" class="desp"><i class="fas fa-home"></i>Description</label>
                            <label for="return" class="return"><i class="fas fa-code"></i>Delivery and Returns</label>
                            <label for="help" class="help"><i class="far fa-envelope"></i>Need Help</label>
                            <div class="slider"></div>
                        </nav>
                        <section class="tab_content_area">
                            <div class="content content-1">
                                <div class="title"><?php echo $product['name']; ?></div>
                                <?php echo $product['description']; ?>
                            </div>
                            <div class="content content-3">
                                <div class="title">This is a Code content</div>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                    Iure, debitis nesciunt! Consectetur officiis, libero nobis
                                    dolorem pariatur quisquam temporibus. Labore quaerat neque
                                    facere itaque laudantium odit veniam consectetur numquam
                                    delectus aspernatur, perferendis repellat illo sequi
                                    excepturi quos ipsam aliquid est consequuntur.
                                </p>
                            </div>
                            <div class="content content-4">
                                <div class="title">This is a Help content</div>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                    Enim reprehenderit null itaq, odio repellat asperiores vel
                                    voluptatem magnam praesentium, eveniet iure ab facere
                                    officiis. Quod sequi vel, rem quam provident soluta nihil,
                                    eos. Illo oditu omnis cumque praesentium voluptate maxime
                                    voluptatibus facilis nulla ipsam quidem mollitia! Veniam,
                                    fuga, possimus. Commodi, fugiat aut ut quorioms stu
                                    necessitatibus, cumque laborum rem provident tenetur.
                                </p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="similar_products_sec">
        <div class="container">
            <div class="heading">
                <h4>Similar <span>Products</span></h4>
                <div class="underline"></div>
            </div>
            <div class="similar_products_all owl-carousel owl-theme">
                <?php foreach ($similar_products as $product): ?>
                    <div class="similar_single_product">
                        <div class="single_product_listing">
                            <?php $this->load->view('templates/product', ['product' => $product]); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/frontend/js/lightslider.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#lightSlider').lightSlider({
                gallery: true,
                item: 1,
                loop: true,
                thumbItem: 9,
                slideMargin: 0,
                enableDrag: false,
                currentPagerPosition: 'left',
                onSliderLoad: function(el) {
                    el.lightGallery({
                        selector: '#lightSlider .lslide'
                    });
                }
            });

            // Array to hold files selected for upload
            var selectedFiles = [];

            // Handle file input change
            $('#product_photos').on('change', function() {
                var files = this.files;
                selectedFiles = []; // Clear previous selection
                $('#photo-previews').empty(); // Clear previous previews

                if (files.length > 0) {
                    for (var i = 0; i < files.length; i++) {
                        var file = files[i];
                        selectedFiles.push(file); // Add to our custom array
                        displayFilePreview(file, i); // Display preview
                    }
                }
            });

            // Function to display file preview
            function displayFilePreview(file, index) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var previewHtml = `
                        <div class="position-relative me-2 mb-2" style="width: 80px; height: 80px; border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                            <img src="${e.target.result}" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;" alt="Preview">
                            <button type="button" class="btn-close position-absolute top-0 end-0 bg-light rounded-circle" style="font-size: 0.6rem; padding: 0.2rem;" data-index="${index}" aria-label="Remove"></button>
                        </div>
                    `;
                    $('#photo-previews').append(previewHtml);
                };
                reader.readAsDataURL(file);
            }

            // Handle remove button click on preview
            $('#photo-previews').on('click', '.btn-close', function() {
                var indexToRemove = $(this).data('index');
                selectedFiles.splice(indexToRemove, 1); // Remove file from array
                $(this).closest('div').remove(); // Remove preview from DOM

                // Re-index remaining previews
                $('#photo-previews .btn-close').each(function(i) {
                    $(this).data('index', i);
                });

                // Update the file input's files property (optional, but good practice)
                // This is tricky with FileList, often easier to manage with FormData directly
                // For now, we'll rely on selectedFiles array for FormData construction
            });

            // Add to Cart AJAX
            $('#add_to_cart_btn').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);

                var product_id = $('input[name="product_id"]').val();
                var quantity = parseInt($('#number').val());
                var custom_message = $('#custom_message').val();
                
                // Client-side validation
                if (quantity <= 0) {
                    alert('Please enter a valid quantity.');
                    return;
                }

                // Validate minimum 2 photos if files are selected
                if (selectedFiles.length > 0 && selectedFiles.length < 2) {
                    alert('Please upload at least two photos.');
                    return;
                }

                var formData = new FormData();
                formData.append('product_id', product_id);
                formData.append('quantity', quantity);
                formData.append('custom_message', custom_message);

                // Append each file from our selectedFiles array to FormData
                for (var i = 0; i < selectedFiles.length; i++) {
                    formData.append('product_photos[]', selectedFiles[i]);
                }

                // Show loader and disable button
                $this.text('Adding...').prop('disabled', true).css({
                    'opacity': '0.6',
                    'cursor': 'not-allowed'
                });
                $('.loader').show();

                $.ajax({
                    url: '<?php echo base_url('cart/add'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false, // Important: tell jQuery not to process the data
                    contentType: false, // Important: tell jQuery not to set contentType
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#cart-item-count').text(response.cart_item_count); // Update header cart count
                            window.location.href = '<?php echo base_url('cart'); ?>'; // Redirect to cart page
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('An error occurred. Please try again.');
                    },
                    complete: function() {
                        // Hide loader and re-enable button
                        $this.text('Add to Cart').prop('disabled', false).css({
                            'opacity': '1',
                            'cursor': 'pointer'
                        });
                        $('.loader').hide();
                    }
                });
            });
        });

        // Quantity increase/decrease functions (already present, just ensuring they are here)
        function increaseValue() {
            var value = parseInt(document.getElementById('number').value, 10);
            value = isNaN(value) ? 0 : value;
            value++;
            document.getElementById('number').value = value;
        }

        function decreaseValue() {
            var value = parseInt(document.getElementById('number').value, 10);
            value = isNaN(value) ? 0 : value;
            value < 1 ? value = 1 : '';
            value--;
            document.getElementById('number').value = value;
        }
    </script>

    <!-- Simple Loader HTML (add this somewhere in your layout or product_details.php) -->
    <div class="loader" style="display:none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; text-align: center; padding-top: 20%;">
        <img src="<?php echo base_url('assets/images/loader.gif'); ?>" alt="Loading..." style="width: 50px; height: 50px;">
    </div>

    </body>