<?php
/**
 * Trait class file.
 *
 * @package: ebay-product-importer
 */
 
namespace Nxtal\EBayProductImporter\classes;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

trait TraitImporter {
	
	public function load_praser( $document ) {
		
		$url = '';
		$html = '';
		
		if (isset($document['url'])) {
			$url = $document['url'];
		} else {
			$url = $document;
		}
		
		if (isset($document['html'])) {
			$html = $document['html'];
		}
		
		if (empty($url)) {
			return false;
		}
		
		wp_raise_memory_limit('admin');
		// phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
		set_time_limit(0);
		
		$contentLoaded = false;
				
		foreach ($this->initClass->get_importer_hosts() as $host) {
			
			if (preg_match($host['valid_url'], $url)) {
				
				$parserClass = 'Nxtal\\ContentParser\\' . $host['parser'];
				
				if (method_exists($parserClass, 'setContent')) {
					$this->parser = new $parserClass();
				} else {
					$this->parser = new $parserClass(stripslashes($html), $url);
					$contentLoaded = true;
				}
				
				break;
			}
		}
		
		if ($contentLoaded) {
			
			return true;
			
		} elseif ($this->parser) {

			if ( !empty($html)) {
			
				$this->parser->setContent($url, stripslashes($html));
			}
			
			return true;
		}
		
		return false;
	}
	
	public function get_import_options() {
		
		$options = array(
			array(
				'name' => 'name',
				'label' => __('Name', 'ebay-product-importer'),
				'desc' => __('Required', 'ebay-product-importer')
			),
			array(
				'name' => 'sku',
				'label' => __('SKU', 'ebay-product-importer'),
				'desc' => __('Required', 'ebay-product-importer')
			),
			array(
				'name' => 'short_description',
				'label' => __('Short Description', 'ebay-product-importer'),
				'desc' => ''
			),
			array(
				'name' => 'description',
				'label' => __('Description', 'ebay-product-importer'),
				'desc' => ''
			),
			array(
				'name' => 'price',
				'label' => __('Price', 'ebay-product-importer'),
				'desc' => __('Required', 'ebay-product-importer')
			),
			array(
				'name' => 'weight',
				'label' => __('Weight', 'ebay-product-importer'),
				'desc' => ''
			),					
			array(
				'name' => 'dimension',
				'label' => __('Dimension', 'ebay-product-importer'),
				'desc' => ''
			),
			array(
				'name' => 'image',
				'label' => __('Image', 'ebay-product-importer'),
				'desc' => ''
			),
			array(
				'name' => 'video',
				'label' => __('Video', 'ebay-product-importer'),
				'desc' => ''
			),
			array(
				'name' => 'brand',
				'label' => __('Manufacturer', 'ebay-product-importer'),
				'desc' => __('New Manufacturer will be created if not exist.', 'ebay-product-importer')
			),
			array(
				'name' => 'category',
				'label' => __('Category', 'ebay-product-importer'),
				'desc' => __('New category will be created if not exist.', 'ebay-product-importer')
			),
			array(
				'name' => 'variant',
				'label' => __('Variation', 'ebay-product-importer'),
				'desc' => __('New Variation attribute will be created if not exist.', 'ebay-product-importer')
			),
			array(
				'name' => 'feature',
				'label' => __('Attributes', 'ebay-product-importer'),
				'desc' => __('New attribute will be created if not exist.','ebay-product-importer')
			),
			array(
				'name' => 'review',
				'label' => __('Customer Reviews', 'ebay-product-importer'),
				'desc' => ''
			)
		);
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_get_import_options', $options );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function get_categories() {
		
		$args = array(
			'taxonomy'   => 'product_cat',
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false
		);
		
		$args = apply_filters( get_class($this->initClass)::PREFIX . '_get_categories_args', $args );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
		$categories = array();
		
		foreach (get_terms($args) as $cat) {
			$categories[] = array(
				'term_id' => $cat->term_id,
				'name' => htmlspecialchars_decode($cat->name, ENT_QUOTES )
			);
		}
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_get_categories', $categories );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function get_tax_classes() {
		
		$taxClasses = array_map(
			function ( $tax) {
				return array(
					'slug' => $this->slugify($tax),
					'name' => $tax
				);
			},
			\WC_Tax::get_tax_classes()
		);
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_get_tax_classes', $taxClasses );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function get_shipping_classes() {
		
		$shipping = new \WC_Shipping();
		
		$shippingClasses = array_map(
			function ( $object) {
				return array(
					'term_id' => $object->term_id,
					'name' => $object->name
				);
			},
			$shipping->get_shipping_classes()
		);
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_get_shipping_classes', $shippingClasses );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function slugify( $str) {
				
		if ('' == $str) {
			return '';
		}
		
		$str = sanitize_title_with_dashes($str);
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_slugify', $str );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function calculate_price_expression( $expression, $variables = null) {
		// Price formula evaluation
				
		try {
			$evaluator = new \Symfony\Component\ExpressionLanguage\ExpressionLanguage();
			
			$evaluator->register('ceil', function ($value) {
				return sprintf('ceil(%s)', $value);
			}, function (array $values, $value) {
				return ceil($value);
			});

			$evaluator->register('floor', function ($value) {
				return sprintf('floor(%s)', $value);
			}, function (array $values, $value) {
				return floor($value);
			});

			$evaluator->register('round', function ($value) {
				return sprintf('round(%s)', $value);
			}, function (array $values, $value) {
				return round($value);
			});

			$evaluator->register('min', function ($a, $b) {
				return sprintf('min(%s, %s)', $a, $b);
			}, function (array $values, $a, $b) {
				return min($a, $b);
			});

			$evaluator->register('max', function ($a, $b) {
				return sprintf('max(%s, %s)', $a, $b);
			}, function (array $values, $a, $b) {
				return max($a, $b);
			});

			$evaluator->register('pow', function ($base, $exp) {
				return sprintf('pow(%s, %s)', $base, $exp);
			}, function (array $values, $base, $exp) {
				return pow($base, $exp);
			});

			$evaluator->register('abs', function ($value) {
				return sprintf('abs(%s)', $value);
			}, function (array $values, $value) {
				return abs($value);
			});

			$evaluator->register('log', function ($value) {
				return sprintf('log(%s)', $value);
			}, function (array $values, $value) {
				return log($value);
			});
			
			$evaluator->register('if', function ($condition, $trueValue, $falseValue) {
				return sprintf('(%s) ? (%s) : (%s)', $condition, $trueValue, $falseValue);
			}, function (array $values, $condition, $trueValue, $falseValue) {
				return $condition ? $trueValue : $falseValue;
			});
			
			return $evaluator->evaluate($expression, $variables);
		} catch (\Exception $e) {
			//print_R($e->getMessage());
			return false;
		}
	}
	
