<?php
/**
 * Admin class to manage configurations
 *
 * @package: ebay-product-importer
 */

namespace Nxtal\EBayProductImporter\classes;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class AdminPage {
	use TraitImporter;
	protected $initClass; 
	public const CONFIG_PAGE_SLUG = Init::PREFIX;
	public const SYNC_NOW_KEY = Init::PREFIX . '-sync-now';
	public const CONFIG_SAVE_ACTION = self::CONFIG_PAGE_SLUG . '-save-configuration';
	
	public function __construct( $initClass) {
		
		$this->initClass = $initClass;
		
		$this->hooks();
	}
	
	protected function hooks() {
		
		add_action(
			'admin_menu',
			array(
				$this,
				'add_configuration_menu'
			)
		);
		
		add_filter(
			'plugin_action_links_' . $this->initClass->get_plugin_basename(),
			array(
				$this,
				'get_plugin_action_links'
			)
		);
		
		add_filter(
			'plugin_row_meta',
			array(
				$this,
				'plugin_row_meta'
			),
			10,
			2
		);

		add_action(
			'admin_post_' . self::CONFIG_SAVE_ACTION,
			array(
				$this,
				'save_configuration'
			)
		);
		
		add_filter(
			'manage_edit-product_columns',
			array(
				$this,
				'add_origin_column_name'
			),
			10,
			1
		);
		
		add_action(
			'manage_product_posts_custom_column',
			array(
				$this,
				'add_origin_column_content'
			),
			10,
			2
		);
		
		add_action(
			'admin_head',
			array(
				$this,
				'add_origin_column_css'
			)
		);	
		
		add_action(
			'admin_enqueue_scripts',
			array( 
				$this,
				'admin_enqueue_scripts'
			)
		);
		
		add_action(
			'add_meta_boxes',
			array( 
				$this,
				'add_meta_boxes'
			)
		);
		
		add_action( 
			'save_post',
			array(
				$this,
				'save_metabox_data'
			)
		);
		
		add_filter(
			'post_row_actions',
			array(
				$this,
				'product_row_actions'
			),
			10,
			2
		);
		
		add_action( 
			'admin_init',
			array(
				$this,
				'sync_product'
			)
		);
		
		add_action(
			'admin_notices',
			array(
				$this,
				'admin_notices'
			)
		);
	}
	
	public function add_configuration_menu() {
		
		add_submenu_page(
			'woocommerce',
			__('eBay Importer', 'ebay-product-importer'),
			__('eBay Importer', 'ebay-product-importer'),
			'manage_options',
			self::CONFIG_PAGE_SLUG,
			array(
				$this,
				'get_configuration_html'
			)
		);
	}
	
