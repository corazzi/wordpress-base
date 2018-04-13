<?php

if (! function_exists('content')) {
    /**
     * Return the content of a post with a custom word limit and an ellipsis as the
     * delimiter.
     *
     * @param  integer $limit The number of words to return
     *
     * @return string  The word-limited content
     */
    function content($limit = 55)
    {
        $content = wp_trim_words(get_the_content(), $limit, '...');

        return apply_filters('the_content', $content);
    }
}

if (! function_exists('current_url')) {
    /**
     * Get the current page URL, including any URL parameters
     *
     * @see https://stackoverflow.com/a/6768831/976529
     *
     * @return string
     */
    function current_url()
    {
        return sprintf(
            '%s://%s%s',
            (isset($_SERVER['HTTPS']) ? "https" : "http"),
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI']
        );
    }
}

if (! function_exists('dd')) {
    /**
     * Used for debugging. Dumps the arguments passed to it to the page as
     * pre-formatted text, then stops the script from processing.
     *
     * @return void
     */
    function dd()
    {
        // For a bit of safety, we don't do anything with this function if the
        // WordPress debugging functionality is turned off.
        if (! defined(WP_DEBUG) || ! WP_DEBUG) {
            if (! array_key_exists('dev', $_GET)) {
                return;
            }
        }

        echo '<pre>';

        foreach (func_get_args() as $arg) {
            var_dump($arg);
        }

        exit;
    }
}

if (! function_exists('excerpt')) {
    /**
     * Return the excerpt for a post, but with a custom word limit and the
     * delimiter changed to an ellipsis.
     *
     * @param  integer $limit The number of words to return
     *
     * @return string  The excerpt
     */
    function excerpt($limit = 55)
    {
        return wp_trim_words(get_the_excerpt(), $limit, '...');
    }
}

if (! function_exists('get_page_parent')) {
    /**
     * Get the current page's parent, if there is one
     *
     * @param null $page
     *
     * @return \App\Support\Wrappers\Page
     */
    function get_page_parent($page = null)
    {
        if (is_null($page)) {
            $page = get_post();
        }

        if (! $page->post_parent) {
            return null;
        }

        return new \App\Support\Wrappers\Page(
            get_post($page->post_parent)
        );
    }
}

if (! function_exists('get_page_submenu')) {
    /**
     * Gets a submenu based on the given page's parent's submenu
     *
     * @param $post
     * @param $menu
     *
     * @return mixed
     */
    function get_page_submenu($post, $menu = 'primary-menu')
    {
        return menu_as_collection($menu)->filter(function ($item) use ($post) {
            return $post->post_parent == $item->object_id;
        })->first();
    }
}

if (! function_exists('get_the_post_thumbnail_path')) {
    /**
     * Get the path of a post's featured image. Either pass a $post object,
     * or it will default to the current $post.
     *
     * @param null $post
     *
     * @return mixed
     */
    function get_the_post_thumbnail_path($post = null)
    {
        if (is_null($post)) {
            global $post;
        }

        return get_attached_file(
            get_post_thumbnail_id($post)
        );
    }
}

if (! function_exists('is_wp_post')) {
    /**
     * Determine if the given parameter is a WP_Post object
     *
     * @param null $post
     *
     * @return bool
     */
    function is_wp_post($post = null)
    {
        if (! is_object($post)) {
            return false;
        }

        return get_class($post) === 'WP_Post';
    }
}


if (! function_exists('is_wp_user')) {
    /**
     * Assert if the given parameter is a WP_User object
     *
     * @param null $user
     *
     * @return bool
     */
    function is_wp_user($user = null)
    {
        if (! is_object($user)) {
            return false;
        }

        return get_class($user) === 'WP_User';
    }
}

if (! function_exists('menu_as_collection')) {
    /**
     * Return a navigation menu as a collection, with top-level items and sub-items.
     * Does not support sub-item sub-items, yet.
     *
     * @param $menu  string
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    function menu_as_collection($menu)
    {
        $grouped = collect(
            wp_get_nav_menu_items($menu)
        )->groupBy(function ($item, $key) {
            return $item->menu_item_parent;
        });

        return $grouped->first()->map(function ($item) use ($grouped) {
            $item->subitems = $grouped->get($item->ID) ?? collect();

            return $item;
        });
    }
}

if (! function_exists('option') && function_exists('get_field')) {
    /**
     * Gets an ACF option value
     *
     * @param      $key
     * @param null $default
     *
     * @return null
     */
    function option($key, $default = null)
    {
        $value = get_field($key, 'option') ?? $default;

        return $value ?: $default;
    }
}

