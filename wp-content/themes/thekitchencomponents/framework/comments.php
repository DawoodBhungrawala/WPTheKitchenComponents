<?php
/**
 * This file disables comments over the entire site, both front and backend.
 */

/**
 * Remove comments from the backend, and do not allow access to the pages
 */
add_action('admin_init', function () {
    global $pagenow;

    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }

    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

/**
 * Close comments on the frontend
 */
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

/**
 * Hide any existing comments
 */
add_filter('comments_array', '__return_empty_array', 10, 2);

/**
 * Remove the comments page from the admin menu
 */
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

/**
 * Hide comments from the admin bar
 */
add_action('wp_before_admin_bar_render', function() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
});
