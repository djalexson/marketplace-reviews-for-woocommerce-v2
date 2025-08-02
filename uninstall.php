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

foreach ($reviews as $review) {
    wp_delete_post($review->ID, true);
}

// Remove options
delete_option('marketplace_reviews_enable_photos');
delete_option('marketplace_reviews_admin_notifications');
delete_option('marketplace_reviews_review_trigger_status');
delete_option('marketplace_reviews_popup_reminder');

// Optional: remove custom taxonomies terms
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
