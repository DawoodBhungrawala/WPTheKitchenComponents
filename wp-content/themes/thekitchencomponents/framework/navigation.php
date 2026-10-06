<?php

/**
 * Bootstrap 5 WordPress Nav Walker
 */

class bootstrap_5_wp_nav_menu_walker extends Walker_Nav_menu
{

	// Start Submenu
	function start_lvl(&$output, $depth = 0, $args = null)
	{
		$indent = str_repeat("\t", $depth);
		$submenu_classes = 'dropdown-menu depth_' . $depth;

		if ($depth === 0) {
			$submenu_classes .= ' mega-menu-panel';
		}

		$output .= "\n$indent<ul class=\"" . esc_attr($submenu_classes) . "\">\n";
	}

	// Start Element
	function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
	{

		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		$classes = empty($item->classes) ? [] : (array) $item->classes;
		$has_children = in_array('menu-item-has-children', $classes);

		// LI classes
		$li_classes = [
			'nav-item',
			'nav-item-' . $item->ID
		];

		if ($has_children && $depth === 0) {
			$li_classes[] = 'dropdown';
		}

		if ($has_children && $depth > 0) {
			$li_classes[] = 'dropdown-submenu';
		}

		$class_names = implode(' ', array_map('esc_attr', $li_classes));

		$output .= $indent . '<li class="' . $class_names . '">';

		// Link attributes
		$atts = '';
		$atts .= ! empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
		$atts .= ! empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
		$atts .= ! empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
		$atts .= ! empty($item->url) ? ' href="' . esc_url($item->url) . '"' : '';

		// Active class
		$active_class = in_array('current-menu-item', $classes) ||
			in_array('current-menu-ancestor', $classes) ? ' active' : '';

		// Link classes
		if ($depth === 0) {
			$link_class = $has_children
				? 'nav-link dropdown-toggle' . $active_class
				: 'nav-link' . $active_class;
		} else {
			$link_class = 'dropdown-item' . $active_class;
		}

		if ($has_children && $depth === 0) {
			$atts .= ' class="' . $link_class . '" data-bs-toggle="dropdown" aria-expanded="false"';
		} else {
			$atts .= ' class="' . $link_class . '"';
		}

		$menu_image = function_exists('get_field') ? get_field('menu_image', $item) : null;
		$menu_image_url = '';

		if (is_array($menu_image) && ! empty($menu_image['url'])) {
			$menu_image_url = $menu_image['url'];
		} elseif (is_string($menu_image) && ! empty($menu_image)) {
			$menu_image_url = $menu_image;
		}

		$item_output  = $args->before;
		$item_output .= '<a' . $atts . '>';

		if ($depth > 0 && ! empty($menu_image_url)) {
			$item_output .= '<span class="menu-item-image" aria-hidden="true">';
			$item_output .= '<img src="' . esc_url($menu_image_url) . '" alt="' . esc_attr($item->title) . '">';
			$item_output .= '</span>';
		}

		$item_output .= '<span class="menu-item-label">';
		$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		$item_output .= '</span>';
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}
}
