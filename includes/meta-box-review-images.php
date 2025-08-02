<?php
add_action('add_meta_boxes', function() {
    add_meta_box(
        'marketplace_review_images_box',
        __('Review Images', 'marketplace-reviews-for-woocommerce'),
        function($post) {
            $images_json = get_post_meta($post->ID, 'review_images', true);
            $image_ids = is_string($images_json) ? json_decode($images_json, true) : [];
            if (!is_array($image_ids)) $image_ids = [];
            ?>
            <div id="marketplace-review-images-container" class="mp-field-visible">
                <ul class="marketplace-review-images-list">
                    <?php foreach ($image_ids as $img_id): ?>
                        <li data-id="<?php echo esc_attr($img_id); ?>">
                            <?php echo wp_get_attachment_image($img_id, 'thumbnail'); ?>
                            <button type="button" class="remove-image button">&times;</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="button add-review-image"><?php _e('Add Images', 'marketplace-reviews-for-woocommerce'); ?></button>
                <input type="hidden" name="marketplace_review_images" id="marketplace_review_images" value="<?php echo esc_attr($images_json); ?>">
            </div>
            <style>
                .marketplace-review-images-list { display: flex; gap: 8px; flex-wrap: wrap; margin: 8px 0; }
                .marketplace-review-images-list li { position: relative; }
                .marketplace-review-images-list img { display: block; border-radius: 4px; }
                .remove-image { position: absolute; top: -8px; right: -8px; background: var(--mp-color-btn-success); color: #fff; border: none; border-radius: 50%; }
            </style>
            <script>
                jQuery(function($) {
                    var frame;
                    $('.add-review-image').on('click', function(e) {
                        e.preventDefault();
                        if (frame) { frame.open(); return; }
                        frame = wp.media({
                            title: '<?php _e('Select Images', 'marketplace-reviews-for-woocommerce'); ?>',
                            button: { text: '<?php _e('Add Images', 'marketplace-reviews-for-woocommerce'); ?>' },
                            multiple: true
                        });
                        frame.on('select', function() {
                            var selection = frame.state().get('selection');
                            var image_ids = [];
                            var images_list = $('.marketplace-review-images-list').empty();
                            selection.each(function(attachment) {
                                image_ids.push(attachment.id);
                                images_list.append(
                                    '<li data-id="'+attachment.id+'">'+
                                        '<img src="'+attachment.attributes.sizes.thumbnail.url+'" />'+
                                        '<button type="button" class="remove-image button">&times;</button>'+
                                    '</li>'
                                );
                            });
                            $('#marketplace_review_images').val(JSON.stringify(image_ids));
                        });
                        frame.open();
                    });

                    $('#marketplace-review-images-container').on('click', '.remove-image', function() {
                        $(this).parent().remove();
                        var ids = [];
                        $('.marketplace-review-images-list li').each(function() {
                            ids.push($(this).data('id'));
                        });
                        $('#marketplace_review_images').val(JSON.stringify(ids));
                    });

                    var $photoCheckbox = $('#marketplace_reviews_enable_photos');
                    function togglePhotoBlock() {
                        var show = $photoCheckbox.is(':checked');
                        $('#marketplace-review-images-container').toggleClass('mp-field-visible', show).toggleClass('mp-field-hidden', !show);
                    }
                    if ($photoCheckbox.length) {
                        togglePhotoBlock();
                        $photoCheckbox.on('change', togglePhotoBlock);
                    }
                });
            </script>
            <?php
        },
        'marketplace_review',
        'normal',
        'default'
    );
});

add_action('save_post_marketplace_review', function($post_id) {
    if (isset($_POST['marketplace_review_images'])) {
        $val = $_POST['marketplace_review_images'];
        if (!empty($val)) {
            update_post_meta($post_id, 'review_images', $val);
        } else {
            delete_post_meta($post_id, 'review_images');
        }
    }
});
