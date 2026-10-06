<?php
/**
 * The form will be used in Chrome extension to import the product.
 *
 * @package: ebay-product-importer
 *
 */
 
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$options = $this->get_import_options();

if ($options) { ?>

	<h5 class="card-title text-center">
		<?php esc_html_e('Import Options', 'ebay-product-importer'); ?>
	</h5>
	<h6 class="text-center">
		<?php esc_html_e('Select the options you want to import.', 'ebay-product-importer'); ?>
	</h6>
	<div class="form-group">
		<input type="text" name="affiliate_link" value="<?php echo esc_attr($this->configVars['affiliate_id']); ?>" placeholder="<?php esc_attr_e('Affiliate link', 'ebay-product-importer'); ?>" class="input">
		<p class="help-block">
			<?php esc_html_e('Enter product affiliate link/ID with tag.', 'ebay-product-importer'); ?>
		</p>
	</div>
	<div class="form-group">
		<input type="text" name="id_product" placeholder="<?php esc_attr_e('Existing product ID', 'ebay-product-importer'); ?>" class="input">
		<p class="help-block">
			<?php esc_html_e('If you want to update any existing product with the data of these options then enter the product ID otherwise a new product will be created in your shop.', 'ebay-product-importer'); ?>
		</p>
	</div>
	
	<?php

	do_action( get_class($this->initClass)::PREFIX . '_before_option' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	foreach ($options as $option) { 
		?>
		<div class="form-group">
			<label>
				<input name="<?php echo esc_attr($option['name']); ?>" type="checkbox" checked="checked" value="1">
				<?php 
				echo esc_html($option['label']);
				
				if ('' != $option['desc']) { 
					?>
					<span class="help-block">
						(<?php echo esc_html($option['desc']); ?>)
					</span>
				<?php } ?>
			</label>
		</div>
		<?php
	}
	
	do_action( get_class($this->initClass)::PREFIX . '_after_option' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
	if ((int)$this->configVars['advance_option']) { 
		?>
		<div class="form-group text-right">
			<a href="#" class="toggle-more">
				<?php esc_html_e('Advanced options', 'ebay-product-importer'); ?>
			</a>
		</div>
		<div class="toggle-content row">
		
			<?php do_action( get_class($this->initClass)::PREFIX . '_before_advance_option' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound ?>
		
			<div class="form-group row">
				<label for="association_sku" class="col-4 col-form-label">
					<?php esc_html_e('SKU', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<input type="text" class="form-control" id="association_sku" name="association[sku]" value="">
					<span class="help-block">
						<?php esc_html_e('Set product SKU.', 'ebay-product-importer'); ?>
					</span>		
				</div>
			</div>				
			<div class="form-group row">
				<label for="association_categories" class="col-4 col-form-label">
					<?php esc_html_e('Category association', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<select name="association[categories][]" id="association_categories" class="form-control chosen" multiple="true">
					
					<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
					foreach ($this->get_categories() as $category) { ?>
						<option value="<?php echo esc_attr($category['term_id']); ?>">
							<?php echo esc_html($category['name']); ?>
						</option>
					<?php } ?>
				
					</select>		
					<span class="help-block">
						<?php esc_html_e('Choose categories to assign to the product.', 'ebay-product-importer'); ?>
					</span>
				</div>		
			</div>
			<div class="form-group row">
				<label for="association_tax_class" class="col-4 col-form-label">
					<?php esc_html_e('Tax class', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<select class="form-control" id="association_tax_class" name="association[tax_class]">
						<option value="0">
							<?php esc_html_e('Standard', 'ebay-product-importer'); ?>
						</option>
						<?php 
						// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
						foreach ($this->get_tax_classes() as $taxClass) { ?>
							<option value="<?php echo esc_attr($taxClass['slug']); ?>">
								<?php echo esc_html($taxClass['name']); ?>
							</option>
						<?php } ?>
					</select>
					<span class="help-block">
						<?php esc_html_e('Set product tax class.', 'ebay-product-importer'); ?>
					</span>	
				</div>		
			</div>
			<div class="form-group row">
				<label for="association_shipping_class" class="col-4 col-form-label">
					<?php esc_html_e('Shipping class', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<select class="form-control" id="association_shipping_class" name="association[shipping_class]">
						<option value="-1">
							<?php esc_html_e('No shipping class', 'ebay-product-importer'); ?>
						</option>
						<?php 
						// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
						foreach ($this->get_shipping_classes() as $shippingClass) { ?>
							<option value="<?php echo esc_attr($shippingClass['term_id']); ?>">
								<?php echo esc_html($shippingClass['name']); ?>
							</option>
						<?php } ?>
					</select>
					<span class="help-block">
						<?php esc_html_e('Set product shipping class.', 'ebay-product-importer'); ?>
					</span>	
				</div>		
			</div>
			<div class="form-group row">
				<label for="association_quantity" class="col-4 col-form-label">
					<?php esc_html_e('Quantity', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<input type="text" class="form-control" id="association_quantity" name="association[quantity]" value="<?php echo esc_attr( $this->initClass->get_configuration(true, 'synchronization_quantity')); ?>">
					<span class="help-block">
						<?php esc_html_e('Set product stock quantity.', 'ebay-product-importer'); ?>
					</span>		
				</div>
			</div>
			<div class="form-group row">
				<label for="association_price" class="col-4 col-form-label">
					<?php esc_html_e('Price', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<input type="text" class="form-control" id="association_price" name="association[price]" value="<?php echo esc_attr( $this->initClass->get_configuration(true, 'price_formula')); ?>">
					<span class="help-block">
						<?php esc_html_e('Set a price or formula to override the current product price. To reference the current product price, use the "P" keyword.', 'ebay-product-importer'); ?>
					</span>		
				</div>
			</div>					
			<div class="form-group row">
				<label for="association_visibility" class="col-4 col-form-label">
					<?php esc_html_e('Visibility', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<select class="form-control" id="association_visibility" name="association[visibility]">
						<option value="visible">
							<?php esc_html_e('Shop and search results', 'ebay-product-importer'); ?>
						</option>
						<option value="catalog">
							<?php esc_html_e('Shop only', 'ebay-product-importer'); ?>
						</option>
						<option value="search">
							<?php esc_html_e('Search results only', 'ebay-product-importer'); ?>
						</option>
						<option value="hidden">
							<?php esc_html_e('Hidden', 'ebay-product-importer'); ?>
						</option>
					</select>
					<span class="help-block">
						<?php esc_html_e('Set catalog visibility.', 'ebay-product-importer'); ?>
					</span>	
				</div>		
			</div>
			<div class="form-group row">
				<label for="association_review" class="col-4 col-form-label">
					<?php esc_html_e('Max reviews', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<input type="text" class="form-control" id="association_review" name="association[review]" value="10">
					<span class="help-block">
						<?php esc_html_e('Set maximum product reviews to import. Set 0 for all available reviews, this may increase the import execution time. Applicable only if Customer Reviews option is checked.', 'ebay-product-importer'); ?>
					</span>		
				</div>
			</div>
			<div class="form-group row">
				<label for="association_post_status" class="col-4 col-form-label">
					<?php esc_html_e('Status', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<select class="form-control" id="association_post_status" name="association[post_status]">
						<option value="draft">
							<?php esc_html_e('Draft', 'ebay-product-importer'); ?>
						</option>
						<option value="pending">
							<?php esc_html_e('Pending Review', 'ebay-product-importer'); ?>
						</option>
						<option value="publish">
							<?php esc_html_e('Published', 'ebay-product-importer'); ?>
						</option>
					</select>		
					<span class="help-block">
						<?php esc_html_e('Set product status.', 'ebay-product-importer'); ?>
					</span>	
				</div>		
			</div>
			<div class="form-group row">
				<label for="association_sku_existing" class="col-4 col-form-label">
					<?php esc_html_e('If SKU exists', 'ebay-product-importer'); ?>
				</label>
				<div class="col-8">
					<select class="form-control" id="association_sku_existing" name="association[sku_existing]">
						<option value="0">
							<?php esc_html_e('Add product with new SKU', 'ebay-product-importer'); ?>
						</option>								
						<option value="1">
							<?php esc_html_e('Update product with this data', 'ebay-product-importer'); ?>
						</option>
						<option value="2">
							<?php esc_html_e('Do not Add or Update', 'ebay-product-importer'); ?>
						</option>
					</select>		
					<span class="help-block">
						<?php esc_html_e('Applicable only if the Existing product ID box is empty.', 'ebay-product-importer'); ?>
					</span>	
				</div>		
			</div>
			
			<?php do_action( get_class($this->initClass)::PREFIX . '_after_advance_option' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound ?>
		</div>
		<?php 
	
	}
}
