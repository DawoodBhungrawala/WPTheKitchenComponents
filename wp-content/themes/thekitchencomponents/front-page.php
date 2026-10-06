<?php

get_header();

?>

<?php

$brand_logo_base = get_template_directory_uri() . '/images/brands/';
$brand_logos = [
    ['file' => 'aeg-logo.jpg', 'name' => 'AEG'],
    ['file' => 'airone-logo.png', 'name' => 'Airone'],
    ['file' => 'amica-logo.jpg', 'name' => 'Amica'],
    ['file' => 'astracast-logo.png', 'name' => 'Astracast'],
    ['file' => 'avelis-logo.jpg', 'name' => 'Avelis'],
    ['file' => 'amtico-logo.png', 'name' => 'Amtico'],
    ['file' => 'beaufort-logo.jpg', 'name' => 'Beaufort'],
    ['file' => 'bidbury-co-logo.png', 'name' => 'Bidbury Co'],
    ['file' => 'bosch-logo.jpg', 'name' => 'Bosch'],
    ['file' => 'candy-logo.jpg', 'name' => 'Candy'],
    ['file' => 'carron-logo.jpg', 'name' => 'Carron'],
    ['file' => 'cavecool-logo.jpg', 'name' => 'Cavecool'],
    ['file' => 'cda-logo.jpg', 'name' => 'CDA'],
    ['file' => 'scudo-logo.png', 'name' => 'Scudo'],
    ['file' => 'eastbrook-logo.jpg', 'name' => 'Eastbrook'],
    ['file' => 'elica-logo.jpg', 'name' => 'Elica'],
    ['file' => 'franke-logo.jpg', 'name' => 'Franke'],
    ['file' => 'harmony-brand-image.webp', 'name' => 'Harmony'],
    ['file' => 'hoover-logo.jpg', 'name' => 'Hoover'],
    ['file' => 'hotpoint-logo.jpg', 'name' => 'Hotpoint'],
    ['file' => 'miro-logo.png', 'name' => 'Miro'],
    ['file' => 'neff-logo.jpg', 'name' => 'Neff'],
    ['file' => 'pevino-logo.jpg', 'name' => 'Pevino'],
    ['file' => 'phoenix-logo.jpg', 'name' => 'Phoenix Bathrooms'],
    ['file' => 'prima-logo.jpg', 'name' => 'Prima'],
    ['file' => 'siena-logo.jpg', 'name' => 'Siena'],
    ['file' => 'viandpro-logo.jpg', 'name' => 'ViandPro'],
    ['file' => 'whirlpool-logo.jpg', 'name' => 'Whirlpool'],
];

// Homepage hero + visual category cards. Product data remains in WooCommerce/database.
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
$showroom_url = home_url('/showrooms/');

$front_categories = [
    ['name' => 'Kitchens',      'slugs' => ['kitchens', 'kitchen'],         'fallback' => home_url('/kitchens/'),                    'static_image' => 'categories/kitchens.jpg'],
    ['name' => 'Sinks',         'slugs' => ['sinks', 'all-sinks'],          'fallback' => home_url('/product-category/sinks/'),      'static_image' => 'categories/sinks.jpg'],
    ['name' => 'Taps',          'slugs' => ['taps'],                        'fallback' => home_url('/product-category/taps/'),       'static_image' => 'categories/taps.jpg'],
    ['name' => 'Appliances',    'slugs' => ['appliances'],                  'fallback' => home_url('/product-category/appliances/'), 'static_image' => 'categories/appliances.jpg'],
    ['name' => 'Bedrooms',      'slugs' => [],                              'fallback' => home_url('/bedrooms/'),                     'static_image' => 'categories/bedrooms.jpg'],
    ['name' => 'Handles',       'slugs' => ['handles'],                     'fallback' => home_url('/product-category/handles/'),    'static_image' => 'categories/handles.jpg'],
    ['name' => 'Water Filters', 'slugs' => ['filters'],                     'fallback' => home_url('/product-category/filters/'),    'static_image' => 'categories/water-filters.jpg'],
];

