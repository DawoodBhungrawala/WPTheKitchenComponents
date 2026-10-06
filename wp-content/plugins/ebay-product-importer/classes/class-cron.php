<?php
/**
 * Cron class to handle all the cron and background processes.
 *
 * @package: ebay-product-importer
 */
 
namespace Nxtal\EBayProductImporter\classes;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Cron extends WP_Background_Process {
	
	use TraitImporter;
	
	public $initClass;
	public $action = Init::PREFIX . '-cron-process';
	protected $parser;
	protected $batch_size = 50;

	public $configVars = array();
	
	public const QUEUED_KEY = 'nxtal-queued-products';
	
	public function __construct( $initClass) {	
	
		$this->initClass = $initClass;
		
		$this->configVars = $this->initClass->get_configuration();		
		
		parent::__construct();		
				
		$this->hooks();
	}
	
	protected function hooks() {
		
		add_action(
			'init',
			array(
				$this,
				'add_cron_link'
			)
		);
		
		add_filter(
			'query_vars',
			array(
				$this,
				'add_cron_query_vars'
			)
		);
		
		add_action(
			'template_redirect',
			array(
				$this,
				'update_scheduler_handler'
			)
		);
		
		add_action(
			'init',
			array(
				$this,
				'schedule_cron_task'
			)
		);
				
		add_action(
			get_class($this->initClass)::CRON_VAR,
			array(
				$this,
				'process_cron_queue'
			)
		);
		
		add_action(
			get_class($this->initClass)::PREFIX . '_cron_description_import',
			array(
				$this,
				'process_description_import'
			)
		);
		
		add_action(
			get_class($this->initClass)::PREFIX . '_cron_video_import',
			array(
				$this,
				'process_video_import'
			)
		);
		
		add_action(
			get_class($this->initClass)::PREFIX . '_cron_background_image_import',
			array(
				$this,
				'process_background_image_import'
			)
		);
		
		add_action(
			get_class($this->initClass)::PREFIX . '_cron_background_reviews_import',
			array(
				$this,
				'process_background_reviews_import'
			)
		);
		
		add_action(
			get_class($this->initClass)::PREFIX . '_cron_import_product_update',
			array(
				$this,
				'process_import_product_update'
			)
		);
	}
	
	public function task( $item ) {
		
		$this->initClass->log(array(__FUNCTION__, $item), __FILE__, __LINE__);

		if (isset( $item['hook'] ) && $item['hook']) {
			
			try {
				
				do_action( get_class($this->initClass)::PREFIX . '_cron_' . $this->slugify($item['hook']), $item );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			
			} catch (Exception $e) {
				
				$this->initClass->log(array(__FUNCTION__, $item, $e->getMessage()), __FILE__, __LINE__);
			}
		}
		
		return false;
	}
	
	public function save() {

		parent::save();
		
		if (in_array('import_product_update', wp_list_pluck($this->data, 'hook'))) {
		
			$queued = get_site_option(self::QUEUED_KEY, array());
			
			$product_ids = array_diff(wp_list_pluck($this->data, 'product_id'), $queued);
			
			if ($product_ids) {
				update_site_option(self::QUEUED_KEY, array_merge($queued, $product_ids) );
			}
		}
		
		$this->data = array();
		
		return $this;
	}
	
	public function complete() {
		
		delete_site_option(self::QUEUED_KEY);

		parent::complete();
	}
	
