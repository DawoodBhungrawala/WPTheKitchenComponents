<?php
/**
 * Front class to handle import request
 *
 * @package: ebay-product-importer
 */

namespace Nxtal\EBayProductImporter\classes;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class FrontImporter {
	
	use TraitImporter;
	
	public $initClass;	
	public $configVars = array();
	protected $parser;	
	
	public const OLD_REST_ROUTE_SLUG = 'ebayproductimporter';
	
	public function __construct( $initClass) {
		
		$this->initClass = $initClass;
		
		$this->configVars = $this->initClass->get_configuration();
		
		$this->hooks();
	}
	
	protected function hooks() {
		
		add_action(
			'rest_api_init',
			function () {			
				register_rest_route(
					'nxtal',
					get_class($this->initClass)::REST_ROUTE_SLUG,
					array(
						'methods' => 'GET, POST', 
						'callback' => array(
							$this,
							'process_importer'
						),
						// phpcs:ignore WordPressVIPMinimum.Security.RestApi.RestApiPermissionCheck, WordPress.Security.EscapeOutput.OutputNotEscaped
						'permission_callback' => '__return_true'
					)
				);
			}
		);
		
		// Backward compatibility
		add_action(
			'rest_api_init',
			function () {			
				register_rest_route(
					'nxtal',
					self::OLD_REST_ROUTE_SLUG,
					array(
						'methods' => 'GET, POST', 
						'callback' => array(
							$this,
							'process_importer'
						),
						// phpcs:ignore WordPressVIPMinimum.Security.RestApi.RestApiPermissionCheck, WordPress.Security.EscapeOutput.OutputNotEscaped
						'permission_callback' => '__return_true'
					)
				);
			}
		);
		
		add_action(
			'wp_head',
			array(
				$this->initClass,
				'review_attachment_css'
			)
		);	
		
		add_action(
			'wp_enqueue_scripts',
			array(
				$this,
				'enqueue_hls_video_script'
			)
		);	
		
	}
	
	public function process_importer() {
		
		$response = array();
		
		if ($this->get_query_param('action') != null && in_array($this->get_query_param('action'), array('connect', 'import'))) {
			$response = $this->validate_access();
			if (!$response) {
				$response = $this->{$this->get_query_param('action') . 'Action'}($this->get_query_param());
			}
		}
	
		if (!$response) {
			$response = array(
				'status' => 0,
				'message' => __('Invalid action!', 'ebay-product-importer')
			);
		}
		
		$this->displayResponse($response);
	}

	protected function validate_access() {
		
		if (!$this->get_query_param('secret_key') || $this->get_query_param('secret_key') != $this->configVars['secret_key']) {
			
			return array(
				'status' => 0,
				'message' => __('Invalid credential!', 'ebay-product-importer')
			);
		}
		
		$this->initClass->log(array(__FUNCTION__, 'Credential validated'), __FILE__, __LINE__);
	}
	
	protected function connectAction() {
		
		$this->initClass->log(array(__FUNCTION__, 'Connection success'), __FILE__, __LINE__);
		
		return array(
			'status' => 1,
			'message' => __('Success.', 'ebay-product-importer'),
			'data' => array(
				'shop' => array(
					'name' => get_bloginfo('name'),
					'url' => get_bloginfo('url'),
					'desc' => $this->initClass->get_importer_hosts_name()
				),
				'form' => $this->iconv($this->get_import_option_html())
			)
		);
	}
	
	protected function get_import_option_html() {
		
		$params = array(
			'options' => $this->get_import_options(),
			'isAdvaceEnabled' => (int) $this->configVars['advance_option'],
			'affiliateLink' => $this->configVars['affiliate_id']
		);

		if ((int) $this->configVars['advance_option']) {
			$categories = $this->get_categories();
			$taxClasses = $this->get_tax_classes();
			$shippingClasses = $this->get_shipping_classes();
			
			$params = array_merge(
				$params,
				array(
					'categories'=> $categories,
					'taxClasses' => $taxClasses,
					'shippingClasses' => $shippingClasses
				)
			);
		}
		
		ob_start();

		include($this->initClass->get_plugin_base_path(false) . '/templates/template-front-importer-options.php');
		
		$html = ob_get_clean();
		
		$html = apply_filters( get_class($this->initClass)::PREFIX . '_get_import_option_html', $html );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
		return $this->trim($html);
	}
	
