<?php
/**
 * Template: Review Popup
 *
 * @package MarketplaceReviews
 */

$title=  Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_popup_title', 'Leave a Review', 'marketplace-reviews-for-woocommerce');
?>
<div id="marketplace-review-popup" class="marketplace-review-popup">
    <div class="marketplace-review-popup-overlay"></div>
    <div class="marketplace-review-popup-content">
        <button type="button" class="marketplace-review-popup-close">&times;</button>
        <h3><?=$title; ?></h3>
        <div class="popup-product-info">
            <p class="popup-product-name"></p>
        </div>
        <div class="popup-review-form">
            <?php
           
                include MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/review-form.php';
            ?>
        </div>
    </div>
</div>
