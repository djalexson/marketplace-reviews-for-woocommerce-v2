<?php
/**
 * Plugin settings page
 *
 * @package MarketplaceReviews
 */
	
class Marketplace_Reviews_Settings {

    public function register_settings() {
        add_option('marketplace_reviews_enable_photos', 'yes');
        add_option('marketplace_reviews_admin_notifications', 'yes');
        add_option('marketplace_reviews_review_trigger_status', 'wc-completed');
        add_option('marketplace_reviews_popup_reminder', 'yes');
        add_option('marketplace_reviews_elementor_support', 'yes');
        add_option('marketplace_reviews_archive_page_id', 0);
        add_option('marketplace_reviews_product_page_id', 0);
        add_option('marketplace_reviews_enable_redis', 'no'); // Redis кеш

        // Новые опции:
        add_option('marketplace_reviews_enable_pros', 'yes');
        add_option('marketplace_reviews_enable_cons', 'yes');
        add_option('marketplace_reviews_pros_label', __('Pros', 'marketplace-reviews-for-woocommerce'));
        add_option('marketplace_reviews_cons_label', __('Cons', 'marketplace-reviews-for-woocommerce'));
        add_option('marketplace_reviews_enable_tab', 'yes');
        add_option('marketplace_reviews_tab_title', __('Marketplace Reviews', 'marketplace-reviews-for-woocommerce'));

        // Новый блок — средний рейтинг!
        add_option('marketplace_reviews_show_average_rating', 'yes');

        // Звёзды
        add_option('marketplace_reviews_star_style', 'default'); // default/svg/font
        add_option('marketplace_reviews_star_svg', ''); // SVG код, если загружен

        // Пагинация и кнопка "Показать все"
        add_option('marketplace_reviews_shortcode_pagination', 'yes');
        add_option('marketplace_reviews_shortcode_showall_button', 'no');

        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_enable_photos');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_admin_notifications');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_review_trigger_status');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_popup_reminder');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_elementor_support');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_archive_page_id');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_product_page_id');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_enable_redis');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_enable_pros');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_enable_cons');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_pros_label');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_cons_label');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_enable_tab');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_tab_title');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_show_average_rating'); // средний рейтинг
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_star_style');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_star_svg');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_shortcode_pagination');
        register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_shortcode_showall_button');

        add_settings_section(
            'marketplace_reviews_main_section',
            __('Marketplace Reviews Settings', 'marketplace-reviews-for-woocommerce'),
            null,
            'marketplace_reviews_settings'
        );

        // Стандартные настройки
        add_settings_field('marketplace_reviews_enable_photos', __('Enable Photo Uploads', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_enable_photos']);
        add_settings_field('marketplace_reviews_admin_notifications', __('Notify Admin on New Reviews', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_admin_notifications']);
        add_settings_field('marketplace_reviews_review_trigger_status', __('Order Status to Trigger Review', 'marketplace-reviews-for-woocommerce'), [$this, 'render_status_select'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_review_trigger_status']);
        add_settings_field('marketplace_reviews_popup_reminder', __('Remind User to Leave Review via Popup', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_popup_reminder']);
        add_settings_field('marketplace_reviews_elementor_support', __('Enable Elementor Support', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_elementor_support']);
        add_settings_field('marketplace_reviews_enable_redis', __('Enable Redis Cache for Reviews', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_enable_redis']);
        add_settings_field('marketplace_reviews_archive_page_id', __('Archive Reviews Page', 'marketplace-reviews-for-woocommerce'), [$this, 'render_page_select'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_archive_page_id']);
        add_settings_field('marketplace_reviews_product_page_id', __('Product Reviews Page', 'marketplace-reviews-for-woocommerce'), [$this, 'render_page_select'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_product_page_id']);

        // Pros & Cons
        add_settings_field('marketplace_reviews_enable_pros', __('Enable Pros Block', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_enable_pros_cons']);
        add_settings_field('marketplace_reviews_enable_cons', __('Enable  Cons Block', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_enable_pros_cons']);
        add_settings_field('marketplace_reviews_pros_label', __('Label for Pros Block', 'marketplace-reviews-for-woocommerce'), [$this, 'render_text_input'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_pros_label']);
        add_settings_field('marketplace_reviews_cons_label', __('Label for Cons Block', 'marketplace-reviews-for-woocommerce'), [$this, 'render_text_input'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_cons_label']);
        // Tab
        add_settings_field('marketplace_reviews_enable_tab', __('Enable Product Tab', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_enable_tab']);
        add_settings_field('marketplace_reviews_tab_title', __('Tab Title', 'marketplace-reviews-for-woocommerce'), [$this, 'render_text_input'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_tab_title']);

        // Average Rating (новое поле!)
        add_settings_field('marketplace_reviews_show_average_rating', __('Show Average Product Rating', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_show_average_rating']);

        // Pagination & "Show All"
        add_settings_field('marketplace_reviews_shortcode_pagination', __('Enable Pagination in Shortcode/Tab', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_shortcode_pagination']);
        add_settings_field('marketplace_reviews_shortcode_showall_button', __('Show "Show All" Button in Shortcode/Tab', 'marketplace-reviews-for-woocommerce'), [$this, 'render_checkbox'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_shortcode_showall_button']);

        // Star icon style
        add_settings_field('marketplace_reviews_star_style', __('Star Icon Style', 'marketplace-reviews-for-woocommerce'), [$this, 'render_star_style_select'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_star_style']);
        add_settings_field('marketplace_reviews_star_svg', __('Upload SVG for Stars (Paste SVG Code)', 'marketplace-reviews-for-woocommerce'), [$this, 'render_svg_input'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_star_svg']);

				add_option('marketplace_reviews_enable_photos', 'yes');
				add_option('marketplace_reviews_max_images', 5); // по умолчанию 5
				add_option('marketplace_reviews_upload_label', __('Upload Images (optional)', 'marketplace-reviews-for-woocommerce'));
				register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_enable_photos');
				register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_max_images');
				register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_upload_label');

				add_settings_field('marketplace_reviews_max_images', __('Max Images', 'marketplace-reviews-for-woocommerce'), [$this, 'render_number_input'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_max_images']);
				add_settings_field('marketplace_reviews_upload_label', __('Upload Block Label', 'marketplace-reviews-for-woocommerce'), [$this, 'render_text_input'], 'marketplace_reviews_settings', 'marketplace_reviews_main_section', ['label_for' => 'marketplace_reviews_upload_label']);


				add_option('marketplace_reviews_popup_title', __('Leave a Review', 'marketplace-reviews-for-woocommerce'));
add_option('marketplace_reviews_popup_button', __('Submit Review', 'marketplace-reviews-for-woocommerce'));
add_option('marketplace_reviews_popup_button_pul', __('Submitting...', 'marketplace-reviews-for-woocommerce'));
add_option('marketplace_reviews_popup_rating_label', __('Rating', 'marketplace-reviews-for-woocommerce'));

register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_popup_title');
register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_popup_button');
register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_popup_button_pul');
register_setting('marketplace_reviews_settings_group', 'marketplace_reviews_popup_rating_label');
$this->register_multilang_field('marketplace_reviews_popup_title', __('Popup Title', 'marketplace-reviews-for-woocommerce'));
$this->register_multilang_field('marketplace_reviews_popup_button', __('Popup Button Text', 'marketplace-reviews-for-woocommerce'));
$this->register_multilang_field('marketplace_reviews_popup_button_pul', __('Popup Button loading text', 'marketplace-reviews-for-woocommerce'));
$this->register_multilang_field('marketplace_reviews_popup_rating_label', __('Popup Rating Label', 'marketplace-reviews-for-woocommerce'));
$this->register_multilang_field('marketplace_reviews_pros_label', __('Label for Pros Block', 'marketplace-reviews-for-woocommerce'));
$this->register_multilang_field('marketplace_reviews_cons_label', __('Label for Cons Block', 'marketplace-reviews-for-woocommerce'));
$this->register_multilang_field('marketplace_reviews_tab_title', __('Tab Title', 'marketplace-reviews-for-woocommerce'));


add_settings_field(
    'marketplace_reviews_popup_title',
    __('Popup Title', 'marketplace-reviews-for-woocommerce'),
    [$this, 'render_text_input'],
    'marketplace_reviews_settings',
    'marketplace_reviews_main_section',
    ['label_for' => 'marketplace_reviews_popup_title']
);
add_settings_field(
    'marketplace_reviews_popup_button',
    __('Popup Button Text', 'marketplace-reviews-for-woocommerce'),
    [$this, 'render_text_input'],
    'marketplace_reviews_settings',
    'marketplace_reviews_main_section',
    ['label_for' => 'marketplace_reviews_popup_button']
);
add_settings_field(
    'marketplace_reviews_popup_button_pul',
    __('Popup Button loading text', 'marketplace-reviews-for-woocommerce'),
    [$this, 'render_text_input'],
    'marketplace_reviews_settings',
    'marketplace_reviews_main_section',
    ['label_for' => 'marketplace_reviews_popup_button_pul']
);
add_settings_field(
    'marketplace_reviews_popup_rating_label',
    __('Popup Rating Label', 'marketplace-reviews-for-woocommerce'),
    [$this, 'render_text_input'],
    'marketplace_reviews_settings',
    'marketplace_reviews_main_section',
    ['label_for' => 'marketplace_reviews_popup_rating_label']
);			

        add_action('admin_menu', [$this, 'add_settings_menu']);
    }
public function render_number_input($args) {
    $option = get_option($args['label_for'], 5); // 5 — дефолт
    echo '<input type="number" min="1" max="20" id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '" value="' . esc_attr($option) . '" class="small-text" />';
}

    public function add_settings_menu() {
        add_submenu_page(
            'marketplace-reviews', // родительский slug
            __('Marketplace Reviews Settings', 'marketplace-reviews-for-woocommerce'),
            __('Settings', 'marketplace-reviews-for-woocommerce'),
            'manage_options',
            'marketplace_reviews_settings',
            [$this, 'settings_page']
        );
    }

    public function settings_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Marketplace Reviews Settings', 'marketplace-reviews-for-woocommerce') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('marketplace_reviews_settings_group');
        do_settings_sections('marketplace_reviews_settings');
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function render_checkbox($args) {
        $option = get_option($args['label_for'], 'yes');
        echo '<input type="checkbox" id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '" value="yes" ' . checked('yes', $option, false) . ' />';
    }

    public function render_status_select($args) {
        $option = get_option($args['label_for'], 'wc-completed');
        $statuses = function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : [];
        echo '<select id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '">';
        foreach ($statuses as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($option, $key, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    public function render_page_select($args) {
        $selected = get_option($args['label_for'], 0);
        wp_dropdown_pages([
            'name' => esc_attr($args['label_for']),
            'selected' => $selected,
            'show_option_none' => __('— Select —', 'marketplace-reviews-for-woocommerce')
        ]);
    }

    public function render_text_input($args) {
        $option = get_option($args['label_for'], '');
        echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '" value="' . esc_attr($option) . '" class="regular-text" />';
    }

    // Селектор стиля звёзд
    public function render_star_style_select($args) {
        $option = get_option($args['label_for'], 'default');
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($args['label_for']); ?>">
            <option value="default" <?php selected($option, 'default'); ?>><?php _e('Default Star (★)', 'marketplace-reviews-for-woocommerce'); ?></option>
            <option value="font" <?php selected($option, 'font'); ?>><?php _e('Font Awesome', 'marketplace-reviews-for-woocommerce'); ?></option>
            <option value="svg" <?php selected($option, 'svg'); ?>><?php _e('Custom SVG', 'marketplace-reviews-for-woocommerce'); ?></option>
        </select>
        <?php
    }

    // Текстовая textarea для SVG-кода
    public function render_svg_input($args) {
        $option = get_option($args['label_for'], '');
        echo '<textarea id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '" rows="4" cols="60" class="large-text code">' . esc_textarea($option) . '</textarea>';
        echo '<p class="description">' . __('Paste your SVG code here for custom stars.', 'marketplace-reviews-for-woocommerce') . '</p>';
    }
// Внутри class Marketplace_Reviews_Settings
public function register_multilang_field($base_key, $label, $type = 'text', $default = '') {
    // Получаем языки, если есть Polylang
    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(['raw' => 1]);
    } else {
        $languages = ['default' => ['name' => __('Default', 'marketplace-reviews-for-woocommerce')]];
    }

    if (empty($languages)) {
        $languages = ['default' => ['name' => __('Default', 'marketplace-reviews-for-woocommerce')]];
    }

    foreach ($languages as $lang_code => $lang) {
        $field_key = ($lang_code === 'default') ? $base_key : "{$base_key}_{$lang_code}";
        register_setting('marketplace_reviews_settings_group', $field_key);

        add_settings_field(
            $field_key,
            sprintf('%s (%s)', $label, $lang['name']),
            function() use ($field_key, $type, $default) {
                $option = get_option($field_key, $default);
                if ($type === 'text') {
                    echo '<input type="text" id="' . esc_attr($field_key) . '" name="' . esc_attr($field_key) . '" value="' . esc_attr($option) . '" class="regular-text" />';
                } elseif ($type === 'textarea') {
                    echo '<textarea id="' . esc_attr($field_key) . '" name="' . esc_attr($field_key) . '" rows="3" class="large-text">' . esc_textarea($option) . '</textarea>';
                }
            },
            'marketplace_reviews_settings',
            'marketplace_reviews_main_section'
        );
    }
}
public static function get_option_lang($base_key, $default = '') {
    $lang = function_exists('pll_current_language') ? pll_current_language() : 'default';
    $field_key = ($lang === 'default') ? $base_key : "{$base_key}_{$lang}";
    return get_option($field_key, $default);
}

}
