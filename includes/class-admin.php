<?php
/**
 * Admin interface for Marketplace Reviews
 *
 * @package    MarketplaceReviews
 * @subpackage MarketplaceReviews/includes
 */

class Marketplace_Reviews_Admin {
     private $settings;
     private $pluse;
     private $minus;

    public function __construct($settings) {
        $this->settings = $settings;
        $this->pluse = get_option('marketplace_reviews_enable_pros', 'yes') === 'yes';
        $this->minus = get_option('marketplace_reviews_enable_cons', 'yes') === 'yes';

        add_action('wp_ajax_mp_load_more_products', [$this, 'ajax_load_more_products']);

        // Фильтр по статусу модерации
        add_action('restrict_manage_posts', [$this, 'add_moderation_status_filter']);
        add_filter('parse_query', [$this, 'filter_by_moderation_status']);

        // Фильтр по рейтингу
        add_action('restrict_manage_posts', [$this, 'add_rating_filter']);
        add_filter('parse_query', [$this, 'filter_by_rating']);

        // Фильтр по продукту
        add_action('restrict_manage_posts', [$this, 'add_product_filter']);
        add_filter('parse_query', [$this, 'filter_by_product']);

        // Показать счетчики по статусам модерации, рейтингу и продукту
        add_filter('views_edit-marketplace_review', [$this, 'add_moderation_status_views']);
        add_filter('views_edit-marketplace_review', [$this, 'add_rating_views']);
        add_filter('views_edit-marketplace_review', [$this, 'add_product_views']);

        // Групповое действие для смены статуса
        add_filter('bulk_actions-edit-marketplace_review', [$this, 'register_bulk_moderation_actions']);
        add_filter('handle_bulk_actions-edit-marketplace_review', [$this, 'handle_bulk_moderation_actions'], 10, 3);
        add_action('admin_notices', [$this, 'bulk_action_admin_notice']);

        add_filter('post_row_actions', [$this, 'add_admin_reply_action_link'], 10, 2);
        add_action('admin_footer', [$this, 'admin_reply_popup_html']);
        add_action('wp_ajax_mp_save_admin_reply', [$this, 'ajax_save_admin_reply']);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Marketplace Reviews', 'marketplace-reviews-for-woocommerce'),
            // Добавляем количество непроверенных отзывов к названию меню
            __('Reviews', 'marketplace-reviews-for-woocommerce') .
                ($this->get_pending_reviews_count() > 0
                    ? ' <span class="awaiting-mod count">' . intval($this->get_pending_reviews_count()) . '</span>'
                    : ''),
            'manage_options',
            'marketplace-reviews',
            '', // callback is empty, handled by submenus
            'dashicons-star-filled'
        );

        // Подсчёт количества непроверенных отзывов
        $pending_count = $this->get_pending_reviews_count();
        $all_reviews_label = __('All Reviews', 'marketplace-reviews-for-woocommerce');
        if ($pending_count > 0) {
            $all_reviews_label .= sprintf(
                ' <span class="awaiting-mod count">%d</span>',
                intval($pending_count)
            );
        }

        add_submenu_page(
            'marketplace-reviews',
            __('All Reviews', 'marketplace-reviews-for-woocommerce'),
            $all_reviews_label,
            'manage_options',
            'edit.php?post_type=marketplace_review'
        );

        // Добавляем подменю для таксономий
        add_submenu_page(
            'marketplace-reviews',
            __('Ratings', 'marketplace-reviews-for-woocommerce'),
            __('Ratings', 'marketplace-reviews-for-woocommerce'),
            'manage_options',
            'edit-tags.php?taxonomy=review_rating&post_type=marketplace_review',
            ''
        );

        add_submenu_page(
            'marketplace-reviews',
            __('Products', 'marketplace-reviews-for-woocommerce'),
            __('Products', 'marketplace-reviews-for-woocommerce'),
            'manage_options',
            'edit-tags.php?taxonomy=review_product&post_type=marketplace_review',
            ''
        );