	protected function importAction( $params) {
		
		$this->initClass->log(array(__FUNCTION__, 'Import action initiated'), __FILE__, __LINE__);
				
		ignore_user_abort(true);
		
		$document = $params['document'];
		
		if (!$this->load_praser($document)) {
			return array(
				'status' => 0,
				'message' => __('The data or product page is invalid.', 'ebay-product-importer')
			);
		}
		
		parse_str($params['form'], $importOptions);
		
		// Check to at least one import option must be selected except id_product, association
		
		if (count($importOptions) < 2) {
			return array(
				'status' => 0,
				'message' => __('One must choose at least one option to import the product.', 'ebay-product-importer')
			);
		}
		
		$importOptions['product_link'] = $document['url'];
		
		/* Set all options as false in default. */
		$optionNames = array_column($this->get_import_options(), 'name');
		array_unshift($optionNames, 'id_product', 'association', 'affiliate_link', 'product_link');
		
		$options = array();
		foreach ($optionNames as $option) {
			if (isset($importOptions[$option])) {
				$options[$option] = $importOptions[$option];
			} else {
				$options[$option] = 0;
			}
		}
		
		$response = $this->createProduct($options);
		
		if (!$response) {
			$response['status'] = false;
			$response['message'] = __('Error in importing, please try again.', 'ebay-product-importer');
		}
		
		return $response;
	}
	
