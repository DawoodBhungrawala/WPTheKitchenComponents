<?php

get_header();

?>

<?php

$current_term           = get_queried_object();
$current_term_id        = $current_term->term_id ?? 0;
$current_term_parent_id = $current_term->parent ?? 0;
$top_parent_id          = ($current_term_parent_id > 0) ? $current_term_parent_id : $current_term_id;
$current_brand_id       = ($current_term && isset($current_term->taxonomy) && $current_term->taxonomy === 'product_brand') ? $current_term->term_id : 0;

if (is_shop() || !is_product_category()) {
	$categories = get_terms([
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
	]);
} else {
	$parent_category = get_term($top_parent_id, 'product_cat');
	$categories      = [];

	if ($parent_category && !is_wp_error($parent_category)) {
		$categories[] = $parent_category;
	}

	$subcategories = get_terms([
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => $top_parent_id,
	]);

	if (!is_wp_error($subcategories)) {
		$categories = array_merge($categories, $subcategories);
	}
}

if (is_shop() || !is_product_category()) {
	$brands = get_terms([
		'taxonomy'   => 'product_brand',
		'hide_empty' => true,
	]);
} else {
	$brands = get_terms([
		'taxonomy'   => 'product_brand',
		'hide_empty' => true,
		'object_ids' => wc_get_products([
			'limit'    => -1,
			'status'   => 'publish',
			'return'   => 'ids',
			'category' => [$current_term->slug],
		]),
	]);
}

$shop_title = woocommerce_page_title(false);
$shop_description = '';
$category_chips = [];
$product_count = 0;

if (is_product_taxonomy()) {
	$shop_description = term_description();
} else {
	$shop_page_id = wc_get_page_id('shop');
	if ($shop_page_id > 0) {
		$shop_description = get_post_field('post_excerpt', $shop_page_id) ?: get_post_field('post_content', $shop_page_id);
	}
}

if (is_product_category()) {
	$category_chips = get_terms([
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => $current_term_id,
	]);

	if (empty($category_chips) || is_wp_error($category_chips)) {
		$category_chips = get_terms([
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => $top_parent_id,
		]);
	}
} elseif (is_shop()) {
	$category_chips = get_terms([
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
	]);
}

if (function_exists('wc_get_loop_prop')) {
	$product_count = (int) wc_get_loop_prop('total');
}
?>

