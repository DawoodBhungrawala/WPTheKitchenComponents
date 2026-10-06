<?php

$files = glob(get_template_directory() . '/framework/*.php');
foreach ($files as $file) {
	require_once($file);
}

/**
 * Registers two menus
 */
register_nav_menus([
	'top-header' => 'Top Header menu',
	'header' => 'Header menu',
	'footer' => 'Footer',
	'footer-quick-links' => 'Footer Quick Links',
	'products' => 'Products',
]);

add_theme_support('post-thumbnails');

function enqueue_styles()
{
	// Get the path to your CSS file
	$css_file = get_template_directory() . '/css/app.css';

	// Enqueue the style with file modification time as version
	wp_enqueue_style('custom-style', get_template_directory_uri() . '/css/app.css', array(), filemtime($css_file));
}

add_action('wp_enqueue_scripts', 'enqueue_styles');


function theme_enqueue_scripts()
{
	// Enqueue custom JavaScript file
	wp_enqueue_script('custom-script', get_template_directory_uri() . '/js/app-dist.js', array(), '1.0', true);
	$hero_carousel_file = get_template_directory() . '/js/hero-carousel.js';
	wp_enqueue_script('hero-carousel', get_template_directory_uri() . '/js/hero-carousel.js', array(), filemtime($hero_carousel_file), true);
	$category_showroom_file = get_template_directory() . '/js/category-showroom.js';
	wp_enqueue_script('category-showroom', get_template_directory_uri() . '/js/category-showroom.js', array(), filemtime($category_showroom_file), true);
}

add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');


add_theme_support('woocommerce');

add_filter('loop_shop_per_page', function ($cols) {
	return 9;
}, 20);





// Show "New Release" badge on products with the "new-release" tag
add_action('woocommerce_before_shop_loop_item_title', 'custom_new_release_badge', 10);
add_action('woocommerce_before_single_product_summary', 'custom_new_release_badge', 10);

function custom_new_release_badge()
{
	global $product;

	if (has_term('new-products', 'product_tag', $product->get_id())) {
		echo '<span class="new-release-badge">New Release</span>';
	}
}


function custom_nav_menu_active_class($classes, $item)
{
	// Check if we are on a product category archive page
	if (is_product_category()) {
		$current_term = get_queried_object();
		$current_term_url = get_term_link($current_term);

		// Compare the menu item URL with the current category URL
		if ($item->url == $current_term_url) {
			$classes[] = 'current-menu-item-primary-color';
		}
	}
	return $classes;
}

add_filter('nav_menu_css_class', 'custom_nav_menu_active_class', 10, 2);

/**
 * Website Reports system
 * Private report portal for logged-in website users.
 */
function tkc_register_reports_cpt() {
    $labels = array(
        'name'               => 'Website Reports',
        'singular_name'      => 'Website Report',
        'menu_name'          => 'Website Reports',
        'add_new'            => 'Add Report',
        'add_new_item'       => 'Add Website Report',
        'edit_item'          => 'Edit Website Report',
        'new_item'           => 'New Website Report',
        'view_item'          => 'View Website Report',
        'search_items'       => 'Search Website Reports',
        'not_found'          => 'No website reports found',
        'all_items'          => 'All Reports',
    );

    register_post_type('tkc_report', array(
        'labels'               => $labels,
        'public'               => true,
        'publicly_queryable'   => true,
        'exclude_from_search'  => true,
        'show_ui'              => true,
        'show_in_menu'         => true,
        'show_in_nav_menus'    => false,
        'menu_icon'            => 'dashicons-media-document',
        'supports'             => array('title', 'editor', 'thumbnail', 'comments'),
        'has_archive'          => false,
        'rewrite'              => array('slug' => 'website-report', 'with_front' => false),
        'query_var'            => true,
        'show_in_rest'         => false,
    ));
}
add_action('init', 'tkc_register_reports_cpt');

/**
 * Keep Website Report permalinks healthy after theme updates.
 * This flush runs once per reports routing version, not on every request.
 */
function tkc_reports_refresh_rewrite_rules() {
    $routing_version = '2.0.1';
    if (get_option('tkc_reports_routing_version') !== $routing_version) {
        flush_rewrite_rules(false);
        update_option('tkc_reports_routing_version', $routing_version, false);
    }
}
add_action('init', 'tkc_reports_refresh_rewrite_rules', 99);

/**
 * Fallback router for /website-report/{report-slug}/.
 * This keeps existing report URLs working even if a host delays rewrite updates.
 */
