<?php
/**
 * Admin dashboard overview
 *
 * @package MarketplaceReviews
 */

?>
<div class="wrap">
    <h1><?php esc_html_e('Marketplace Reviews Dashboard', 'marketplace-reviews-for-woocommerce'); ?></h1>
    <p><?php esc_html_e('Welcome to the reviews management area.', 'marketplace-reviews-for-woocommerce'); ?></p>

    <ul>
        <li><a href="edit.php?post_type=marketplace_review">📋 <?php _e('All Reviews', 'marketplace-reviews-for-woocommerce'); ?></a></li>
        <li><a href="admin.php?page=marketplace-reviews-settings">⚙️ <?php _e('Settings', 'marketplace-reviews-for-woocommerce'); ?></a></li>
    </ul>
</div>
