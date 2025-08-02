<?php
/**
 * Plugin Name: Marketplace Reviews for WooCommerce
 * Plugin URI: https://yourwebsite.com/marketplace-reviews-for-woocommerce
 * Description: Advanced review system for WooCommerce with rating, pros/cons, moderation, display conditions, and integration with Elementor and YITH Ajax Product Filter.
 * Version: 1.0.0
 * Author: A.S Group
 * Text Domain: marketplace-reviews-for-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 *
 * @package MarketplaceReviews
 */

if (!defined('WPINC')) {
    die;
}
add_action('enqueue_block_editor_assets', function() {
    wp_enqueue_script(
        'marketplace-review-images-block',
        plugins_url('/admin/js/marketplace-review-images-block.js', __FILE__),
        [ 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-data' ],
        '1.0',
        true
    );
});
// HPOS compatibility declaration
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }
});


// Define plugin constants
define('MARKETPLACE_REVIEWS_VERSION', '1.0.0');
define('MARKETPLACE_REVIEWS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MARKETPLACE_REVIEWS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MARKETPLACE_REVIEWS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Check if WooCommerce is active
 */
function marketplace_reviews_woocommerce_is_active() {
    $active_plugins = (array) get_option('active_plugins', array());
    
    if (is_multisite()) {
        $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
    }
    
    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}

/**
 * The code that runs during plugin activation.
 */
function activate_marketplace_reviews() {
    if (!marketplace_reviews_woocommerce_is_active()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('Please install and activate WooCommerce before activating Marketplace Reviews for WooCommerce.', 'marketplace-reviews-for-woocommerce'));
    }
    
    require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-activator.php';
    Marketplace_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_marketplace_reviews() {
    require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-deactivator.php';
    Marketplace_Reviews_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_marketplace_reviews');
register_deactivation_hook(__FILE__, 'deactivate_marketplace_reviews');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-marketplace-reviews.php';

/**
 * Begins execution of the plugin.
 */
function run_marketplace_reviews() {
    $plugin = new Marketplace_Reviews();
    $plugin->run();
}

// Check if WooCommerce is active before running the plugin
if (marketplace_reviews_woocommerce_is_active()) {
    run_marketplace_reviews();
} else {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>' . __('Marketplace Reviews for WooCommerce requires WooCommerce to be installed and activated.', 'marketplace-reviews-for-woocommerce') . '</p></div>';
    });
}