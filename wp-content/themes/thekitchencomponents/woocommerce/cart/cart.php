<?php
	defined('ABSPATH') || exit;
	
	do_action('woocommerce_before_cart');
?>

<section class="cart-page">
	<div class="cart-page__header">
		<div class="cart-page__header-copy">
			<span class="cart-page__eyebrow">Shopping Basket</span>
			<h1><?php esc_html_e('Review your basket', 'woocommerce'); ?></h1>
			<p class="mb-0">
				Update quantities, apply a coupon, and move through to checkout when everything looks right.
			</p>
		</div>
	</div>

	<div class="cart-page__main">
		<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
			<?php do_action('woocommerce_before_cart_table'); ?>
			
			<div class="table-responsive cart-page__table-wrap">
				<table class="table table-bordered table-hover align-middle">
					<thead class="table-light">
					<tr>
						<th class="text-center"><?php esc_html_e('Remove', 'woocommerce'); ?></th>
						<th><?php esc_html_e('Product', 'woocommerce'); ?></th>
						<th class="text-center"><?php esc_html_e('Price', 'woocommerce'); ?></th>
						<th class="text-center"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
						<th class="text-end"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php do_action('woocommerce_before_cart_contents'); ?>
					
					<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
						$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
						$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
						
						if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
							$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
							$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
							?>
							<tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
								<td class="text-center" data-title="<?php esc_attr_e('Remove', 'woocommerce'); ?>">
									<?php
										echo apply_filters('woocommerce_cart_item_remove_link',
											sprintf('<a href="%s" class="btn btn-sm btn-danger" aria-label="%s">&times;</a>',
												esc_url(wc_get_cart_remove_url($cart_item_key)),
												esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name)))
											),
											$cart_item_key);
									?>
								</td>
								
								<td data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
									<div class="d-flex align-items-center">
										<div class="me-3">
											<?php
												$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
												echo $product_permalink ? '<a href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>' : $thumbnail;
											?>
										</div>
										<div>
											<h6 class="mb-1"><?php echo $product_permalink ? '<a href="' . esc_url($product_permalink) . '">' . $product_name . '</a>' : $product_name; ?></h6>
											<?php echo wc_get_formatted_cart_item_data($cart_item); ?>
											<?php if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) : ?>
												<p class="text-warning small"><?php esc_html_e('Available on backorder', 'woocommerce'); ?></p>
											<?php endif; ?>
										</div>
									</div>
								</td>
								
								<td class="text-center" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>"><?php echo WC()->cart->get_product_price($_product); ?></td>
								
								<td class="text-center" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
									<?php
										$product_quantity = woocommerce_quantity_input(array(
											'input_name' => "cart[{$cart_item_key}][qty]",
											'input_value' => $cart_item['quantity'],
											'max_value' => $_product->get_max_purchase_quantity(),
											'min_value' => $_product->is_sold_individually() ? 1 : 0,
											'product_name' => $product_name,
										), $_product, false);
										
										echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
									?>
								</td>
								
								<td class="text-end" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>"><?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?></td>
							</tr>
						<?php endif; endforeach; ?>
					
					<?php do_action('woocommerce_cart_contents'); ?>
					</tbody>
				</table>
			</div>
			
			<div class="row mt-3 g-3 align-items-end">
				<div class="col-md-6">
					<?php if (wc_coupons_enabled()) : ?>
						<div class="input-group mb-0">
							<input type="text" name="coupon_code" class="form-control"
							       placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>"/>
							<button class="btn btn-outline-primary" type="submit" name="apply_coupon"
							        value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply', 'woocommerce'); ?></button>
						</div>
					<?php endif; ?>
				</div>
				
				<div class="col-md-6 text-end">
					<button type="submit" class="btn btn-primary" name="update_cart"
					        value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
						<?php esc_html_e('Update cart', 'woocommerce'); ?>
					</button>
					<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
				</div>
			</div>
		</form>

		<?php do_action('woocommerce_before_cart_collaterals'); ?>

		<div class="cart-collaterals cart-page__totals">
			<?php do_action('woocommerce_cart_collaterals'); ?>
		</div>
	</div>
</section>

<?php do_action('woocommerce_after_cart'); ?>