if (! function_exists('partial')) {
    /**
     * Include a partial relative to the partials/ directory
     *
     * @param $path  string
     * @param $data  null|array
     *
     * @throws \InvalidArgumentException
     */
    function partial($path, $data = [])
    {
        // Build up the full path to the given template
        $fullpath = sprintf('%s/partials/%s.php', get_template_directory(), trim($path, '/')
        );

        if (! file_exists($fullpath)) {
            throw new InvalidArgumentException(
                sprintf('Could not find partial [%s] in [%s]', $path, $fullpath)
            );
        }

        // Get any variables that were passed through
        // so that they're propagated into the include
        extract($data);

        include($fullpath);
    }
}

if (! function_exists('placeholder_image')) {
    /**
     * If we don't have an image to use somewhere, use this function as a quick placeholder
     *
     * @param string $size
     * @param string $background
     * @param string $text
     *
     * @return string
     */
    function placeholder_image($size = '500x500', $background = 'ffffff', $text = '+')
    {
        $path = sprintf('%s/assets/images/placeholders/%s_%s_%s.png', get_template_directory(), $size, $background, $text);

        // Check if we've already used this image and saved it locally. If so, serve our local copy.
        if (file_exists($path)) {
            return sprintf('%s/assets/images/placeholders/%s_%s_%s.png', get_template_directory_uri(), $size, $background, $text);
        }

        // If we haven't already used it, fetch and save it, then recursively return this function
        $url = sprintf('https://via.placeholder.com/%s/%s&text=%s', $size, $background, $text);
        file_put_contents($path, fopen($url, 'r'));

        return placeholder_image($size, $background, $text);
    }
}

if (! function_exists('reduce_text')) {
    /**
     * Reduces text to $limit amount of characters, whilst appending an optional $ending.
     * Note, this does not take into account full words (it will break words)
     *
     *
     * @param        $text
     * @param        $limit
     * @param string $ending
     *
     * @return string
     */
    function reduce_text($text, $limit, $ending = '...')
    {
        $text = strip_tags($text);

        return strlen($text) > $limit ? rtrim(substr($text, 0, $limit)) . $ending : $text;
    }
}

if (! function_exists('render_partial')) {
    /**
     * Identical to partial(), except it returns the contents
     * instead of outputting them
     *
     * @param       $path
     * @param array $data
     *
     * @return string
     */
    function render_partial($path, $data = [])
    {
        ob_start();

        partial($path, $data);

        $partial = ob_get_contents();

        ob_end_clean();

        return $partial;
    }
}

if (! function_exists('split_text')) {
    /**
     * Split a bit of text, like a title, so that you can style part of it
     *
     * @param             $text
     * @param int         $at_word
     * @param null|string $additional_classes
     *
     * @return string
     */
    function split_text($text, $at_word = 1, $additional_classes = null)
    {
        $text = explode(' ', $text);

        // Split the array at the relative indices
        // to get the first and second "halves"
        $first = implode(' ', array_slice($text, 0, $at_word));
        $second = implode(' ', array_slice($text, $at_word));


        return sprintf('<span class="split-text %s">%s <span>%s</span></span>', $additional_classes, $first, $second);
    }
}

if (! function_exists('theme_dir')) {
    /**
     * Return the theme directory with the specified path added onto the end of it
     *
     * @param  string $path The path to append onto the theme directory
     *
     * @return string
     */
    function theme_dir($path = '')
    {
        return sprintf('%s%s',
            get_stylesheet_directory_uri(),
            '/' . trim($path, '/')
        );
    }
}

if (! function_exists('theme_image_url')) {
    /**
     * Get an image URL relative to the theme's `assets/images` directory
     *
     * @param $image  string
     *
     * @return string
     */
    function theme_image_url($image)
    {
        return sprintf('%s/assets/images/%s', get_template_directory_uri(), $image);
    }
}

if (! function_exists('url')) {
    /**
     * Gets the site's URL and appends the optional $path
     *
     * @param string $path
     *
     * @return string
     */
    function url($path = '')
    {
        return sprintf('%s/%s', site_url(), trim($path, '/'));
    }
}

if (! function_exists('user')) {
    /**
     * Get the currently logged in user
     *
     * @return \User
     */
    function user()
    {
        return new User([
            'wp_user' => wp_get_current_user()
        ]);
    }
}

if (! function_exists('wpdb')) {
    /**
     * Get the $wpdb global
     *
     * @return object
     */
    function wpdb()
    {
        global $wpdb;

        return $wpdb;
    }
}

if (! function_exists('wp_query')) {
    /**
     * Get the $wp_query global
     *
     * @return object
     */
    function wp_query()
    {
        global $wp_query;

        return $wp_query;
    }
}
