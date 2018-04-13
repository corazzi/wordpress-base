<?php

/**
 * Enqueue our JavaScript files. We also remove jQuery from the registered
 * scripts as we now use the CDN to get a later version of jQuery, with a
 * dynamic fallback if the CDN fails.
 * @return void
 */
function site_scripts()
{
	// Deregister the scripts we don't need
	wp_deregister_script('jquery');

	// Register the scripts we want
	wp_register_script('app', theme_dir('assets/js/app.js'), null, null, true);

	// And now enqueue them
	wp_enqueue_script('app');
}
add_action('wp_enqueue_scripts', 'site_scripts');

/**
 * Enqueue our CSS assets
 * @return void
 */
function site_styles()
{
	wp_register_style('app', get_stylesheet_directory_uri().'/assets/css/app.css');

	wp_enqueue_style(['app']);
}
add_action('wp_enqueue_scripts', 'site_styles');
