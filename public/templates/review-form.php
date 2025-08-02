<?php
/**
 * Template: Review Form
 *
 * @package MarketplaceReviews
 */

// Проверяем, включены ли плюсы/минусы и получаем тексты из настроек
$enable_cons = get_option('marketplace_reviews_enable_cons', 'yes') === 'yes';
$enable_pros = get_option('marketplace_reviews_enable_pros', 'yes') === 'yes';
$pros_label =  Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_pros_label', 'Pros', 'marketplace-reviews-for-woocommerce');
$cons_label =  Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_cons_label', 'Cons', 'marketplace-reviews-for-woocommerce');
$enable_photos = get_option('marketplace_reviews_enable_photos', 'yes') === 'yes';
$max_images = intval(get_option('marketplace_reviews_max_images', 5));
$upload_label = get_option('marketplace_reviews_upload_label', __('Upload Images (optional)', 'marketplace-reviews-for-woocommerce'));

$btn = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_popup_button',  'Submit Review', 'marketplace-reviews-for-woocommerce');
$btn_p = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_popup_button_pul',  'Submitting...', 'marketplace-reviews-for-woocommerce');
$rating_label = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_popup_rating_label', 'Rating', 'marketplace-reviews-for-woocommerce');
?>

<form id="marketplace-review-form" class="marketplace-review-form" method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('marketplace_review_submission'); ?>

    <input type="hidden" name="action" value="submit_marketplace_review">
    <input type="hidden" id="popup_product_id" name="product_id" value="">

    <div class="form-group">
        <label for="review_author"><?php _e('Your Name', 'marketplace-reviews-for-woocommerce'); ?>*</label>
        <input type="text" name="review_author" id="review_author" required>
    </div>
    <div class="form-group">
        <label><?=$rating_label ?>*</label>
        <div class="stars-rating" id="popup-stars-rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star" data-value="<?php echo $i; ?>">&#9733;</span>
            <?php endfor; ?>
            <input type="hidden" name="rating" id="popup-rating-input" value="0" required>
        </div>
    </div>

    <?php if ($enable_pros): ?>
			<div class="form-group">
				<label for="review_pros"><?php echo esc_html($pros_label); ?></label>
				<textarea name="review_pros" id="review_pros"></textarea>
			</div>
			<?php endif; ?>
			
			<?php if ($enable_cons): ?>
        <div class="form-group">
            <label for="review_cons"><?php echo esc_html($cons_label); ?></label>
            <textarea name="review_cons" id="review_cons"></textarea>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="review_content"><?php _e('Your Review', 'marketplace-reviews-for-woocommerce'); ?>*</label>
        <textarea name="review_content" id="review_content" required></textarea>
    </div>

<?php if ($enable_photos): ?>
<div class="form-group">
    <label for="review_images[]"><?php echo esc_html($upload_label); ?></label>
    <div id="drop-area" data-max="<?php echo esc_attr($max_images); ?>">
        <span id="drop-label"><?php echo esc_html($upload_label); ?></span>
        <input type="file" name="review_images[]" id="review_images" multiple accept="image/*" style="display:none;" data-max="<?php echo esc_attr($max_images); ?>">
        <div id="gallery"></div>
    </div>
    <div class="limit-hint" style="font-size:0.9em;color:#888;">
        <?php printf(__('Max %d images', 'marketplace-reviews-for-woocommerce'), $max_images); ?>
    </div>
</div>
<?php endif; ?>


    <button type="submit" data-loading-text="<?=$btn_p?>" class="button submit-review-button">
        <?=$btn ?>
    </button>
</form>