	public function calculate_price( $price, $options) {
		
		if (isset($options['association']['price']) && trim($options['association']['price'])) {
			
			$customPrice = trim($options['association']['price']);
			
			if ($customPrice) {			

				$price = (float) $this->calculate_price_expression(
					$customPrice,
					array('P' => $price)
				);
			}
		}
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_calculate_price', round($price, 2));// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function is_string( $text) {
	
		return (bool) preg_replace('/[0-9.]/', '', $text);
	}
	
	public function get_query_param( $key = null, $default = null) {
		
		if (null == $key) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $_REQUEST;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended 
		if (isset($_REQUEST[$key])) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return sanitize_text_field(wp_unslash($_REQUEST[$key]));
		}
		
		return $default;
	}
	
	public function clean_text( $text, $length = null, $wordWrap = true) {
		
		$text = str_replace(array('^','<','>','=','{','}', '#', ';', '【', '】'), '', $text);
		
		if ($length && mb_strlen($text) > $length) {
			
			if ($wordWrap) {
			
				$words = explode(' ', $text);
				
				$newText = '';
				
				foreach ($words as $word) {
					if (mb_strlen($newText . ' ' . $word) <= $length) {
						$newText = trim($newText) . ' ' . $word;
					} else {
						break;
					}
				}
			} else {
				$newText = mb_substr($text, 0, $length);
			}
			
			$text = $newText;
		}
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_clean_text', $text );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function trim( $text) {
		
		$text = preg_replace(array('/\s+/', '/<\s+/', '/\s+>/', '/>\s+</', '/&nbsp;/'), array(' ', '<', '>', '><', ''), $text);
		$text = preg_replace('/<!--([^-](?!(->)))*-->/', '', $text);
		
		$text = apply_filters( get_class($this->initClass)::PREFIX . '_trim', $text );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
		return trim($text);
	}
	
	public function generate_unique_id( $unique_id, $product_id = 0, $key = 'sku', $maxlength = 32 ) {
		
		$unique_id = $this->clean_text($unique_id, 32, false);
		
		$id_from_type = $this->get_product_id_by_unique_id( $unique_id, '_' . $key );
			
		if ($id_from_type && ( ( $product_id && $product_id != $id_from_type ) || !$product_id )) {
		
			return $this->generate_unique_id($this->change_unique_id($unique_id, $maxlength ), $product_id, $key, $maxlength);
		}
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_generate_unique_id', $unique_id, $key, $maxlength );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound	
	}
	
	// Get product ID by meta key even if the product has been moved to trash.
	public function get_product_id_by_unique_id( $unique_id, $key = '_sku' ) {
	
		$posts_table 	= $this->initClass->wpdb->posts;
		$postmeta_table = $this->initClass->wpdb->postmeta;
		return (int) $this->initClass->wpdb->get_var( 
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
			$this->initClass->wpdb->prepare("SELECT ID FROM $posts_table AS posts LEFT JOIN $postmeta_table AS meta ON posts.ID = meta.post_id WHERE meta.meta_key = %s AND meta.meta_value = %s AND posts.post_type IN ('product', 'product_variation')",
				$key, // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
				$unique_id // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
			)
		);
	}
	
	public function change_unique_id( $unique_id, $maxlength = 32 ) {
		
		$srting = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$randomChar = mb_substr(str_shuffle($srting), 0, 1);
		if (mb_strlen($unique_id) >= $maxlength) {
			$unique_id = $randomChar . $unique_id;
		} else {
			$unique_id .= $randomChar;
		}
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_change_unique_id', $unique_id, $maxlength );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function convert_weight( $value, $fromUnit, $toUnit) {
		
		$fromUnit = trim($fromUnit);
		$toUnit = trim($toUnit);
		
		if (false !== stripos($fromUnit, 'kilogram')) {
			$fromUnit = 'kg';
		} elseif (false !== stripos($fromUnit, 'gram')) {
			$fromUnit = 'g';
		}
		
		if (false !== stripos($fromUnit, 'pound')) {
			$fromUnit = 'lbs';
		}
		
		if (false !== stripos($fromUnit, 'ounce')) {
			$fromUnit = 'oz';
		}
		
		if (!$fromUnit || $fromUnit == $toUnit) {
			
			return apply_filters( get_class($this->initClass)::PREFIX . '_convert_weight', $value );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		}
		
		$conversionFactors = array(
			'kg' => array('g' => 1000, 'lbs' => 2.20462, 'oz' => 35.274),
			'g' => array('kg' => 0.001, 'lbs' => 0.00220462, 'oz' => 0.035274),
			'lbs' => array('kg' => 0.453592, 'g' => 453.592, 'oz' => 16),
			'oz' => array('kg' => 0.0283495, 'g' => 28.3495, 'lbs' => 0.0625)
		);
		
		$conversionFactors = apply_filters( get_class($this->initClass)::PREFIX . '_weight_unit', $conversionFactors );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound

		if (!isset($conversionFactors[$fromUnit]) || !isset($conversionFactors[$toUnit])) {
			
			return apply_filters( get_class($this->initClass)::PREFIX . '_convert_weight', $value );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		}

		$value *= $conversionFactors[$fromUnit][$toUnit];
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_convert_weight', $value );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function convert_dimension( $value, $fromUnit, $toUnit) {
		
		$fromUnit = trim($fromUnit);
		$toUnit = trim($toUnit);
		
		if (false !== stripos($fromUnit, 'millimeter')) {
			$fromUnit = 'mm';
		}
		
		if (false !== stripos($fromUnit, 'centimeter')) {
			$fromUnit = 'cm';
		}
		
		if (false !== stripos($fromUnit, 'meter')) {
			$fromUnit = 'm';
		}
		
		if (false !== stripos($fromUnit, 'inch')) {
			$fromUnit = 'in';
		}
		
		if (false !== stripos($fromUnit, 'yard')) {
			$fromUnit = 'yd';
		}
		
		if (!$fromUnit || $fromUnit == $toUnit) {
			
			return apply_filters( get_class($this->initClass)::PREFIX . '_convert_dimension', $value );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		}
		
		$conversionFactors = array(
			'm' => array('cm' => 100, 'mm' => 1000, 'in' => 39.3701, 'yd' => 1.09361),
			'cm' => array('m' => 0.01, 'mm' => 10, 'in' => 0.393701, 'yd' => 0.0109361),
			'mm' => array('m' => 0.001, 'cm' => 0.1, 'in' => 0.0393701, 'yd' => 0.00109361),
			'in' => array('m' => 0.0254, 'cm' => 2.54, 'mm' => 25.4, 'yd' => 0.0277778),
			'yd' => array('m' => 0.9144, 'cm' => 91.44, 'mm' => 914.4, 'in' => 36)
		);
		
		$conversionFactors = apply_filters( get_class($this->initClass)::PREFIX . '_dimension_unit', $conversionFactors );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound

		if (isset($conversionFactors[$fromUnit])
			&& isset($conversionFactors[$fromUnit][$toUnit])
		) {
			
			$value *= $conversionFactors[$fromUnit][$toUnit];
		}
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_convert_dimension', $value );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function replace_text( $text) {
		
		$replace_texts = $this->configVars['replace_texts'];
		
		if ($replace_texts) {
			
			$replace_texts = explode(',', $replace_texts);
			
			$replace_texts = array_map('trim', $replace_texts);
			
			foreach ($replace_texts as $replace_text) {
				$replace_text = explode(':', $replace_text);
				
				if (!isset($replace_text[0]) || !$replace_text[0]) {
					continue;
				}
				
				$find = $replace_text[0];
				
				if (isset($replace_text[1])) {
					$replace = $replace_text[1];
				} else {
					$replace = '';
				}
				
				$text = str_replace($find, $replace, $text);
			}		
		}
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_replace_text', $text );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound	
	}
	
	public function add_product_description( $product, $description) {
		
		if (is_int($product)) {
			$product = wc_get_product( (int) $product );
		}
		
		if (!$product || !$description) {
			
			return false;
		}
		
		$description = $this->purifyHTML(
			$this->replace_text(
				$this->replaceDescriptionImage(
					$description
				)
			)
		);
		
		$this->set_property($product, 'description', $description);
				
		$product->save();	
			
		$this->initClass->log(array(__FUNCTION__, $product->get_id(), 'Description updated'), __FILE__, __LINE__);
	}
	
	public function add_product_videos( $product, $videos) {
		
		if (is_int($product)) {
			$product = wc_get_product( (int) $product );
		}
		
		if (!$product || !$videos) {
			
			return false;
		}
		
		$videoEmbed = '';
				
		foreach ($videos as $video) {
			
			$poster = '';
			if (is_array($video)) {
				
				$poster = 'poster="' . $video['cover'] . '"';
				$video = $video['url'];
			}
			//if (false !== strpos($video, '.') && 4 > strpos(strrev($video), '.')) {
				//$videoEmbed .= '<center>[embed]' . $video . '[/embed]</center><br/>';
				$videoEmbed .= '<video '. $poster .' controls="controls" src="' . $video . '"></video><br/>';
			//}
		}
		
		if ($videoEmbed) {
	
			$videoEmbed .= $product->get_description();
			
			$this->set_property($product, 'description', $videoEmbed);
			
			$product->save();
			
			$this->initClass->log(array(__FUNCTION__, $product->get_id(), 'Description video updated'), __FILE__, __LINE__);
		}		
	}
	
	public function add_images_to_product( $product, $image_urls, $type = 'cover') {
				
		if (is_int($product)) {
			$product = wc_get_product( (int) $product );
		}
		
		if (!$product || !$image_urls) {
			
			return false;
		}
		
		if (!is_array($image_urls)) {
			$image_urls = array($image_urls);
		}
				
		$productImagesIDs = $this->add_images($image_urls);
						
		if ($productImagesIDs) {
			
			$productImagesIDs = apply_filters( get_class($this->initClass)::PREFIX . '_product_images_ids', $productImagesIDs, $product, $type );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			
			if (count($productImagesIDs) > 0) {
			
				if ('cover' == $type) {					
					
					$this->set_property($product, 'image_id', reset($productImagesIDs));			
				} else {
						
					$productImagesIDs = array_merge($product->get_gallery_image_ids(), $productImagesIDs);
					
					$productImagesIDs = array_diff($productImagesIDs, array($product->get_image_id()));
					
					$this->set_property($product, 'gallery_image_ids', $productImagesIDs );
				}
				
				$product->save();
				
				$this->initClass->log(array(__FUNCTION__, $product->get_id(), 'Image updated', $type, $image_urls), __FILE__, __LINE__);
			}
		}
	}
	
	public function add_images ( $image_urls, $resize = true) {
				
		if (!$image_urls) {
			return false;
		}
		
		static $images = array();
		
		$imageIds = array();
		
		if (!is_array($image_urls)) {
			$image_urls = array($image_urls);
		}
		
		foreach ($image_urls as $image_url) {
			
			$key = base64_encode($image_url);
			
			if (isset($images[$key])) {
				
				$imageIds[] = $images[$key];
			} else {	
			
				$attach_id = $this->import_image($image_url, $resize);
				
				if (is_int($attach_id)) {
					
					$images[$key] = $attach_id;
					$imageIds[] = $attach_id;
				}				
			}			
		}		
		
		return $imageIds;
	}
	
	public function import_image_fallback( $image_url ) {
		
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Download image to temp file
		$tmp = download_url( $image_url );

		if ( is_wp_error( $tmp ) ) {
			return 0;
		}

		// Prepare file array
		$file = [
			'name'     => basename( wp_parse_url( $image_url, PHP_URL_PATH ) ),
			'tmp_name' => $tmp,
		];

		// Insert into Media Library
		$attachment_id = media_handle_sideload( $file );

		if ( is_wp_error( $attachment_id ) ) {
			wp_delete_file( $tmp );
			return 0;
		}

		return $attachment_id;
	}
	
	public function import_image( $image_url, $resize) {
		
		$image_url = strpos($image_url, 'http') === false ? 'https:' . $image_url : $image_url;
		
		$image_url = apply_filters( get_class($this->initClass)::PREFIX . '_import_image_url', $image_url );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound

		if (!extension_loaded('gd')) {
			return $this->import_image_fallback($image_url);
		}

		$unique_name = uniqid() . bin2hex(random_bytes(20)) . '.jpg';
		
		$uploads = wp_upload_dir();
		
		$upload_path = $uploads['path'];
		$upload_url = $uploads['url'];
		
		$filename = wp_unique_filename($upload_path, $unique_name);	

		$file_path = $upload_path . '/' . $filename;
		$file_url = $upload_url . '/' . $filename;
		
		$success = @copy($image_url, $file_path);	
		
		if (!$success) {
			
			$response = wp_remote_get($image_url);

			if (is_wp_error($response) || stripos(wp_remote_retrieve_header( $response, 'content-type' ), 'image/' ) === false) {
				
				$this->initClass->log(array(__FUNCTION__, $image_url, $response), __FILE__, __LINE__);
				
				unset($response);
				
				return false;
			}

			$image_body = wp_remote_retrieve_body($response);
			
			if (!$image_body || !file_put_contents($file_path, $image_body)) {
				
				$this->initClass->log(array(__FUNCTION__, $image_url, 'Error saving the image.'), __FILE__, __LINE__);	

				unset($response);
				
				return false;
			}

			$success = true;
		}
		
		if ($success) {
			
			//if ($resize) {
				$this->generate_and_resize_image($file_path, $file_path, $resize);
			//}

			$filetype = wp_check_filetype($filename, null);

			$attachment = array(
				'guid' => $file_url,
				'post_mime_type' => $filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit',
			);

			$attach_id = wp_insert_attachment($attachment, $file_path);
			
			if (is_wp_error($attach_id)) {
				
				$this->initClass->log(array(__FUNCTION__, $attach_id), __FILE__, __LINE__);
				
				return false;
			}

			$this->update_image_postmeta ($attach_id, $file_path);

			return $attach_id;			
		}
		
		return 0;
	}
	
	public function generate_and_resize_image( $sourceFile, $destinationFile, $resize = false) {
		
		clearstatcache(true, $sourceFile);

		if (!file_exists($sourceFile) || !filesize($sourceFile)) {
			return false;
		}
		
		list($origWidth, $origHeight) = getimagesize($sourceFile);
		
		if (!$origWidth || !$origHeight) {
			return false;
		}

		$width = $origWidth;
		$height = $origHeight;		
		
		if ($resize) {		
			
			$newWidth = $this->configVars['image_width'];
			$newHeight = $this->configVars['image_height'];
			
			$newWidth = apply_filters( get_class($this->initClass)::PREFIX . '_resized_image_width', $newWidth );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
			$newHeight = apply_filters( get_class($this->initClass)::PREFIX . '_resized_image_height', $newHeight );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
			
			if ($newWidth || $newHeight) {
				
				if (!$newWidth) {
					$newWidth = ( $width / $height ) * $newHeight;				
				}
				
				if (!$newHeight) {
					$newHeight = ( $height / $width ) * $newWidth;				
				}

				if ($width > $newWidth) {
					$width = $newWidth;
					$height = ( $newWidth / $origWidth ) * $origHeight;
				}
				
				if ($height > $newHeight) {
					$height = $newHeight;
					$width = ( $newHeight / $origHeight ) * $origWidth;
				}		
			}
		}

		$imageResized = imagecreatetruecolor($width, $height);

		$imageOriginal = imagecreatefromstring(file_get_contents($sourceFile));

		imagecopyresampled($imageResized, $imageOriginal, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
		
		$success = imagejpeg($imageResized, $destinationFile, 100); 
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod
		@chmod($destinationFile, 0664);

		imagedestroy($imageResized);
		imagedestroy($imageOriginal);
		
		return $success;
	}
	
	public function update_image_postmeta ( $attach_id, $file_path) {
		
		if (!function_exists('wp_generate_attachment_metadata')) {
			include_once ABSPATH . 'wp-admin/includes/image.php';
		}
		
		wp_update_attachment_metadata(
			$attach_id,
			wp_generate_attachment_metadata($attach_id, $file_path)
		);
	}
	
	public function set_property( &$object, $key, $value) {
		
		$value = apply_filters( get_class($this->initClass)::PREFIX . '_product_' . $key, $value, $object );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			
		$object->{'set_' . $key}($value);
		
		return $value;
	}
	
	protected function getDomObject( $html ) {
		
		if (!$html) {
			return $html;
		}
		
		$dom = new \DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="utf-8" ?><nxtal>' . $html . '</nxtal>', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
		libxml_use_internal_errors(false);
		
		return $dom;
	}
	
	public function replaceDescriptionImage( $html) {

		if (!$html) {
			return $html;
		}
		
		$dom = $this->getDomObject($html);
		$images = $dom->getElementsByTagName('img');
		
		foreach ($images as $image) {
			
			$img = $image->getAttribute('data-src');
			
			if (!$img) {
				$img = $image->getAttribute('data-a-hires');
			}
			
			if (!$img) {
				$img = $image->getAttribute('src');
			}
			
			if ($img) {
				$ids = $this->add_images(array($img), false);

				if ($ids) {
					$img = wp_get_attachment_url(current($ids));
				}
			}
			
			if ($img) {
				$image->setAttribute('src', $img);
			}
			
			unset($img, $image);
		}
		
		unset($images);

		return $this->getRealContent($dom->saveHTML());
		
	}
	
	protected function purifyHTML( $html) {
		
		if (empty($html)) {
			return '';
		}
		
		$html = preg_replace('/<!--(.|\s)*?-->/i', '', $html);
		$html = preg_replace('/\s+/', ' ', $html);
		$html = preg_replace('/<video/', '<video controls', $html);
		$html = preg_replace('/javascript:/', '#', $html); // Remove javascript.
		$html = preg_replace('/max-height:/', 'a:', $html); // Remove max height attribute.
		$html = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '$1', $html); // Remove all links.
		
		$html = $this->removeElements(
			$html,
			array(
				'//div[contains(@class, "a-expander-header")]',
				'//div[contains(@class, "apm-tablemodule")]',
				'//div[contains(@class, "-comparison-table")]',
				'//div[contains(@class, "-carousel")]',
				'//*[@data-action="a-expander-toggle"]',
				'//a[@href="javascript:void(0)"]',
				'//div[@class="vjs-poster"]',
				'//div[@class="vjs-text-track-display"]',
				'//div[@class="vjs-loading-spinner"]',
				'//div[@class="vjs-control-bar"]',
				'//div[contains(@class, "vjs-hidden")]',
				'//iframe',
				'//script',
				'//style',
				'//form',
				'//object',
				'//embed',
				'//select',
				'//input',
				'//textarea',
				'//button',
				'//noscript',
			)
		);
		
		return trim($html);
	}
		
	protected function removeElements( $html, $selectors) {
		
		if (!$html) {
			return $html;
		}
		
		$dom = $this->getDomObject( $html );
		$xpath = new \DOMXPath($dom);
	
		foreach ($selectors as $selector) {
			
			$nodes = $xpath->query($selector);
			
			foreach ($nodes as $node) {
				$node->parentNode->removeChild($node);
			}
		}
		
		return $this->getRealContent($dom->saveHTML());
	}
	
	protected function getRealContent( $html) {
		
		if (preg_match('/<nxtal>(.*)<\/nxtal>/', $html, $matches)) {
			return $matches[1];
		}

		return $html;
	}
	
	protected function add_product_reviews( $product, $reviews) {
		
		if (is_int($product)) {
			$product = wc_get_product( (int) $product );
		}
		
		if (!$product || !$reviews) {
			
			return false;
		}
		
		$reviews = apply_filters( get_class($this->initClass)::PREFIX . '_product_reviews', $reviews, $product );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
				
		foreach ($reviews as $review) {
			$review = wp_slash($review);
					
			$review['comment_type'] = '';
			
			$review = apply_filters('preprocess_comment', $review);// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound	
			
			$attachment = '';
			
			if (isset($review['images']) && $review['images']) {
				foreach ($review['images'] as $imgUrl) {
					
					$ids = $this->add_images(array($imgUrl), false);

					if ($ids) {
						$imgUrl = wp_get_attachment_url(current($ids));
					}					
					
					$attachment .= '<a href="' . $imgUrl . '"><img src="' . $imgUrl . '" width="100" height="100"></a>';
				}
			}
						
			if (isset($review['videos']) && $review['videos']) {
				foreach ($review['videos'] as $videoUrl) {
					
					if (strpos($videoUrl, 'blob:') === false) {
					$attachment .= '<video width="320" height="240" controls><source src="' . $videoUrl . '"></video>';
					}
				}
			}
			
			if ($attachment) {
				$attachment = '<div class="review_attachments">' . $attachment . '</div>';
			}
			
			$commentdata = array();
			
			$commentdata['comment_post_ID'] = (int) $product->get_id();
			$commentdata['comment_date'] = gmdate('Y-m-d', strtotime($review['timestamp']));
			$commentdata['comment_date_gmt'] = gmdate('H:i:s', strtotime($review['timestamp']));
			$commentdata['comment_author'] = $this->replace_text($review['author']);
			$commentdata['comment_content'] = '<b>' . $this->replace_text($review['title']) . '</b><br/>' . $this->replace_text($review['content']) . $attachment;
			$commentdata['comment_author_IP'] = '';
			$commentdata['comment_author_url'] = '';
			$commentdata['comment_author_email'] = '';
			$commentdata['comment_parent'] = 0;
			$commentdata['comment_approved'] = 1;
			
			$rating = (int) $review['rating'];
			if (5 < $rating) {
				$rating /= 2;
			}
				
			$rating = intval($rating);
			if (0 < $rating) {
				$commentdata['comment_meta'] = array('rating' => $rating);
			}
			
			$commentdata = apply_filters( get_class($this->initClass)::PREFIX . '_product_review_data', $commentdata, $product );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound

			wp_insert_comment($commentdata);
		}
		
		$this->initClass->log(array(__FUNCTION__, $product->get_id(), 'Reviews updated'), __FILE__, __LINE__);
	}
	
	public function is_product_synchronizable( $product_id ) {
		
		static $status = array();
		
		$product = wc_get_product($product_id);
		
		if ($product) {
		
			$parent_id = $product->get_parent_id();
			
			if (0 < $parent_id) {
				
				$product_id = $parent_id;
			}
		}		
		
		if (!isset($status[$product_id])) {
			
			$status[$product_id] = get_post_meta( $product_id, get_class($this->initClass)::METABOX_KEY, true );
		}
		
		return $status[$product_id];
	}
	
	public function update_product_synchronizable_status( $product_id, $value ) {
		
		return update_post_meta( $product_id, get_class($this->initClass)::METABOX_KEY, $value );	
	}
	
	public function iconv( $input_string) {
		
		$converted = iconv('UTF-8', 'UTF-8//IGNORE', $input_string);
		
		if (false === $converted) {
			
			$converted = mb_convert_encoding($input_string, 'UTF-8', 'UTF-8');
		}
		
		if (false === $converted) {
			
			$converted = $input_string;
		}
		
		return $converted;
	}
}
