<?php
namespace MarketplaceReviews;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class Widget_Review_Thread extends Widget_Base {
    public function get_name() {
        return 'marketplace_review_thread';
    }

    public function get_title() {
        return __('Review Thread', 'marketplace-reviews-for-woocommerce');
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
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('review_id', [
            'label' => __('Review ID', 'marketplace-reviews-for-woocommerce'),
            'type'  => Controls_Manager::NUMBER,
            'default' => 0,
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['review_id'])) {
            $tree = \Marketplace_Review_Replies::build_tree(\Marketplace_Review_Replies::get_replies($settings['review_id']));
            \Marketplace_Review_Replies::render_tree($tree);
        }
    }
}
