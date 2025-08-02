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

    public static function get_replies($review_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE review_id = %d ORDER BY created_at ASC", $review_id));
    }

    public static function build_tree($replies) {
        $map = [];
        foreach ($replies as $reply) {
            $reply->children = [];
            $map[$reply->id] = $reply;
        }
        $root = [];
        foreach ($map as $id => $reply) {
            if ($reply->parent_id && isset($map[$reply->parent_id])) {
                $map[$reply->parent_id]->children[] = $reply;
            } else {
                $root[] = $reply;
            }
        }
        return $root;
    }

    public static function render_tree($replies, $with_forms = true) {
        if (empty($replies)) {
            return;
        }
        echo '<ul class="review-replies">';
        foreach ($replies as $reply) {
            $user = get_userdata($reply->user_id);
            $name = $user ? $user->display_name : __('User', 'marketplace-reviews-for-woocommerce');
            echo '<li><div class="reply-content"><strong>' . esc_html($name) . '</strong><p>' . esc_html($reply->content) . '</p></div>';
            if (!empty($reply->children)) {
                self::render_tree($reply->children, $with_forms);
            } elseif ($with_forms && $reply->is_admin && is_user_logged_in() && !current_user_can('manage_options')) {
                echo '<form class="review-reply-form" data-review="' . esc_attr($reply->review_id) . '" data-parent="' . esc_attr($reply->id) . '">';
                echo '<textarea name="reply_content" required></textarea>';
                echo '<button type="submit">' . esc_html__('Reply', 'marketplace-reviews-for-woocommerce') . '</button>';
                echo '</form>';
            }
            echo '</li>';
        }
        echo '</ul>';
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
