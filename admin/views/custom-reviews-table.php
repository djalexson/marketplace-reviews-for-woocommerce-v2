<?php
// Получаем отзывы (пример, можно доработать под свои нужды)
$reviews = get_posts([
    'post_type' => 'marketplace_review',
    'posts_per_page' => 20,
    'post_status' => ['publish', 'pending', 'draft'],
    'orderby' => 'date',
    'order' => 'DESC',
]);
?>
<div class="marketplace-reviews-table-wrap">
    <h1><?php esc_html_e('Product Reviews', 'marketplace-reviews-for-woocommerce'); ?></h1>
    <table class="marketplace-reviews-table wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Title', 'marketplace-reviews-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Author', 'marketplace-reviews-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Date', 'marketplace-reviews-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Moderation', 'marketplace-reviews-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Actions', 'marketplace-reviews-for-woocommerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reviews): foreach ($reviews as $review): ?>
                <tr>
                    <td>
                        <a href="<?php echo get_edit_post_link($review->ID); ?>">
                            <?php echo esc_html(get_the_title($review)); ?>
                        </a>
                    </td>
                    <td><?php echo esc_html(get_the_author_meta('display_name', $review->post_author)); ?></td>
                    <td><?php echo esc_html(get_the_date('', $review)); ?></td>
                    <td>
                        <?php
                        $status = get_post_meta($review->ID, 'moderation_status', true);
                        $class = '';
                        if ($status === 'approved') $class = 'marketplace-review-status-approved';
                        elseif ($status === 'pending') $class = 'marketplace-review-status-pending';
                        elseif ($status === 'rejected') $class = 'marketplace-review-status-rejected';
                        echo '<span class="' . esc_attr($class) . '">' . esc_html($status) . '</span>';
                        ?>
                    </td>
                    <td>
                        <a href="<?php echo get_edit_post_link($review->ID); ?>" class="button button-small"><?php esc_html_e('Edit', 'marketplace-reviews-for-woocommerce'); ?></a>
                        <a href="<?php echo get_delete_post_link($review->ID); ?>" class="button button-small" onclick="return confirm('<?php esc_attr_e('Delete this review?', 'marketplace-reviews-for-woocommerce'); ?>')"><?php esc_html_e('Delete', 'marketplace-reviews-for-woocommerce'); ?></a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="5"><?php esc_html_e('No reviews found.', 'marketplace-reviews-for-woocommerce'); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

