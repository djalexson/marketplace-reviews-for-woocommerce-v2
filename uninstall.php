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

// Очистка кэша
wp_cache_flush();

// Удаление таксономий
$taxonomies = ['review_rating', 'review_product'];
foreach ($taxonomies as $taxonomy) {
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ]);
    foreach ($terms as $term) {
        wp_delete_term($term->term_id, $taxonomy);
    }
}