	protected function createProduct( $options) {
			
		$response = array();
		
		if ($this->parser->getTitle() == '') {
			return array(
				'status' => false,
				'message' => __('The data or product page is invalid.', 'ebay-product-importer')
			);
		}
		
		/*
		$available_to_import = apply_filters( get_class($this->initClass)::PREFIX . '_available_to_import', true, $this->parser->isAvailableForSale(), $options);
		
		if (!$available_to_import) {
			return array(
				'status' => false,
				'message' => __('This product is not available for sale on the source website!', 'ebay-product-importer')
			);
		}*/
		
		if (isset($options['association']['sku']) && $options['association']['sku']) {
			$sku = $options['association']['sku'];
			$sku = $this->clean_text($sku, 32, false);
		} else {
			$sku = $this->parser->getSKU();	
		}		
		
		if (( !isset($options['association']['sku_existing']) || ( isset($options['association']['sku_existing']) && !$options['association']['sku_existing'] ) ) && !$options['id_product']) {
			$sku = $this->generate_unique_id($this->clean_text($sku, 32, false));
		}
		
		$id_from_sku = wc_get_product_id_by_sku( $sku );
		
		if ($options['id_product']) {
			
			$product = wc_get_product( $options['id_product'] );
			
			if (!$product) {
				return array(
					'status' => false,
					'message' => __('Product does not exists!', 'ebay-product-importer')
				);
			}
			
			if ($id_from_sku) {
			
				$product = wc_get_product( $id_from_sku );
				
				if ($product && ( 0 < $product->get_parent_id() || $id_from_sku != $options['id_product'] )) {
					
					return array(
						'status' => false,
						'message' => __('A product with the same SKU already exists!', 'ebay-product-importer') .
						' #' . $id_from_sku
					);
				}
			}
			
		} elseif ($id_from_sku) {
				
			if (isset($options['association']['sku_existing']) && 2 == $options['association']['sku_existing'] ) {
				
				return array(
					'status' => false,
					'message' => __('A product with the same SKU already exists!', 'ebay-product-importer') .
					' #' . $id_from_sku
				);
			}
		
			$product = wc_get_product( $id_from_sku );	

			if ($product && 0 < $product->get_parent_id()) {
				$options['id_product'] = $product->get_parent_id();
			} else {
				$options['id_product'] = $id_from_sku;
			}		
		}

		if ($options['affiliate_link']) {
			
			$product = new \WC_Product_External((int) $options['id_product']);
		} elseif ($options['variant'] && $this->parser->getCombinations()) {
			
			$product = new \WC_Product_Variable((int) $options['id_product']);
		} else {
			
			$product = new \WC_Product((int) $options['id_product']);
		}
			
		if (( $product->get_id() && $options['name'] ) || !$product->get_id()) {
			
			$product_title = $this->clean_text($this->replace_text(sanitize_text_field($this->parser->getTitle())));
			
			$this->set_property($product, 'name', $product_title);
		}
		
		if (isset($options['short_description']) && $options['short_description']) {
			
			$short_description = $this->purifyHTML(
				$this->replace_text(
					$this->parser->getShortDescription()
				)
			);
			
			$this->set_property($product, 'short_description', $short_description);
		}
		
		if (( !$product->get_id() || $options['sku'] ) && $sku) {
			
			$sku = $this->generate_unique_id($sku, $product->get_id());
			
			$this->set_property($product, 'sku', $sku);
		}
		
		$upc = sanitize_text_field($this->parser->getUPC());
		
		if (isset($options['upc']) && $options['upc'] && $upc) {
			
			$this->set_property($product, 'global_unique_id', $this->generate_unique_id($upc, $product->get_id(), 'global_unique_id' ));
		}
		
		$price = (float) sanitize_text_field($this->parser->getPrice());
		
		if (( $product->get_id() && $options['price'] ) || !$product->get_id()) {
						
			if (isset($options['association']['price'])
				&& $this->is_string($options['association']['price'])
				&& false === $this->calculate_price_expression($options['association']['price'], array('P' => $price))
			) {
				return array(
					'status' => false,
					'message' => __('Invalid price formula!', 'ebay-product-importer')
				);
			}

			$regular_price = (float) sanitize_text_field($this->parser->getRegularPrice());
					
			$sale_price = $this->calculate_price(max($price, 0), $options);
			
			$regular_price = $this->calculate_price(max($regular_price, 0), $options);
			
			if ($sale_price >= $regular_price) {
									
				$regular_price = $sale_price;
				$sale_price = '';
			}
			
			if (!$regular_price) {
				$regular_price = '';
			}
			
			$this->set_property($product, 'sale_price', $sale_price);
			$this->set_property($product, 'regular_price', $regular_price);
		}
		
		$weight = $this->parser->getWeight();
		
		if (isset($options['weight']) && $options['weight'] && $weight) {
			
			$finalWeight = $this->convert_weight(
				$weight['value'],
				$weight['unit'],
				get_option('woocommerce_weight_unit')
			);
			
			$this->set_property($product, 'weight', $finalWeight);
		}	
		
		if (isset($options['association']['tax_class'])) {
			$taxClass = sanitize_text_field($options['association']['tax_class']);
			if ($taxClass) {
				
				$this->set_property($product, 'tax_class', $taxClass);
			}
		}
		
		if (isset($options['association']['shipping_class'])) {
			$shippingClass = (int)sanitize_text_field($options['association']['shipping_class']);
			if ($shippingClass) {
				
				$this->set_property($product, 'shipping_class_id', $shippingClass);
			}
		}
		
		if ($options['affiliate_link']) {			
			$product->set_manage_stock(false);
		} elseif (isset($options['association']['quantity'])) {
			
			$this->set_property($product, 'manage_stock', true);
			
			$quantity = (int) sanitize_text_field($options['association']['quantity']);
			
			if (0 < $quantity) {
				
				$this->set_property($product, 'stock_quantity', $quantity);
				$this->set_property($product, 'stock_status', 'instock');
			}
		}
		
		$visibility = 'visible';
		if (isset($options['association']['visibility'])) {
			$visibility = sanitize_text_field($options['association']['visibility']);
		}
		
		$this->set_property($product, 'catalog_visibility', $visibility);
		
		$categories = array();
		if (isset($options['association']['categories'])) {
			$categories = (array) $options['association']['categories'];
		}
		if (!$categories && $options['category']) {
			$categories = $this->addCategories((array) $this->parser->getCategories());
		}
		
		if ($categories) {
			
			$this->set_property($product, 'category_ids', $categories);
		}
		
		if (isset($options['association']['post_status'])) {
			$postStatus = sanitize_text_field($options['association']['post_status']);
			
			$this->set_property($product, 'status', $postStatus);
		}
		
		$dimension = $this->parser->getDimension();
		
		if (isset($options['dimension']) && $options['dimension'] && $dimension) {
			
			$length = $this->convert_dimension(
				$dimension['length'],
				$dimension['unit'],
				get_option('woocommerce_dimension_unit')
			);
			
			$this->set_property($product, 'length', $length );

			$width = $this->convert_dimension(
				$dimension['width'],
				$dimension['unit'],
				get_option('woocommerce_dimension_unit')
			);
			
			$this->set_property($product, 'width', $width );

			$height = $this->convert_dimension(
				$dimension['height'],
				$dimension['unit'],
				get_option('woocommerce_dimension_unit')
			);
			
			$this->set_property($product, 'height', $height );
		}
		
		$product = apply_filters( get_class($this->initClass)::PREFIX . '_product_before_save', $product, $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
		// To fix the WooCommerce Action Scheduler SKU processing issue.
		add_filter( 'woocommerce_is_rest_api_request', '__return_false' ); 
		
		$product->save();
		
		$combinations = $this->parser->getCombinations();
				
		if ($options['image']) {
			
			$images = $this->parser->getImages();
			
			$coverImage = $this->parser->getCoverImage();
			
			if (!$coverImage && $images) {

				$coverImage = array_shift($images[key($images)]);
			}
			
			$coverImage = apply_filters( get_class($this->initClass)::PREFIX . '_product_cover_image', $coverImage, $options, 'product' );	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound		
			
			if ($images) {
			
				if (!$options['variant']) {
					$imgs = array();
					
					if ($combinations) {
						foreach ($combinations as $combination) {
							if ($this->parser->getSKU() == $combination['sku']) {
								if (isset($images[$combination['image_index']])) {
									$imgs = $images[$combination['image_index']];
								}								
								break 1;
							}
						}
					}

					if ($imgs) {
						$images = $imgs;
					} else {
						$images = current($images);
					} 
				} else {
					$imgs = array();
					foreach ($images as $img) {
						$imgs = array_merge($imgs, $img);
					}
					$images = $imgs;
				}
				
				$images = array_unique($images);
				
				$images = apply_filters( get_class($this->initClass)::PREFIX . '_product_gallery_images', $images, $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
				
				$images = $this->update_image_count($images);
								
				if ($this->configVars['background_processing']) {

					$this->initClass->cronClass->push_to_queue(
						array(
							'hook' => 'background_image_import',
							'type' => 'cover',
							'product_id' => $product->get_id(),
							'image_urls' => $coverImage
						)
					);
					
					$images = array_chunk($images, 5);
					
					foreach ($images as $imgs) {
						
						$this->initClass->cronClass->push_to_queue(
							array(
								'hook' => 'background_image_import',
								'type' => 'gallery',
								'product_id' => $product->get_id(),
								'image_urls' => $imgs
							)
						);
					}
					
				} else {
					
					$this->add_images_to_product($product, $coverImage);
					
					$this->add_images_to_product($product, $images, 'gallery');
				}
			}
		}

		$isDescriptionScheduled = false;
		
		if ($options['description']) {
			
			$description = $this->parser->getDescription();
			
			if ($description) {
				
				// If the image is found in the description, schedule it for background import.
				if (false !== stripos($description, '<img')) {
					$isDescriptionScheduled = true;
				}
			
				if (true === $isDescriptionScheduled
					&& $this->configVars['background_processing']
				) {
					$this->initClass->cronClass->push_to_queue(
						array(
							'hook' => 'description_import',
							'product_id' => $product->get_id(),
							'description' => $description
						)
					);
						
				} else {
					$this->add_product_description($product, $description);
				}			
			}
		}
		
		if (isset($options['video']) && $options['video']) {
		
			$videos = $this->parser->getVideos();
			
			$videos = apply_filters( get_class($this->initClass)::PREFIX . '_product_videos', $videos, $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			
			if ($videos) {
				
				if (true === $isDescriptionScheduled
					&& $this->configVars['background_processing']
				) {
					$this->initClass->cronClass->push_to_queue(
						array(
							'hook' => 'video_import',
							'product_id' => $product->get_id(),
							'videos' => $videos
						)
					);
						
				} else {
					$this->add_product_videos($product, $videos);
				}
			}
		}
		
		if (isset($options['product_link']) && $options['product_link']) {
			
			$product_origin = apply_filters( get_class($this->initClass)::PREFIX . '_product_origin', $options['product_link'], $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			
			update_post_meta($product->get_id(), 'product_origin', $product_origin);
		}
		
		if (method_exists($this->parser, 'getPriceLink')) {
			
			$price_link = apply_filters( get_class($this->initClass)::PREFIX . '_product_price_link', $this->parser->getPriceLink(), $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			
			if ($price_link) {
				update_post_meta($product->get_id(), '_PRICE_LINK', $price_link);
			}
		}
		
		if ($options['affiliate_link'] || !$combinations || !$options['variant']) {
			
			update_post_meta($product->get_id(), '_ORIGIN_ID', $this->parser->getProductId());
			update_post_meta($product->get_id(), '_UPDATED_AT', gmdate('Y-m-d H:i:s'));
		}
		
		if ($this->parser->isSynchronizable()) {
				
			$this->update_product_synchronizable_status(
				$product->get_id(),
				(int) $this->initClass->cronClass->is_auto_update_enabled()
			);
		}
		
		$features = array();
		
		if (isset($options['feature']) && $options['feature']) {
			
			$featureGroups = $this->parser->getFeatures(); // Get all the product features to import.
			
			if ($featureGroups) {
				
				foreach ($featureGroups as $featureGroup) {
					foreach ($featureGroup['attributes'] as $feature) {
					
						$features[] = array(
							'name' => $feature['name'],
							'values' => array($feature['value'])
						);
					}
				}
			}
		}
		
		$brand = sanitize_text_field($this->parser->getBrand());
		
		$brand = apply_filters( get_class($this->initClass)::PREFIX . '_product_brand', $brand, $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
		if (isset($options['brand']) && $options['brand'] && $brand) {
			// Add manufacturer as attribute
			$features[] = array(
				'name' => __('Manufacturer', 'ebay-product-importer'),
				'values' => array( $brand )
			);
				
			$brandTerm = get_term_by( 'slug', $this->slugify($brand), 'product_brand', ARRAY_A);
			
			if (!$brandTerm) {
			
				$brand = wp_insert_term(
					$brand,
					'product_brand',
					array(
						'description' => '',
						'slug' => $this->slugify($brand)
					)
				);
				
				if (!is_wp_error($brand) && isset($brand['term_id'])) {
					$brandTerm = $brand;
				}
			}
			
			if ($brandTerm) {
				wp_set_object_terms( $product->get_id(), array($brandTerm['term_id']), 'product_brand' );
			}
		}
		
		$features = apply_filters( get_class($this->initClass)::PREFIX . '_product_features', $features, $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
		if ($features) {
			// Product features will be added as attribute
			$this->addProductAttributes($features, $product);
		}
	   
		if (!$options['affiliate_link']) {
			$this->createProductVariations($product, $combinations, $options);
		} else {
			// Add affiliate information
			$this->addAffiliate($product, $options);
		}
		
		if (isset($options['review']) && $options['review']) {
			
			$maxReviews = 10; // All reviews
			
			if (isset($options['association']['review'])) {
				$maxReviews = (int) $options['association']['review'];
			}
				
			$reviews = $this->parser->getCustomerReviews($maxReviews);
			
			if (0 < $maxReviews) {
				$reviews = array_splice($reviews, 0, (int) $maxReviews);
			}
			
			if ($this->configVars['background_processing']) {

				$this->initClass->cronClass->push_to_queue(
					array(
						'hook' => 'background_reviews_import',
						'product_id' => $product->get_id(),
						'reviews' => $reviews
					)
				);
					
			} else {
				
				$this->add_product_reviews( $product, $reviews);
			}
		}
		
		if ($this->configVars['background_processing']) {
			$this->initClass->cronClass->save()->dispatch();
		}		
		
		if ($product->get_id()) {
			
			do_action( get_class($this->initClass)::PREFIX . '_product_import_after', $product, (bool)$options['id_product'] );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			
			$this->initClass->log(array(__FUNCTION__, $product->get_id(), 'Product created/updated'), __FILE__, __LINE__);	
			
			$response['status'] = true;
			if ($options['id_product']) {
				$message = __('Product updated successfully.', 'ebay-product-importer');
			} else {
				$message = __('Product imported successfully.', 'ebay-product-importer');
			}
			if ($product->get_status() == 'publish') {
				$message .= '<br><a href="' . get_permalink($product->get_id()) . '">' . get_permalink($product->get_id()) . '</a>';
			}
			$response['message'] = $message;
		}		

		return $response;
	}
	
	protected function addCategories( $categoryNameArray) {
		
		$categoryIds = array();
		$parentId = 0;
		$existingCategories = $this->get_categories();
		if (is_array($categoryNameArray)) {
			foreach ($categoryNameArray as $categoryName) {
				$key = array_search($categoryName, array_column($existingCategories, 'name'));
				if ('' != $key) {
					$parentId = $existingCategories[$key]['term_id'];
					$categoryIds[] = $parentId;
					continue;
				}
				$category = wp_insert_term(
					$categoryName,
					'product_cat',
					array(
						'description' => '',
						'slug' => $this->slugify($categoryName),
						'parent' => $parentId
					)
				);
				if (!is_wp_error($category) && isset($category['term_id'])) {
					$parentId = $category['term_id'];
					$categoryIds[] = $parentId;
				}
			}
		}
		
		return $categoryIds;
	}
	
	protected function addProductAttributes($attributes_data, $product, $variation = 0) {
		
		if ($attributes_data) {
			
			$productAttributes = get_post_meta($product->get_id(), '_product_attributes', true);
			
			if (!$productAttributes) {
				$productAttributes = array();
			}
						
			foreach ($attributes_data as $attribute) {
				
				$attribute_name = sanitize_text_field(trim($attribute['name']));

				$attribute_slug = wc_sanitize_taxonomy_name(substr($attribute_name, 0, 27));
				
				$attribute_slug = 'type' === $attribute_slug ? $attribute_slug . '1' : $attribute_slug; // Slug "type" is not allowed because it is a reserved term.
				
				$taxonomy_name = wc_attribute_taxonomy_name($attribute_slug);

				if (!taxonomy_exists($taxonomy_name)) {

					$attribute_id = wc_create_attribute(array(
						'name' => $attribute_name,
						'slug' => $attribute_slug,
						'type' => 'select'
					));
					
					if ( is_wp_error( $attribute_id )) {
						
						continue;
					}
					
					register_taxonomy($taxonomy_name, 'product', array(
						'hierarchical' => true,
						'label' => $attribute_name,
						'query_var' => true,
						'rewrite' => array('slug' => $attribute_slug),
					));
				}
				
				$attribute_values = array();
				
				if (isset($productAttributes[ $taxonomy_name ]['value'])) {
					
					$attribute_values = $productAttributes[ $taxonomy_name ]['value'];
				}
				
				if ($attribute['values']) {
					
					foreach ($attribute['values'] as $attribute_value) {
						
						$attribute_value = sanitize_text_field(trim($attribute_value));
						
						$term_slug = wc_sanitize_taxonomy_name($attribute_value);

						if (!term_exists($term_slug, $taxonomy_name)) {

							$term_id = wp_insert_term(
								$attribute_value,
								$taxonomy_name,
								array('slug' => $term_slug)
							);
							
							if ( is_wp_error( $term_id )) {
								
								continue;
							}
						}
						
						$attribute_values[] = $attribute_value;
					}

					wp_set_object_terms($product->get_id(), $attribute_values, $taxonomy_name, true);					
				}				
				
				$productAttributes[ $taxonomy_name ] = array(
					'name' => $taxonomy_name,
					'value' => $attribute_values,
					'position' => count($productAttributes) + 1,
					'is_visible' => 1,
					'is_variation' => $variation,
					'is_taxonomy' => 1
				);
			}
			
			$productAttributes = apply_filters( get_class($this->initClass)::PREFIX . '_product_attributes', $productAttributes, $product );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
				
			update_post_meta($product->get_id(), '_product_attributes', $productAttributes);
			
		}
	}
	
	protected function createProductVariations( $product, $variations, $options) {
		
		$variations = apply_filters( get_class($this->initClass)::PREFIX . '_product_variations', $variations, $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
		$images = $this->parser->getImages();		
		
		if ($options['variant'] && $variations) {
			
			try {
				
				$this->addProductAttributes($this->parser->getAttributes(), $product, 1);
				
				$default_attributes = $product->get_default_attributes();
				
				foreach ($variations as $variation) {
					
					$varAttributes = array();
					
					foreach ($variation['attributes'] as $vattribute) {
						
						$attribute_name = sanitize_text_field(trim($vattribute['name']));
						$attribute_value = sanitize_text_field(trim($vattribute['value']));

						$attribute_slug = wc_sanitize_taxonomy_name(substr($attribute_name, 0, 27));
						
						$attribute_slug = 'type' === $attribute_slug ? $attribute_slug . '1' : $attribute_slug; // Slug "type" is not allowed because it is a reserved term.
						
						$taxonomy_name = sanitize_title(wc_attribute_taxonomy_name($attribute_slug));
						
						$varAttributes[$taxonomy_name] = sanitize_title(wc_sanitize_taxonomy_name($attribute_value));
					}
					
					$variation_id = (int) $this->get_variation_id_by_attributes($product, $varAttributes);
										
					if (isset($options['association']['sku']) && $options['association']['sku']) {
						$sku = $this->clean_text($options['association']['sku'], 32);
					} elseif ($options['sku'] && $variation['sku']) {
						$sku = $this->clean_text($variation['sku'], 32);
						;
					} elseif ($variation_id) {
						$sku = get_post_meta($variation_id, '_sku', true);
					} else {
						$sku = $product->get_id();
					}
				
					$productVariation = new \WC_Product_Variation($variation_id);
					
					$sku = $this->generate_unique_id($sku, $variation_id);
					
					if ($options['sku'] && $sku) {
						
						$this->set_property($productVariation, 'sku', $sku );
					}
					
					$upc = sanitize_text_field($variation['upc']);
					
					if (isset($options['upc']) && $options['upc'] && $upc) {
						
						$this->set_property($productVariation, 'global_unique_id', $this->generate_unique_id($upc, $variation_id, 'global_unique_id' ));
					}
					
					if ($options['price'] || !$options['id_product']) {
					
						$price = (float) sanitize_text_field($variation['price']);
						
						if (isset($variation['regular_price'])) {
							$regular_price = (float) sanitize_text_field($variation['regular_price']);
						} else {
							$regular_price = $price;
						}
						
						$sale_price = $this->calculate_price(max($price, 0), $options);
						
						$regular_price = $this->calculate_price(max($regular_price, 0), $options);
						
						if ($sale_price >= $regular_price) {
									
							$regular_price = $sale_price;
							$sale_price = '';
						}
						
						if (!$regular_price) {
							$regular_price = '';
						}
						
						$this->set_property($productVariation, 'sale_price', $sale_price);
						$this->set_property($productVariation, 'regular_price', $regular_price);
					}
					
					$this->set_property($productVariation, 'parent_id', $product->get_id() );
					
					if (isset($options['weight']) && $options['weight'] && isset($variation['weight']['value'])) {
						
						$finalWeight = $this->convert_weight(
							$variation['weight']['value'],
							$variation['weight']['unit'],
							get_option('woocommerce_weight_unit')
						);
						
						$this->set_property($productVariation, 'weight', $finalWeight );
					}
					
					if (isset($options['dimension']) && $options['dimension']) {
						
						$this->set_property($productVariation, 'length', $product->get_length() );
						$this->set_property($productVariation, 'width', $product->get_width() );
						$this->set_property($productVariation, 'height', $product->get_height() );
					}
					
					if (isset($options['association']['quantity'])) {
						
						$this->set_property($productVariation, 'manage_stock', true );
						
						$quantity = (int) sanitize_text_field($options['association']['quantity']);
						
						if ($quantity) {
							
							$this->set_property($productVariation, 'stock_quantity', $quantity );
							$this->set_property($productVariation, 'stock_status', 'instock' );
						}
					}
					
					if (!$variation_id) {
						$productVariation->set_attributes($varAttributes);
					}
					
					if (!$default_attributes) {
						
						$default_attributes = $varAttributes;
						
						$this->set_property($product, 'default_attributes', $default_attributes );
						
						$product->save();
					}
					
					$productVariation = apply_filters( get_class($this->initClass)::PREFIX . '_product_before_variation_save', $productVariation, $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
					
					$productVariation->save();
					
					if ($options['image'] && $images) {
						if (isset($images[$variation['image_index']])) {
							
							$imgs = $images[$variation['image_index']];
							
							$imgs = apply_filters( get_class($this->initClass)::PREFIX . '_product_cover_image', $imgs, 'variation' );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
							
							$imgs = $this->update_image_count($imgs);
							
							if ($this->configVars['background_processing']) {

								$this->initClass->cronClass->push_to_queue(
									array(
										'hook' => 'background_image_import',
										'type' => 'cover',
										'product_id' => $productVariation->get_id(),
										'image_urls' => array_shift($imgs)
									)
								);
								
								$this->initClass->cronClass->push_to_queue(
            						array(
            							'hook' => 'background_image_import',
            							'type' => 'gallery',
            							'product_id' => $productVariation->get_id(),
            							'image_urls' => $imgs
            						)
            					);
								
							} else {
								
								$this->add_images_to_product($productVariation, array_shift($imgs));
								
								$this->add_images_to_product($productVariation, $imgs, 'gallery');
							}
						}
					}
					
					if (isset($variation['id'])) {
						
						update_post_meta($productVariation->get_id(), '_ORIGIN_ID', $variation['id']);
					}
						
					update_post_meta($productVariation->get_id(), '_UPDATED_AT', gmdate('Y-m-d H:i:s'));
					
				}
			} catch (Exception $e) {
				// echo $e->getMessage();
				return false;
			}
		}		
	}
	
	protected function update_image_count( $images) {
		
		$max_image_count = (int) $this->configVars['max_image_count'];
			
		if ($max_image_count) {
			$images = array_slice($images, 0, $max_image_count);
		}
		
		return $images;
	}
	
	protected function get_variation_id_by_attributes( $product, $attributes) {

		foreach ($product->get_available_variations() as $variation) {
			$variation_object = wc_get_product($variation['variation_id']);

			if ($variation_object->get_attributes() === $attributes) {
				
				return $variation['variation_id'];
			}
		}

		return 0;
	}
	
	protected function addAffiliate( $product, $options) {
				
		if ($options['affiliate_link']) {
			$affiliateLink = urldecode($options['affiliate_link']);
			
			if (!filter_var($affiliateLink, FILTER_VALIDATE_URL)) {
				$affiliateParams = array();
				parse_str($affiliateLink, $affiliateParams);
				$parseProductUrl = explode('?', urldecode($options['product_link']));
				
				$urlParams = array();
				if (isset($parseProductUrl[1])) {
					parse_str($parseProductUrl[1], $urlParams);
				}
				
				foreach ($affiliateParams as $key => $value) {
					$urlParams[$key] = $value;
				}
				
				$affiliateLink = $parseProductUrl[0] . '?' . http_build_query($urlParams);
			}
			
			$this->set_property($product, 'product_url', $affiliateLink );
			
			if (!$options['id_product']) {
				
				$buttonText = $this->configVars['affiliate_button_text'];
				
				if (empty($buttonText)) {
					$buttonText = __('Buy now', 'ebay-product-importer');
				}
				
				$this->set_property($product, 'button_text', $buttonText );
			}
			
			$product->save();
		}
	}
	
	protected function displayResponse( $response) {
		header('Content-Type: application/json');
		die(json_encode($response));
	}
	
	public function enqueue_hls_video_script() {	
		
		if ( is_product() ) {
			
			$hls_video_player_js = 'assets/js/hls-video-player.js';
			//$hls_url = 'https://cdn.jsdelivr.net/npm/hls.js@latest';
			$hls_url = $this->initClass->get_plugin_dir_url() . 'assets/js/hls.js';

			wp_enqueue_script(
				'hls-video-player',
				$this->initClass->get_plugin_dir_url() . $hls_video_player_js,
				array(),
				filemtime($this->initClass->get_plugin_dir_path() . $hls_video_player_js),
				true
			);
			
			wp_localize_script(
				'hls-video-player',
				'nxtal_importer_params',
				array(
					'hls_url' => $hls_url
				)
			);
		}
	}
}
