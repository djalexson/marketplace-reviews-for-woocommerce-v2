<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Marketplace_Reviews_Widget_Review_Form extends Widget_Base {
    public function get_name() { return 'marketplace_review_form'; }
    public function get_title() { return __('Marketplace Review Form', 'marketplace-reviews-for-woocommerce'); }
    public function get_icon() { return 'eicon-form-horizontal'; }
    public function get_categories() { return ['general']; }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Form Settings', 'marketplace-reviews-for-woocommerce'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => __('Show Rating', 'marketplace-reviews-for-woocommerce'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_photos',
            [
                'label' => __('Show Photo Upload', 'marketplace-reviews-for-woocommerce'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style Settings', 'marketplace-reviews-for-woocommerce'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_background',
            [
                'label' => __('Form Background', 'marketplace-reviews-for-woocommerce'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .marketplace-review-form' => 'background-color: {{VALUE}}'
                ],
            ]
        );

        $this->add_control(
            'form_padding',
            [
                'label' => __('Form Padding', 'marketplace-reviews-for-woocommerce'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .marketplace-review-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $product_id = get_the_ID();
        
        // Prepare attributes for shortcode
        $shortcode_atts = [
            'product_id' => $product_id,
            'show_rating' => $settings['show_rating'],
            'show_photos' => $settings['show_photos'],
        ];

        // Convert attributes to string
        $atts_string = '';
        foreach ($shortcode_atts as $key => $value) {
            $atts_string .= ' ' . $key . '="' . esc_attr($value) . '"';
        }

        echo '<div class="elementor-marketplace-review-form">';
        echo do_shortcode('[marketplace_review_form' . $atts_string . ']');
        echo '</div>';
    }

    protected function content_template() {
        ?>
        <div class="elementor-marketplace-review-form">
            <?php echo __('Review Form Preview', 'marketplace-reviews-for-woocommerce'); ?>
        </div>
        <?php
    }
}
