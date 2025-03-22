<?php

namespace WPLangSwitch\Compatibility;

/**
 * Timber compatibility layer to support both 1.x and 2.x versions
 */
class TimberCompat {
    /**
     * Storage for menu locations
     *
     * @var array
     */
    public static $menu_locations = [];

    /**
     * Check if Timber is available
     *
     * @return bool
     */
    public static function is_timber_available() {
        return class_exists('\\Timber\\Timber');
    }

    /**
     * Get Timber version
     *
     * @return string|null
     */
    public static function get_version() {
        if (self::is_timber_available()) {
            return \Timber\Timber::$version;
        }
        return null;
    }

    /**
     * Check if Timber 2.x is being used
     *
     * @return bool
     */
    public static function is_timber_2() {
        $version = self::get_version();
        return $version && version_compare($version, '2.0.0', '>=');
    }

    /**
     * Get a menu by location or slug - compatible with both versions
     *
     * @param string $location_or_slug
     * @param array $args
     * @return \Timber\Menu|null
     */
    public static function get_menu($location_or_slug, $args = []) {
        if (!self::is_timber_available()) {
            return null;
        }

        // Apply a filter to the Timber\Timber::get_menu method for Timber 2.x compatibility
        if (!has_filter('timber/get_menu', [self::class, 'timber_get_menu_filter'])) {
            add_filter('timber/get_menu', [self::class, 'timber_get_menu_filter'], 10, 3);
        }

        // Use the original Timber method which will be filtered by our filter if needed
        return \Timber\Timber::get_menu($location_or_slug, $args);
    }

    /**
     * Filter for timber/get_menu that makes it work with Timber 2.x
     * 
     * @param mixed $menu
     * @param string|int $slug_or_id
     * @param array $args
     * @return \Timber\Menu
     */
    public static function timber_get_menu_filter($menu, $slug_or_id, $args) {
        if (!self::is_timber_2()) {
            return $menu;
        }

        if ($menu) {
            return $menu;
        }

        // Handle the case for Timber 2.x when the menu is not found
        if (is_string($slug_or_id)) {
            $locations = get_nav_menu_locations();
            if (isset($locations[$slug_or_id])) {
                // Get the menu ID from the location
                $menu_id = $locations[$slug_or_id];
                if ($menu_id) {
                    // Use get_term to get a WP_Term object if needed by Timber 2.x
                    $menu_term = get_term($menu_id, 'nav_menu');
                    return new \Timber\Menu($menu_term, $args);
                }
            }
            // Try by slug or name
            $menu_term = get_term_by('slug', $slug_or_id, 'nav_menu') ?: get_term_by('name', $slug_or_id, 'nav_menu');
            return new \Timber\Menu($menu_term ?: $slug_or_id, $args);
        }

        // The ID was directly passed
        $menu_term = get_term($slug_or_id, 'nav_menu');
        return new \Timber\Menu($menu_term ?: $slug_or_id, $args);
    }

    /**
     * Render a template
     *
     * @param string|array $template
     * @param array $context
     * @return string
     */
    public static function render($template, $context = []) {
        if (!self::is_timber_available()) {
            return '';
        }

        if (self::is_timber_2()) {
            return \Timber\Timber::compile($template, $context);
        } else {
            return \Timber\Timber::compile($template, $context);
        }
    }

    /**
     * Get Timber context
     *
     * @return array
     */
    public static function get_context() {
        if (!self::is_timber_available()) {
            return [];
        }

        return \Timber\Timber::context();
    }

    /**
     * Configure Timber locations
     *
     * @param string $directory
     */
    public static function set_locations($directory) {
        // Store the directory for later use if Timber isn't available yet
        static $stored_locations = [];

        if ($directory) {
            $stored_locations[] = $directory;
        }

        if (!self::is_timber_available()) {
            // If Timber isn't available, we'll set the locations later
            add_action('after_setup_theme', function () use ($stored_locations) {
                if (self::is_timber_available()) {
                    foreach ($stored_locations as $dir) {
                        self::apply_locations($dir);
                    }
                }
            });
            return;
        }

        // Timber is available, set locations immediately
        self::apply_locations($directory);
    }

    /**
     * Internal method to apply locations based on Timber version
     * 
     * @param string $directory
     */
    private static function apply_locations($directory) {
        if (!$directory || !self::is_timber_available()) {
            return;
        }

        if (self::is_timber_2()) {
            add_filter('timber/locations', function ($paths) use ($directory) {
                $paths['wpls'] = [$directory];
                return $paths;
            });
        } else {
            \Timber\Timber::$locations = $directory;
        }
    }

    /**
     * Apply all the necessary fixes to make the plugin work with both Timber 1.x and 2.x
     */
    public static function apply_compatibility_fixes() {
        // First, handle the immediate compatibility needs for admin pages
        // This needs to run immediately because the admin panel constructor uses set_locations

        // Add a filter to make Timber::get_menu work in both versions if Timber is already available
        if (self::is_timber_available()) {
            add_filter('timber/get_menu', [self::class, 'timber_get_menu_filter'], 10, 3);
        }

        // Defer the more complex compatibility checks to after the theme is loaded
        add_filter('after_setup_theme', function () {
            // Now the theme has loaded, so Timber should be available if it's going to be
            if (!self::is_timber_available()) {
                // Only show admin notice if we're in the admin area
                if (is_admin()) {
                    add_action('admin_notices', function () {
                        echo '<div class="error"><p>' .
                            sprintf(
                                __('WP Language Switch requires the %sTimber plugin%s to be installed and activated for some functionality.', 'wp-language-switch'),
                                '<a href="https://wordpress.org/plugins/timber-library/" target="_blank">',
                                '</a>'
                            ) .
                            '</p></div>';
                    });
                }
                return;
            }

            // If Timber 2.x is being used, add a backwards compatibility layer
            if (self::is_timber_2()) {
                // Add a method to the global namespace if it doesn't exist to make the theme work
                // This is a bit of a hack, but it's necessary for backwards compatibility
                if (!method_exists('\\Timber\\Timber', 'get_menu_locations')) {
                    add_filter('get_nav_menu_locations', function ($locations) {
                        // Store menu locations for our compatibility layer
                        self::$menu_locations = $locations;
                        return $locations;
                    });

                    // Define a function to allow themes to use Timber::get_menu_locations()
                    if (!function_exists('timber_compat_get_menu_locations')) {
                        function timber_compat_get_menu_locations() {
                            return get_nav_menu_locations();
                        }

                        // Add a way to access the function through a filter since we can't dynamically add methods
                        add_filter('timber/menu/locations', 'timber_compat_get_menu_locations');

                        // Add a workaround for themes directly calling Timber::get_menu_locations()
                        // We can't actually add methods to the Timber class, but we can define a function with the same name
                        // that will be used if the method doesn't exist in the class
                        if (!function_exists('\\Timber\\Timber::get_menu_locations')) {
                            // Define a global function that can be used as fallback
                            function timber_get_menu_locations() {
                                return apply_filters('timber/menu/locations', []);
                            }
                        }
                    }
                }
            }
        });
    }
}
