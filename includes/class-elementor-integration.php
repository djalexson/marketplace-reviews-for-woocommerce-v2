<?php
/**
 * Elementor Integration
 *
 * @package MarketplaceReviews
 */

class Marketplace_Reviews_Elementor {

    public function register_widgets($widgets_manager) {
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'elementor/class-widget-product-reviews.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'elementor/class-widget-review-form.php';
        require_once MARKETPLACE_REVIEWS_PLUGIN_DIR . 'elementor/class-widget-average-rating.php';

        $widgets_manager->register(new \MarketplaceReviews\Widget_Product_Reviews());
        $widgets_manager->register(new \MarketplaceReviews\Widget_Review_Form());
        $widgets_manager->register(new \MarketplaceReviews\Widget_Average_Rating());
    }

    public function add_elementor_widget_category($elements_manager) {
        $elements_manager->add_category(
            'marketplace-reviews',
            [
                'title' => __('Marketplace Reviews', 'marketplace-reviews-for-woocommerce'),
                'icon'  => 'fa fa-star'
            ]
        );
    }
}
