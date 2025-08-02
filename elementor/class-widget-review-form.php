<?php
namespace MarketplaceReviews;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class Widget_Review_Form extends Widget_Base {

    public function get_name() {
        return 'marketplace_review_form';
    }

    public function get_title() {
        return __('Review Form', 'marketplace-reviews-for-woocommerce');
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
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
            include MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/review-form.php';
        }
    }
}