	public function get_configuration_html() {
		
		ob_start();
		
		include(
			$this->initClass->get_plugin_base_path(false) . '/templates/template-admin-configuration.php'
		);
		
		$html = ob_get_clean();
		
		echo apply_filters( get_class($this->initClass)::PREFIX . '_get_configuration_html', $html );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function get_plugin_action_links( $links ) {
			
		$pluginLinks = array();
		
		$configuration    = admin_url('admin.php?page=' . self::CONFIG_PAGE_SLUG);
		
		$pluginLinks[] = '<a href="' . esc_url($configuration) . '">' . esc_html__('Configuration', 'ebay-product-importer') . '</a>';
		
		$links = array_merge($pluginLinks, $links);
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_get_plugin_action_links', $links );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function plugin_row_meta( $links, $file ) {
				
		if ( $this->initClass->get_plugin_basename() == $file ) {
			
			$links['docs'] = '<a href="' . get_class($this->initClass)::PLUGIN_DOCS_LINK . '" target="_blank" aria-label="' . esc_attr__( 'View documentation', 'ebay-product-importer' ) . '">' . esc_html__( 'Docs', 'ebay-product-importer' ) . '</a>';
		}
		
		return $links;
	}
	
	public function save_configuration() {
		
		check_admin_referer('nxtal_importer_fields_verify');
		
		if (!current_user_can('manage_options')) {
			wp_die(
				esc_html(__('You are not authorized to edit this configuration.', 'ebay-product-importer'))
			);
		}
		
		$secretKey = '';
		if (isset($_POST['secret_key'])) {
			$secretKey = sanitize_text_field(wp_unslash($_POST['secret_key']));
		}
		if (isset($_POST['advance_option'])) {
			$advanceOption = 1;
		} else {
			$advanceOption = 0;
		}
		
		$price_formula = '';
		if (isset($_POST['price_formula'])) {
			$price_formula = sanitize_text_field(wp_unslash($_POST['price_formula']));
		}
		
		$max_image_count = '';
		if (isset($_POST['max_image_count'])) {
			$max_image_count = (int) sanitize_text_field(wp_unslash($_POST['max_image_count']));
		}
		
		$image_width = '';
		if (isset($_POST['image_width'])) {
			$image_width = sanitize_text_field(wp_unslash($_POST['image_width']));
		}
		
		$image_height = '';
		if (isset($_POST['image_height'])) {
			$image_height = sanitize_text_field(wp_unslash($_POST['image_height']));
		}
		
		if (isset($_POST['background_processing'])) {
			$background_processing = 1;
		} else {
			$background_processing = 0;
		}
		
		$affiliateId = '';
		if (isset($_POST['affiliate_id'])) {
			$affiliateId = sanitize_text_field(wp_unslash($_POST['affiliate_id']));
		}
		
		$affiliate_button_text = '';
		if (isset($_POST['affiliate_button_text'])) {
			$affiliate_button_text = sanitize_text_field(wp_unslash($_POST['affiliate_button_text']));
		}
		
		$replace_texts = '';
		if (isset($_POST['replace_texts'])) {
			$replace_texts = sanitize_text_field(wp_unslash($_POST['replace_texts']));
		}
		
		$synchronization_schedule = 0;
		if (isset($_POST['synchronization_schedule'])) {
			$synchronization_schedule = sanitize_text_field(wp_unslash($_POST['synchronization_schedule']));
		}
		
		if (isset($_POST['synchronization_price'])) {
			$synchronization_price = 1;
		} else {
			$synchronization_price = 0;
		}
		
		if (isset($_POST['synchronization_stock'])) {
			$synchronization_stock = 1;
		} else {
			$synchronization_stock = 0;
		}
		
		$synchronization_quantity = 1000;
		if (isset($_POST['synchronization_quantity'])) {
			$synchronization_quantity = (int) sanitize_text_field(wp_unslash($_POST['synchronization_quantity']));
			;
		}
		
		$synchronization_unavailability = 0;
		if (isset($_POST['synchronization_unavailability'])) {
			$synchronization_unavailability = sanitize_text_field(wp_unslash($_POST['synchronization_unavailability']));
		}
		
		$messageAttribute = 'update';
		
		if (empty($secretKey)
			|| strlen($secretKey) < 8
		) {
			$messageAttribute = 'error';
			
		} else {
			
			$this->initClass->update_configuration(
				
				array(
					'secret_key' => $secretKey,
					'advance_option' => $advanceOption,
					'price_formula' => $price_formula,
					'max_image_count' => $max_image_count,
					'image_width' => $image_width,
					'image_height' => $image_height,
					'background_processing' => $background_processing,
					'affiliate_id' => $affiliateId,
					'affiliate_button_text' => $affiliate_button_text,
					'replace_texts' => $replace_texts,
					'synchronization_schedule' => $synchronization_schedule,
					'synchronization_price' => $synchronization_price,
					'synchronization_stock' => $synchronization_stock,
					'synchronization_quantity' => $synchronization_quantity,
					'synchronization_unavailability' => $synchronization_unavailability
				)
			);
			
			if (!$synchronization_schedule) {
				
				$this->initClass->cronClass->unschedule_cron_task();
			}
		}
		
		$redirect_link = get_admin_url() . 'admin.php?page=' . self::CONFIG_PAGE_SLUG . '&' . $messageAttribute;
		
		wp_safe_redirect($redirect_link);
	}
	
	public function add_origin_column_name( $columns) {
		
		$columns['origin'] = __( 'Origin', 'ebay-product-importer' );
		
		return apply_filters( get_class($this->initClass)::PREFIX . '_add_origin_column_name', $columns );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
	
	public function add_origin_column_content( $column, $postid ) {
		
		if ('origin' == $column ) {
			
			$link = get_post_meta($postid, 'product_origin', true);

			if ($link) {
				echo '<a href="' . esc_url_raw($link) . '" target="_blank">' . esc_html($this->get_host_from_url($link)) . '</a> <br>';
			} else {
				echo '-';
			}
		}
	}
	
	public function get_host_from_url( $url ) {
		
		return wp_parse_url($url, PHP_URL_HOST);
	}
	
	public function add_origin_column_css() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset($_GET['post_type']) && 'product' == sanitize_text_field(wp_unslash($_GET['post_type']))) {
			?>
			<style type="text/css" >
				th#origin { width: 12%; }
			</style>
			<?php
			
		}
		
		global $post;
		
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, 
		if (( isset($_GET['post_type']) && 'product' == sanitize_text_field(wp_unslash($_GET['post_type'] ))) || ( isset($post->post_type) && 'product' == $post->post_type )
		) {
			$this->initClass->review_attachment_css();
		}
	}
	
