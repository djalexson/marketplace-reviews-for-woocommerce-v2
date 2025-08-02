<?php
/**
 * Review form handling
 *
 * @package    MarketplaceReviews
 * @subpackage MarketplaceReviews/includes
 */

class Marketplace_Reviews_Form {

    public function __construct() {
        // Initialize if needed
    }

    // Добавляем колонку "Review" в таблицу заказов пользователя
    public function add_review_column_to_orders($columns) {
        $columns['review'] = __('Review', 'marketplace-reviews-for-woocommerce');
        return $columns;
    }
public function add_review_button_to_orders($order) {
   foreach ($order->get_items() as $item) {
    $product_id = $item->get_product_id();
    list($review_id, $review_status) = $this->get_user_product_review_status($product_id);
	  if ($review_id) {
        if ($review_status === 'pending') {
            $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_pending', 'Review pending moderation');
            echo '<span style="color:#aaa;">' . esc_html($text) . '</span>';
        } elseif ($review_status === 'publish' || $review_status === 'approved') {
            $review_link = get_permalink($review_id);
            $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_view_review', 'View review');
            echo '<a href="' . esc_url($review_link) . '" class="button view-review">' . esc_html($text) . '</a>';
        } else {
            // Для других статусов (например, отклонён, черновик)
            $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_submitted', 'Review submitted');
            echo '<span style="color:#aaa;">' . esc_html($text) . '</span>';
        }
        break;
    }

    // Проверяем статус заказа — разрешаем оставить отзыв только если "Выполнен"
    if ($order->get_status() !== 'completed') {
        $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_not_completed', 'Available after order is completed');
        echo '<span style="color:#aaa;">' . esc_html($text) . '</span>';
        break;
    }

    //// Можно оставить отзыв?
    if ($this->can_user_leave_review($product_id)) {
       $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_leave', 'Leave a review');
       echo  '<a href="' . esc_url(get_permalink($product_id)) . '#leave_review" data-product-id="' . $product_id . '" class="button open-review-popup">' . esc_html($text) . '</a>';
       break;
    } else {
        $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_already', 'Already reviewed');
        echo '<span style="color:#aaa;">' . esc_html($text) . '</span>';
      break;
    }
}
								
}


    public function render_review_popup() {
        if (!is_user_logged_in()) return;

        $enable_photos = get_option('marketplace_reviews_enable_photos', 'yes');
        include MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/review-popup.php';
    }

    public function can_user_leave_review($product_id, $user_id = 0) {
        if (!$user_id) $user_id = get_current_user_id();
        if (!$user_id) return false;

        $existing = $this->get_user_product_review($product_id, $user_id);
        if ($existing) return false;

        $orders = wc_get_orders(['customer_id' => $user_id, 'status' => ['wc-completed'], 'limit' => -1]);
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                if ($item->get_product_id() == $product_id) {
                ///    $delivered = get_post_meta($order->get_id(), '_order_delivered', true);
                    //if ($delivered === 'yes') {
                        return true;
                    //}
                }
            }
        }
        return false;
    }

    public function get_user_product_review($product_id, $user_id = 0) {
        if (!$user_id) $user_id = get_current_user_id();
        if (!$user_id) return null;

        $query = new WP_Query([
            'post_type' => 'marketplace_review',
            'author' => $user_id,
            'meta_query' => [[
                'key' => 'product_id',
                'value' => $product_id,
                'compare' => '=',
            ]],
            'posts_per_page' => 1,
        ]);

        return $query->have_posts() ? $query->posts[0]->ID : null;
    }
// Получить статус (post_status) пользовательского отзыва
public function get_user_product_review_status($product_id, $user_id = 0) {
if (!$user_id) $user_id = get_current_user_id();
if (!$user_id) return false;

$query = new WP_Query([
    'post_type' => 'marketplace_review',
    'author' => $user_id,
    'meta_query' => [[
        'key' => 'product_id',
        'value' => (string)$product_id,
        'compare' => '=',
    ]],
    'posts_per_page' => 1,
    'fields' => 'ids',
    'post_status' => 'any',
]);

if ($query->have_posts()) {
    $review_id = $query->posts[0];
    $status = get_post_status($review_id);
    return [$review_id, $status];
}
return [null, null];

	}
public function handle_review_submission() {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'marketplace_review_submission')) {
        $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_security_fail', 'Security check failed.');
        wp_send_json_error(['message' => esc_html($text)]);
    }

    if (!is_user_logged_in()) {
        $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_login_required', 'You must be logged in to submit a review.');
        wp_send_json_error(['message' => esc_html($text)]);
    }

    $product_id = intval($_POST['product_id']);
    $user_id = get_current_user_id();

    if (!$this->can_user_leave_review($product_id, $user_id)) {
        $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_cannot_leave', 'You cannot leave a review for this product.');
        wp_send_json_error(['message' => esc_html($text)]);
    }

    $rating = max(1, min(5, intval($_POST['rating'])));
    $review_content = sanitize_textarea_field($_POST['review_content']);
    $review_author = sanitize_text_field($_POST['review_author']);
    $review_pros = sanitize_textarea_field($_POST['review_pros'] ?? '');
    $review_cons = sanitize_textarea_field($_POST['review_cons'] ?? '');