	public function delete_all_batches() {		

		$table  = $this->initClass->wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $this->initClass->wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $this->identifier . '_batch_%';

		$this->initClass->wpdb->query( $this->initClass->wpdb->prepare( "DELETE FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return $this;
	}
	
	public function cancel_all_process() {
		
		if ( ! $this->is_queue_empty() ) {
			$this->delete_all_batches();			
		}
		
		wp_clear_scheduled_hook( $this->cron_hook_identifier );
	}
	
	public function add_cron_link() {
		
		add_rewrite_rule('^' . get_class($this->initClass)::CRON_SLUG . '/?$', 'index.php?' . get_class($this->initClass)::CRON_VAR . '=1', 'top');
	}
	
	public function add_cron_query_vars( $vars) {
				
		$vars[] = get_class($this->initClass)::CRON_VAR;
		
		return $vars;
	}
	
	public function update_scheduler_handler() {

		if (get_query_var(get_class($this->initClass)::CRON_VAR)) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended 
			if ('external' === $this->configVars['synchronization_schedule'] || isset($_GET['synchronize_products'])) {
				
				$queues = $this->process_cron_queue();
				
				if ($queues) {
					/* translators: %d: queue count */
					$echo = sprintf(esc_html(__('%d products scheduled for update!', 'ebay-product-importer')), count($queues));
					
					$this->initClass->log(array(__FUNCTION__, $echo), __FILE__, __LINE__);
		
					echo esc_html($echo);
				} else {
					echo esc_html(__('No products to update!', 'ebay-product-importer'));
				}
			}

			die(' Done');
		}
	}
	
	public function schedule_cron_task() {
		
		if ($this->is_auto_update_enabled()
			&& 'external' !== $this->configVars['synchronization_schedule']
			&& !wp_next_scheduled( get_class($this->initClass)::CRON_VAR )
		) {
			
			$ve = get_option( 'gmt_offset' ) > 0 ? '-' : '+';
			
			wp_schedule_event(
				strtotime( '00:00 tomorrow ' . $ve . absint( get_option( 'gmt_offset' ) ) . ' HOURS' ),
				$this->configVars['synchronization_schedule'],
				get_class($this->initClass)::CRON_VAR
			);
			
		} else {
			
			$this->unschedule_cron_task();
		}
	}
	
	public function unschedule_cron_task() {
		
		wp_clear_scheduled_hook( get_class($this->initClass)::CRON_VAR );
	}
	
	public function process_cron_queue() {		
		
		if (!$this->is_auto_update_enabled()) {
			return false;
		}

		return $this->process_queue();		
	}
	
	public function process_description_import ( $params) {
				
		if (!isset($params['product_id']) || !$params['product_id'] || !isset($params['description']) || !$params['description']) {
			return false;
		}
		
		$product = wc_get_product( $params['product_id'] );
				
		if (!$product || !$product->get_id()) {
			return false;
		}
		
		$this->add_product_description($params['product_id'], $params['description']);
		
	}
	
	public function process_video_import ( $params) {
				
		if (!isset($params['product_id']) || !$params['product_id'] || !isset($params['videos']) || !$params['videos']) {
			return false;
		}
		
		$product = wc_get_product( $params['product_id'] );
				
		if (!$product || !$product->get_id()) {
			return false;
		}
		
		$this->add_product_videos($params['product_id'], $params['videos']);		
	}
	
	public function process_background_image_import ( $params) {
				
		if (!isset($params['product_id']) || !$params['product_id'] || !isset($params['image_urls']) || !$params['image_urls']) {
			return false;
		}
		
		$product = wc_get_product( $params['product_id'] );
				
		if (!$product || !$product->get_id()) {
			return false;
		}
		
		$this->add_images_to_product($params['product_id'], $params['image_urls'], $params['type']);
		
	}
	
	public function process_background_reviews_import ( $params) {
				
		if (!isset($params['product_id']) || !$params['product_id'] || !isset($params['reviews']) || !is_array($params['reviews']) || !$params['reviews']) {
			return false;
		}
		
		$product = wc_get_product( $params['product_id'] );
				
		if (!$product || !$product->get_id()) {
			return false;
		}
		
		$this->add_product_reviews($params['product_id'], $params['reviews']);
		
	}
	
	public function process_import_product_update( $params) {
		
		$this->initClass->log(array(__FUNCTION__, $params, 'params'), __FILE__, __LINE__);
		
		if (!isset($params['product_id']) || !$params['product_id']) {
			return false;
		}
		
		$this->process_update($params['product_id']);
		
		$queued = get_site_option(self::QUEUED_KEY, array());
		
		$diff_ids = array_diff($queued, array($params['product_id']));
		
		update_site_option(self::QUEUED_KEY, $diff_ids );
	}
	
	public function process_queue() {

		$excludes = array();
		$queued = array();

		$synchronizable_ids = array();

		$batch = array(
			'hook' => 'import_product_update',
			'product_id' => 0
		);

		while ($product_ids = $this->get_product_id_need_to_be_updated($excludes, $this->batch_size)) {
			
			$excludes = array_merge($excludes, $product_ids);
			
			$synchronizable_ids = array_merge($synchronizable_ids, $this->filter_synchronizable_products($product_ids));

			while (count($synchronizable_ids) >= $this->batch_size) {
				$ids = array_splice($synchronizable_ids, 0, $this->batch_size);

				foreach ($ids as $id) {
					
					$batch['product_id'] = $id;
					$this->push_to_queue($batch);
					
					$queued[] = $id;
				}
				
				$this->save()->dispatch();
			}
		}

		if (!empty($synchronizable_ids)) {
			foreach ($synchronizable_ids as $id) {
				$batch['product_id'] = $id;
				$this->push_to_queue($batch);
				$queued[] = $id;
			}

			$this->save()->dispatch();
		}

		return $queued;
	}
	
	public function filter_synchronizable_products($product_ids) {

		$placeholders = implode(',', array_fill(0, count($product_ids), '%d'));
		$query = "
			SELECT ID, post_parent
			FROM {$this->initClass->wpdb->posts}
			WHERE ID IN ($placeholders)";
		
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $this->initClass->wpdb->get_results($this->initClass->wpdb->prepare($query, ...$product_ids));

		$parents_map = array();
		foreach ($results as $row) {
			$parents_map[$row->ID] = $row->post_parent > 0 ? $row->post_parent : $row->ID;
		}

		$parent_ids = array_unique(array_values($parents_map));
		$placeholders = implode(',', array_fill(0, count($parent_ids), '%d'));
		$query = "
			SELECT post_id, meta_value 
			FROM {$this->initClass->wpdb->postmeta}
			WHERE meta_key = %s 
			AND post_id IN ($placeholders)";
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$meta_results = $this->initClass->wpdb->get_results($this->initClass->wpdb->prepare($query, get_class($this->initClass)::METABOX_KEY, ...$parent_ids));

		$status_map = array();
		foreach ($meta_results as $row) {
			$status_map[$row->post_id] = $row->meta_value;
		}

		foreach ($product_ids as $i => $product_id) {
			
			$parent_id = $parents_map[$product_id];
			
			if (!isset($status_map[$parent_id]) || !$status_map[$parent_id]) {
				unset($product_ids[$i]);
			}
		}
		
		$queued = get_site_option(self::QUEUED_KEY, array());
		
		$product_ids = array_diff($product_ids, $queued);
		
		return $product_ids;
	}
	
	public function process_update( $product_id ) {
		
		$this->initClass->log(array(__FUNCTION__, $product_id, 'product_id'), __FILE__, __LINE__);
		
		try {
			
			if ( $product_id && $this->is_auto_update_enabled() ) {
				
				$product = wc_get_product( $product_id );
				
				if ($product && $product->get_id()) {
				
					$origin_link = $this->get_product_origin_link($product_id);
										
					$origin_id 	 = get_post_meta($product_id, '_ORIGIN_ID', true);
														
					if (!$origin_id) {
						//$origin_id = $this->get_origin_id_from_url($origin_link);
					}
					
					$this->load_praser($origin_link);
						
					if ($this->parser && $origin_id) {					
						
						$prices = array();
						
						if (method_exists($this->parser, 'getPriceByAsin')) {
						
							$price_link  = $this->get_price_link($product_id);
							
							if ($price_link) {
							
								$price_link = str_replace('[ORIGIN_ID]', $origin_id, $price_link);
								
								$prices = $this->parser->getPriceByAsin($origin_id, $price_link);
							}
						}
						
						if (!$prices || !array_filter($prices)) {
							
							$this->parser->setContent($origin_link);
							
							if (method_exists($this->parser, 'getPriceFromContent')) {
								
								$prices = $this->parser->getPriceFromContent();
								
							} else {
								
								$combinations = $this->parser->getCombinations();
								
								if ($combinations) {
									
									foreach ($combinations as $combination) {
										
										if (isset($combination['id'])
											&& $combination['id'] == $origin_id
										) {
											$prices = array(
												'price' => $combination['price'],
												'regular_price' => $combination['regular_price'],
												'is_available_for_sale' => $combination['is_available_for_sale']
											);
											
											break;
										}
									}
								} else {
									
									$prices = array(
										'price' => $this->parser->getPrice(),
										'regular_price' => $this->parser->getRegularPrice(),
										'is_available_for_sale' => $this->parser->isAvailableForSale()
									);									
								}
							}		
							
						}
						
						$this->initClass->log(array(__FUNCTION__, $prices, 'prices'), __FILE__, __LINE__);
						
						$isUpdated = false;				
						$isRemoved = false;
						
						if ($this->configVars['synchronization_unavailability']) {
							
							if (isset($prices['is_available_for_sale'])
								&& !$prices['is_available_for_sale']
							) {
																
								if ('status' === $this->configVars['synchronization_unavailability']) {
									
									$this->set_property($product, 'status', 'private' );
									
									$isUpdated = true;
									
								} elseif ('delete' === $this->configVars['synchronization_unavailability']) {
									
									$product->delete();	
									
									$isRemoved = true;									
								}
								
								$this->update_parent_product($product, $this->configVars['synchronization_unavailability']);
							}
						}
						
						if (false === $isRemoved) {
							
							$sale_price = 0;
							$regular_price = 0;
						
							if ($this->configVars['synchronization_price']) {
								
								$price_formula = $this->configVars['price_formula'];
								
								if (isset($prices['price'])) {
									
									$sale_price = (float) $prices['price'];
									
									if ($price_formula) {
										
										$sale_price = (float) $this->calculate_price(max($sale_price, 0), array('association' => array('price' => $price_formula)));
									}
								}
								
								if (isset($prices['regular_price'])) {
									
									$regular_price = (float) $prices['regular_price'];
																		
									if ($price_formula) {
										
										$regular_price = (float) $this->calculate_price(max($regular_price, 0), array('association' => array('price' => $price_formula)));
									}
								}
								
								if ($sale_price >= $regular_price) {
									
									$regular_price = $sale_price;
									$sale_price = '';
								}
								
								if (!$regular_price) {
									$regular_price = '';
								}
								
								if ($sale_price || $regular_price) {
								
								$sale_price = $this->set_property($product, 'sale_price', $sale_price );
									
								$regular_price = $this->set_property($product, 'regular_price', $regular_price );
								
								$isUpdated = true;
								}
							}
							
							if ($this->configVars['synchronization_stock']) {
								
								$quantity = 0;
								
								if (isset($prices['is_available_for_sale']) && $prices['is_available_for_sale'] && ($sale_price || $regular_price)) {
									$quantity = (int) $this->configVars['synchronization_quantity'];
								}
								
								$stock_quantity = $this->set_property($product, 'stock_quantity', $quantity );
																
								if ($stock_quantity) {
									
									$this->set_property($product, 'status', 'publish' );
								}
								
								$isUpdated = true;
							}
							
							if (true === $isUpdated) {
							
								update_post_meta($product_id, '_UPDATED_AT', gmdate('Y-m-d H:i:s'));
								
								$product->save();
								
								wc_delete_product_transients($product_id);
								
								$this->update_parent_product($product, $this->configVars['synchronization_unavailability']);
								
								$this->initClass->log(array(__FUNCTION__, $product_id, 'Updated'), __FILE__, __LINE__);
							}							
						} else {
							
							$this->initClass->log(array(__FUNCTION__, $product_id, 'Deleted'), __FILE__, __LINE__);
						}
						
						return true;
					}

				}				
			}

		} catch ( \Exception $e ) {
			
			$this->initClass->log(array(__FUNCTION__, $e->getMessage()), __FILE__, __LINE__);
		}

		return false;
	}
	
	public function update_parent_product( $product, $status) {
		
		$parent_id = (int) $product->get_parent_id();
		
		if ($parent_id) {
			
			$product = wc_get_product($parent_id);
			
			if ($product) {
				
				if (!$product->get_available_variations()) {
					
					if ('status' === $status) {
										
						$this->set_property($product, 'status', 'private' );
						
						$product->save();
						
					} elseif ('delete' === $status) {
						
						$product->delete();								
					}
					
				} else {
					
					$this->set_property($product, 'status', 'publish' );
						
					$product->save();
				}					
			}		
		}
	}
	
	public function get_product_id_need_to_be_updated($excludes = array(), $limit = 100) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended 
		if (isset($_GET['synchronize_products'])) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended 
			$synchronize_products = explode(',', sanitize_text_field(wp_unslash($_GET['synchronize_products'])));
			
			$synchronize_products = array_filter( $synchronize_products, function( $id ) {
				return ! empty( trim( $id ) ) && is_numeric($id);
			});
			
			$synchronize_products = array_unique( $synchronize_products );
			
			if ($synchronize_products) {
				
				$synchronize_products = array_diff($synchronize_products, $excludes);
				
				return array_splice($synchronize_products, 0, (int) $limit);
			}			
		}
		
