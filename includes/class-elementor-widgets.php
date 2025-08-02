<?php
if (!defined('ABSPATH')) exit;

class Marketplace_Reviews_Elementor_Widgets {
    public static function register_widgets($widgets_manager) {
        require_once __DIR__ . '/widgets/class-widget-reviews-list.php';
        require_once __DIR__ . '/widgets/class-widget-review-form.php';
        $widgets_manager->register(new \Marketplace_Reviews_Widget_Reviews_List());
        $widgets_manager->register(new \Marketplace_Reviews_Widget_Review_Form());
    }
}
add_action('elementor/widgets/register', ['Marketplace_Reviews_Elementor_Widgets', 'register_widgets']);
