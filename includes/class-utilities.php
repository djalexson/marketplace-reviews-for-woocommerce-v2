<?php
/**
 * Helper utilities for Marketplace Reviews
 *
 * @package MarketplaceReviews
 */
if (!function_exists('wc_get_product_id_by_slug')) {
    function wc_get_product_id_by_slug($slug) {
        $post = get_page_by_path($slug, OBJECT, 'product');
        return $post ? $post->ID : 0;
    }
}

class Marketplace_Reviews_Utilities {

    /**
     * Calculate average rating for a product
     */
    public static function get_average_rating($product_id) {
        $reviews = get_posts([
            'post_type' => 'marketplace_review',
            'meta_key' => 'product_id',
            'meta_value' => $product_id,
            'post_status' => 'publish',
            'numberposts' => -1,
        ]);

        if (empty($reviews)) return 0;

        $total = 0;
        $count = 0;

        foreach ($reviews as $review) {
            $rating = get_post_meta($review->ID, 'review_rating', true);
            if ($rating) {
                $total += (int) $rating;
                $count++;
            }
        }

        return $count ? round($total / $count, 1) : 0;
    }

    /**
     * Format review date
     */
    public static function format_review_date($date) {
        return date_i18n(get_option('date_format'), strtotime($date));
    }
}
