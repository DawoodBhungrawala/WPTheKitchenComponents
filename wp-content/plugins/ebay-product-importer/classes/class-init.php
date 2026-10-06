<?php
/**
 * Init class to handle Admin and Front pages.
 *
 * @package: ebay-product-importer
 */

namespace Nxtal\EBayProductImporter\classes;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Init {
	
	public const PREFIX = 'ebay-product-importer';
	
	public const OPTION_KEY = self::PREFIX . '-setting';
	
	public const REST_ROUTE_SLUG = self::PREFIX;
	
	public const CRON_SLUG = self::PREFIX . '-product-update';
	
	public const CRON_VAR = self::CRON_SLUG;
	
	public const METABOX_KEY = '_nxtal_importer_synchronization';
	
	public const PLUGIN_LINK = 'https://woocommerce.com/products/ebay-product-importer/';
	
	public const PLUGIN_DOCS_LINK = 'https://woocommerce.com/document/ebay-product-importer/';
	
	public const CHROME_EXTENSION_LINK = 'https://chromewebstore.google.com/detail/advanced-importer/fnckhcfokjndphlmkpoihcjmcpghcofh';
	
	public $configParams = array(
	
		'secret_key' => '', // Random string
		'advance_option' => 1,
		'price_formula' => '',
		'max_image_count' => 0,
		'image_width' => '',
		'image_height' => '',
		'background_processing' => 0,
		'affiliate_id' => '',
		'affiliate_button_text' => '',
		'replace_texts' => '',
		'synchronization_schedule' => 0,
		'synchronization_price' => 0,
		'synchronization_stock' => 0,
		'synchronization_quantity' => 1000,
		'synchronization_unavailability' => 0
	);
	
	public $cronClass;
	
	public $wpdb;
	
	public function __construct() {
		
		global $wpdb;
	
		$this->wpdb = $wpdb;
		
		$this->includes();
		
		$this->hooks();
		
		$this->load_screen();
		
		$this->cronClass = new Cron($this);
	}
	
	protected function includes() {
		
		include($this->get_plugin_dir_path() . 'classes/wp-async-request.php');
		include($this->get_plugin_dir_path() . 'classes/wp-background-process.php');	
		include($this->get_plugin_dir_path() . 'classes/class-trait-importer.php');		
		include($this->get_plugin_dir_path() . 'classes/class-admin-page.php');
		include($this->get_plugin_dir_path() . 'classes/class-front-importer.php');		
		include($this->get_plugin_dir_path() . 'classes/class-cron.php');
	}
	
	protected function hooks() {
		
		register_activation_hook(		
			$this->get_plugin_base_path(),
			array(
				$this,
				'activation'
			)
		);
		
		register_deactivation_hook(
			$this->get_plugin_base_path(),
			array(
				$this,
				'deactivation'
			)
		);
		
		add_action(
			'before_woocommerce_init',
			array(
				$this,
				'woo_hpos_compatibility'
			)
		);
		
		add_action(
			'upgrader_process_complete',
			array(
				$this,
				'upgrader_process'
			),
			10,
			2
		);		
		
	}
	
	public function get_plugin_base_path ( $with_file = true) {
		
		$dir = dirname(dirname(__FILE__));
		
		if ($with_file) {
			$dir .= DIRECTORY_SEPARATOR . basename($dir) . '.php';			
		}
		
		return $dir;
	}
	
	public function get_plugin_basename () {
		
		return plugin_basename( $this->get_plugin_base_path() );
	}
	
	public function get_plugin_dir_path () {
		
		return plugin_dir_path( $this->get_plugin_base_path() );
	}
	
	public function get_plugin_dir_url () {
		
		return plugin_dir_url( $this->get_plugin_base_path() );
	}
	
	public function activation() {

		if (!$this->get_configuration(false)) {
			
			$this->configParams['secret_key'] = md5(wp_rand());
			$this->configParams['affiliate_button_text'] = __('Buy now', 'ebay-product-importer');
			
			$this->update_configuration($this->configParams);
		}
		
		$this->cronClass->add_cron_link();
		
		delete_option('rewrite_rules');
	}
	
	public function deactivation() {

		$this->cronClass->cancel_all_process();
		
		$this->delete_configuration();
		
		flush_rewrite_rules();
	}
	
	public function woo_hpos_compatibility() {
				
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->get_plugin_base_path(), true );
		}
	}
	
	public function upgrader_process( $upgrader_object, $options) {
				
		//
	}	
	
	protected function load_screen() {
		
		if (is_admin()) {
			new AdminPage($this);
		} else {
			new FrontImporter($this);
		}
	}
	
	public function get_configuration( $include_default = true, $key = '') {
		
		$configs = get_option(self::OPTION_KEY, array());
		
		if ($include_default) {
			
			$configs = wp_parse_args(
				$configs,
				$this->configParams
			);		
		}
		
		if ($key) {
			
			if (isset($configs[$key])) {
				$configs = $configs[$key];
			} else {
				$configs = '';
			}
		}
				
		return apply_filters( self::PREFIX . '_get_configuration', $configs, $key );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound	
	}
	
	public function update_configuration( $args) {
		
		$args = wp_parse_args(
			$args,
			$this->configParams
		);
		
		$args = apply_filters( self::PREFIX . '_update_configuration', $args );	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		
		return update_option( self::OPTION_KEY,	$args );		
	}
	
	public function delete_configuration() {
		
		return delete_option( self::OPTION_KEY );		
	}
	
	public function get_importer_hosts() {
		
		return array(
			
			'EbayParser' => array(
				'name' => '<b>ebay.com</b>, ebay.com.au, ebay.at, benl.ebay.be, befr.ebay.be, ebay.ca, ebay.cn, ebay.fr, ebay.de, ebay.ie, ebay.it, ebay.com.hk, ebay.com.my, ebay.nl, ebay.ph, ebay.pl, ebay.com.sg, ebay.es, ebay.ch, ebay.co.uk, ebay.vn',
				'parser' => 'EbayParser',
				'valid_url' => '/ebay\.(.+)/'
			)
		);
	}
				
	public function get_importer_hosts_name() {
		
		$names = array_column($this->get_importer_hosts(), 'name');
		
		$names = apply_filters( self::PREFIX . '_get_importer_hosts_name', $names );// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound	
		
		return implode(', ', $names);
	}
	
	public function review_attachment_css() {
		?>
		<style type="text/css" >
			.review_attachments {
				display: inline-block;
				width: 100%;
			}
			.review_attachments a img {
				height: 100px !important;
				width: 100px !important;
				float: left !important;
				padding: 0.5px;
				margin: 1px;
			}
		</style>
		<?php
	}
	
	public function log( $message, $filePath = null, $line = null ) {
	
		self::logStatic( $message, $filePath, $line );
	}
	
	public static function logStatic( $message, $filePath = null, $line = null ) {
		
		return; // Remove and call the function to log the process.
		
	/*	$log = gmdate('Y-m-d H:i:s') . "\t" . 
				print_r($message, true) . "\t" . 
				$filePath . "\t" . 
				$line . PHP_EOL;
		
		error_log($log, 3, dirname(__DIR__) . '/debug.txt');*/
	}
}

$initImporter = new \Nxtal\EBayProductImporter\classes\Init();// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
