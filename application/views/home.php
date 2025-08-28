<section class="banner">
    <div class="container">
        <div class="home_banner home_banner_full_width">
            <div class="home_banner_all owl-carousel owl-theme">
                <div class="home_banner_Single"><img src="<?php echo base_url('assets/frontend/images/banner_1.jpg'); ?>" alt=""></div>
                <div class="home_banner_Single"><img src="<?php echo base_url('assets/frontend/images/banner_2.jpg'); ?>" alt=""></div>
            </div>
        </div>
    </div>
</section>
<section class="popular_collection mb-0 mb-md-5">
    <div class="container">
        <div class="row">
            <div class="popular_collection_heading">
                <h4>Popular <span>Collection</span></h4>
                <a href="#">Show All</a>
            </div>
            <div class="popular_collection_content_area owl-carousel owl-theme">
                <?php
                foreach ($categories as $category):
                ?>
                    <?php $this->load->view('templates/category', ['category' => $category]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>


<section class="latest_product">
    <div class="container">
        <div class="row">
            <!-- <div class="col-lg-3">
                <div class="latest_product_left">
                    <img
                        src="<?php echo base_url('assets/frontend/images/product_add_1.jpg'); ?>"
                        class="img-fluid"
                        alt="adds" />
                    <img
                        src="<?php echo base_url('assets/frontend/images/product_add_2.jpg'); ?>"
                        class="img-fluid"
                        alt="adds" />
                </div>
            </div> -->
            <div class="col-lg-12">
                <div class="latest_product_right">
                    <div class="latest_product_right_heading">
                        <h4>Latest Product</h4>
                        <ul class="tabs">
                            <li class="tab-link current" data-tab="tab-1">Show all</li>
                            <li class="tab-link" data-tab="tab-2">Popular</li>
                            <li class="tab-link" data-tab="tab-3">Best rated</li>
                            <li class="tab-link" data-tab="tab-4">Deal of the Day</li>
                        </ul>
                    </div>
                    <div class="latest_product_right_content">
                        <div id="tab-1" class="tab-content current">
                            <div class="row">
                                <?php foreach ($latest_products as $product): ?>
                                    <div class="col-6 col-lg-3">
                                        <?php $this->load->view('templates/product', ['product' => $product]); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div id="tab-2" class="tab-content">
                            <div class="row">
                                <?php foreach ($popular_products as $product): ?>
                                    <div class="col-6 col-lg-3">
                                        <?php $this->load->view('templates/product', ['product' => $product]); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div id="tab-3" class="tab-content">
                            <div class="row">
                                <?php foreach ($best_products as $product): ?>
                                    <div class="col-6 col-lg-3">
                                        <?php $this->load->view('templates/product', ['product' => $product]); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div id="tab-4" class="tab-content">
                            <div class="row">
                                <?php foreach ($deal_products as $product): ?>
                                    <div class="col-6 col-lg-3">
                                        <?php $this->load->view('templates/product', ['product' => $product]); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="new_arrivals mb-0 mb-md-5">
    <div class="container">
        <div class="new_arrivals_heading">
            <h4>Explore <span>The New Arrivals</span></h4>
        </div>
        <div class="new_arrivals_content_area">
            <div class="new_arrivals_content_all owl-carousel owl-theme">
                <?php foreach ($new_arrairval as $product): ?>
                    <div class="new_arrivals_content_single">
                        <div class="card">
                            <?php $this->load->view('templates/product', ['product' => $product]); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="delight mb-0 mb-md-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 m-auto">
                <div class="delight_header">
                    <h4>We have more <span>to delight you</span> </h4>
                    <p>we have more reason to delight you.</p>
                </div>
            </div>
        </div>

        <div class="delight_content_area">
            <div class="row">
                <div class="col-3 col-lg-3">
                    <div class="delight_content_single">
                        <img
                            src="<?php echo base_url('assets/frontend/images/offer.png'); ?>"
                            class="img-fluid"
                            alt="img" />
                        <h6>Best<br />Offer in Price</h6>
                    </div>
                </div>
                <div class="col-3 col-lg-3">
                    <div class="delight_content_single">
                        <img
                            src="<?php echo base_url('assets/frontend/images/satisfaction.png') ?>"
                            class="img-fluid"
                            alt="img" />
                        <h6>100%<br />Satisfaction</h6>
                    </div>
                </div>
                <div class="col-3 col-lg-3">
                    <div class="delight_content_single">
                        <img
                            src="<?php echo base_url('assets/frontend/images/delivery.png') ?>"
                            class="img-fluid"
                            alt="img" />
                        <h6>Safe<br />Delivery</h6>
                    </div>
                </div>
                <div class="col-3 col-lg-3">
                    <div class="delight_content_single">
                        <img
                            src="<?php echo base_url('assets/frontend/images/support.png') ?>"
                            class="img-fluid"
                            alt="img" />
                        <h6>Expert<br />Support</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        $('.popular_collection_content_area').owlCarousel({
            loop: true,
            margin: 10,
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 4
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 7
                }
            }
        });
    });
</script>