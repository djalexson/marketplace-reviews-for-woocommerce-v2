<?php
/**
 * Admin reviews list custom display
 *
 * @package MarketplaceReviews
 */

?>
<div class="wrap">
    <h1><?php esc_html_e('Customer Reviews', 'marketplace-reviews-for-woocommerce'); ?></h1>
    <p><?php esc_html_e('Below is a list of all product reviews submitted by customers.', 'marketplace-reviews-for-woocommerce'); ?></p>

    <?php
    $reviews = new WP_Query([
        'post_type' => 'marketplace_review',
        'posts_per_page' => 20,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);

    if ($reviews->have_posts()) {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>' . __('Author', 'marketplace-reviews-for-woocommerce') . '</th><th>' . __('Rating', 'marketplace-reviews-for-woocommerce') . '</th><th>' . __('Content', 'marketplace-reviews-for-woocommerce') . '</th><th>' . __('Status', 'marketplace-reviews-for-woocommerce') . '</th></tr></thead>';
        echo '<tbody>';

        while ($reviews->have_posts()) {
            $reviews->the_post();
            $id = get_the_ID();
            $author = get_post_meta($id, 'review_author', true);
            $rating = get_post_meta($id, 'review_rating', true);
            $content = get_the_excerpt();
            $status = get_post_meta($id, 'moderation_status', true);

            echo '<tr>';
            echo '<td>' . esc_html($author) . '</td>';
            echo '<td>' . esc_html($rating) . '/5</td>';
            echo '<td>' . esc_html($content) . '</td>';
            echo '<td class="marketplace-review-status-' . esc_attr($status) . '">' . esc_html(ucfirst($status)) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        wp_reset_postdata();
    } else {
        echo '<p>' . __('No reviews found.', 'marketplace-reviews-for-woocommerce') . '</p>';
    }
    ?>
</div>