function tkc_reports_parse_request_fallback($wp) {
    if (empty($wp->request)) {
        return;
    }

    $request_path = trim($wp->request, '/');
    if (!preg_match('#^website-report/([^/]+)/?$#', $request_path, $matches)) {
        return;
    }

    $report_slug = sanitize_title($matches[1]);
    $report = get_page_by_path($report_slug, OBJECT, 'tkc_report');
    if (!$report || $report->post_status !== 'publish') {
        return;
    }

    $wp->query_vars = array(
        'post_type' => 'tkc_report',
        'name'      => $report->post_name,
    );
}
add_action('parse_request', 'tkc_reports_parse_request_fallback', 1);

function tkc_report_meta_boxes() {
    add_meta_box('tkc_report_details', 'Report Details', 'tkc_report_details_box', 'tkc_report', 'normal', 'high');
    add_meta_box('tkc_report_metrics', 'Report Metrics', 'tkc_report_metrics_box', 'tkc_report', 'side', 'default');
}
add_action('add_meta_boxes', 'tkc_report_meta_boxes');

function tkc_report_details_box($post) {
    wp_nonce_field('tkc_save_report', 'tkc_report_nonce');
    $fields = array(
        'project' => get_post_meta($post->ID, '_tkc_report_project', true),
        'scope' => get_post_meta($post->ID, '_tkc_report_scope', true),
        'prepared_by' => get_post_meta($post->ID, '_tkc_report_prepared_by', true),
        'summary' => get_post_meta($post->ID, '_tkc_report_summary', true),
        'risks' => get_post_meta($post->ID, '_tkc_report_risks', true),
        'recommendations' => get_post_meta($post->ID, '_tkc_report_recommendations', true),
        'evidence_urls' => get_post_meta($post->ID, '_tkc_report_evidence_urls', true),
    );
    $status = get_post_meta($post->ID, '_tkc_report_status', true) ?: 'completed';
    ?>
    <style>
      .tkc-report-field{margin:0 0 18px}.tkc-report-field label{display:block;font-weight:600;margin-bottom:6px}.tkc-report-field input,.tkc-report-field select,.tkc-report-field textarea{width:100%;max-width:900px}.tkc-report-field textarea{min-height:90px}.tkc-report-help{color:#666;font-size:12px}
    </style>
    <div class="tkc-report-field"><label for="tkc_report_project">Project / Website</label><input id="tkc_report_project" name="tkc_report_project" type="text" value="<?php echo esc_attr($fields['project']); ?>" placeholder="The Kitchen Components"></div>
    <div class="tkc-report-field"><label for="tkc_report_scope">Scope</label><input id="tkc_report_scope" name="tkc_report_scope" type="text" value="<?php echo esc_attr($fields['scope']); ?>" placeholder="Homepage, performance, WooCommerce, SEO..."></div>
    <div class="tkc-report-field"><label for="tkc_report_prepared_by">Prepared By</label><input id="tkc_report_prepared_by" name="tkc_report_prepared_by" type="text" value="<?php echo esc_attr($fields['prepared_by']); ?>" placeholder="Name or company"></div>
    <div class="tkc-report-field"><label for="tkc_report_status">Status</label><select id="tkc_report_status" name="tkc_report_status">
      <option value="completed" <?php selected($status,'completed'); ?>>Completed / Live</option>
      <option value="in-progress" <?php selected($status,'in-progress'); ?>>In Progress</option>
      <option value="review" <?php selected($status,'review'); ?>>Review Required</option>
      <option value="attention" <?php selected($status,'attention'); ?>>Attention Required</option>
      <option value="planned" <?php selected($status,'planned'); ?>>Planned</option>
    </select></div>
    <div class="tkc-report-field"><label for="tkc_report_summary">Executive Summary</label><textarea id="tkc_report_summary" name="tkc_report_summary" placeholder="Short overview of the work completed and current status."><?php echo esc_textarea($fields['summary']); ?></textarea></div>
    <div class="tkc-report-field"><label for="tkc_report_risks">Open Items / Risks</label><textarea id="tkc_report_risks" name="tkc_report_risks" placeholder="One item per line. Example: Mobile menu spacing | Medium | Website Owner"><?php echo esc_textarea($fields['risks']); ?></textarea><div class="tkc-report-help">Use one line per item. You can separate Issue | Severity | Owner.</div></div>
    <div class="tkc-report-field"><label for="tkc_report_recommendations">Recommended Next Steps</label><textarea id="tkc_report_recommendations" name="tkc_report_recommendations" placeholder="One recommendation per line."><?php echo esc_textarea($fields['recommendations']); ?></textarea></div>
    <div class="tkc-report-field"><label for="tkc_report_evidence_urls">Evidence Image URLs</label><textarea id="tkc_report_evidence_urls" name="tkc_report_evidence_urls" placeholder="Paste one Media Library image URL per line."><?php echo esc_textarea($fields['evidence_urls']); ?></textarea><div class="tkc-report-help">Featured Image is also used as the primary report image.</div></div>
    <p><strong>Report body:</strong> use the normal WordPress editor above for the detailed change log, notes, links, and explanations.</p>
    <?php
}

function tkc_report_metrics_box($post) {
    $metrics = array(
        'progress' => get_post_meta($post->ID, '_tkc_report_progress', true),
        'completed' => get_post_meta($post->ID, '_tkc_report_completed', true),
        'issues' => get_post_meta($post->ID, '_tkc_report_issues', true),
        'closed' => get_post_meta($post->ID, '_tkc_report_closed', true),
        'actions' => get_post_meta($post->ID, '_tkc_report_actions', true),
    );
    $metrics['progress'] = $metrics['progress'] === '' ? 100 : $metrics['progress'];
    foreach (array('progress'=>'Overall Progress (%)','completed'=>'Completed Items','issues'=>'Issues Found','closed'=>'Issues Closed','actions'=>'Actions Required') as $key=>$label) : ?>
      <p><label for="tkc_report_<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label><br><input style="width:100%" type="number" min="0" <?php echo $key==='progress'?'max="100"':''; ?> id="tkc_report_<?php echo esc_attr($key); ?>" name="tkc_report_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($metrics[$key]); ?>"></p>
    <?php endforeach;
}

function tkc_save_report_meta($post_id) {
    if (!isset($_POST['tkc_report_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tkc_report_nonce'])), 'tkc_save_report')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $text_fields = array('project','scope','prepared_by');
    foreach ($text_fields as $field) {
        update_post_meta($post_id, '_tkc_report_'.$field, isset($_POST['tkc_report_'.$field]) ? sanitize_text_field(wp_unslash($_POST['tkc_report_'.$field])) : '');
    }
    foreach (array('summary','risks','recommendations','evidence_urls') as $field) {
        update_post_meta($post_id, '_tkc_report_'.$field, isset($_POST['tkc_report_'.$field]) ? sanitize_textarea_field(wp_unslash($_POST['tkc_report_'.$field])) : '');
    }
    $allowed = array('completed','in-progress','review','attention','planned');
    $status = isset($_POST['tkc_report_status']) ? sanitize_key(wp_unslash($_POST['tkc_report_status'])) : 'completed';
    update_post_meta($post_id, '_tkc_report_status', in_array($status,$allowed,true) ? $status : 'completed');
    foreach (array('progress','completed','issues','closed','actions') as $field) {
        $value = isset($_POST['tkc_report_'.$field]) ? max(0, (int) $_POST['tkc_report_'.$field]) : 0;
        if ($field === 'progress') $value = min(100, $value);
        update_post_meta($post_id, '_tkc_report_'.$field, $value);
    }
}
add_action('save_post_tkc_report', 'tkc_save_report_meta');

