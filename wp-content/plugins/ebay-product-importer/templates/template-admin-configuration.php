<?php
/**
 * The form will display in admin for plugin cofiguration.
 *
 * @package: ebay-product-importer
 */
 
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$configuration = $this->initClass->get_configuration(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

do_action( get_class($this->initClass)::PREFIX . '_configuration_start' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound

?>	

<?php 
// phpcs:ignore WordPress.Security.NonceVerification.Recommended 
if (isset($_GET['update'])) { ?>
	<div class="notice notice-success settings-error is-dismissible"> 
		<p><strong><?php echo esc_html(__('The setting has been updated successfully.', 'ebay-product-importer')); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php echo esc_html(__('Dismiss this notice.', 'ebay-product-importer')); ?></span>
		</button>
	</div>	
		<?php 
}
// phpcs:ignore WordPress.Security.NonceVerification.Recommended 	
if (isset($_GET['error'])) { 
	?>
	<div class="notice notice-error settings-error is-dismissible"> 
		<p><strong><?php echo esc_html(__('Invalid Secret key.', 'ebay-product-importer')); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php echo esc_html(__('Dismiss this notice.', 'ebay-product-importer')); ?></span>
		</button>
	</div>	
<?php } ?>
	
<div class="head">
	<div>
		<a class="inline-text" target="_blank" title="<?php echo esc_html(__('Rate this plugin', 'ebay-product-importer')); ?>" href="<?php echo esc_attr(get_class($this->initClass)::PLUGIN_LINK); ?>">
			<span class="label"><?php echo esc_html(__('Rate:', 'ebay-product-importer')); ?></span> 
			<span class="star">&#9733;</span>
			<span class="star">&#9733;</span>
			<span class="star">&#9733;</span>
			<span class="star">&#9733;</span>
			<span class="star">&#9733;</span>
		</a>
	</div>
	<div>
		<a class="inline-text" target="_blank" title="<?php echo esc_html(__('Send your query at support@nxtal.com', 'ebay-product-importer')); ?>" href="mailto:support@nxtal.com">
			<span class="label"><?php echo esc_html(__('Need Help?', 'ebay-product-importer')); ?></span> 
		</a>
	</div>
	<div>
		<a class="btn-link" target="_blank" href="<?php echo esc_attr(get_class($this->initClass)::PLUGIN_DOCS_LINK); ?>"><?php echo esc_html(__('Documentation', 'ebay-product-importer')); ?></a>
		
		<a class="btn-link" target="_blank" href="<?php echo esc_attr(get_class($this->initClass)::CHROME_EXTENSION_LINK); ?>"><?php echo esc_html(__('Download Chrome Extension', 'ebay-product-importer')); ?></a>
	</div>				
</div>

<?php

$incompatibilities = $this->get_php_incompatibilities();// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

if ($incompatibilities) { 
	?>
<div class="incompatibility-containter">
	<h3 data-target="toggle-incompatibilities" title="<?php echo esc_html(__('Click to toggle', 'ebay-product-importer')); ?>">
		<?php echo esc_html(__('PHP Incompatibilities', 'ebay-product-importer')); ?>
		<span class="dropdown-arrow">›</span>
	</h3>
	<div data-target-content="toggle-incompatibilities" class="incompatibilities">
		<table class="table" role="presentation">
			<thead>
				<tr>
					<th scope="row">
						<?php echo esc_html(__('Settings', 'ebay-product-importer')); ?>
					</th>						
					<th>
						<?php echo esc_html(__('Current', 'ebay-product-importer')); ?>
					</th>						
					<th>
						<?php echo esc_html(__('Recommended', 'ebay-product-importer')); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			foreach ($incompatibilities as $incompatibility) { ?>
				<tr>
					<th scope="row">
						<?php echo esc_html($incompatibility['name']); ?>
					</th>						
					<td>
						<?php 
						if (is_bool($incompatibility['current'])) { 
							?>
								<span class="icon error">&#x2716;</span>
							<?php							
						} else { 
							?>
								<span class="recommend1">
									<?php echo esc_html($incompatibility['current']); ?>
								</span>
						<?php } ?>
					</td>						
					<td>
						<?php 
						if (is_bool($incompatibility['recommended'])) { 
							?>
								<span class="icon success">&#x2714;</span>
							<?php							
						} else { 
							?>
								<span class="recommend">
									<?php echo esc_html($incompatibility['recommended']); ?>
								</span>
							<?php 
						}
						?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<p><?php echo esc_html(__('You may need to adjust your server\'s PHP configuration to the recommended values.', 'ebay-product-importer')); ?>
		</p>
	</div>
</div>
<?php } ?>

<div class="incompatibility-containter">
	<div class="important-note">
		<?php 
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		$cloudflare = $this->initClass->get_plugin_dir_url() . 'assets/img/cloudflare_firewall_page_rule.png';
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		$wordfence = $this->initClass->get_plugin_dir_url() . 'assets/img/wordfence_firewall_page_rule.png';
		/* translators: %1$s: Wordfence image link, %2$s: Wordfence image link, %3$s: Cloudflare image link, %4$s: Cloudflare image link  */
		echo sprintf(wp_kses_post(__('<b>Important:</b> Please note that for the plugin to function properly without any issues, you may need to disable security settings like ModSecurity on your server. If you are using a security plugin such as <a href="%1$s" target="_blank">"Wordfence"</a> on your WordPress site, you will need to <a href="%2$s" target="_blank">whitelist your import link</a> in the security plugin or consider disabling the plugin. Additionally, if <a href="%3$s" target="_blank">Cloudflare\'s</a> security firewall is enabled on your server, you might need to add a <a href="%4$s" target="_blank">page rule in Cloudflare’s firewall</a> settings to whitelist the import link.', 'ebay-product-importer')), esc_attr($wordfence), esc_attr($wordfence), esc_attr($cloudflare), esc_attr($cloudflare)); 
		?>
		
	</div>
</div>

<?php

do_action( get_class($this->initClass)::PREFIX . '_after_configuration_head' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
?>

<div class="wrap">		
	<h3>
		<?php echo esc_html(__('Configuration', 'ebay-product-importer')); ?>
	</h3>
	
	<div data-target="toggle-hosts" class="supported-hosts notice notice-warning" data-open="<?php echo esc_attr(__('Click to expand', 'ebay-product-importer')); ?>" data-close="<?php echo esc_attr(__('Click to close', 'ebay-product-importer')); ?>"> 
		<p>
			<strong>
				<?php echo esc_html(__('You can import products from:', 'ebay-product-importer')); ?>
			</strong>
		</p>
		<div class="ellipsis-text">					
			<?php echo wp_kses($this->initClass->get_importer_hosts_name(), array('b' => array())); ?>
		</div>			
		
	</div>
	<form method="post" action="admin-post.php">
	
		<?php do_action( get_class($this->initClass)::PREFIX . '_configuration_form_start' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound ?>
		
		<input type="hidden" name="action" value="<?php echo esc_attr(self::CONFIG_SAVE_ACTION); ?>">
		<?php wp_nonce_field('nxtal_importer_fields_verify'); ?>
				
		<fieldset>
			<legend><?php echo esc_html(__('Connection', 'ebay-product-importer')); ?>
			</legend>
			
			<div class="notice notice-info"> 
				<p>
					<?php echo esc_html(__('Use these credentials to establish a seamless connection between the plugin and the Chrome extension.', 'ebay-product-importer')); ?>
				</p>
			</div>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="import_link"><?php echo esc_html(__('Import link', 'ebay-product-importer')); ?></label>
							
						</th>						
						<td>
							<div class="form-group">
								<div class="input-group">
									<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										$importLink = get_rest_url(null, 'nxtal/' . get_class($this->initClass)::REST_ROUTE_SLUG); 
									?>
									<input type="text"
										id="import_link"
										value="<?php echo esc_html( $importLink ); ?>"
										readonly="readonly"
									>
									<span class="input-group-addon" onClick="copyToClipboard('import_link');"><?php echo esc_html(__('Copy', 'ebay-product-importer')); ?></span>
								</div>
								<p class="description"><?php echo esc_html(__('Use this link to connect.', 'ebay-product-importer')); ?></p>
								
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="secret_key"><?php echo esc_html(__('Secret key', 'ebay-product-importer')); ?></label>
						</th>
						<td>
							<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
							$secret_key = '';
							if (isset($configuration['secret_key'])) {
								// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
								$secret_key = $configuration['secret_key'];
							} 
							?>
							<div class="form-group">
								<div class="input-group">
									<input type="text" id="secret_key" name="secret_key" value="<?php echo esc_html($secret_key); ?>" required>
									<span class="input-group-addon" onClick="copyToClipboard('secret_key');"><?php echo esc_html(__('Copy', 'ebay-product-importer')); ?></span>
								</div>
								<p class="description"><?php echo esc_html(__('The secret key can be any random string at least 8 characters long.', 'ebay-product-importer')); ?></p>
							</div>
							<input type="button" onclick="return changeKey();" id="nxt-generateHashKey" class="button button-default" value="<?php echo esc_html(__('Generate new key', 'ebay-product-importer')); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="advance_option">
								<?php echo esc_html(__('Advanced options', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">								
								<div class="input-row">								
									<input name="advance_option" type="checkbox" id="advance_option" value="1"
									<?php 
									if (isset($configuration['advance_option']) && $configuration['advance_option']) {
										?>
									 checked <?php } ?>>
									
									<p class="description">
									<?php echo esc_html(__('Enable advanced option to import. After changing this option you will need to refresh the Chrome extension settings.', 'ebay-product-importer')); ?></p>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>				
		</fieldset>
		<fieldset>
			<legend><?php echo esc_html(__('Data', 'ebay-product-importer')); ?></legend>
			<table class="form-table" role="presentation">
				<tbody>	
					<tr>
						<th scope="row">
							<label for="price_formula">
								<?php echo esc_html(__('Price formula', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">
								<div class="input-row">
									<div class="input-group">
										<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
											$price_formula = '';
										if (isset($configuration['price_formula'])) {
											$price_formula = $configuration['price_formula'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										} 
										?>
										<input type="text" id="price_formula" name="price_formula" placeholder="P+10" value="<?php echo esc_html($price_formula); ?>">
										<span class="input-group-addon"><?php echo esc_html(__('ƒ', 'ebay-product-importer')); ?></span>
									</div>
									<div class="input-group"></div>
								</div>								
								<p class="description">
									<?php echo esc_html(__('Set up a pricing formula to adjust the product price before importing. You can use functions such as "ceil", "floor", "round", "min", "max", "pow", "abs", and "log" in your formula. To reference the current product price, use the "P" keyword.', 'ebay-product-importer')); ?>
									
									<a data-target="toggle-price_formula"><?php echo esc_html(__('See examples', 'ebay-product-importer')); ?></a>
																		
									<div data-target-content="toggle-price_formula">
									
										<?php echo esc_html(__('For example:', 'ebay-product-importer')); ?>
										
										<ul class="disc-list">
											<li>
												<code>ceil(P * 1.5)</code> 
												<?php echo esc_html(__('will round the price up to the nearest whole number after increasing it by 50%.', 'ebay-product-importer')); ?>
											</li>
											<li>
												<code>P + 10</code> 
												<?php echo esc_html(__('will increase the price by 10.', 'ebay-product-importer')); ?>
											</li>
											<li>
												<code>P - 10</code> 
												<?php echo esc_html(__('will decrease the price by 10.', 'ebay-product-importer')); ?>
											</li>
											<li>
												<code>P * 1.5</code> 
												<?php echo esc_html(__('will increase the price by 50%.', 'ebay-product-importer')); ?>
											</li>
											<li>
												<code>P * 0.95</code> 
												<?php echo esc_html(__('will decrease the price by 5%.', 'ebay-product-importer')); ?>
											</li>
											<li>
												<code>P / 2</code> 
												<?php echo esc_html(__('will halve the price.', 'ebay-product-importer')); ?>
											</li>
											<li>
												<code>if(P <= 50, P + 5, P * 1.15)</code> 
												<?php echo esc_html(__('conditional pricing.', 'ebay-product-importer')); ?>
											</li>
										</ul>
										
										<?php echo esc_html(__('Adjust these formulas as needed to fit your pricing requirements.', 'ebay-product-importer')); ?>
										
									</div>								
								</p>
							</div>							
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="max_image_count">
								<?php echo esc_html(__('Max image count', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">								
								<div class="input-row">	
									<?php 
									// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
									$max_image_count = 0;
									
									if (isset($configuration['max_image_count']) && $configuration['max_image_count']) {
										$max_image_count = $configuration['max_image_count'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
									} 
									?>
									<input name="max_image_count" type="number" min="0" id="max_image_count" value="<?php echo esc_html($max_image_count); ?>">
									</div>
								<p class="description">
								<?php echo esc_html(__('Set a maximum limit for product images to be imported. Set 0 for all image imports.', 'ebay-product-importer')); ?></p>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="image_width">
								<?php echo esc_html(__('Image size (width, height)', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">
								<div class="input-row">
									<div class="input-group">
										<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
											$image_width = '';
										if (isset($configuration['image_width'])) {
											$image_width = $configuration['image_width'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound			
										} 
										?>
										<input type="number" min="0" id="image_width" name="image_width" placeholder="<?php echo esc_html(__('width', 'ebay-product-importer')); ?>" value="<?php echo esc_html($image_width); ?>">
										
										<span class="input-group-addon"><?php echo esc_html(__('px', 'ebay-product-importer')); ?></span>
									</div>
									
									<div class="input-group">
										<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
											$image_height = '';
										if (isset($configuration['image_height'])) {
											$image_height = $configuration['image_height'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										} 
										?>
										<input type="number" min="0" id="image_height" name="image_height" placeholder="<?php echo esc_html(__('height', 'ebay-product-importer')); ?>" value="<?php echo esc_html($image_height); ?>">
										<span class="input-group-addon"><?php echo esc_html(__('px', 'ebay-product-importer')); ?></span>
									</div>								
								</div>								
								<p class="description"><?php echo esc_html(__('Set the width and height of the product image for resizing otherwise leave blank.', 'ebay-product-importer')); ?></p>
							</div>							
						</td>
					</tr>					
					<tr>
						<th scope="row">
							<label for="replace_texts">
								<?php echo esc_html(__('Replace texts', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">
								<div class="input-group">
									<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										$replace_texts = '';
									if (isset($configuration['replace_texts'])) {
										$replace_texts = $configuration['replace_texts'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound			
									} 
									?>
									<textarea id="replace_texts" name="replace_texts" placeholder="<?php echo esc_html(__('Find:Replace', 'ebay-product-importer')); ?>"><?php echo esc_html($replace_texts); ?></textarea>
								</div>
								<p class="description"><?php echo esc_html(__('Add the text you want to replace with the imported product information (eg: Find:Replace, Flipkart:Nxtal). You can add multiple text separated by commas.', 'ebay-product-importer')); ?></p>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="background_processing">
								<?php echo esc_html(__('Background processing', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">								
								<div class="input-row">								
									<input name="background_processing" type="checkbox" id="background_processing" value="1"
									<?php 
									if (isset($configuration['background_processing']) && $configuration['background_processing']) {
										?>
									 checked <?php } ?>>
									
									<p class="description">
									<?php echo esc_html(__('Enable to import product images, description, reviews in the background instead of importing them instantly. This option helps to speed up the overall product import process.', 'ebay-product-importer')); ?></p>
								</div>
							</div>							
						</td>
					</tr>
					<?php do_action( get_class($this->initClass)::PREFIX . '_after_data_configuration' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound ?>
				</tbody>
			</table>				
		</fieldset>
		<fieldset>
			<legend><?php echo esc_html(__('Affiliate', 'ebay-product-importer')); ?></legend>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="affiliate_id">
								<?php echo esc_html(__('Affiliate ID', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">
								<div class="input-group">
									<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										$affiliate_id = '';
									if (isset($configuration['affiliate_id'])) {
										$affiliate_id = $configuration['affiliate_id'];	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound		
									} 
									?>
									<input type="text" id="affiliate_id" name="affiliate_id" placeholder="affid=nxtal" value="<?php echo esc_html($affiliate_id); ?>">
								</div>								
								<p class="description"><?php echo esc_html(__('Your affiliate url parameters (eg: affid=nxtal). It can be changed when importing the product.', 'ebay-product-importer')); ?></p>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="affiliate_button_text">
								<?php echo esc_html(__('Button text', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">
								<div class="input-group">
									<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										$affiliate_button_text = __('Buy now', 'ebay-product-importer');
										
									if (isset($configuration['affiliate_button_text'])) {
										$affiliate_button_text = $configuration['affiliate_button_text'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound			
									} 
									?>
									<input type="text" id="affiliate_button_text" name="affiliate_button_text" value="<?php echo esc_attr($affiliate_button_text); ?>">
								</div>								
								<p class="description"><?php echo esc_html(__('This text will be shown on the button linking to the external/affiliate product.', 'ebay-product-importer')); ?></p>
							</div>
						</td>
					</tr>					
				</tbody>
			</table>				
		</fieldset>
		 <fieldset>
			<legend><?php echo esc_html(__('Synchronization', 'ebay-product-importer')); ?></legend>
			<div class="notice notice-info"> 
				<p>
					<?php echo esc_html(__('Enable synchronization to automatically update imported product/product data.', 'ebay-product-importer')); ?>
				</p>
			</div>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="synchronization_schedule">
								<?php echo esc_html(__('Schedule', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">
							
								<select id="synchronization_schedule" name="synchronization_schedule">
									<option value="0"><?php echo esc_html( __('Disabled', 'ebay-product-importer')); ?></option>
									<?php
									// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										$synchronization_schedule = 0;
									
									if (isset($configuration['synchronization_schedule'])) {
										$synchronization_schedule = $configuration['synchronization_schedule'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
									}
									// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										$option_groups = array(
											array(
												'name' => __('WordPress Scheduler', 'ebay-product-importer'),
												'options' => array(
													'daily' => __('Once in a Day', 'ebay-product-importer'),
													'weekly' => __('Once in a Week', 'ebay-product-importer'),
													'monthly' => __('Once in a Month', 'ebay-product-importer')
												)
											),
											array(
												'name' => __('External Scheduler', 'ebay-product-importer'),
												'options' => array(
													'external' => __('External', 'ebay-product-importer')
												)
											)
										);
										// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										foreach ($option_groups as $option_group) {
											
											echo '<optgroup label="' . esc_attr($option_group['name']) . '">';
											// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
											foreach ($option_group['options'] as $val => $option) {
												echo '<option value="' . esc_attr($val) . '" ' . selected($val, $synchronization_schedule, false) . '>' . esc_attr($option) . '</option>';
												
											}
											
											echo '</optgroup>';
											
										}	
										?>
									</optgroup>
								</select>
								<div class="external-scheduler">
<pre class="code-text">
<b>1 0 * * * curl -s <?php echo esc_html(home_url(get_class($this->initClass)::CRON_SLUG . '/')); ?></b>
| | | | |
| | | | └─── <?php echo esc_html(__('Day of the week (0 - 7) (Sunday = 0 or 7)', 'ebay-product-importer')); ?> 
| | | └────── <?php echo esc_html(__('Month (1 - 12)', 'ebay-product-importer')); ?> 
| | └────────── <?php echo esc_html(__('Day of the month (1 - 31)', 'ebay-product-importer')); ?> 
| └────────────── <?php echo esc_html(__('Hour (0 - 23)', 'ebay-product-importer')); ?> 
└────────────────── <?php echo esc_html(__('Minute (0 - 59)', 'ebay-product-importer')); ?> 
</pre>
								</div>
								
								<p class="description">
								<?php echo esc_html(__('Disable or set the synchronization scheduler to update the information. If you set it as "External" you will need to manually setup a crontab in your server to schedule the updates.', 'ebay-product-importer')); ?></p>
							
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="synchronization_price">
								<?php echo esc_html(__('Price', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">								
								<div class="input-row">								
									<input name="synchronization_price" type="checkbox" id="synchronization_price" value="1"
									<?php 
									if (isset($configuration['synchronization_price']) && $configuration['synchronization_price']) {
										?>
									 checked <?php } ?>>
									
									<p class="description">
									<?php echo esc_html(__('Enable automatic updates for imported product prices when there are changes on the source website.', 'ebay-product-importer')); ?></p>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="synchronization_stock">
								<?php echo esc_html(__('Stock', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">								
								<div class="input-row">								
									<input name="synchronization_stock" type="checkbox" id="synchronization_stock" value="1"
									<?php 
									if (isset($configuration['synchronization_stock']) && $configuration['synchronization_stock']) {
										?>
									 checked <?php } ?>>
									
									<p class="description">
									<?php echo esc_html(__('Enable automatic updates for imported product stock when there are changes on the source website. If the product is not available for sale on the source website, the stock quantity will be set to 0.', 'ebay-product-importer')); ?></p>
								</div>										
							</div>
							<div class="form-group synchronization_stock_element">	

								<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
									$synchronization_quantity = 1000;
								if (isset($configuration['synchronization_quantity'])) {
									$synchronization_quantity = (int) $configuration['synchronization_quantity'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound			
								} 
								?>
										
								<input name="synchronization_quantity" type="number" min="0" value="<?php echo esc_attr($synchronization_quantity); ?>">
								<p class="description">
									<?php echo esc_html(__('Set the default WooCommerce product stock quantity if the product is available for sale on the source website.', 'ebay-product-importer')); ?></p>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="synchronization_unavailability">
								<?php echo esc_html(__('If not available for sale', 'ebay-product-importer')); ?>
							</label>
						</th>
						<td>
							<div class="form-group">									
								<?php
									// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
									$synchronization_unavailability = 0;
									
								if (isset($configuration['synchronization_unavailability'])) {
									$synchronization_unavailability = $configuration['synchronization_unavailability'];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
								}										
								// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
									$options = array(
										0 => __('Do nothing', 'ebay-product-importer'),
										'status' => __('Disable product or variation', 'ebay-product-importer'),
										'delete' => __('Delete product or variation', 'ebay-product-importer'),
									);
									?>
								<select id="synchronization_unavailability" name="synchronization_unavailability">
								<?php	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
								foreach ($options as $val => $option) {
									echo '<option value="' . esc_attr($val) . '" ' . selected($val, $synchronization_unavailability, false) . '>' . esc_attr($option) . '</option>';
								}
								?>
																			
								</select>											
								<p class="description">
									<?php echo esc_html(__('Enable automatic disabling or deletion of imported products or variations when they are unavailable or out of stock on the source website.', 'ebay-product-importer')); ?>
								</p>
							</div>
						</td>
					</tr>							
				</tbody>
			</table>				
		</fieldset> 
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html( __('Save Changes', 'ebay-product-importer') ); ?>">
		</p>
		
		<?php do_action( get_class($this->initClass)::PREFIX . '_configuration_form_end' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound ?>
	</form>
</div>

<?php do_action( get_class($this->initClass)::PREFIX . '_configuration_end' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound ?>