		$query = "
			SELECT pm.post_id 
			FROM {$this->initClass->wpdb->prefix}postmeta pm
			INNER JOIN {$this->initClass->wpdb->prefix}posts p 
				ON (pm.post_id = p.ID)
			WHERE p.post_type IN ('product', 'product_variation') 
			AND p.post_status IN ('publish', 'private') 
			AND pm.meta_key = '_UPDATED_AT' 
			AND pm.meta_value < DATE_SUB(NOW(), INTERVAL 1 DAY)";

		if (!empty($excludes)) {
			$placeholders = implode(',', array_fill(0, count($excludes), '%d'));
			$query .= " AND pm.post_id NOT IN ($placeholders)";
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$query = $this->initClass->wpdb->prepare($query, ...$excludes);
		}

		$query .= " LIMIT %d";
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$query = $this->initClass->wpdb->prepare($query, $limit);

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $this->initClass->wpdb->get_col($query);
	}
	
	public function get_product_origin_link( $product_id) {
		
		$product = wc_get_product($product_id);
		
		if ($product) {
			
			$parent_id = $product->get_parent_id();
			
			if (0 < $parent_id) {
				
				$product_id = $parent_id;
			}
			
			return get_post_meta($product_id, 'product_origin', true);		
		}
		
		return false;
	}
	
	public function get_price_link( $product_id) {
		
		$product = wc_get_product($product_id);
		
		if ($product) {
		
			$parent_id = $product->get_parent_id();
			
			if (0 < $parent_id) {
				
				$product_id = $parent_id;
			}
			
			$price_link  = get_post_meta($product_id, '_PRICE_LINK', true);
			
			return $price_link;
		}
		
		return false;
		
	}
	
	// Backward compatibility for the products which has already imported without new parameters.
	
	/*public function get_origin_id_from_url( $url) {
		
		$pattern = '/\/([A-Z0-9]{10})(\/|$|\?)/';
		
		if (preg_match($pattern, $url, $matches)) {
			
			return $matches[1];
		}
		
		return false;		
	}*/
	
	public function get_price_link_from_other_product( $product_id) {
		
		$product = wc_get_product($product_id);

		if ($product && $product->is_type( 'variation' )) {
			
			$find = 'twister';
			
		} else {
			
			$find = 'aodAjaxMain';
		}
		
		$table = $this->initClass->wpdb->prefix.'postmeta';
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $this->initClass->wpdb->get_var($this->initClass->wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT meta_value FROM $table WHERE meta_key = '_PRICE_LINK' AND meta_value LIKE %s",
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			'%' . $this->initClass->wpdb->esc_like($find) . '%'
		));
	}
	
	public function is_auto_update_enabled() {
		
		if (!$this->configVars['synchronization_schedule']) {
			return false;
		}
		
		if (!$this->configVars['synchronization_price']
			&& !$this->configVars['synchronization_stock']
			&& !$this->configVars['synchronization_unavailability']
		) {
			return false;
		}
		
		return true;
	}
}