function tkc_report_admin_columns($columns) {
    $columns['tkc_report_status'] = 'Status';
    $columns['tkc_report_scope'] = 'Scope';
    $columns['tkc_report_progress'] = 'Progress';
    return $columns;
}
add_filter('manage_tkc_report_posts_columns','tkc_report_admin_columns');
function tkc_report_admin_column_content($column,$post_id) {
    if ($column==='tkc_report_status') echo esc_html(ucwords(str_replace('-', ' ', get_post_meta($post_id,'_tkc_report_status',true) ?: 'completed')));
    if ($column==='tkc_report_scope') echo esc_html(get_post_meta($post_id,'_tkc_report_scope',true));
    if ($column==='tkc_report_progress') echo esc_html((int)get_post_meta($post_id,'_tkc_report_progress',true).'%');
}
add_action('manage_tkc_report_posts_custom_column','tkc_report_admin_column_content',10,2);

function tkc_report_assets() {
    if (is_page_template('template-reports.php') || is_singular('tkc_report')) {
        $css = get_template_directory().'/css/reports.css';
        wp_enqueue_style('tkc-reports', get_template_directory_uri().'/css/reports.css', array(), file_exists($css)?filemtime($css):'1.0');
    }
}
add_action('wp_enqueue_scripts','tkc_report_assets',40);

function tkc_reports_require_login() {
    if ((is_page_template('template-reports.php') || is_singular('tkc_report')) && !is_user_logged_in()) {
        auth_redirect();
    }
}
add_action('template_redirect','tkc_reports_require_login');

function tkc_report_status_label($status) {
    $labels = array('completed'=>'Completed / Live','in-progress'=>'In Progress','review'=>'Review Required','attention'=>'Attention Required','planned'=>'Planned');
    return isset($labels[$status]) ? $labels[$status] : 'Completed / Live';
}

