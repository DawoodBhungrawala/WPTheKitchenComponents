<?php
/**
 * The template for displaying single products
 *
 * @package WooCommerce/Templates
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) :
    the_post();
    global $product;

    $main_image_id = $product->get_image_id();
    $gallery_image_ids = $product->get_gallery_image_ids();
    $all_image_ids = array_filter(array_unique(array_merge(array($main_image_id), $gallery_image_ids)));
    ?>

    <section class="single-product-page">
        <div class="container">
            <div class="single-product-page__breadcrumbs">
                <?php
                if (function_exists('yoast_breadcrumb')) {
                    yoast_breadcrumb('<p id="breadcrumbs" class="small fw-bold text-secondary mb-0">', '</p>');
                }
                ?>
            </div>

            <div class="row g-4 g-xl-5 align-items-start">
                <div class="col-12 col-xl-6">
                    <div class="single-product-gallery">
                        <?php if (!empty($all_image_ids)) : ?>
                            <?php $main_image_url = wp_get_attachment_image_url($main_image_id, 'large'); ?>
                            <div class="single-product-gallery__frame">
                                <div class="single-product-gallery__main">
                                    <img
                                            id="mainProductImage"
                                            src="<?php echo esc_url($main_image_url); ?>"
                                            class="img-fluid w-100"
                                            alt="<?php echo esc_attr(get_the_title()); ?>"
                                    />
                                </div>
                            </div>

                            <div class="single-product-gallery__thumbs row g-3">
                                <?php foreach ($all_image_ids as $image_id) : ?>
                                    <?php
                                    $thumb_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                                    $large_url = wp_get_attachment_image_url($image_id, 'large');
                                    ?>
                                    <div class="col-4 col-sm-3">
                                        <button
                                                type="button"
                                                class="single-product-gallery__thumb"
                                                data-full="<?php echo esc_url($large_url); ?>"
                                        >
                                            <img
                                                    src="<?php echo esc_url($thumb_url); ?>"
                                                    class="img-fluid thumbnail-image"
                                                    alt="<?php echo esc_attr(get_the_title()); ?>"
                                            />
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="single-product-gallery__frame">
                                <div class="single-product-gallery__main">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        echo get_the_post_thumbnail(get_the_ID(), 'full', array('class' => 'img-fluid w-100'));
                                    } else {
                                        echo wc_placeholder_img('woocommerce_single', array('class' => 'img-fluid w-100'));
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="single-product-summary">
                        <div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
                            <?php
                            woocommerce_template_single_title();
                            woocommerce_template_single_excerpt();
                            woocommerce_template_single_price();
                            woocommerce_template_single_add_to_cart();
                            ?>
                        </div>

                        <div class="single-product-summary__assurance">
                            <div class="single-product-summary__assurance-item">
                                <strong>Trusted Support</strong>
                                <span>Helpful advice before and after your order from our Bradford team.</span>
                            </div>
                            <div class="single-product-summary__assurance-item">
                                <strong>Showroom & Online</strong>
                                <span>Compare products in person or order online with straightforward service.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="single-product-details row g-4 mt-4 mt-xl-5">
                <div class="col-12 col-xl-7">
                    <div class="single-product-panel h-100">
                        <div class="single-product-panel__heading">
                            <span class="single-product-panel__eyebrow">Product Overview</span>
                            <h2>Description</h2>
                        </div>
                        <div class="product-description">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-5">
                    <div class="single-product-panel h-100">
                        <?php
                        $attributes = $product->get_attributes();
                        $weight = $product->get_weight();
                        $length = $product->get_length();
                        $width = $product->get_width();
                        $height = $product->get_height();

                        if (!empty($attributes) || $weight || $length || $width || $height) {
                            echo '<div class="single-product-panel__heading">';
                            echo '<span class="single-product-panel__eyebrow">Product Details</span>';
                            echo '<h2>Additional Information</h2>';
                            echo '</div>';
                            echo '<div class="single-product-specs">';

                            if ($weight) {
                                echo '<div class="single-product-specs__row"><strong>' . esc_html__('Weight', 'woocommerce') . '</strong><span>' . esc_html($weight) . ' ' . esc_html(get_option('woocommerce_weight_unit')) . '</span></div>';
                            }

                            if ($length) {
                                echo '<div class="single-product-specs__row"><strong>' . esc_html__('Length', 'woocommerce') . '</strong><span>' . esc_html($length) . ' ' . esc_html(get_option('woocommerce_dimension_unit')) . '</span></div>';
                            }
                            if ($width) {
                                echo '<div class="single-product-specs__row"><strong>' . esc_html__('Width', 'woocommerce') . '</strong><span>' . esc_html($width) . ' ' . esc_html(get_option('woocommerce_dimension_unit')) . '</span></div>';
                            }
                            if ($height) {
                                echo '<div class="single-product-specs__row"><strong>' . esc_html__('Height', 'woocommerce') . '</strong><span>' . esc_html($height) . ' ' . esc_html(get_option('woocommerce_dimension_unit')) . '</span></div>';
                            }

                            foreach ($attributes as $attribute) {
                                if ($attribute->is_taxonomy()) {
                                    $values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'));
                                    $value = implode(', ', $values);
                                    $attribute_label = wc_attribute_label($attribute->get_name());
                                } else {
                                    $value = implode(', ', $attribute->get_options());
                                    $attribute_label = $attribute->get_name();
                                }

                                if ($value) {
                                    echo '<div class="single-product-specs__row"><strong>' . esc_html($attribute_label) . '</strong><span>' . esc_html($value) . '</span></div>';
                                }
                            }

                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php
            $related_product_ids = wc_get_related_products($product->get_id(), 4);
            if (!empty($related_product_ids)) :
                ?>
                <div class="single-product-related mt-5">
                    <section class="single-product-related__section">
                        <div class="single-product-related__header">
                            <span class="single-product-related__eyebrow">You may also like</span>
                            <h2>Related products</h2>
                        </div>

                        <div class="row g-4">
                            <?php
                            $original_product = $product;
                            $original_post = $post;

                            foreach ($related_product_ids as $related_product_id) :
                                $post_object = get_post($related_product_id);
                                if (!$post_object) {
                                    continue;
                                }

                                setup_postdata($GLOBALS['post'] = $post_object);
                                $product = wc_get_product($related_product_id);

                                if (!$product) {
                                    continue;
                                }
                                ?>
                                <div class="col-12 col-sm-6 col-xl-3">
                                    <article <?php wc_product_class('single-product-related__card', $product); ?>>
                                        <a href="<?php the_permalink(); ?>" class="single-product-related__media">
                                            <?php
                                            if (has_post_thumbnail()) {
                                                echo wp_get_attachment_image(
                                                    $product->get_image_id(),
                                                    'full',
                                                    false,
                                                    array(
                                                        'class' => 'img-fluid w-100',
                                                    )
                                                );
                                            } else {
                                                echo wc_placeholder_img('woocommerce_single', array('class' => 'img-fluid w-100'));
                                            }
                                            ?>
                                        </a>

                                        <div class="single-product-related__content">
                                            <h3 class="single-product-related__title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>

                                            <div class="single-product-related__price">
                                                <?php echo wp_kses_post($product->get_price_html()); ?>
                                            </div>

                                            <div class="single-product-related__actions">
                                                <?php
                                                woocommerce_template_loop_add_to_cart(array(
                                                    'class' => implode(
                                                        ' ',
                                                        array_filter(array(
                                                            'button',
                                                            'product_type_' . $product->get_type(),
                                                            $product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button ajax_add_to_cart' : '',
                                                        ))
                                                    ),
                                                ));
                                                ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>

                            <?php
                            $product = $original_product;
                            $GLOBALS['post'] = $original_post;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mainImage = document.getElementById('mainProductImage');
            const thumbnails = document.querySelectorAll('.single-product-gallery__thumb');

            if (!mainImage || !thumbnails.length) {
                return;
            }

            thumbnails.forEach(function (thumb) {
                const thumbImage = thumb.querySelector('img');

                if (thumb.dataset.full === mainImage.getAttribute('src')) {
                    thumb.classList.add('is-active');
                }

                thumb.addEventListener('click', function () {
                    const fullImage = thumb.dataset.full;

                    if (!fullImage) {
                        return;
                    }

                    mainImage.setAttribute('src', fullImage);

                    if (thumbImage) {
                        mainImage.setAttribute('alt', thumbImage.getAttribute('alt') || mainImage.getAttribute('alt'));
                    }

                    thumbnails.forEach(function (item) {
                        item.classList.remove('is-active');
                    });

                    thumb.classList.add('is-active');
                });
            });
        });
    </script>

<?php
endwhile;
get_footer('shop');
