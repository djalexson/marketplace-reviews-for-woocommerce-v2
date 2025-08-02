<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Marketplace_Reviews_Widget_Reviews_List extends Widget_Base {
    public function get_name() { return 'marketplace_reviews_list'; }
    public function get_title() { return __('Marketplace Reviews List', 'marketplace-reviews-for-woocommerce'); }
    public function get_icon() { return 'eicon-comments'; }
    public function get_categories() { return ['general']; }

    protected function render() {
        $product_id = get_the_ID();
        echo do_shortcode('[marketplace_product_reviews product_id="' . esc_attr($product_id) . '"]');
    }
}
