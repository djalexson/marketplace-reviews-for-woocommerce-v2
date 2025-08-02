<p class="mp-review-field">
    <label for="review_author"><strong><?php esc_html_e('Reviewer Name', 'marketplace-reviews-for-woocommerce'); ?></strong></label><br>
    <input type="text" name="review_author" id="review_author" value="<?php echo esc_attr($author); ?>" class="regular-text">
</p>
<p class="mp-review-field">
	<label for="product_id"><strong>
		<?php
		// Получаем плейсхолдер и перевод подписи из настроек
		$product_select_placeholder = class_exists('Marketplace_Reviews_Settings') && method_exists('Marketplace_Reviews_Settings', 'get_option_lang')
			? Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_product_select_placeholder', '— Select Product —')
			: __('— Select Product —', 'marketplace-reviews-for-woocommerce');
		$product_mess = class_exists('Marketplace_Reviews_Settings') && method_exists('Marketplace_Reviews_Settings', 'get_option_lang')
			? Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_product_mass', 'Select one or more products from the list or enter IDs separated by commas.')
			: __('Select one or more products from the list or enter IDs separated by commas.', 'marketplace-reviews-for-woocommerce');
		$product_label = class_exists('Marketplace_Reviews_Settings') && method_exists('Marketplace_Reviews_Settings', 'get_option_lang')
			? Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_product_label', 'Product')
			: __('Product', 'marketplace-reviews-for-woocommerce');
		echo esc_html($product_label);
		?>
	</strong></label><br>
	<?php
	// Получаем лимит из настроек (для первой загрузки)
	$product_limit = get_option('marketplace_reviews_product_select_limit', 8);
	// Получаем список товаров WooCommerce (только первые $product_limit)
	if (class_exists('WC_Product_Query')) {
		$products = wc_get_products([
			'limit' => $product_limit,
			'orderby' => 'title',
			'order' => 'ASC',
			'status' => array('publish'),
		]);
	} else {
		$products = [];
	}
	// Получаем выбранные товары (массив ID)
	$selected_products = [];
	if (!empty($product_id)) {
		if (is_array($product_id)) {
			$selected_products = $product_id;
		} elseif (is_string($product_id) && strpos($product_id, ',') !== false) {
			$selected_products = array_map('intval', explode(',', $product_id));
		} else {
			$selected_products = [(int)$product_id];
		}
	}
	?>
	<select name="product_id[]" id="product_id" class="mp-product-select" multiple="multiple" size="6" data-loaded="<?php echo esc_attr(count($products)); ?>">
		<?php foreach ($products as $product): ?>
			<option value="<?php echo esc_attr($product->get_id()); ?>" <?php echo in_array($product->get_id(), $selected_products) ? 'selected' : ''; ?>>
				<?php echo esc_html($product->get_name() . ' (ID: ' . $product->get_id() . ')'); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<input type="text" name="product_id_manual" id="product_id_manual" hidden value="<?php echo esc_attr(implode(',', $selected_products)); ?>" class="regular-text" placeholder="<?php esc_attr_e('Product IDs (comma separated)', 'marketplace-reviews-for-woocommerce'); ?>" style="margin-left:10px;">
	<div class="description"><?=$product_mess ?></div>
</p>
<script>
jQuery(function($){
	// При выборе товаров из списка — подставляем ID в инпут
	$('#product_id').on('change', function(){
		var ids = $(this).val() || [];
		$('#product_id_manual').val(ids.join(','));
	});
	// При ручном вводе ID — выделяем соответствующие опции
	$('#product_id_manual').on('input', function(){
		var ids = $(this).val().split(',').map(function(v){return v.trim();});
		$('#product_id option').each(function(){
			$(this).prop('selected', ids.includes($(this).val()));
		});
	});

	// Подгрузка товаров при прокрутке вниз
	var loading = false;
	var allLoaded = false;
	$('#product_id').on('scroll', function() {
		var $select = $(this);
		if (loading || allLoaded) return;
		var scrollBottom = $select[0].scrollTop + $select[0].clientHeight;
		if (scrollBottom + 10 >= $select[0].scrollHeight) {
			loading = true;
			var loaded = parseInt($select.attr('data-loaded')) || 0;
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'mp_load_more_products',
					offset: loaded,
					limit: <?php echo (int)$product_limit; ?>
				},
				success: function(response) {
					if (response && response.success && response.data && response.data.length > 0) {
						$.each(response.data, function(i, prod){
							if ($select.find('option[value="'+prod.id+'"]').length === 0) {
								$select.append(
									$('<option>')
										.val(prod.id)
										.text(prod.name + ' (ID: ' + prod.id + ')')
								);
							}
						});
						$select.attr('data-loaded', loaded + response.data.length);
						loading = false;
						if (response.data.length < <?php echo (int)$product_limit; ?>) {
							allLoaded = true;
						}
					} else {
						allLoaded = true;
						loading = false;
					}
				},
				error: function() {
					loading = false;
				}
			});
		}
	});
});
</script>
<?php
// УДАЛИТЬ этот блок из review-meta-fields.php! 
// add_action('wp_ajax_mp_load_more_products', function() { ... });

