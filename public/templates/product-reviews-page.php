<?php
/**
 * Template for displaying all reviews for a product
 */
get_header();

$slug_or_id = get_query_var('marketplace_reviews_product');

$product = is_numeric($slug_or_id)
    ? wc_get_product((int)$slug_or_id)
    : wc_get_product(wc_get_product_id_by_slug($slug_or_id));

if (!$product) {
    echo '<p>' . esc_html__('Product not found.', 'marketplace-reviews-for-woocommerce') . '</p>';
    get_footer();
    return;
}
$product_id = $product->get_id() ;
echo '<pre>Текущий product_id: ' . $product_id . '</pre>';
echo '<pre>marketplace_reviews_product: ' . $slug_or_id . '</pre>';
?>

<div class="mp-review-page">

    <!-- рџ§· Top section -->
    <div class="mp-review-header">
        <h1><?php echo esc_html($product->get_name()); ?></h1>
        <div class="mp-review-meta">
            <span class="price"><?php echo $product->get_price_html(); ?></span>
            <span class="avg-rating"><?php echo wc_get_rating_html($product->get_average_rating()); ?></span>
        </div>
        <button class="mp-leave-review-btn" data-product-id="<?php echo esc_attr($product_id); ?>">
            <?php esc_html_e('РћСЃС‚Р°РІРёС‚СЊ РѕС‚Р·С‹РІ', 'marketplace-reviews-for-woocommerce'); ?>
        </button>
    </div>

    <!-- рџ“ё Gallery -->
    <div class="mp-review-gallery">
        <?php
        $reviews_with_photos = new WP_Query([
            'post_type' => 'marketplace_review',
            'meta_query' => [
                [
                    'key'     => 'product_id',
                    'value'   => $product_id,
                    'compare' => '=',
                ],
                [
                    'key'     => 'review_images',
                    'compare' => 'EXISTS'
                ]
            ],
            'posts_per_page' => 12
        ]);
        while ($reviews_with_photos->have_posts()) : $reviews_with_photos->the_post();
            $images = get_post_meta(get_the_ID(), 'review_images', true);
            if (!empty($images) && is_array($images)) {
                foreach ($images as $img_id) {
                    echo wp_get_attachment_image($img_id, 'thumbnail', false, ['class' => 'review-photo-thumb']);
                }
            }
        endwhile;
        wp_reset_postdata();
        ?>
    </div>

    <!-- рџ“‘ Sorting -->
    <div class="mp-review-sorting">
        <form method="get">
            <label><?php esc_html_e('РЎРѕСЂС‚РёСЂРѕРІР°С‚СЊ РїРѕ:', 'marketplace-reviews-for-woocommerce'); ?></label>
            <select name="orderby" onchange="this.form.submit()">
                <option value="date" <?php selected($_GET['orderby'] ?? '', 'date'); ?>><?php esc_html_e('Р”Р°С‚Рµ', 'marketplace-reviews-for-woocommerce'); ?></option>
                <option value="rating" <?php selected($_GET['orderby'] ?? '', 'rating'); ?>><?php esc_html_e('РћС†РµРЅРєРµ', 'marketplace-reviews-for-woocommerce'); ?></option>
            </select>
        </form>
    </div>

    <!-- рџ’¬ Reviews list -->
    <div class="mp-reviews-list">
        <?php
        $args = [
            'post_type' => 'marketplace_review',
            'posts_per_page' => 10,
            'meta_key' => ($_GET['orderby'] ?? '') === 'rating' ? 'review_rating' : '',
            'orderby' => ($_GET['orderby'] ?? '') === 'rating' ? 'meta_value_num' : 'date',
            'order' => 'DESC',
            'meta_query' => [
                [
                    'key'     => 'product_id',
                    'value'   => $product_id,
                    'compare' => '='
                ],
                [
                    'key'     => 'moderation_status',
                    'value'   => 'approved',
                    'compare' => '='
                ]
            ]
        ];

        $reviews = new WP_Query($args);

        if ($reviews->have_posts()) :
            while ($reviews->have_posts()) : $reviews->the_post();load_template(MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/single-review.php', false);


            endwhile;

            echo paginate_links([
                'total' => $reviews->max_num_pages
            ]);
        else :
            echo '<p>' . esc_html__('No reviews yet.', 'marketplace-reviews-for-woocommerce') . '</p>';
        endif;

        wp_reset_postdata();
        ?>
    </div>
</div>

<?php
get_footer();
