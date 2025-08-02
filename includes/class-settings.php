<?php
/**
 * Marketplace Reviews Settings Class
 *
 * Handles the settings for the Marketplace Reviews plugin.
 *
 * @package MarketplaceReviews
 */
class Marketplace_Reviews_Settings {

public function __construct() {
	$config = require __DIR__ . '/settings-config.php';
	$this->settings_config = $config['settings_config'];
	$this->tabs = $config['tabs'];
}
public function register_settings() {
    // Сначала обработаем hide_fields для чекбоксов
    foreach ($this->settings_config as $key => &$config) {
        if (
            isset($config['type']) &&
            $config['type'] === 'checkbox' &&
            isset($config['hide_fields']) &&
            is_array($config['hide_fields'])
        ) {
            foreach ($config['hide_fields'] as $field_to_hide) {
                if (isset($this->settings_config[$field_to_hide])) {
                    // Добавляем show_if в скрываемое поле
                    $this->settings_config[$field_to_hide]['show_if'][] = [
                        'field' => $key,
                        'value' => 'yes'
                    ];
                }
            }
        }
    }
    unset($config);

    // Регистрируем секции для каждой вкладки
    foreach ($this->tabs as $tab_key => $tab_data) {
        add_settings_section(
            'marketplace_reviews_' . $tab_key . '_section',
            $tab_data['title'],
            [$this, 'section_callback'],
            'marketplace_reviews_settings_' . $tab_key
        );
    }

    // Регистрируем каждое поле
    foreach ($this->settings_config as $key => $config) {
        // --- Исправлено: безопасно получаем default ---
        $default = '';
        if (isset($config['default'])) {
            $default = isset($config['multilang']) && $config['multilang']
                ? __($config['default'], 'marketplace-reviews-for-woocommerce')
                : $config['default'];
        }
        add_option($key, $default);

        // --- изменено: регистрируем в группе по табу ---
        $group = 'marketplace_reviews_settings_group_' . $config['tab'];
        register_setting($group, $key);

        if (isset($config['multilang']) && $config['multilang']) {
            $this->register_multilang_field($key, $config['label'], $config['type'], $default, $config['tab'], $group);
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
        if (!method_exists($this, $render_method)) {
            $render_method = 'render_text';
        }
        $tab = isset($config['tab']) ? $config['tab'] : 'general';

        $args = ['label_for' => $key, 'config' => $config];
        $custom_data = '';

        // Универсальная обработка зависимостей через show_if (поддержка массива условий)
        if (isset($config['show_if'])) {
            $showIf = is_array($config['show_if'][0] ?? null) ? $config['show_if'] : [$config['show_if']];
            foreach ($showIf as $dep) {
                $custom_data .= ' data-mp-toggle="' . esc_attr($dep['field']) . '" data-mp-toggle-value="' . esc_attr($dep['value']) . '"';
            }
        } elseif (isset($this->field_dependencies[$key])) {
            // Для обратной совместимости
            $dep = $this->field_dependencies[$key];
            $custom_data = ' data-mp-toggle="' . esc_attr($dep['toggle']) . '" data-mp-toggle-value="' . esc_attr($dep['value']) . '"';
        }

        add_settings_field(
            $key,
            __($config['label'], 'marketplace-reviews-for-woocommerce'),
            function($args) use ($render_method, $custom_data) {
                echo '<div' . $custom_data . '>';
                $this->{$render_method}($args);
                echo '</div>';
            },
            'marketplace_reviews_settings_' . $tab,
            'marketplace_reviews_' . $tab . '_section',
            $args
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

        // Получаем активную вкладку
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        if (!array_key_exists($active_tab, $this->tabs)) {
            $active_tab = 'general';
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

        // Подключаем шаблон страницы настроек (в котором уже подключаются стили и скрипты)
        $active_tab = esc_attr($active_tab);
        include dirname(__DIR__) . '/admin/views/settings.php';
    }

    // Методы рендеринга полей (остаются без изменений)
    public function render_checkbox($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? 'no');
        printf(
            '<input  type="checkbox" id="%s" name="%s" value="yes" %s />',
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

    public function render_textarea($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? '');
        printf(
            '<textarea id="%s" name="%s" rows="4" class="large-text">%s</textarea>',
            esc_attr($args['label_for']),
            esc_attr($args['label_for']),
            esc_textarea($option)
        );
    }
    public function render_number($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? 1);
        printf(
            '<input type="number" min="1" max="100" id="%s" name="%s" value="%s" class="small-text" />',
            esc_attr($args['label_for']),
            esc_attr($args['label_for']),
            esc_attr($option)
        );
    }

    public function render_status_select($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? 'wc-completed');
        $statuses = function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : [];
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
        echo '<div class="page-select-wrapper">';
        wp_dropdown_pages([
            'name' => esc_attr($args['label_for']),
            'id' => esc_attr($args['label_for']),
            'selected' => $selected,
            'show_option_none' => __('— Select —', 'marketplace-reviews-for-woocommerce')
        ]);
        // Кнопка для быстрого создания страницы через AJAX
        printf(
            '<button type="button" class="create-page-btn button-small" 
                data-field="%s" 
                data-type="%s"
                data-title="%s"
                data-content="%s">%s</button>',
            esc_attr($args['label_for']),
            esc_attr($args['label_for']), // Тип совпадает с ключом
            esc_attr($args['config']['label']),
            '', // content можно доработать по необходимости
            esc_html__('Create Page', 'marketplace-reviews-for-woocommerce')
        );
        echo '</div>';
    }

    public function render_star_style_select($args) {
        $option = get_option($args['label_for'], $args['config']['default'] ?? 'default');
        $options = [
            'default' => __('Default Star (★)', 'marketplace-reviews-for-woocommerce'),
            'font'    => __('Font Awesome', 'marketplace-reviews-for-woocommerce'),
            'svg'     => __('Custom SVG', 'marketplace-reviews-for-woocommerce')
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
	public function register_multilang_field($base_key, $label, $type = 'text', $default = '', $tab = 'general', $group = '') {
		$languages = $this->get_available_languages();
		foreach ($languages as $lang_code => $lang) {
			$field_key = ($lang_code === 'default') ? $base_key : "{$base_key}_{$lang_code}";
			$register_group = $group ?: 'marketplace_reviews_settings_group_' . $tab;
			register_setting($register_group, $field_key);

			// Универсальная обработка зависимостей
			$custom_data = '';
			if (isset($this->settings_config[$base_key]['show_if'])) {
				$showIf = is_array($this->settings_config[$base_key]['show_if'][0] ?? null)
					? $this->settings_config[$base_key]['show_if']
					: [$this->settings_config[$base_key]['show_if']];
				foreach ($showIf as $dep) {
					$custom_data .= ' data-mp-toggle="' . esc_attr($dep['field']) . '" data-mp-toggle-value="' . esc_attr($dep['value']) . '"';
				}
			} elseif (isset($this->field_dependencies[$base_key])) {
				$dep = $this->field_dependencies[$base_key];
				$custom_data = ' data-mp-toggle="' . esc_attr($dep['toggle']) . '" data-mp-toggle-value="' . esc_attr($dep['value']) . '"';
			}

			add_settings_field(
				$field_key,
				sprintf('%s (%s)', __($label, 'marketplace-reviews-for-woocommerce'), $lang['name']),
				function() use ($field_key, $type, $default, $custom_data) {
					echo '<div' . $custom_data . '>';
					$this->render_multilang_field($field_key, $type, $default);
					echo '</div>';
				},
				'marketplace_reviews_settings_' . $tab,
				'marketplace_reviews_' . $tab . '_section'
			);
		}
	}

    private function render_multilang_field($field_key, $type, $default) {
        $option = get_option($field_key, $default);
        switch ($type) {
            case 'text':
                printf(
                    '<input type="text" id="%s" name="%s" value="%s" class="regular-text" />',
                    esc_attr($field_key),
                    esc_attr($field_key),
                    esc_attr($option)
                );
                break;
            case 'textarea':
                printf(
                    '<textarea id="%s" name="%s" rows="3" class="large-text">%s</textarea>',
                    esc_attr($field_key),
                    esc_attr($field_key),
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

    // ----- AJAX создание страниц -----
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_marketplace_reviews' && strpos($hook, 'marketplace_reviews_settings') === false) {
            return;
        }
        wp_enqueue_script('jquery');
    }

    public function ajax_create_page() {
        check_ajax_referer('create_marketplace_page', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No permission');
        }

        $title = sanitize_text_field($_POST['page_title']);
        $content = wp_kses_post($_POST['page_content']);
        $field = sanitize_text_field($_POST['field']);

        $page_id = wp_insert_post([
            'post_title'   => $title,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => $content,
        ]);

        if (!is_wp_error($page_id)) {
            update_option($field, $page_id);
            wp_send_json_success(['page_id' => $page_id, 'page_title' => $title]);
        } else {
            wp_send_json_error($page_id->get_error_message());
        }
    }

    /**
     * Получить опцию с поддержкой мультиязычности (Polylang)
     * @param string $key Ключ опции
     * @param mixed $default Значение по умолчанию
     * @param string|null $lang Язык (например, 'en', 'ru'). Если не указан — текущий.
     * @return mixed
     */
    public static function get_option_lang($key, $default = '', $lang = null) {
        // Если Polylang не установлен — вернуть обычную опцию
        if (!function_exists('pll_current_language')) {
            $val = get_option($key, $default);
            return $val !== '' ? $val : $default;
        }

        // Определяем язык
        if ($lang === null) {
            $lang = function_exists('pll_current_language') ? pll_current_language() : 'default';
        }
        if ($lang === 'default') {
            $option_key = $key;
        } else {
            $option_key = "{$key}_{$lang}";
        }

        $val = get_option($option_key, '');
        if ($val !== '') {
            return $val;
        }
        // fallback: если нет перевода — вернуть дефолт или обычную опцию
        $val = get_option($key, $default);
        return $val !== '' ? $val : $default;
    }
}