	public function admin_enqueue_scripts() {
		
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended 
		if (isset($_GET['page']) && self::CONFIG_PAGE_SLUG === sanitize_text_field(wp_unslash($_GET['page']))) {
		
			$backend_css = 'assets/css/backend.css';

			wp_enqueue_style(
				'ebay-product-importer-admin-backend_css',
				$this->initClass->get_plugin_dir_url() . $backend_css,
				array(),
				filemtime($this->initClass->get_plugin_dir_path() . $backend_css),
			);

			$backend_js = 'assets/js/backend.js';
			
			wp_enqueue_script(
				'ebay-product-importer-admin-backend_js',
				$this->initClass->get_plugin_dir_url() . $backend_js,
				array(
					'jquery'
				),
				filemtime($this->initClass->get_plugin_dir_path() . $backend_js),
				'all'
			);
		}				
	}
	
	public function add_meta_boxes() {
		
		global $post;
		
		if (is_object($post)) {
			
			$is_synchronizable_enabled = $this->is_product_synchronizable($post->ID);
					
			if (!$this->initClass->cronClass->is_auto_update_enabled()
				|| '' === $is_synchronizable_enabled
			) {
				return false;			
			}
			
			add_meta_box(
				'nxtal_importer_synchronization',
				__( 'Synchronization', 'ebay-product-importer' ),
				array(
					$this,
					'synchronization_meta_boxes'
				),
				'product',
				'side',
				'default',
				array(
					'is_synchronizable_enabled' => $is_synchronizable_enabled
				)
			);
		}
	}
	
	public function synchronization_meta_boxes( $post, $metabox  ) {
		
		$is_synchronizable_enabled = 0;
		
		if (isset($metabox['args']['is_synchronizable_enabled'])) {
			
			$is_synchronizable_enabled = (int) $metabox['args']['is_synchronizable_enabled'];
		}
		
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is safely handled by prior sanitization

		echo '<div class="nxtal-importer-synchronization">
				<p class="form-row form-row-full form-field">
					<label for="nxtal_importer_synchronization">' . esc_html(__( 'Enable synchronization if you want to update product information automatically.', 'ebay-product-importer' )) . '</label>
					
					<select id="nxtal_importer_synchronization" name="' . esc_attr(get_class($this->initClass)::METABOX_KEY ). '" class="select short" style="margin: 8px 0px;">	
					
						<option value="0">' . esc_html(__( 'Disabled', 'ebay-product-importer' )) . '</option>
						<option value="1" ' . ( $is_synchronizable_enabled ? 'selected="selected"' : '' ) . '>' . esc_html(__( 'Enabled', 'ebay-product-importer' )) . '</option>
					
					</select>
				</p>
			</div>';
			// phpcs:enable
	}
	
	public function save_metabox_data( $post_id) {
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST[get_class($this->initClass)::METABOX_KEY] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$boxValue = (int) $_POST[get_class($this->initClass)::METABOX_KEY];
		
		$this->update_product_synchronizable_status( $post_id, $boxValue );	
	}
	