foreach ($front_categories as &$front_category) {
    $front_category['url'] = $front_category['fallback'];
    $front_category['image'] = get_template_directory_uri() . '/images/' . $front_category['static_image'];

    foreach ($front_category['slugs'] as $candidate_slug) {
        $term = get_term_by('slug', $candidate_slug, 'product_cat');
        if ($term && !is_wp_error($term)) {
            $term_link = get_term_link($term);
            if (!is_wp_error($term_link)) {
                $front_category['url'] = $term_link;
            }
            break;
        }
    }
}
unset($front_category);

?>
    <?php
    $hero_slides = [
        [
            'image' => get_template_directory_uri() . '/images/homepage-hero.jpg',
            'eyebrow' => 'The Kitchen Components',
            'title' => 'Premium Kitchen Components for Homes Across the UK',
            'copy' => 'Shop quality sinks, taps, appliances, handles, water filters and kitchen essentials from trusted brands, with nationwide UK delivery and expert support.',
        ],
        [
            'image' => get_template_directory_uri() . '/images/categories/kitchens.jpg',
            'eyebrow' => 'Kitchen Inspiration',
            'title' => 'Create a Kitchen Designed Around You',
            'copy' => 'Explore contemporary kitchen solutions, premium finishes and trusted components for a beautifully considered space.',
        ],
        [
            'image' => get_template_directory_uri() . '/images/categories/appliances.jpg',
            'eyebrow' => 'Trusted Appliances',
            'title' => 'Performance Meets Everyday Style',
            'copy' => 'Discover appliances from leading brands selected to make everyday cooking, entertaining and living easier.',
        ],
        [
            'image' => get_template_directory_uri() . '/images/categories/sinks.jpg',
            'eyebrow' => 'Finishing Details',
            'title' => 'Beautiful Components. Better Kitchens.',
            'copy' => 'From statement sinks to refined taps and handles, find the details that bring the whole room together.',
        ],
    ];
    ?>
    <section class="front-hero front-hero--carousel" aria-labelledby="front-hero-title" data-hero-carousel>
        <div class="front-hero__slides" aria-hidden="true">
            <?php foreach ($hero_slides as $index => $hero_slide) : ?>
                <div class="front-hero__slide<?php echo $index === 0 ? ' is-active' : ''; ?>"
                     style="background-image: url('<?php echo esc_url($hero_slide['image']); ?>');"
                     data-hero-slide="<?php echo esc_attr($index); ?>"></div>
            <?php endforeach; ?>
        </div>
        <div class="front-hero__overlay"></div>
        <div class="container front-hero__content">
            <div class="front-hero__inner">
                <?php foreach ($hero_slides as $index => $hero_slide) : ?>
                    <div class="front-hero__copy-slide<?php echo $index === 0 ? ' is-active' : ''; ?>" data-hero-copy="<?php echo esc_attr($index); ?>">
                        <span class="front-hero__eyebrow"><?php echo esc_html($hero_slide['eyebrow']); ?></span>
                        <?php if ($index === 0) : ?>
                            <h1 id="front-hero-title" class="front-hero__title"><?php echo esc_html($hero_slide['title']); ?></h1>
                        <?php else : ?>
                            <h2 class="front-hero__title"><?php echo esc_html($hero_slide['title']); ?></h2>
                        <?php endif; ?>
                        <p class="front-hero__copy"><?php echo esc_html($hero_slide['copy']); ?></p>
                        <div class="front-hero__actions">
                            <a class="front-hero__button front-hero__button--primary" href="<?php echo esc_url($shop_url); ?>">Shop Collection</a>
                            <a class="front-hero__button front-hero__button--secondary" href="<?php echo esc_url($showroom_url); ?>">Visit Showroom</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="front-hero__arrow front-hero__arrow--prev" type="button" aria-label="Previous slide" data-hero-prev>&#10094;</button>
        <button class="front-hero__arrow front-hero__arrow--next" type="button" aria-label="Next slide" data-hero-next>&#10095;</button>
        <div class="front-hero__dots" aria-label="Hero slides">
            <?php foreach ($hero_slides as $index => $hero_slide) : ?>
                <button class="front-hero__dot<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" aria-label="Go to slide <?php echo esc_attr($index + 1); ?>" data-hero-dot="<?php echo esc_attr($index); ?>"></button>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="front-categories front-categories--showroom" aria-labelledby="front-categories-title" data-category-showroom>
        <div class="container-fluid">
            <div class="front-section-heading front-categories__heading text-center">
                <span class="front-section-heading__eyebrow">Explore the range</span>
                <h2 id="front-categories-title" class="front-section-heading__title">Explore Kitchen &amp; Home Components</h2>
                <p class="front-section-heading__copy">Browse sinks, taps, appliances, handles, kitchens, bedrooms and water filters for projects across the UK.</p>
            </div>
            <div class="front-categories__grid" data-category-track>
                <?php foreach ($front_categories as $index => $front_category) : ?>
                    <a class="front-category-card" href="<?php echo esc_url($front_category['url']); ?>" data-category-card style="--category-index: <?php echo esc_attr($index); ?>;">
                        <img class="front-category-card__image" src="<?php echo esc_url($front_category['image']); ?>" alt="<?php echo esc_attr($front_category['name'] . ' collection - The Kitchen Components UK'); ?>" loading="lazy">
                        <span class="front-category-card__shade"></span>
                        <span class="front-category-card__content">
                            <span class="front-category-card__meta">Collection <?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <strong class="front-category-card__title"><?php echo esc_html($front_category['name']); ?></strong>
                            <span class="front-category-card__rule" aria-hidden="true"></span>
                            <span class="front-category-card__link">Explore Collection <span aria-hidden="true">→</span></span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="front-categories__swipe-hint" aria-hidden="true">
                <span>Swipe to explore</span><span class="front-categories__swipe-arrow">→</span>
            </div>
        </div>
    </section>

    <section class="front-featured-products front-featured-products--rotating" aria-labelledby="front-featured-title">
        <div class="container-fluid">
            <div class="front-section-heading text-center">
                <span class="front-section-heading__eyebrow">Featured Picks</span>
                <h2 id="front-featured-title" class="front-section-heading__title">Featured Kitchen &amp; Home Products</h2>
                <p class="front-section-heading__copy">A rotating selection of standout products from across our kitchen and home collection.</p>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="swiper featuredProductsSwiper" aria-label="Featured products carousel">
                        <div class="swiper-wrapper">
                            <?php
                            // Build one 8-product carousel. Manually featured products appear first;
                            // if fewer than eight are marked featured, fill the remaining slots with
                            // the latest published WooCommerce products so the carousel always uses
                            // up to eight unique products without requiring a developer.
                            $featured_ids = get_posts(array(
                                'post_type'      => 'product',
                                'post_status'    => 'publish',
                                'posts_per_page' => 8,
                                'fields'         => 'ids',
                                'meta_query'     => array(
                                    array(
                                        'key'     => 'featured_product',
                                        'value'   => '1',
                                        'compare' => '='
                                    )
                                ),
                                'orderby'        => 'date',
                                'order'          => 'DESC',
                            ));

                            $featured_ids = array_values(array_unique(array_map('intval', $featured_ids)));

                            if (count($featured_ids) < 8) {
                                $fill_ids = get_posts(array(
                                    'post_type'      => 'product',
                                    'post_status'    => 'publish',
                                    'posts_per_page' => 8 - count($featured_ids),
                                    'fields'         => 'ids',
                                    'post__not_in'   => $featured_ids,
                                    'orderby'        => 'date',
                                    'order'          => 'DESC',
                                ));
                                $featured_ids = array_merge($featured_ids, array_map('intval', $fill_ids));
                            }

                            $featured_ids = array_slice($featured_ids, 0, 8);
                            $featured_query = new WP_Query(array(
                                'post_type'      => 'product',
                                'post_status'    => 'publish',
                                'posts_per_page' => 8,
                                'post__in'       => $featured_ids,
                                'orderby'        => 'post__in',
                            ));

                            if ($featured_query->have_posts()) :
                                while ($featured_query->have_posts()) :
                                    $featured_query->the_post();
                                    global $product;
                                    if (!$product) {
                                        $product = wc_get_product(get_the_ID());
                                    }

                                    $category_terms = get_the_terms(get_the_ID(), 'product_cat');
                                    $category_label = (!empty($category_terms) && !is_wp_error($category_terms)) ? $category_terms[0]->name : 'Featured Product';

                                    $mobile_description = '';
                                    if ($product) {
                                        $raw_description = $product->get_short_description();
                                        if (!$raw_description) {
                                            $raw_description = $product->get_description();
                                        }
                                        if (!$raw_description) {
                                            $raw_description = get_the_excerpt();
                                        }
                                        $mobile_description = wp_trim_words(wp_strip_all_tags(strip_shortcodes((string) $raw_description)), 18, '…');
                                        if (!$mobile_description) {
                                            $mobile_description = 'Explore this featured product for specifications, options and availability.';
                                        }
                                    }
                                    ?>
                                    <div class="swiper-slide">
                                        <article class="front-featured-product-card h-100">
                                            <a href="<?php the_permalink(); ?>" class="front-featured-product-card__image" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                                <?php
                                                if (has_post_thumbnail()) {
                                                    the_post_thumbnail('medium_large', array(
                                                        'class' => 'img-fluid',
                                                        'loading' => 'lazy',
                                                        'alt' => esc_attr(get_the_title())
                                                    ));
                                                }
                                                ?>
                                                <span class="front-featured-product-card__badge">Featured</span>
                                            </a>

                                            <div class="front-featured-product-card__body">
                                                <span class="front-featured-product-card__category"><?php echo esc_html($category_label); ?></span>
                                                <h3 class="front-featured-product-card__title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                <p class="front-featured-product-card__description"><?php echo esc_html($mobile_description); ?></p>
                                                <?php if ($product) : ?>
                                                    <div class="front-featured-product-card__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                                                <?php endif; ?>
                                                <div class="front-featured-product-card__actions">
                                                    <a class="front-featured-product-card__view" href="<?php the_permalink(); ?>">View Product <span aria-hidden="true">→</span></a>
                                                    <?php if ($product) :
                                                        $cart_classes = array('button', 'front-featured-product-card__cart-button', 'product_type_' . $product->get_type());
                                                        if ($product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock()) {
                                                            $cart_classes[] = 'add_to_cart_button';
                                                            $cart_classes[] = 'ajax_add_to_cart';
                                                        }
                                                        echo apply_filters(
                                                            'woocommerce_loop_add_to_cart_link',
                                                            sprintf(
                                                                '<a href="%s" data-quantity="1" class="%s" %s>%s</a>',
                                                                esc_url($product->add_to_cart_url()),
                                                                esc_attr(implode(' ', $cart_classes)),
                                                                wc_implode_html_attributes(array(
                                                                    'data-product_id'  => $product->get_id(),
                                                                    'data-product_sku' => $product->get_sku(),
                                                                    'aria-label'       => $product->add_to_cart_description(),
                                                                    'rel'              => 'nofollow',
                                                                )),
                                                                esc_html($product->add_to_cart_text())
                                                            ),
                                                            $product
                                                        );
                                                    endif; ?>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                <?php endwhile; wp_reset_postdata(); ?>
                            <?php else : ?>
                                <div class="swiper-slide"><div class="front-featured-products__empty">No featured products found.</div></div>
                            <?php endif; ?>
                        </div>
                        <div class="front-featured-carousel__controls" aria-label="Featured products carousel controls">
                            <button class="front-featured-carousel__nav front-featured-carousel__prev" type="button" aria-label="Previous featured products">‹</button>
                            <div class="front-featured-carousel__pagination" aria-hidden="true"></div>
                            <button class="front-featured-carousel__nav front-featured-carousel__next" type="button" aria-label="Next featured products">›</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

 <section class="py-5 d-none">
    <div class="container">
        <div class="row gy-3">

            <!-- Sinks and Taps -->
            <div class="col-md-6 col-12">
                <div class="card rounded-4 overflow-hidden text-bg-dark h-100 w-100">
                    <img 
                        src="/wp-content/uploads/2025/08/table-wood-house-chair-floor-window.jpg"
                        class="card-img img-fluid h-100 object-fit-cover"
                        alt="Modern kitchen sink and tap setup"
                    >
                    <div class="card-img-overlay card-overlay-dark d-flex align-items-end">
                        <div class="py-4">
                            <h5 class="card-title">Sinks and Taps</h5>
                            <p class="card-text">
                                At The Kitchen Components, we supply high-quality kitchen sinks and taps 
                                designed to combine style, durability, and everyday practicality.
                            </p>
                            <a href="/product-category/sinks/" class="btn btn-primary rounded-5 px-4 py-2">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 360 Showroom -->
            <div class="col-md-3 col-12">
                <div class="card rounded-4 overflow-hidden text-bg-dark h-100 w-100">
                    <img 
                        src="/wp-content/uploads/homepage-hero.jpg"
                        class="card-img img-fluid h-100 object-fit-cover"
                        alt="360 degree showroom view"
                    >
                    <div class="card-img-overlay card-overlay-dark d-flex align-items-end">
                        <div class="py-4">
                            <h5 class="card-title">View 360° Showroom</h5>
                            <a 
                                href="https://www.google.com/local/place/fid/0x487be6b03e506b35:0x23a24f3f60b4b0e6/photosphere?iu=https://lh3.googleusercontent.com/gps-cs-s/AG0ilSyUDRzKi1DuVo8_QusYYaL6q2WnTIEWeflxUVSbaXISkgRceESuyLLEW_r4xrD4l-2NniVqbLB9P4RM_zL6jbLU-Qe_cx7oYk2F63BqQtKoK3bQ2Rn_GLIBallsTDuuifvUln2w%3Dw160-h106-k-no-pi-0-ya199.55576-ro-0-fo100&ik=CAoSF0NJSE0wb2dLRUlDQWdJRGx3SjNJMXdF"
                                target="_blank"
                                rel="noopener"
                                class="btn btn-primary rounded-5 px-4 py-2"
                            >
                                Take Virtual Tour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Showroom -->
            <div class="col-md-3 col-12">
                <div class="card rounded-4 overflow-hidden text-bg-dark h-100 w-100">
                    <img 
                        src="/wp-content/uploads/homepage-hero.jpg"
                        class="card-img img-fluid h-100 object-fit-cover"
                        alt="Kitchen showroom interior"
                    >
                    <div class="card-img-overlay card-overlay-dark d-flex align-items-end">
                        <div class="py-4">
                            <h5 class="card-title">Visit Our Showroom</h5>
                            <p class="card-text">
                                Find directions or take a virtual tour of our Bradford showroom.
                            </p>
                            <a href="/contact" class="btn btn-primary rounded-5 px-4 py-2">
                                Get Directions
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
    <section class="front-brand-strip">
        <div class="container">
            <div class="front-section-heading front-section-heading--compact text-center">
                <span class="front-section-heading__eyebrow">Trusted Brands</span>
                <h2 class="front-section-heading__title">Kitchen Appliances &amp; Components from Trusted Brands</h2>
            </div>
            <div class="swiper brandSwiper">
                <div class="swiper-wrapper align-items-center">
                    <?php foreach ($brand_logos as $brand_logo) : ?>
                        <div class="swiper-slide">
                            <div class="front-brand-strip__logo">
                                <img
                                    src="<?php echo esc_url($brand_logo_base . $brand_logo['file']); ?>"
                                    alt="<?php echo esc_attr($brand_logo['name'] . ' brand available from The Kitchen Components'); ?>"
                                    class="img-fluid"
                                >
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <script>
        var swiper = new Swiper(".brandSwiper", {
            slidesPerView: 6,
            spaceBetween: 30,
            loop: true,
            speed: 2000,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
            },
            breakpoints: {
                320: {slidesPerView: 1},
                576: {slidesPerView: 3},
                768: {slidesPerView: 4},
                992: {slidesPerView: 5},
                1200: {slidesPerView: 6}
            }
        });
    </script>


    <section class="front-uk-seo" aria-labelledby="front-uk-seo-title">
        <div class="container">
            <div class="front-uk-seo__panel">
                <div class="front-uk-seo__intro">
                    <span class="front-section-heading__eyebrow">Nationwide UK Supply</span>
                    <h2 id="front-uk-seo-title" class="front-uk-seo__title">Kitchen Components Delivered Across the UK</h2>
                    <p>The Kitchen Components supplies quality kitchen and home products to customers throughout the United Kingdom. Shop sinks, taps, appliances, handles, water filters and finishing components from trusted brands, with nationwide delivery and expert support for projects of all sizes.</p>
                    <p>Whether you are replacing a single fitting, upgrading appliances or planning a complete kitchen project, our online collection makes it easy to compare products and find the right components for your space. Customers can also visit our Bradford showroom for product advice, inspiration and help selecting suitable products.</p>
                </div>
                <nav class="front-uk-seo__links" aria-label="Popular product categories">
                    <a href="<?php echo esc_url(home_url('/product-category/all-sinks/')); ?>">Shop Kitchen Sinks <span aria-hidden="true">→</span></a>
                    <a href="<?php echo esc_url(home_url('/product-category/taps/')); ?>">Explore Kitchen Taps <span aria-hidden="true">→</span></a>
                    <a href="<?php echo esc_url(home_url('/product-category/appliances/')); ?>">Browse Kitchen Appliances <span aria-hidden="true">→</span></a>
                    <a href="<?php echo esc_url(home_url('/product-category/handles/')); ?>">Shop Kitchen Handles <span aria-hidden="true">→</span></a>
                    <a href="<?php echo esc_url(home_url('/product-category/filters/')); ?>">Shop Water Filters <span aria-hidden="true">→</span></a>
                    <a href="<?php echo esc_url(home_url('/kitchens/')); ?>">View Fitted Kitchens <span aria-hidden="true">→</span></a>
                    <a href="<?php echo esc_url(home_url('/showrooms/')); ?>">Visit Our Bradford Showroom <span aria-hidden="true">→</span></a>
                </nav>
            </div>
        </div>
    </section>
    <script id="front-featured-products-carousel">
        document.addEventListener('DOMContentLoaded', function () {
            var slider = document.querySelector('.featuredProductsSwiper');
            if (!slider || typeof Swiper === 'undefined') return;

            new Swiper(slider, {
                slidesPerView: 1.08,
                slidesPerGroup: 1,
                spaceBetween: 16,
                loop: true,
                speed: 520,
                autoplay: {
                    delay: 1800,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                grabCursor: true,
                watchOverflow: true,
                keyboard: { enabled: true },
                navigation: {
                    nextEl: '.front-featured-carousel__next',
                    prevEl: '.front-featured-carousel__prev'
                },
                pagination: {
                    el: '.front-featured-carousel__pagination',
                    clickable: true,
                    dynamicBullets: false
                },
                breakpoints: {
                    576: { slidesPerView: 2, spaceBetween: 18 },
                    768: { slidesPerView: 3, spaceBetween: 20 },
                    1200: { slidesPerView: 4, spaceBetween: 22 }
                }
            });
        });
    </script>

<?php
get_footer();