        add_submenu_page(
            'marketplace-reviews',
            __('Settings', 'marketplace-reviews-for-woocommerce'),
            __('Settings', 'marketplace-reviews-for-woocommerce'),
            'manage_options',
            'marketplace_reviews_settings',
            [$this->settings, 'settings_page']
        );
    }

    public function enqueue_styles() {
        wp_enqueue_style('marketplace-reviews-admin', MARKETPLACE_REVIEWS_PLUGIN_URL . 'admin/css/admin.css', [], MARKETPLACE_REVIEWS_VERSION);
    }

    public function enqueue_scripts() {
        wp_enqueue_script('marketplace-reviews-admin', MARKETPLACE_REVIEWS_PLUGIN_URL . 'admin/js/admin.js', ['jquery'], MARKETPLACE_REVIEWS_VERSION, true);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'marketplace_review_meta',
            __('Review Details', 'marketplace-reviews-for-woocommerce'),
            [$this, 'render_meta_box'],
            'marketplace_review',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        $rating = get_post_meta($post->ID, 'review_rating', true);
        $pros = get_post_meta($post->ID, 'review_pros', true);
        $cons = get_post_meta($post->ID, 'review_cons', true);
        $author = get_post_meta($post->ID, 'review_author', true);
        $moderation_status = get_post_meta($post->ID, 'moderation_status', true);
        $product_id = get_post_meta($post->ID, 'product_id', true);
        include MARKETPLACE_REVIEWS_PLUGIN_DIR . 'admin/views/review-meta-fields.php';
    }

    public function save_review_meta($post_id, $post) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if ($post->post_type !== 'marketplace_review') return;
        if (!current_user_can('edit_post', $post_id)) return;

        if (isset($_POST['review_author'])) {
            update_post_meta($post_id, 'review_author', sanitize_text_field($_POST['review_author']));
        }
				if (isset($_POST['product_id'])) {
					$product_ids = $_POST['product_id'];
					if (!is_array($product_ids)) {
						$product_ids = [$product_ids];
					}
					$product_ids = array_map('intval', $product_ids);
					update_post_meta($post_id, 'product_id', $product_ids);

					// Обновляем таксономию review_product по названию продукта
					$term_ids = [];
					foreach ($product_ids as $product_id) {
						if ($product_id > 0) {
							$product = get_post($product_id);
							if ($product && $product->post_type === 'product') {
								$product_title = $product->post_title;
								$term = get_term_by('name', $product_title, 'review_product');
								if (!$term) {
									// Создаём термин, если не существует
									$term_result = wp_insert_term($product_title, 'review_product');
									if (!is_wp_error($term_result) && isset($term_result['term_id'])) {
										$term_ids[] = $term_result['term_id'];
									}
								} else {
									$term_ids[] = $term->term_id;
								}
							}
						}
					}
					if (!empty($term_ids)) {
						wp_set_object_terms($post_id, $term_ids, 'review_product', false);
					}
				}
				if (isset($_POST['review_rating'])) {
            $review_rating = intval($_POST['review_rating']);
            update_post_meta($post_id, 'review_rating', $review_rating);

            // Обновляем таксономию review_rating
            if ($review_rating > 0) {
                $term = get_term_by('name', (string)$review_rating, 'review_rating');
                if (!$term) {
                    // Создаём термин, если не существует
                    $term_result = wp_insert_term((string)$review_rating, 'review_rating');
                    if (!is_wp_error($term_result) && isset($term_result['term_id'])) {
                        $term_id = $term_result['term_id'];
                    } else {
                        $term_id = 0;
                    }
                } else {
                    $term_id = $term->term_id;
                }
                if ($term_id) {
                    wp_set_object_terms($post_id, [$term_id], 'review_rating', false);
                }
            }
        }
        if (isset($_POST['review_pros'])) {
            update_post_meta($post_id, 'review_pros', sanitize_textarea_field($_POST['review_pros']));
        }
        if (isset($_POST['review_cons'])) {
            update_post_meta($post_id, 'review_cons', sanitize_textarea_field($_POST['review_cons']));
        }
        if (isset($_POST['moderation_status'])) {
            update_post_meta($post_id, 'moderation_status', sanitize_text_field($_POST['moderation_status']));
        }
    }

    public function set_custom_columns($columns) {
        $columns['moderation_status'] = __('Moderation', 'marketplace-reviews-for-woocommerce');
        $columns['admin_reply'] = __('Ответить', 'marketplace-reviews-for-woocommerce'); // Новая колонка
        return $columns;
    }

    public function custom_column($column, $post_id) {
        if ($column === 'moderation_status') {
            echo esc_html(get_post_meta($post_id, 'moderation_status', true));
        }
        if ($column === 'admin_reply') {
            // Кнопка "Ответить на отзыв" прямо в колонке
            echo '<a href="#" class="mp-admin-reply-link-row button" data-review-id="' . esc_attr($post_id) . '">' . esc_html__('Ответить', 'marketplace-reviews-for-woocommerce') . '</a>';
        }
    }

    public function sortable_columns($columns) {
        $columns['moderation_status'] = 'moderation_status';
        return $columns;
    }

    public function review_status_changed($new_status, $old_status, $post) {
        if ($post->post_type !== 'marketplace_review') return;
        if ($new_status === 'publish' && $old_status !== 'publish') {
            // Optional: notify admin or log event
        }
    }

    // AJAX handler for loading more products
    public function ajax_load_more_products() {
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error();
        }
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 30;
        $args = [
            'limit' => $limit,
            'offset' => $offset,
            'orderby' => 'title',
            'order' => 'ASC',
            'status' => array('publish'),
            'return' => 'objects',
        ];
        $products = function_exists('wc_get_products') ? wc_get_products($args) : [];
        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
            ];
        }
        wp_send_json_success($result);
    }

    // Фильтр по статусу модерации
    public function add_moderation_status_filter($post_type) {
        if ($post_type !== 'marketplace_review') return;
        $selected = isset($_GET['moderation_status']) ? $_GET['moderation_status'] : '';
        $options = [
            '' => __('All Moderation Statuses', 'marketplace-reviews-for-woocommerce'),
            'pending' => __('Pending', 'marketplace-reviews-for-woocommerce'),
            'approved' => __('Approved', 'marketplace-reviews-for-woocommerce'),
            'rejected' => __('Rejected', 'marketplace-reviews-for-woocommerce'),
        ];
        echo '<select name="moderation_status">';
        foreach ($options as $value => $label) {
            printf('<option value="%s"%s>%s</option>', esc_attr($value), selected($selected, $value, false), esc_html($label));
        }
        echo '</select>';
    }

    public function filter_by_moderation_status($query) {
        global $pagenow;
        if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'marketplace_review' && isset($_GET['moderation_status']) && $_GET['moderation_status'] !== '') {
            $meta_query = $query->get('meta_query') ?: [];
            $meta_query[] = [
                'key' => 'moderation_status',
                'value' => sanitize_text_field($_GET['moderation_status']),
                'compare' => '=',
            ];
            $query->set('meta_query', $meta_query);
        }
    }

    // Фильтр по рейтингу
    public function add_rating_filter($post_type) {
        if ($post_type !== 'marketplace_review') return;
        $selected = isset($_GET['review_rating_filter']) ? $_GET['review_rating_filter'] : '';
        // Получаем все рейтинги (таксономия review_rating)
        $terms = get_terms([
            'taxonomy' => 'review_rating',
            'hide_empty' => false,
        ]);
        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<select name="review_rating_filter">';
            echo '<option value="">' . esc_html__('Все рейтинги', 'marketplace-reviews-for-woocommerce') . '</option>';
            foreach ($terms as $term) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($term->term_id),
                    selected($selected, $term->term_id, false),
                    esc_html($term->name)
                );
            }
            echo '</select>';
        }
    }

    public function filter_by_rating($query) {
        global $pagenow;
        if ($pagenow === 'edit.php'
            && isset($_GET['post_type']) && $_GET['post_type'] === 'marketplace_review'
            && !empty($_GET['review_rating_filter'])
        ) {
            $query->set('tax_query', array_merge(
                (array)$query->get('tax_query'),
                [[
                    'taxonomy' => 'review_rating',
                    'field' => 'term_id',
                    'terms' => intval($_GET['review_rating_filter']),
                ]]
            ));
        }
    }

    // Фильтр по продукту
    public function add_product_filter($post_type) {
        if ($post_type !== 'marketplace_review') return;
        $selected = isset($_GET['review_product_filter']) ? $_GET['review_product_filter'] : '';
        $terms = get_terms([
            'taxonomy' => 'review_product',
            'hide_empty' => false,
        ]);
        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<select name="review_product_filter">';
            echo '<option value="">' . esc_html__('Все товары', 'marketplace-reviews-for-woocommerce') . '</option>';
            foreach ($terms as $term) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($term->term_id),
                    selected($selected, $term->term_id, false),
                    esc_html($term->name)
                );
            }
            echo '</select>';
        }
    }

    public function filter_by_product($query) {
        global $pagenow;
        if ($pagenow === 'edit.php'
            && isset($_GET['post_type']) && $_GET['post_type'] === 'marketplace_review'
            && !empty($_GET['review_product_filter'])
        ) {
            $query->set('tax_query', array_merge(
                (array)$query->get('tax_query'),
                [[
                    'taxonomy' => 'review_product',
                    'field' => 'term_id',
                    'terms' => intval($_GET['review_product_filter']),
                ]]
            ));
        }
    }

    // Групповые действия для смены статуса модерации
    public function register_bulk_moderation_actions($bulk_actions) {
        $bulk_actions['set_moderation_pending'] = __('Set Moderation: Pending', 'marketplace-reviews-for-woocommerce');
        $bulk_actions['set_moderation_approved'] = __('Set Moderation: Approved', 'marketplace-reviews-for-woocommerce');
        $bulk_actions['set_moderation_rejected'] = __('Set Moderation: Rejected', 'marketplace-reviews-for-woocommerce');
        return $bulk_actions;
    }

    public function handle_bulk_moderation_actions($redirect_to, $doaction, $post_ids) {
        $status = false;
        if ($doaction === 'set_moderation_pending') $status = 'pending';
        if ($doaction === 'set_moderation_approved') $status = 'approved';
        if ($doaction === 'set_moderation_rejected') $status = 'rejected';

        if ($status) {
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, 'moderation_status', $status);
            }
            $redirect_to = add_query_arg([
                'bulk_moderation_status' => $status,
                'changed' => count($post_ids)
            ], $redirect_to);
        }
        return $redirect_to;
    }

    public function bulk_action_admin_notice() {
        if (!empty($_REQUEST['bulk_moderation_status']) && !empty($_REQUEST['changed'])) {
            $status = sanitize_text_field($_REQUEST['bulk_moderation_status']);
            $count = intval($_REQUEST['changed']);
            printf(
                '<div id="message" class="updated notice is-dismissible"><p>%s</p></div>',
                sprintf(
                    _n('%d review status set to "%s".', '%d reviews status set to "%s".', $count, 'marketplace-reviews-for-woocommerce'),
                    $count,
                    esc_html(ucfirst($status))
                )
            );
        }
    }

    // Счетчики по статусу модерации
    public function add_moderation_status_views($views) {
        global $wpdb;
        $base_url = admin_url('edit.php?post_type=marketplace_review');
        $statuses = [
            'all'      => __('All', 'marketplace-reviews-for-woocommerce'),
            'pending'  => __('Pending', 'marketplace-reviews-for-woocommerce'),
            'approved' => __('Approved', 'marketplace-reviews-for-woocommerce'),
            'rejected' => __('Rejected', 'marketplace-reviews-for-woocommerce'),
        ];
        $counts = [];
        // Получаем количество для каждого статуса
        foreach (['pending', 'approved', 'rejected'] as $status) {
            $counts[$status] = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->postmeta pm
                 INNER JOIN $wpdb->posts p ON pm.post_id = p.ID
                 WHERE pm.meta_key = 'moderation_status' AND pm.meta_value = %s AND p.post_type = 'marketplace_review' AND p.post_status IN ('publish','pending','draft')",
                $status
            ));
        }
        // Всего
        $counts['all'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'marketplace_review' AND post_status IN ('publish','pending','draft')"
        );

        // Формируем ссылки
        $current = isset($_GET['moderation_status']) ? $_GET['moderation_status'] : '';
        $new_views = [];
        foreach ($statuses as $key => $label) {
            $url = $base_url;
            if ($key !== 'all') {
                $url = add_query_arg('moderation_status', $key, $url);
            }
            $class = ($current === $key || ($key === 'all' && $current === '')) ? 'class="current"' : '';
            $count = intval($counts[$key]);
            $new_views[$key] = "<a href='" . esc_url($url) . "' $class>" . esc_html($label) . " <span class='count'>($count)</span></a>";
        }
        return array_merge($new_views, $views);
    }

    // Счетчики по рейтингу
    public function add_rating_views($views) {
        $taxonomy = 'review_rating';
        $screen = get_current_screen();
        if ($screen->post_type !== 'marketplace_review') return $views;

        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);
        $current = isset($_GET['review_rating_filter']) ? intval($_GET['review_rating_filter']) : 0;
        $base_url = admin_url('edit.php?post_type=marketplace_review');
        foreach ($terms as $term) {
            $url = add_query_arg('review_rating_filter', $term->term_id, $base_url);
            $class = ($current === $term->term_id) ? 'class="current"' : '';
            $views['rating_' . $term->term_id] = "<a href='" . esc_url($url) . "' $class>" . esc_html($term->name) . " <span class='count'>($term->count)</span></a>";
        }
        return $views;
    }

    // Счетчики по продукту
    public function add_product_views($views) {
        $taxonomy = 'review_product';
        $screen = get_current_screen();
        if ($screen->post_type !== 'marketplace_review') return $views;

        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'number' => 10, // Показываем только первые 10 продуктов
            'orderby' => 'count',
            'order' => 'DESC',
        ]);
        $current = isset($_GET['review_product_filter']) ? intval($_GET['review_product_filter']) : 0;
        $base_url = admin_url('edit.php?post_type=marketplace_review');
        foreach ($terms as $term) {
            $url = add_query_arg('review_product_filter', $term->term_id, $base_url);
            $class = ($current === $term->term_id) ? 'class="current"' : '';
            $views['product_' . $term->term_id] = "<a href='" . esc_url($url) . "' $class>" . esc_html($term->name) . " <span class='count'>($term->count)</span></a>";
        }
        return $views;
    }

    /**
     * Получить количество отзывов со статусом модерации "pending"
     * @return int
     */
    private function get_pending_reviews_count() {
        global $wpdb;
        $count = $wpdb->get_var(
            "SELECT COUNT(*) FROM $wpdb->postmeta pm
             INNER JOIN $wpdb->posts p ON pm.post_id = p.ID
             WHERE pm.meta_key = 'moderation_status'
             AND pm.meta_value = 'pending'
             AND p.post_type = 'marketplace_review'
             AND p.post_status IN ('publish','pending','draft')"
        );
        return intval($count);
    }

    /**
     * Добавляет ссылку "Ответить на отзыв" в действиях строки в админке
     */
    public function add_admin_reply_action_link($actions, $post) {
     
           
            $actions = array_merge(
                ['mp_admin_reply' => '<a href="#" class="mp-admin-reply-link-row" data-review-id="' . esc_attr($post->ID) . '">' . esc_html__('Ответить на отзыв', 'marketplace-reviews-for-woocommerce') . '</a>'],
                $actions
            );
        
        return $actions;
    }

    /**
     * HTML попапа для ответа администратора (один раз на страницу, но с разными id)
     */
    public function admin_reply_popup_html() {
        global $typenow, $pagenow;
        if ($pagenow !== 'edit.php' || $typenow !== 'marketplace_review') return;
        ?>
        <div id="mp-admin-reply-popup" class="mp-admin-reply-popup" style="display:none;">
            <div class="mp-popup-content">
                <h4><?php _e('Ответ администратора', 'marketplace-reviews-for-woocommerce'); ?></h4>
                <form id="mp-admin-reply-form" method="post">
                    <textarea name="admin_reply_content" id="mp_admin_reply_content" rows="4" placeholder="<?php esc_attr_e('Введите ответ...', 'marketplace-reviews-for-woocommerce'); ?>"></textarea>
                    <input type="hidden" name="review_id" id="mp_admin_reply_review_id" value="">
                    <input type="hidden" name="comment_id" id="mp_admin_reply_comment_id" value="">
                    <div class="mp-popup-buttons">
                        <button type="submit" class="button button-primary"><?php _e('Отправить', 'marketplace-reviews-for-woocommerce'); ?></button>
                        <button type="button" class="button mp-popup-close"><?php _e('Отмена', 'marketplace-reviews-for-woocommerce'); ?></button>
                    </div>
                    <div class="mp-admin-reply-message" style="margin-top:10px;"></div>
                </form>
            </div>
        </div>
        <script>
        jQuery(function($){
            let popup = $('#mp-admin-reply-popup');
            let form = $('#mp-admin-reply-form');
            let textarea = $('#mp_admin_reply_content');
            let reviewIdInput = $('#mp_admin_reply_review_id');
            let commentIdInput = $('#mp_admin_reply_comment_id');
            let message = $('.mp-admin-reply-message');

            // Открытие попапа
            $('.mp-admin-reply-link-row').on('click', function(e){
                e.preventDefault();
                let reviewId = $(this).data('review-id');
                reviewIdInput.val(reviewId);
                commentIdInput.val('');
                textarea.val('');
                message.text('');
                // Получаем существующий ответ через AJAX
                $.post(ajaxurl, {
                    action: 'mp_save_admin_reply',
                    get_reply: 1,
                    review_id: reviewId,
                    _ajax_nonce: '<?php echo wp_create_nonce('mp_admin_reply_nonce'); ?>'
                }, function(resp){
                    if (resp.success && resp.data) {
                        textarea.val(resp.data.content);
                        commentIdInput.val(resp.data.comment_id);
                    }
                });
                popup.fadeIn(200);
            });

            // Закрытие попапа
            $('.mp-popup-close, #mp-admin-reply-popup').on('click', function(e){
                if (e.target === this) {
                    popup.fadeOut(150);
                }
            });
            $(document).on('keydown', function(e){
                if (e.key === 'Escape') popup.fadeOut(150);
            });

            // Сохранение через AJAX
            form.on('submit', function(e){
                e.preventDefault();
                message.text('');
                $.post(ajaxurl, {
                    action: 'mp_save_admin_reply',
                    review_id: reviewIdInput.val(),
                    comment_id: commentIdInput.val(),
                    content: textarea.val(),
                    _ajax_nonce: '<?php echo wp_create_nonce('mp_admin_reply_nonce'); ?>'
                }, function(resp){
                    if (resp.success) {
                        message.css('color', 'green').text('<?php echo esc_js(__('Ответ сохранён', 'marketplace-reviews-for-woocommerce')); ?>');
                        setTimeout(function(){ popup.fadeOut(150); location.reload(); }, 800);
                    } else {
                        message.css('color', 'red').text(resp.data || 'Ошибка');
                    }
                });
            });
        });
        </script>
        <style>
        .mp-admin-reply-popup {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;
        }
        .mp-popup-content {
            background: #fff; padding: 25px; border-radius: 8px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;
        }
        .mp-popup-content h4 { margin-top: 0; margin-bottom: 15px; color: #333; }
        .mp-popup-content textarea { width: 100%; min-height: 100px; margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; resize: vertical; }
        .mp-popup-buttons { text-align: right; }
        .mp-popup-buttons .button { margin-left: 10px; }
        </style>
        <?php
    }

    /**
     * AJAX: Сохранение или получение ответа администратора (comment)
     */
    public function ajax_save_admin_reply() {
        check_ajax_referer('mp_admin_reply_nonce');
        $user_id = get_current_user_id();
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Нет прав');
        }
        $review_id = intval($_POST['review_id']);
        if (isset($_POST['get_reply'])) {
            // Получить существующий ответ
            $reply = get_comments([
                'post_id' => $review_id,
                'parent' => 0,
                'status' => 'approve',
                'type' => 'admin_reply',
                'number' => 1,
                'author_email' => wp_get_current_user()->user_email,
            ]);
            if ($reply) {
                wp_send_json_success([
                    'content' => $reply[0]->comment_content,
                    'comment_id' => $reply[0]->comment_ID,
                ]);
            } else {
                // Поискать любой ответ администратора (не только текущего)
                $reply = get_comments([
                    'post_id' => $review_id,
                    'parent' => 0,
                    'status' => 'approve',
                    'type' => 'admin_reply',
                    'number' => 1,
                ]);
                if ($reply) {
                    wp_send_json_success([
                        'content' => $reply[0]->comment_content,
                        'comment_id' => $reply[0]->comment_ID,
                    ]);
                }
                wp_send_json_success(['content' => '', 'comment_id' => 0]);
            }
        }
        $content = isset($_POST['content']) ? trim(wp_unslash($_POST['content'])) : '';
        $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
        if (!$review_id || $content === '') {
            wp_send_json_error('Пустой ответ');
        }
        if ($comment_id) {
            // Обновить
            wp_update_comment([
                'comment_ID' => $comment_id,
                'comment_content' => $content,
            ]);
            wp_send_json_success();
        } else {
            // Создать
            $commentdata = [
                'comment_post_ID' => $review_id,
                'comment_content' => $content,
                'user_id' => $user_id,
                'comment_type' => 'admin_reply',
                'comment_approved' => 1,
            ];
            $cid = wp_insert_comment($commentdata);
            if ($cid) {
                wp_send_json_success();
            } else {
                wp_send_json_error('Ошибка сохранения');
            }
        }
    }

    /**
     * Вывод ответа администратора под отзывом (фронт и админка)
     * Ответ хранится как комментарий с comment_type = 'admin_reply'
     */
    function mp_show_admin_reply($review_id) {
        $args = [
            'post_id' => $review_id,
            'parent' => 0,
            'status' => 'approve',
            'type' => 'admin_reply',
            'number' => 1,
        ];
        $reply = get_comments($args);
        if ($reply) {
            $comment = $reply[0];
            echo '<div class="mp-admin-reply">';
            echo '<strong>' . esc_html__('Ответ администратора:', 'marketplace-reviews-for-woocommerce') . '</strong>';
            // Выводим как обычный комментарий WordPress
            echo '<div class="mp-admin-reply-content">';
            echo apply_filters('comment_text', $comment->comment_content, $comment);
            echo '</div>';
            echo '</div>';
        }
    }
}
