<?php
/**
 * Single review template
 * Used to render individual reviews
 *
 * @package MarketplaceReviews
 */

if (!isset($post)) return;

$rating = get_post_meta($post->ID, 'review_rating', true);
$pros = get_post_meta($post->ID, 'review_pros', true);
$cons = get_post_meta($post->ID, 'review_cons', true);
$author_name = get_post_meta($post->ID, 'review_author', true);
$images = get_post_meta($post->ID, 'review_images', true);
$date = get_the_date('', $post);

?>

<div class="mp-single-review">
    <div class="mp-review-header">
        <div class="mp-review-author">
            <strong><?php echo esc_html($author_name); ?></strong>
            <span class="mp-review-date"><?php echo esc_html($date); ?></span>
        </div>
        <div class="mp-review-rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star <?php echo ($i <= $rating) ? 'filled' : 'empty'; ?>">★</span>
            <?php endfor; ?>
        </div>
    </div>
<?php
if (is_post_type_archive('marketplace_review')) {
    $product_id = get_post_meta(get_the_ID(), 'product_id', true);
    $product = wc_get_product($product_id);

    if ($product): ?>
        <div class="review-product-title">
            <strong>Product:</strong>
            <a href="<?php echo get_permalink($product_id); ?>">
                <?php echo esc_html($product->get_name()); ?>
            </a>
        </div>
    <?php endif;
}
?>

    <div class="mp-review-body">
        <div class="mp-review-content">
        <?php echo apply_filters('the_content', $post->post_content); ?>

        </div>

        <?php if (!empty($pros)) : ?>
            <div class="mp-review-pros">
                <strong><?php esc_html_e('Pros:', 'marketplace-reviews-for-woocommerce'); ?></strong>
                <p><?php echo esc_html($pros); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($cons)) : ?>
            <div class="mp-review-cons">
                <strong><?php esc_html_e('Cons:', 'marketplace-reviews-for-woocommerce'); ?></strong>
                <p><?php echo esc_html($cons); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($images) && is_array($images)) : ?>
            <div class="mp-review-images">
                <?php foreach ($images as $image_id) : ?>
                    <?php echo wp_get_attachment_image($image_id, 'medium', false, ['class' => 'review-image']); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