$product   = wc_get_product($product_id);
$product_name = $product ? $product->get_name() : 'Product';
$review_author = sanitize_text_field($_POST['review_author']);

$review_id = wp_insert_post([
    'post_title'   => sprintf(
        __('Review for "%s" by %s', 'marketplace-reviews-for-woocommerce'),
        $product_name,
        $review_author
    ),
    'post_content' => $review_content,
    'post_status'  => 'pending',
    'post_type'    => 'marketplace_review',
    'post_author'  => $user_id,
]);


    if (is_wp_error($review_id)) {
        wp_send_json_error(['message' => $review_id->get_error_message()]);
    }

    update_post_meta($review_id, 'product_id', $product_id);
    update_post_meta($review_id, 'review_rating', $rating);
    update_post_meta($review_id, 'review_author', $review_author);
    update_post_meta($review_id, 'review_pros', $review_pros);
    update_post_meta($review_id, 'review_cons', $review_cons);
    update_post_meta($review_id, 'moderation_status', 'pending');

    // === PHOTO UPLOADS ===
    $uploaded_image_ids = [];

    if (!empty($_FILES['review_images']) && !empty($_FILES['review_images']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $max_files = (int) get_option('marketplace_reviews_photo_limit', 6); // лимит из настроек, дефолт 6
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB на файл

        $files = $_FILES['review_images'];
        $count = min(count($files['name']), $max_files);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
            if (!in_array($files['type'][$i], $allowed_types)) continue;
            if ($files['size'][$i] > $max_size) continue;

            $file_array = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i]
            ];

            $attachment_id = media_handle_sideload($file_array, $review_id, null);

            if (!is_wp_error($attachment_id)) {
                $uploaded_image_ids[] = $attachment_id;
            }
        }

        if (!empty($uploaded_image_ids)) {
            update_post_meta($review_id, 'review_images', $uploaded_image_ids);
        }
    }
    // === /PHOTO UPLOADS ===

    $this->send_admin_notification($review_id, wc_get_product($product_id));

    $success_text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_text_submit_success', 'Thank you! Your review has been submitted and is awaiting moderation.');
    wp_send_json_success([
        'message' => esc_html($success_text),
        'review_id' => $review_id,
    ]);
}

    private function send_admin_notification($review_id, $product) {
        if (get_option('marketplace_reviews_admin_notifications', 'yes') !== 'yes') return;

        $admin_email = get_option('admin_email');
        $review = get_post($review_id);
        $rating = get_post_meta($review_id, 'review_rating', true);
        $author = get_post_meta($review_id, 'review_author', true);

        $subject_template = Marketplace_Reviews_Settings::get_option_lang(
            'marketplace_reviews_email_subject',
            '[{site_name}] New Product Review: {product}'
        );
        $message_template = Marketplace_Reviews_Settings::get_option_lang(
            'marketplace_reviews_email_body',
            "A new review has been submitted for {product}.\n\nRating: {rating}/5\nAuthor: {author}\n\nReview: {content}\n\nManage this review: {edit_link}"
        );

        $replacements = [
            '{site_name}' => get_bloginfo('name'),
            '{product}'   => $product->get_name(),
            '{rating}'    => $rating,
            '{author}'    => $author,
            '{content}'   => $review->post_content,
            '{edit_link}' => admin_url('post.php?post=' . $review_id . '&action=edit'),
        ];

        $subject = strtr($subject_template, $replacements);
        $message = strtr($message_template, $replacements);

        wp_mail($admin_email, $subject, $message);
    }
public function can_user_reply($review_id, $user_id = 0) {
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id) return false;
    // Можно отвечать только если есть ответ администратора
    return Marketplace_Review_Replies::has_admin_reply($review_id);
}

// При ответе пользователя — отправить уведомление автору
public function handle_user_reply($review_id, $content) {
    $user_id = get_current_user_id();
    if (!$this->can_user_reply($review_id, $user_id)) {
        return new WP_Error('no_permission', __('You cannot reply yet.', 'marketplace-reviews-for-woocommerce'));
    }
    $reply_id = wp_insert_post([
        'post_type' => 'marketplace_review_reply',
        'post_parent' => $review_id,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_author' => $user_id,
    ]);
    if (!is_wp_error($reply_id)) {
        $review = get_post($review_id);
        $author_email = get_the_author_meta('user_email', $review->post_author);
        wp_mail($author_email, __('New reply to your review', 'marketplace-reviews-for-woocommerce'), $content);
    }
    return $reply_id;
}
}