function tkc_report_signoff_handler() {
    if (!is_user_logged_in()) wp_die('Login required.');
    $report_id = isset($_POST['report_id']) ? absint($_POST['report_id']) : 0;
    if (!$report_id || get_post_type($report_id)!=='tkc_report') wp_die('Invalid report.');
    check_admin_referer('tkc_report_signoff_'.$report_id, 'tkc_report_signoff_nonce');
    $name = isset($_POST['signoff_name']) ? sanitize_text_field(wp_unslash($_POST['signoff_name'])) : '';
    $email = isset($_POST['signoff_email']) ? sanitize_email(wp_unslash($_POST['signoff_email'])) : '';
    $role = isset($_POST['signoff_role']) ? sanitize_text_field(wp_unslash($_POST['signoff_role'])) : '';
    $approved = isset($_POST['signoff_approved']) ? 'yes' : 'no';
    if (!$name || !$email || $approved !== 'yes') {
        wp_safe_redirect(add_query_arg('signoff','missing',get_permalink($report_id)).'#report-signoff'); exit;
    }
    update_post_meta($report_id,'_tkc_report_signed_off','yes');
    update_post_meta($report_id,'_tkc_report_signoff_name',$name);
    update_post_meta($report_id,'_tkc_report_signoff_email',$email);
    update_post_meta($report_id,'_tkc_report_signoff_role',$role);
    update_post_meta($report_id,'_tkc_report_signoff_date',current_time('mysql'));
    wp_safe_redirect(add_query_arg('signoff','success',get_permalink($report_id)).'#report-signoff'); exit;
}
add_action('admin_post_tkc_report_signoff','tkc_report_signoff_handler');

function tkc_report_comment_form_defaults($defaults) {
    if (is_singular('tkc_report')) {
        $defaults['title_reply'] = 'Comments';
        $defaults['label_submit'] = 'Post comment';
        $defaults['comment_notes_before'] = '';
        $defaults['comment_notes_after'] = '';
    }
    return $defaults;
}
add_filter('comment_form_defaults','tkc_report_comment_form_defaults');

/**
 * Keep the custom header and floating basket counters in sync with WooCommerce.
 * WooCommerce returns these fragments after AJAX add/remove/update actions.
 */
function tkc_refresh_cart_count_fragments($fragments) {
    $count = 0;

    if (function_exists('WC') && WC()->cart) {
        $count = (int) WC()->cart->get_cart_contents_count();
    }

    $header_badge = '<span class="site-header-action__count" aria-live="polite">' . esc_html($count) . '</span>';
    $quick_badge  = '<span class="site-quick-link__count" aria-live="polite">' . esc_html($count) . '</span>';

    $fragments['span.site-header-action__count'] = $header_badge;
    $fragments['span.site-quick-link__count'] = $quick_badge;

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'tkc_refresh_cart_count_fragments');


/**
 * Homepage SEO defaults.
 * Yoast SEO is used on the live site. These defaults only fill the homepage
 * title/meta description when the editor has not already set custom Yoast values.
 */
function tkc_homepage_seo_title_default($title) {
    if (!is_front_page()) return $title;

    $front_id = (int) get_option('page_on_front');
    $custom_title = $front_id ? get_post_meta($front_id, '_yoast_wpseo_title', true) : '';
    if ($custom_title) return $title;

    return 'Kitchen Components UK | Sinks, Taps & Appliances | TKC';
}

function tkc_homepage_seo_description_default($description) {
    if (!is_front_page()) return $description;

    $front_id = (int) get_option('page_on_front');
    $custom_description = $front_id ? get_post_meta($front_id, '_yoast_wpseo_metadesc', true) : '';
    if ($custom_description) return $description;

    return 'Shop premium kitchen components across the UK, including sinks, taps, appliances, handles and water filters. Nationwide delivery from The Kitchen Components.';
}

if (defined('WPSEO_VERSION')) {
    add_filter('wpseo_title', 'tkc_homepage_seo_title_default', 20);
    add_filter('wpseo_metadesc', 'tkc_homepage_seo_description_default', 20);
} else {
    add_filter('pre_get_document_title', function ($title) {
        return is_front_page() ? 'Kitchen Components UK | Sinks, Taps & Appliances | TKC' : $title;
    }, 20);

    add_action('wp_head', function () {
        if (is_front_page()) {
            echo '<meta name="description" content="' . esc_attr('Shop premium kitchen components across the UK, including sinks, taps, appliances, handles and water filters. Nationwide delivery from The Kitchen Components.') . '">' . "\n";
        }
    }, 2);
}
