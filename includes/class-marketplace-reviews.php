<?php
/**
 * The core plugin class.
 *
 * @package    MarketplaceReviews
 * @subpackage MarketplaceReviews/includes
 */

class Marketplace_Reviews {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = MARKETPLACE_REVIEWS_VERSION;
        $this->plugin_name = 'marketplace-reviews-for-woocommerce';

        $this->run();
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_elementor_hooks();
        $this->define_yith_hooks();
    }

    private function load_dependencies() {
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-utilities.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-review-form.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-review-display.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-admin.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-settings.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-elementor-integration.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-yith-integration.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-review-replies.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/class-review-fields.php';

        $this->post_types     = new Marketplace_Reviews_Post_Types();
        $this->review_form    = new Marketplace_Reviews_Form();
        $this->review_display = new Marketplace_Reviews_Display();
        $this->settings = new Marketplace_Reviews_Settings();
        $this->admin = new Marketplace_Reviews_Admin($this->settings);

        $this->elementor      = new Marketplace_Reviews_Elementor();
        $this->yith           = new Marketplace_Reviews_YITH();
    }

  

    private function define_admin_hooks() {
        add_action('admin_menu', array($this->admin, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_scripts'));
        add_action('admin_init', array($this->settings, 'register_settings'));        
        add_action('add_meta_boxes', array($this->admin, 'add_meta_boxes'));
        add_action('save_post_marketplace_review', array($this->admin, 'save_review_meta'), 10, 2);

        add_filter('manage_marketplace_review_posts_columns', array($this->admin, 'set_custom_columns'));
        add_action('manage_marketplace_review_posts_custom_column', array($this->admin, 'custom_column'), 10, 2);
        add_filter('manage_edit-marketplace_review_sortable_columns', array($this->admin, 'sortable_columns'));

        add_action('transition_post_status', array($this->admin, 'review_status_changed'), 10, 3);
    }

    private function define_public_hooks() {
        add_action('wp_enqueue_scripts', array($this->review_display, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this->review_display, 'enqueue_scripts'));
        add_action('init', array($this->review_display, 'register_shortcodes'));

        add_action('wp_ajax_submit_marketplace_review', array($this->review_form, 'handle_review_submission'));
        add_action('wp_ajax_nopriv_submit_marketplace_review', array($this->review_form, 'handle_review_submission'));

        add_action('wp_ajax_submit_review_reply', array('Marketplace_Review_Replies', 'handle_ajax'));
        add_action('wp_ajax_nopriv_submit_review_reply', array('Marketplace_Review_Replies', 'handle_ajax'));

       add_filter('woocommerce_account_orders_columns', array($this->review_form, 'add_review_column_to_orders'));
      add_action('woocommerce_my_account_my_orders_column_review', array($this->review_form, 'add_review_button_to_orders'), 10, 2);

        add_filter('woocommerce_product_tabs', array($this->review_display, 'product_reviews_tab'));

        add_action('init', array($this->review_display, 'register_review_page_endpoint'));
        add_filter('template_include', array($this->review_display, 'load_review_page_template'));

    }

    private function define_elementor_hooks() {
        if (!did_action('elementor/loaded')) {
            return;
        }

        add_action('elementor/widgets/widgets_registered', array($this->elementor, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this->elementor, 'add_elementor_widget_category'));
    }

    private function define_yith_hooks() {
        if (!class_exists('YITH_WCAN')) {
            return;
        }

        add_filter('yith_wcan_register_premium_taxonomies', array($this->yith, 'register_review_rating_filter'));
    }

    public function run() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
            $this->post_types->register_post_types();
            $this->post_types->register_meta_fields();
            remove_post_type_support('marketplace_review', 'comments');
            remove_post_type_support('marketplace_review', 'trackbacks');
            $this->post_types->disable_gutenberg_for_reviews();
						 load_plugin_textdomain(
                'marketplace-reviews-for-woocommerce',
                false,
                dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
            );
    }
}
