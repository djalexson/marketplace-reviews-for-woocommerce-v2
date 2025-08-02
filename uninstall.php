<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package MarketplaceReviews
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Delete all reviews
$reviews = get_posts([
    'post_type' => 'marketplace_review',
    'numberposts' => -1,
    'post_status' => 'any',
]);

// Удаление всех записей отзывов
foreach ($reviews as $review) {
    wp_delete_post($review->ID, true);
}

// Удаление всех комментариев к отзывам
$comments = get_comments([
    'post_type' => 'marketplace_review',
    'status' => 'any'
]);
foreach ($comments as $comment) {
    wp_delete_comment($comment->comment_ID, true);
}

// Удаление медиа-файлов отзывов
$review_attachments = get_posts([
    'post_type' => 'attachment',
    'posts_per_page' => -1,
    'meta_query' => [[
        'key' => '_marketplace_review_image',
        'compare' => 'EXISTS'
    ]]
]);
foreach ($review_attachments as $attachment) {
    wp_delete_attachment($attachment->ID, true);
}

// Удаление всех опций плагина
$options = [
    'marketplace_reviews_enable_photos',
    'marketplace_reviews_admin_notifications',
    'marketplace_reviews_review_trigger_status',
    'marketplace_reviews_popup_reminder',
    'marketplace_reviews_version',
    'marketplace_reviews_settings'
];

foreach ($options as $option) {
    delete_option($option);
}

// Дополнительные опции для удаления
$additional_options = [
    'marketplace_reviews_pros_label',
    'marketplace_reviews_cons_label',
    'marketplace_reviews_star_style',
    'marketplace_reviews_star_svg',
    'marketplace_reviews_upload_label',
    'marketplace_reviews_max_images',
    'marketplace_reviews_email_subject',
    'marketplace_reviews_email_body'
];
foreach ($additional_options as $option) {
    delete_option($option);
    // Удаляем также все языковые версии опций
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $option . '_%'
    ));
}

// Удаление всех мета-полей
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'marketplace_review_%'");
$wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE meta_key LIKE 'marketplace_review_%'");

// Удаление всех таблиц плагина
$tables = [
    'marketplace_review_photos',
    'marketplace_review_votes',
    'marketplace_review_ratings',
    'marketplace_review_metadata'
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
}

// Дополнительные таблицы
$extra_tables = [
    'marketplace_review_replies',
    'marketplace_review_likes',
    'marketplace_review_reports',
    'marketplace_review_statistics'
];
foreach ($extra_tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
}

// Очистка всех видов кэша
wp_cache_flush();
if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
}
if (function_exists('w3tc_flush_all')) {
    w3tc_flush_all();
}
if (class_exists('WpeCommon')) {
    WpeCommon::purge_memcached();
    WpeCommon::clear_maxcdn_cache();
}
if (function_exists('rocket_clean_domain')) {
    rocket_clean_domain();
}
if (class_exists('WC_Cache_Helper')) {
    WC_Cache_Helper::get_transient_version('product', true);
}

// Удаление ролей и возможностей
$wp_roles = wp_roles();
if (!empty($wp_roles)) {
    $capabilities = [
        'manage_marketplace_reviews',
        'edit_marketplace_reviews',
        'delete_marketplace_reviews',
        'moderate_marketplace_reviews'
    ];
    foreach ($capabilities as $cap) {
        $wp_roles->remove_cap('administrator', $cap);
        $wp_roles->remove_cap('shop_manager', $cap);
    }
}
