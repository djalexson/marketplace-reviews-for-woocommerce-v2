<?php
namespace MarketplaceReviews;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class Widget_Average_Rating extends Widget_Base {

    public function get_name() {
        return 'marketplace_average_rating';
    }

    public function get_title() {
        return __('Average Rating', 'marketplace-reviews-for-woocommerce');
    }

    public function get_icon() {
        return 'eicon-star';
    }

    public function get_categories() {
        return ['marketplace-reviews'];
    }

    protected function _register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Settings', 'marketplace-reviews-for-woocommerce'),
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
            $average = Marketplace_Reviews_Utilities::get_average_rating($settings['product_id']);
            echo '<div class="marketplace-average-rating">';
            echo '<strong>' . esc_html__('Average Rating:', 'marketplace-reviews-for-woocommerce') . '</strong> ' . esc_html($average) . '/5';
            echo '</div>';
        }
    }
}
