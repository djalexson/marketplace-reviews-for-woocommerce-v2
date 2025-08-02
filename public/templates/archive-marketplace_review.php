<?php
/**
 * Template for displaying archive of product reviews
 *
 * @package MarketplaceReviews
 */



?>
<div class="marketplace-reviews-archive">
    <div class="container">
        <h1 class="page-title">
            <?php esc_html_e('Отзывы о товарах', 'marketplace-reviews-for-woocommerce'); ?>
        </h1>

        <?php
        if (have_posts()) :
            echo '<div class="marketplace-reviews-list">';
            while (have_posts()) : the_post();
                include MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/single-review.php';
            endwhile;
            echo '</div>';

            the_posts_pagination([
                'mid_size'  => 2,
                'prev_text' => __('Назад', 'marketplace-reviews-for-woocommerce'),
                'next_text' => __('Вперёд', 'marketplace-reviews-for-woocommerce'),
            ]);
        else :
            echo '<p>' . esc_html__('Пока нет отзывов.', 'marketplace-reviews-for-woocommerce') . '</p>';
        endif;
        ?>
    </div>
</div>
<?php

get_footer();
