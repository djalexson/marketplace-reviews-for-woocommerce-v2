<?php
/**
 * Plugin settings page
 *
 * @package MarketplaceReviews
 */

class Marketplace_Reviews_Settings {
    
   private $settings_config = [
    // Основные настройки
    'marketplace_reviews_admin_notifications' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Notify Admin on New Reviews'
    ],
    'marketplace_reviews_review_trigger_status' => [
        'type' => 'status_select', 'default' => 'wc-completed', 'label' => 'Order Status to Trigger Review'
    ],
    'marketplace_reviews_popup_reminder' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Remind User to Leave Review via Popup'
    ],

    // Интеграции
    'marketplace_reviews_elementor_support' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Elementor Support'
    ],
    'marketplace_reviews_enable_redis' => [
        'type' => 'checkbox', 'default' => 'no', 'label' => 'Enable Redis Cache for Reviews'
    ],

    // Страницы
    'marketplace_reviews_archive_page_id' => [
        'type' => 'page_select', 'default' => 0, 'label' => 'Archive Reviews Page'
    ],
    'marketplace_reviews_product_page_id' => [
        'type' => 'page_select', 'default' => 0, 'label' => 'Product Reviews Page'
    ],

    // Плюсы и минусы
    'marketplace_reviews_enable_pros' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Pros Block'
    ],
    'marketplace_reviews_enable_cons' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Cons Block'
    ],
    'marketplace_reviews_pros_label' => [
        'type' => 'text', 'default' => 'Pros', 'label' => 'Label for Pros Block', 'multilang' => true
    ],
    'marketplace_reviews_cons_label' => [
        'type' => 'text', 'default' => 'Cons', 'label' => 'Label for Cons Block', 'multilang' => true
    ],

    // Вкладки
    'marketplace_reviews_enable_tab' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Product Tab'
    ],
    'marketplace_reviews_tab_title' => [
        'type' => 'text', 'default' => 'Marketplace Reviews', 'label' => 'Tab Title', 'multilang' => true
    ],

    // Рейтинг и звёзды
    'marketplace_reviews_show_average_rating' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Show Average Product Rating'
    ],
    'marketplace_reviews_star_style' => [
        'type' => 'star_style_select', 'default' => 'default', 'label' => 'Star Icon Style'
    ],
    'marketplace_reviews_star_svg' => [
        'type' => 'svg_input', 'default' => '', 'label' => 'Upload SVG for Stars (Paste SVG Code)'
    ],

    // Пагинация
    'marketplace_reviews_shortcode_pagination' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Pagination in Shortcode/Tab'
    ],
    'marketplace_reviews_shortcode_showall_button' => [
        'type' => 'checkbox', 'default' => 'no', 'label' => 'Show "Show All" Button in Shortcode/Tab'
    ],

    // Попап
    'marketplace_reviews_popup_title' => [
        'type' => 'text', 'default' => 'Leave a Review', 'label' => 'Popup Title', 'multilang' => true
    ],
    'marketplace_reviews_popup_button' => [
        'type' => 'text', 'default' => 'Submit Review', 'label' => 'Popup Button Text', 'multilang' => true
    ],
    'marketplace_reviews_popup_button_pul' => [
        'type' => 'text', 'default' => 'Submitting...', 'label' => 'Popup Button Loading Text', 'multilang' => true
    ],
    'marketplace_reviews_popup_rating_label' => [
        'type' => 'text', 'default' => 'Rating', 'label' => 'Popup Rating Label', 'multilang' => true
    ],

    // Фото
    'marketplace_reviews_enable_photos' => [
        'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Photo Uploads'
    ],
    'marketplace_reviews_max_images' => [
        'type' => 'number', 'default' => 5, 'label' => 'Max Images'
    ],
    'marketplace_reviews_upload_label' => [
        'type' => 'text', 'default' => 'Upload Images (optional)', 'label' => 'Upload Block Label', 'multilang' => false
    ],

    // Тексты кнопок и сообщений
    'marketplace_reviews_text_pending' => [
        'type' => 'text', 'default' => 'Review pending moderation', 'label' => 'Text: Review Pending', 'multilang' => true
    ],
    'marketplace_reviews_text_view_review' => [
        'type' => 'text', 'default' => 'View review', 'label' => 'Text: View Review', 'multilang' => true
    ],
    'marketplace_reviews_text_submitted' => [
        'type' => 'text', 'default' => 'Review submitted', 'label' => 'Text: Review Submitted', 'multilang' => true
    ],
    'marketplace_reviews_text_not_completed' => [
        'type' => 'text', 'default' => 'Available after order is completed', 'label' => 'Text: Order Not Completed', 'multilang' => true
    ],
    'marketplace_reviews_text_leave' => [
        'type' => 'text', 'default' => 'Leave a review', 'label' => 'Text: Leave Review', 'multilang' => true
    ],
    'marketplace_reviews_text_already' => [
        'type' => 'text', 'default' => 'Already reviewed', 'label' => 'Text: Already Reviewed', 'multilang' => true
    ],
    'marketplace_reviews_text_security_fail' => [
        'type' => 'text', 'default' => 'Security check failed.', 'label' => 'Text: Security Failed', 'multilang' => true
    ],
    'marketplace_reviews_text_login_required' => [
        'type' => 'text', 'default' => 'You must be logged in to submit a review.', 'label' => 'Text: Login Required', 'multilang' => true
    ],
    'marketplace_reviews_text_cannot_leave' => [
        'type' => 'text', 'default' => 'You cannot leave a review for this product.', 'label' => 'Text: Cannot Leave Review', 'multilang' => true
    ],
    'marketplace_reviews_text_submit_success' => [
        'type' => 'text', 'default' => 'Thank you! Your review has been submitted and is awaiting moderation.', 'label' => 'Text: Review Submitted Successfully', 'multilang' => true
    ],

    // Email notifications
    'marketplace_reviews_email_subject' => [
        'type' => 'text',
        'default' => '[{site_name}] New Product Review: {product}',
        'label' => 'Email Subject Template',
        'multilang' => true,
    ],
    'marketplace_reviews_email_body' => [
        'type' => 'textarea',
        'default' => "A new review has been submitted for {product}.\n\nRating: {rating}/5\nAuthor: {author}\n\nReview: {content}\n\nManage this review: {edit_link}",
        'label' => 'Email Body Template',
        'multilang' => true,
    ],

    // Service (Служебная кнопка для создания страниц)
    'marketplace_reviews_create_pages' => [
        'type' => 'create_pages_button',
        'label' => 'Create Default Pages',
    ],
];

		/**
		 * Конструктор класса
		 */
    
    public function register_settings() {
        // Регистрируем секцию настроек
        add_settings_section(
            'marketplace_reviews_main_section',
            __('Marketplace Reviews Settings', 'marketplace-reviews-for-woocommerce'),
            [$this, 'section_callback'],
            'marketplace_reviews_settings'
        );

        // Регистрируем каждое поле
        foreach ($this->settings_config as $key => $config) {
            // Добавляем опцию с переводом по умолчанию
            $default = isset($config['multilang']) && $config['multilang'] 
                ? __($config['default'], 'marketplace-reviews-for-woocommerce') 
                : $config['default'];
            
            add_option($key, $default);
            register_setting('marketplace_reviews_settings_group', $key);
            
            // Обрабатываем мультиязычные поля
            if (isset($config['multilang']) && $config['multilang']) {
                $this->register_multilang_field($key, $config['label'], $config['type'], $default);
            } else {
                $this->add_settings_field($key, $config);
            }
        }
        add_action('admin_menu', [$this, 'add_settings_menu']);
        add_action('wp_ajax_create_marketplace_page', [$this, 'ajax_create_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
			}

    public function section_callback() {
        echo '<p>' . __('Configure your marketplace reviews settings below.', 'marketplace-reviews-for-woocommerce') . '</p>';
    }

    private function add_settings_field($key, $config) {
        $render_method = 'render_' . $config['type'];
        
        // Проверяем, существует ли метод рендеринга
        if (!method_exists($this, $render_method)) {
            $render_method = 'render_text'; // fallback
        }
        
        add_settings_field(
            $key,
            __($config['label'], 'marketplace-reviews-for-woocommerce'),
            [$this, $render_method],
            'marketplace_reviews_settings',
            'marketplace_reviews_main_section',
            ['label_for' => $key, 'config' => $config]
        );
    }

    public function add_settings_menu() {
        add_submenu_page(
            'marketplace-reviews',
            __('Marketplace Reviews Settings', 'marketplace-reviews-for-woocommerce'),
            __('Settings', 'marketplace-reviews-for-woocommerce'),
            'manage_options',
            'marketplace_reviews_settings',
            [$this, 'settings_page']
        );
    }

    public function settings_page() {
        // Проверяем права доступа
        if (!current_user_can('manage_options')) {
            return;
        }

        // Показываем сообщение об успешном сохранении
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
            add_settings_error(
                'marketplace_reviews_settings',
                'settings_updated',
                __('Settings saved successfully!', 'marketplace-reviews-for-woocommerce'),
                'updated'
            );
        }
        
        // Отображаем сообщения об ошибках/успехе
        settings_errors('marketplace_reviews_settings');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Marketplace Reviews Settings', 'marketplace-reviews-for-woocommerce'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('marketplace_reviews_settings_group');
                do_settings_sections('marketplace_reviews_settings');
                submit_button();
                ?>
            </form>
        </div>
        
        <style>
        .wrap {
            max-width: none;
        }
        .form-table th {
            width: 300px;
            padding: 20px 10px 20px 0;
        }
        .form-table td {
            padding: 20px 10px;
        }
        .form-table input[type="text"],
        .form-table textarea {
            width: 100%;
            max-width: 500px;
        }
        .form-table select {
            min-width: 200px;
        }
        .page-select-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .page-status {
            font-weight: bold;
        }
        .page-status.success {
            color: #46b450;
        }
        .page-status.error {
            color: #dc3232;
        }
        .create-page-btn {
            white-space: nowrap;
        }
        .create-page-btn:disabled {
            opacity: 0.6;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('.create-page-btn').on('click', function() {
                var $btn = $(this);
                var field = $btn.data('field');
                var pageType = $btn.data('type');
                var pageTitle = $btn.data('title');
                var pageContent = $btn.data('content');
                
                $btn.prop('disabled', true).text('<?php echo esc_js(__('Creating...', 'marketplace-reviews-for-woocommerce')); ?>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'create_marketplace_page',
                        field: field,
                        page_type: pageType,
                        page_title: pageTitle,
                        page_content: pageContent,
                        nonce: '<?php echo wp_create_nonce('create_marketplace_page'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Обновляем dropdown
                            var $select = $('#' + field);
                            $select.append('<option value="' + response.data.page_id + '">' + response.data.page_title + '</option>');
                            $select.val(response.data.page_id);
                            
                            // Показываем сообщение об успехе
                            $btn.closest('.page-select-wrapper').append(
                                '<span class="page-status success">✅ <?php echo esc_js(__('Page created successfully!', 'marketplace-reviews-for-woocommerce')); ?></span>'
                            );
                            
                            // Перезагружаем страницу для обновления интерфейса
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            alert('<?php echo esc_js(__('Error creating page:', 'marketplace-reviews-for-woocommerce')); ?> ' + response.data);
                        }
                    },
                    error: function() {
                        alert('<?php echo esc_js(__('AJAX error occurred', 'marketplace-reviews-for-woocommerce')); ?>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('<?php echo esc_js(__('Create Page', 'marketplace-reviews-for-woocommerce')); ?>');
                    }
                });
            });
        });
        </script>
        <?php
    }

    // Методы рендеринга полей
    public function render_checkbox($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? 'no');
        printf(
            '<input type="checkbox" id="%s" name="%s" value="yes" %s />',
            esc_attr($args['label_for']),
            esc_attr($args['label_for']),
            checked('yes', $option, false)
        );
    }

    public function render_text($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? '');
        printf(
            '<input type="text" id="%s" name="%s" value="%s" class="regular-text" />',
            esc_attr($args['label_for']),
            esc_attr($args['label_for']),
            esc_attr($option)
        );
    }

    public function render_number($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? 5);
        printf(
            '<input type="number" min="1" max="20" id="%s" name="%s" value="%s" class="small-text" />',
            esc_attr($args['label_for']),
            esc_attr($args['label_for']),
            esc_attr($option)
        );
    }

    public function render_status_select($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? 'wc-completed');
        $statuses = function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : [
            'wc-pending' => 'Pending payment',
            'wc-processing' => 'Processing',
            'wc-on-hold' => 'On hold',
            'wc-completed' => 'Completed',
            'wc-cancelled' => 'Cancelled',
            'wc-refunded' => 'Refunded',
            'wc-failed' => 'Failed'
        ];
        
        printf('<select id="%s" name="%s">', esc_attr($args['label_for']), esc_attr($args['label_for']));
        foreach ($statuses as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($option, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    public function render_page_select($args) {
        $selected = get_option($args['label_for'], $args['config']['default'] ?? 0);
        $field_id = $args['label_for'];
        
        // Определяем тип страницы для создания
        $page_type = '';
        $page_title = '';
        $page_content = '';
        $shortcode = '';
        
        if (strpos($field_id, 'archive_page') !== false) {
            $page_type = 'archive';
            $page_title = __('Reviews Archive', 'marketplace-reviews-for-woocommerce');
            $shortcode = '[marketplace_reviews_archive]';
            $page_content = __('This page displays all reviews.', 'marketplace-reviews-for-woocommerce') . "\n\n" . $shortcode;
        } elseif (strpos($field_id, 'product_page') !== false) {
            $page_type = 'product';
            $page_title = __('Product Reviews', 'marketplace-reviews-for-woocommerce');
            $shortcode = '[marketplace_reviews_product]';
            $page_content = __('This page displays product reviews.', 'marketplace-reviews-for-woocommerce') . "\n\n" . $shortcode;
        }
        
        echo '<div class="page-select-wrapper">';
        
        // Dropdown для выбора страницы
        wp_dropdown_pages([
            'name' => esc_attr($args['label_for']),
            'id' => esc_attr($args['label_for']),
            'selected' => $selected,
            'show_option_none' => __('— Select —', 'marketplace-reviews-for-woocommerce')
        ]);
        
        // Кнопка создания страницы
        if ($page_type) {
            printf(
                ' <button type="button" class="button button-secondary create-page-btn" data-field="%s" data-type="%s" data-title="%s" data-content="%s">%s</button>',
                esc_attr($field_id),
                esc_attr($page_type),
                esc_attr($page_title),
                esc_attr($page_content),
                __('Create Page', 'marketplace-reviews-for-woocommerce')
            );
        }
        
        // Проверка статуса страницы
        if ($selected > 0) {
            $page = get_post($selected);
            if (!$page || $page->post_status !== 'publish') {
                echo ' <span class="page-status error">❌ ' . __('Page not found or not published', 'marketplace-reviews-for-woocommerce') . '</span>';
            } else {
                echo ' <span class="page-status success">✅ ' . __('Page exists', 'marketplace-reviews-for-woocommerce') . '</span>';
                printf(' <a href="%s" target="_blank" class="button button-small">%s</a>', 
                    get_permalink($selected), 
                    __('View Page', 'marketplace-reviews-for-woocommerce')
                );
                printf(' <a href="%s" target="_blank" class="button button-small">%s</a>', 
                    get_edit_post_link($selected), 
                    __('Edit Page', 'marketplace-reviews-for-woocommerce')
                );
            }
        }
        
        echo '</div>';
    }

    public function render_star_style_select($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? 'default');
        $options = [
            'default' => __('Default Star (★)', 'marketplace-reviews-for-woocommerce'),
            'font' => __('Font Awesome', 'marketplace-reviews-for-woocommerce'),
            'svg' => __('Custom SVG', 'marketplace-reviews-for-woocommerce')
        ];
        
        printf('<select id="%s" name="%s">', esc_attr($args['label_for']), esc_attr($args['label_for']));
        foreach ($options as $value => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($value),
                selected($option, $value, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    public function render_svg_input($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? '');
        printf(
            '<textarea id="%s" name="%s" rows="4" cols="60" class="large-text code">%s</textarea>',
            esc_attr($args['label_for']),
            esc_attr($args['label_for']),
            esc_textarea($option)
        );
        printf('<p class="description">%s</p>', __('Paste your SVG code here for custom stars.', 'marketplace-reviews-for-woocommerce'));
    }

    // Мультиязычные поля
    public function register_multilang_field($base_key, $label, $type = 'text', $default = '') {
        $languages = $this->get_available_languages();

        foreach ($languages as $lang_code => $lang) {
            $field_key = ($lang_code === 'default') ? $base_key : "{$base_key}_{$lang_code}";
            add_option($field_key, $default);
            register_setting('marketplace_reviews_settings_group', $field_key);

            add_settings_field(
                $field_key,
                sprintf('%s (%s)', __($label, 'marketplace-reviews-for-woocommerce'), $lang['name']),
                [$this, 'render_multilang_field'],
                'marketplace_reviews_settings',
                'marketplace_reviews_main_section',
                ['label_for' => $field_key, 'type' => $type, 'default' => $default]
            );
        }
    }

    public function render_multilang_field($args) {
        $option = get_option($args['label_for'], $args['default']);
        
        switch ($args['type']) {
            case 'text':
                printf(
                    '<input type="text" id="%s" name="%s" value="%s" class="regular-text" />',
                    esc_attr($args['label_for']),
                    esc_attr($args['label_for']),
                    esc_attr($option)
                );
                break;
            case 'textarea':
                printf(
                    '<textarea id="%s" name="%s" rows="3" class="large-text">%s</textarea>',
                    esc_attr($args['label_for']),
                    esc_attr($args['label_for']),
                    esc_textarea($option)
                );
                break;
        }
    }

    private function get_available_languages() {
        if (function_exists('pll_the_languages')) {
            $languages = pll_the_languages(['raw' => 1]);
            return !empty($languages) ? $languages : ['default' => ['name' => __('Default', 'marketplace-reviews-for-woocommerce')]];
        }
        
        return ['default' => ['name' => __('Default', 'marketplace-reviews-for-woocommerce')]];
    }

    public static function get_option_lang($base_key, $default = '') {
        $lang = function_exists('pll_current_language') ? pll_current_language() : 'default';
        $field_key = ($lang === 'default') ? $base_key : "{$base_key}_{$lang}";
        return get_option($field_key, $default);
    }

    /**
     * Подключаем скрипты для админки
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'marketplace-reviews_page_marketplace_reviews_settings') {
            return;
        }
        
        wp_enqueue_script('jquery');
    }
    
    /**
     * AJAX обработчик для создания страниц
     */
    public function ajax_create_page() {
        // Проверяем nonce
        if (!wp_verify_nonce($_POST['nonce'], 'create_marketplace_page')) {
            wp_die(__('Security check failed', 'marketplace-reviews-for-woocommerce'));
        }
        
        // Проверяем права
        if (!current_user_can('manage_options')) {
            wp_die(__('Access denied', 'marketplace-reviews-for-woocommerce'));
        }
        
        $field = sanitize_text_field($_POST['field']);
        $page_type = sanitize_text_field($_POST['page_type']);
        $page_title = sanitize_text_field($_POST['page_title']);
        $page_content = wp_kses_post($_POST['page_content']);
        
        // Создаем страницу
        $page_id = wp_insert_post([
            'post_title' => $page_title,
            'post_content' => $page_content,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => get_current_user_id(),
            'meta_input' => [
                '_marketplace_reviews_page_type' => $page_type
            ]
        ]);
        
        if (is_wp_error($page_id)) {
            wp_send_json_error($page_id->get_error_message());
        }
        
        // Сохраняем ID страницы в настройки
        update_option($field, $page_id);
        
        wp_send_json_success([
            'page_id' => $page_id,
            'page_title' => $page_title,
            'page_url' => get_permalink($page_id)
        ]);
    }
    
    /**
     * Проверяет существование страницы и её статус
     */
    private function check_page_status($page_id) {
        if (!$page_id) {
            return ['status' => 'not_selected', 'message' => __('No page selected', 'marketplace-reviews-for-woocommerce')];
        }
        
        $page = get_post($page_id);
        
        if (!$page) {
            return ['status' => 'not_found', 'message' => __('Page not found', 'marketplace-reviews-for-woocommerce')];
        }
        
        if ($page->post_status !== 'publish') {
            return ['status' => 'not_published', 'message' => __('Page exists but not published', 'marketplace-reviews-for-woocommerce')];
        }
        
        return ['status' => 'ok', 'message' => __('Page exists and published', 'marketplace-reviews-for-woocommerce')];
    }
}
