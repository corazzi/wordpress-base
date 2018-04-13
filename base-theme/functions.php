<?php

/* Custom Image Sizes Here */
add_image_size('blog-article-thumb', 458, 245, true);

function site_setup()
{
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');

	register_nav_menus(array(
		'primary'   => 'Top primary menu',
	));

}
add_action('after_setup_theme', 'site_setup');