/*
  ВАЖНО: 
  Код add_action('wp_ajax_mp_load_more_products', ...) должен быть размещён в PHP-файле плагина, который всегда загружается в админке (например, в class-admin.php или отдельном includes/ajax.php).
  В шаблоне (review-meta-fields.php) не должно быть add_action!
*/
$show_rating = true;
$rating_label = __('Rating', 'marketplace-reviews-for-woocommerce');
$star_style = get_option('marketplace_reviews_star_style', 'default');
$star_svg = get_option('marketplace_reviews_star_svg', '');
if (class_exists('Marketplace_Reviews_Settings') && method_exists('Marketplace_Reviews_Settings', 'get_option_lang')) {
    $rating_label = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_popup_rating_label', $rating_label);
}
if ($show_rating) :
?>
<p class="mp-review-field">
    <label for="review_rating"><strong><?php echo esc_html($rating_label); ?></strong></label><br>
    <span class="mp-review-stars">
        <?php
        if ($star_style === 'font') {
            echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">';
        }
        for ($i = 1; $i <= 5; $i++) : ?>
            <label>
                <input type="radio" name="review_rating" value="<?php echo $i; ?>" <?php checked($rating, $i); ?> />
                <?php
                if ($star_style === 'svg' && !empty($star_svg)) {
                    echo '<span class="mp-star-svg">' . $star_svg . '</span>';
                } elseif ($star_style === 'font') {
                    echo '<span class="mp-star-fa"><i class="fa fa-star"></i></span>';
                } else {
                    echo '<span class="mp-star-default">★</span>';
                }
                ?>
            </label>
        <?php endfor; ?>
    </span>
</p>
<?php endif; ?>
<?php if ($this->pluse) : $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_pros_label', 'Pros'); ?>
<p class="mp-review-field">
	<label for="review_pros"><strong><?= $text ?></strong></label><br>
	<textarea name="review_pros" id="review_pros" rows="3" class="widefat"><?php echo esc_textarea($pros); ?></textarea>
</p>
<?php endif; ?>
<?php if ($this->minus) : $text = Marketplace_Reviews_Settings::get_option_lang('marketplace_reviews_cons_label', 'Cons'); ?>
<p class="mp-review-field">
	<label for="review_cons"><strong><?= $text ?></strong></label><br>
	<textarea name="review_cons" id="review_cons" rows="3" class="widefat"><?php echo esc_textarea($cons); ?></textarea>
</p>
<?php endif; ?>
<p class="mp-review-field">
	<label for="moderation_status"><strong><?php esc_html_e('Moderation Status', 'marketplace-reviews-for-woocommerce'); ?></strong></label><br>
	<select name="moderation_status" id="moderation_status">
		<option value="pending" <?php selected($moderation_status, 'pending'); ?>><?php esc_html_e('Pending', 'marketplace-reviews-for-woocommerce'); ?></option>
		<option value="approved" <?php selected($moderation_status, 'approved'); ?>><?php esc_html_e('Approved (Visible)', 'marketplace-reviews-for-woocommerce'); ?></option>
		<option value="rejected" <?php selected($moderation_status, 'rejected'); ?>><?php esc_html_e('Rejected (Hidden)', 'marketplace-reviews-for-woocommerce'); ?></option>
	</select>
</p>
