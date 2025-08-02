<?php
namespace MarketplaceReviews;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class Widget_Product_Reviews extends Widget_Base {

    public function get_name() {
        return 'marketplace_product_reviews';
    }

    public function get_title() {
        return __('Product Reviews', 'marketplace-reviews-for-woocommerce');
    }

    public function get_icon() {
        return 'eicon-comments';
    }

    public function get_categories() {
        return ['marketplace-reviews'];
    }

    protected function _register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'marketplace-reviews-for-woocommerce'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('product_id', [
            'label' => __('Product ID', 'marketplace-reviews-for-woocommerce'),
            'type' => Controls_Manager::NUMBER,
            'default' => get_the_ID(),
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['product_id'])) {
            echo '<div class="elementor-widget-marketplace-reviews">';
            do_action('marketplace_reviews_render_reviews', $settings['product_id']);
            echo '</div>';
        }
    }
}