	public function product_row_actions($actions, $post) {
		
		if ( 'product' === $post->post_type) {
			
			$is_synchronizable_enabled = $this->is_product_synchronizable($post->ID);
					
			if ($this->initClass->cronClass->is_auto_update_enabled()
				&& '' !== $is_synchronizable_enabled
			) {
				
				$sync_url = add_query_arg(self::SYNC_NOW_KEY, $post->ID, admin_url('edit.php?post_type=product'));

				$actions['nxt_sync_action'] = '<a href="' . esc_url($sync_url) . '">'. esc_html(__( 'Sync now', 'ebay-product-importer' )) .'</a>';
			}
		}
		
		return $actions;
	}
		
	public function sync_product() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (isset($_GET[self::SYNC_NOW_KEY]) && sanitize_text_field(wp_unslash($_GET[self::SYNC_NOW_KEY]))) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product_id = (int)$_GET[self::SYNC_NOW_KEY];
			
			$product = wc_get_product($product_id);
			
			$status = false;
		
			if ($product) {
			
				if ($product->is_type( 'variable' )) {
						
					$product_ids = $product->get_children();
					
				} else {
					
					$product_ids = array($product_id);
				}
				
				$batch = array(
					'hook' => 'import_product_update',
					'product_id' => 0
				);
			
				foreach($product_ids as $product_id) {
					
					$batch['product_id'] = $product_id;
					$this->initClass->cronClass->push_to_queue($batch);					
				}
				
				$status = $this->initClass->cronClass->save()->dispatch();
			}
						
			wp_safe_redirect(add_query_arg('sync-status', (bool)$status, wp_get_referer()));
			
			exit;
		}		
	}
	
	public function admin_notices() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (isset($_GET['sync-status']) && $this->initClass->cronClass->is_auto_update_enabled()) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if (sanitize_text_field(wp_unslash($_GET['sync-status']))) {
				
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(__( 'The product is scheduled for update.', 'ebay-product-importer' )) . '</p></div>';
				
			} else {
				
				echo '<div class="notice notice-error is-dismissible"><p>' . esc_html(__( 'Product synchronization was failed!', 'ebay-product-importer' )) . '</p></div>';
				
			}
		}
	}
	
	public function get_php_incompatibilities() {
		
		$incompatibilities = array();
		
		if (!extension_loaded('gd')) {
			
			$incompatibilities[] = array(
				
				'name' => 'GD',
				'current' => false,
				'recommended' => true
			);
		}
		
		if (!extension_loaded('curl')) {
			
			$incompatibilities[] = array(
				
				'name' => 'cURL',
				'current' => false,
				'recommended' => true
			);
		}
		
		if (!extension_loaded('iconv')) {
			
			$incompatibilities[] = array(
				
				'name' => 'iconv',
				'current' => false,
				'recommended' => true
			);
		}
		
		if (!extension_loaded('mbstring')) {
			
			$incompatibilities[] = array(
				
				'name' => 'mbstring',
				'current' => false,
				'recommended' => true
			);
		}
		
		$max_execution_time = ini_get('max_execution_time');
		
		if ($max_execution_time && 320 > $max_execution_time) {
			
			$incompatibilities[] = array(
				
				'name' => 'max_execution_time',
				'current' => $max_execution_time,
				'recommended' => '> 320'
			);
		}
		
		$memory_limit = ini_get('memory_limit');
		
		if ($memory_limit && 536870912 > $this->convertToBytes($memory_limit)) {
			
			$incompatibilities[] = array(
				
				'name' => 'memory_limit',
				'current' => $memory_limit,
				'recommended' => '> 512M'
			);
		}
		
		$post_max_size = ini_get('post_max_size');
		
		if ($post_max_size && 272629760 > $this->convertToBytes($post_max_size)) {
			
			$incompatibilities[] = array(
				
				'name' => 'post_max_size',
				'current' => $post_max_size,
				'recommended' => '> 260M'
			);
		}
		
		return $incompatibilities;
		
	}
	
	public function convertToBytes( $value) {
		
		$unit = strtolower(substr($value, -1));
		$number = (int) $value;
		
		switch ($unit) {
			case 'g':
				return $number * 1024 * 1024 * 1024;
			case 'm':
				return $number * 1024 * 1024;
			case 'k':
				return $number * 1024;
			default:
				return $number;
		}
	}
}
