<?php
/**
 * Template: Reviews List
 *
 * @package MarketplaceReviews
 */

if (!isset($product_id)) {
    echo '<p>' . __('Product not specified.', 'marketplace-reviews-for-woocommerce') . '</p>';
    return;
}

$reviews = new WP_Query([
    'post_type' => 'marketplace_review',
    'meta_query' => [
        [
            'key' => 'product_id',
            'value' => $product_id,
            'compare' => '='
        ],
        [
            'key' => 'moderation_status',
            'value' => 'approved',
            'compare' => '='
        ]
    ],
    'posts_per_page' => 10,
    'paged' => get_query_var('paged') ?: 1,
    'orderby' => 'date',
    'order' => 'DESC'
]);

if ($reviews->have_posts()) {
    echo '<div class="marketplace-reviews-list">';
    while ($reviews->have_posts()) {
        $reviews->the_post();
        include MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/single-review.php';
    }
    echo '</div>';
    echo '<div class="marketplace-reviews-pagination">';
    echo paginate_links([
        'total' => $reviews->max_num_pages,
        'current' => max(1, get_query_var('paged')),
    ]);
    echo '</div>';
} else {
    echo '<p>' . __('There are no reviews yet.', 'marketplace-reviews-for-woocommerce') . '</p>';
}

wp_reset_postdata();
