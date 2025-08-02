<?php
class Marketplace_Reviews_Elementor_Init {
    public function __construct() {
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
    }

    public function register_widgets($widgets_manager) {
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/widgets/class-widget-review-form.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'includes/widgets/class-widget-reviews-list.php';
        
        $widgets_manager->register(new Marketplace_Reviews_Widget_Review_Form());
        $widgets_manager->register(new Marketplace_Reviews_Widget_Reviews_List());
    }

    public function add_elementor_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'marketplace-reviews',
            [
                'title' => __('Marketplace Reviews', 'marketplace-reviews-for-woocommerce'),
                'icon' => 'fa fa-star',
            ]
        );
    }
}
