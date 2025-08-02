<?php
/**
 * Review replies handling with custom table.
 *
 * @package MarketplaceReviews
 */
class Marketplace_Review_Replies {
    const TABLE_NAME = 'marketplace_review_replies';

    public static function create_table() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            review_id BIGINT UNSIGNED NOT NULL,
            parent_id BIGINT UNSIGNED DEFAULT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            content TEXT NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY review_id (review_id),
            KEY parent_id (parent_id)
        ) $charset;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function add_reply($review_id, $parent_id, $user_id, $content) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $is_admin = user_can($user_id, 'manage_options') ? 1 : 0;

        if ($parent_id) {
            $parent = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $parent_id));
            if (!$parent || !$parent->is_admin) {
                return false;
            }
        } elseif (!$is_admin) {
            // Non-admins cannot create top-level replies
            return false;
        }

        $wpdb->insert($table, [
            'review_id' => $review_id,
            'parent_id' => $parent_id,
            'user_id'   => $user_id,
            'content'   => $content,
            'is_admin'  => $is_admin,
            'created_at'=> current_time('mysql'),
        ]);

        $insert_id = $wpdb->insert_id;

        // Notify the opposite party
        if ($is_admin) {
            $review_author = get_post_field('post_author', $review_id);
            $user = get_user_by('id', $review_author);
            if ($user) {
                wp_mail($user->user_email, __('New reply to your review', 'marketplace-reviews-for-woocommerce'), $content);
            }
        } elseif ($parent_id && !empty($parent)) {
            $parent_user = get_user_by('id', $parent->user_id);
            if ($parent_user) {
                wp_mail($parent_user->user_email, __('New reply to your comment', 'marketplace-reviews-for-woocommerce'), $content);
            }
        }

        return $insert_id;
    }

    // Получить все ответы для отзыва
    public static function get_replies($review_id) {
        return get_posts([
            'post_type' => 'marketplace_review_reply',
            'post_parent' => $review_id,
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'ASC'
        ]);
    }

    // Построить дерево ответов
    public static function build_tree($replies) {
        $tree = [];
        foreach ($replies as $reply) {
            $tree[$reply->ID] = $reply;
        }
        return $tree;
    }

    // Рендер дерева (рекурсивно)
    public static function render_tree($replies, $admin = false) {
        if (empty($replies)) return;
        echo '<ul class="review-replies">';
        foreach ($replies as $reply) {
            echo '<li class="reply-content">';
            echo esc_html($reply->post_content);
            if ($admin) {
                // Кнопка ответа для пользователя, если есть ответ админа
                echo '<button class="reply-to-admin" data-reply="' . esc_attr($reply->ID) . '">' . __('Ответить', 'marketplace-reviews-for-woocommerce') . '</button>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    // Проверка: есть ли ответ администратора
    public static function has_admin_reply($review_id) {
        $args = [
            'post_type' => 'marketplace_review_reply',
            'post_parent' => $review_id,
            'meta_key' => 'is_admin',
            'meta_value' => '1',
            'numberposts' => 1
        ];
        $replies = get_posts($args);
        return !empty($replies);
    }

    public static function handle_ajax() {
        check_ajax_referer('marketplace_review_reply', '_wpnonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Login required', 'marketplace-reviews-for-woocommerce')]);
        }
        $review_id = intval($_POST['review_id']);
        $parent_id = intval($_POST['parent_id'] ?? 0);
        $content   = sanitize_textarea_field($_POST['content']);
        $user_id   = get_current_user_id();
        $id = self::add_reply($review_id, $parent_id, $user_id, $content);
        if ($id) {
            wp_send_json_success(['id' => $id]);
        }
        wp_send_json_error(['message' => __('Unable to add reply', 'marketplace-reviews-for-woocommerce')]);
    }
}