<section class="shop-archive py-4 py-lg-5">
	<div class="container-fluid">
		<div class="shop-archive__intro mb-4 mb-lg-5">
			<?php if (function_exists('woocommerce_breadcrumb')) : ?>
				<div class="shop-archive__breadcrumbs mb-3">
					<?php woocommerce_breadcrumb(); ?>
				</div>
			<?php endif; ?>

			<div class="shop-archive__header">
				<div class="shop-archive__header-copy">
					<div class="shop-archive__eyebrow-row mb-3">
						<span class="shop-archive__eyebrow-pill">
							<i class="fa-solid fa-grid-2"></i>
							Shop Collection
						</span>
						<?php if ($product_count > 0) : ?>
							<span class="shop-archive__eyebrow-pill shop-archive__eyebrow-pill--count">
								<?php
								echo esc_html(
									sprintf(
										_n('%s product', '%s products', $product_count, 'thekitchencomponents'),
										number_format_i18n($product_count)
									)
								);
								?>
							</span>
						<?php endif; ?>
					</div>
					<h1 class="shop-archive__title mb-2"><?php echo esc_html($shop_title); ?></h1>
					<?php if (!empty($shop_description)) : ?>
						<div class="shop-archive__description"><?php echo wp_kses_post(wpautop(wp_trim_words(wp_strip_all_tags($shop_description), 30))); ?></div>
					<?php endif; ?>
				</div>
			</div>

			<?php if (!empty($category_chips) && !is_wp_error($category_chips)) : ?>
				<div class="shop-archive__chips">
					<?php foreach ($category_chips as $chip) : ?>
						<?php $is_chip_active = ((int) $chip->term_id === (int) $current_term_id); ?>
						<a class="shop-archive__chip <?php echo $is_chip_active ? 'is-active-category' : ''; ?>"
						   href="<?php echo esc_url(get_term_link($chip)); ?>">
							<?php echo esc_html($chip->name); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if (function_exists('wc_print_notices')) : ?>
			<div class="mb-3">
				<?php wc_print_notices(); ?>
			</div>
		<?php endif; ?>

		<div class="row g-4 g-xl-5 align-items-start">
			<aside class="col-xl-3 col-12">
				<div class="shop-filters">
					<div class="shop-filter-card">
						<div class="shop-filter-card__heading">
							<span><i class="fa-solid fa-layer-group"></i> Product Categories</span>
						</div>
						<ul class="shop-filter-list list-unstyled mb-0">
							<?php if (!empty($categories) && !is_wp_error($categories)) : ?>
								<?php foreach ($categories as $category) : ?>
									<?php $is_active = ((int) $category->term_id === (int) $current_term_id); ?>
									<li>
										<a class="shop-filter-link <?php echo $is_active ? 'is-active-category' : ''; ?>"
										   href="<?php echo esc_url(get_term_link($category)); ?>">
											<span><?php echo esc_html($category->name); ?></span>
											<span class="shop-filter-link__count"><?php echo esc_html($category->count); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>

					<div class="shop-filter-card">
						<div class="shop-filter-card__heading">
							<span><i class="fa-solid fa-award"></i> Brands</span>
						</div>
						<ul class="shop-filter-list list-unstyled mb-0">
							<?php if (is_shop()) : ?>
								<li>
									<a class="shop-filter-link <?php echo is_post_type_archive('product') ? 'is-active-category' : ''; ?>"
									   href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">
										<span>All Brands</span>
										<span class="shop-filter-link__count">All</span>
									</a>
								</li>
							<?php endif; ?>

							<?php if (!empty($brands) && !is_wp_error($brands)) : ?>
								<?php foreach ($brands as $brand) : ?>
									<?php $is_active = ((int) $brand->term_id === (int) $current_brand_id); ?>
									<li>
										<a class="shop-filter-link <?php echo $is_active ? 'is-active-category' : ''; ?>"
										   href="<?php echo esc_url(get_term_link($brand)); ?>">
											<span><?php echo esc_html($brand->name); ?></span>
											<span class="shop-filter-link__count"><?php echo esc_html($brand->count); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>

					<div class="shop-filter-card shop-filter-card--widget">
						<div class="shop-filter-card__heading">
							<span><i class="fa-solid fa-sliders"></i> Refine Your Search</span>
						</div>
						<div class="shop-filter-card__widget">
							<?php echo do_shortcode('[fe_widget]'); ?>
						</div>
					</div>
				</div>
			</aside>

			<div class="col-xl-9 col-12">
				<?php if (woocommerce_product_loop()) : ?>
					<div class="shop-archive__toolbar mb-4">
						<div class="shop-archive__toolbar-copy"><?php woocommerce_result_count(); ?></div>
						<div class="shop-archive__toolbar-sort">
							<span class="shop-archive__sort-label">Sort by</span>
							<?php woocommerce_catalog_ordering(); ?>
						</div>
					</div>

					<div class="row g-3 g-lg-4">
						<?php while (have_posts()) : the_post(); ?>
							<?php global $product; ?>
							<div class="col-sm-6 col-lg-4">
								<div <?php wc_product_class('shop-product-card h-100', $product); ?>>
									<a class="shop-product-card__image-wrap" href="<?php the_permalink(); ?>">
										<?php woocommerce_show_product_loop_sale_flash(); ?>
										<div class="shop-product-card__image">
											<?php echo get_the_post_thumbnail(get_the_ID(), 'full', ['class' => 'img-fluid']); ?>
										</div>
									</a>

									<div class="shop-product-card__body">
										<h3 class="shop-product-card__title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</h3>

										<div class="shop-product-card__meta mt-auto">
											<div class="shop-product-card__price"><?php woocommerce_template_loop_price(); ?></div>
											<div class="shop-product-card__actions">
												<?php woocommerce_template_loop_add_to_cart(); ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
					</div>

					<section class="pt-4 pt-lg-5">
						<?php do_action('woocommerce_after_shop_loop'); ?>
					</section>
				<?php else : ?>
					<?php do_action('woocommerce_no_products_found'); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
?>
