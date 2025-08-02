<?php
/**
 * Register custom post types and taxonomies for reviews
 *
 * @package    MarketplaceReviews
 * @subpackage MarketplaceReviews/includes
 */
 class Marketplace_Reviews_Post_Types {
public function disable_gutenberg_for_reviews() {
    add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
        if ($post_type === 'marketplace_review') {
            return false;
        }
        return $use_block_editor;
    }, 10, 2);
}

    public function register_post_types() {
        $labels = array(
            'name'                  => _x('Product Reviews', 'Post Type General Name', 'marketplace-reviews-for-woocommerce'),
            'singular_name'         => _x('Product Review', 'Post Type Singular Name', 'marketplace-reviews-for-woocommerce'),
            'menu_name'             => __('Reviews', 'marketplace-reviews-for-woocommerce'),
            'name_admin_bar'        => __('Review', 'marketplace-reviews-for-woocommerce'),
            'archives'              => __('Review Archives', 'marketplace-reviews-for-woocommerce'),
            'attributes'            => __('Review Attributes', 'marketplace-reviews-for-woocommerce'),
            'parent_item_colon'     => __('Parent Review:', 'marketplace-reviews-for-woocommerce'),
            'all_items'             => __('All Reviews', 'marketplace-reviews-for-woocommerce'),
            'add_new_item'          => __('Add New Review', 'marketplace-reviews-for-woocommerce'),
            'add_new'               => __('Add New', 'marketplace-reviews-for-woocommerce'),
            'new_item'              => __('New Review', 'marketplace-reviews-for-woocommerce'),
            'edit_item'             => __('Edit Review', 'marketplace-reviews-for-woocommerce'),
            'update_item'           => __('Update Review', 'marketplace-reviews-for-woocommerce'),
            'view_item'             => __('View Review', 'marketplace-reviews-for-woocommerce'),
            'view_items'            => __('View Reviews', 'marketplace-reviews-for-woocommerce'),
            'search_items'          => __('Search Review', 'marketplace-reviews-for-woocommerce'),
            'not_found'             => __('Not found', 'marketplace-reviews-for-woocommerce'),
            'not_found_in_trash'    => __('Not found in Trash', 'marketplace-reviews-for-woocommerce'),
            'featured_image'        => __('Review Image', 'marketplace-reviews-for-woocommerce'),
            'set_featured_image'    => __('Set review image', 'marketplace-reviews-for-woocommerce'),
            'remove_featured_image' => __('Remove review image', 'marketplace-reviews-for-woocommerce'),
            'use_featured_image'    => __('Use as review image', 'marketplace-reviews-for-woocommerce'),
            'insert_into_item'      => __('Insert into review', 'marketplace-reviews-for-woocommerce'),
            'uploaded_to_this_item' => __('Uploaded to this review', 'marketplace-reviews-for-woocommerce'),
            'items_list'            => __('Reviews list', 'marketplace-reviews-for-woocommerce'),
            'items_list_navigation' => __('Reviews list navigation', 'marketplace-reviews-for-woocommerce'),
            'filter_items_list'     => __('Filter reviews list', 'marketplace-reviews-for-woocommerce'),
        );

        $args = array(
            'label'               => __('Product Review', 'marketplace-reviews-for-woocommerce'),
            'description'         => __('Customer reviews for products', 'marketplace-reviews-for-woocommerce'),
            'labels'              => $labels,
            'supports' => array('title', 'editor', 'author', 'thumbnail'),
            'hierarchical'        => true, // Включаем поддержку саб-постов (дочерних постов)
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => 'marketplace-reviews', // главное меню (slug из add_menu_page)
  
						'menu_position'       => 56,
            'menu_icon'           => 'dashicons-star-filled',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => 'marketplace-reviews',
            'can_export'          => true,
            'has_archive'         => false, // архивная страница будет доступна по /product-reviews/	
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
            'rest_base'           => 'product-reviews',
           'rewrite' => [
							'slug' => 'product-review', // уникальный слаг
							'with_front' => false, // чаще false, чтобы не было дублей
							'pages' => true,
							'feeds' => false,
					],

            'register_meta_box_cb' => array($this, 'remove_default_meta_boxes'),
        );

        register_post_type('marketplace_review', $args);

        // Регистрируем таксономии сразу после post type!
        $this->register_taxonomies();
    }

    public function register_taxonomies() {
        register_taxonomy('review_rating', ['marketplace_review'], [
            'label'        => __('Ratings', 'marketplace-reviews-for-woocommerce'),
            'public'       => true,
            'hierarchical' => false,
            'show_ui'      => true,
            'show_in_rest' => true,
						'labels'       => [
								'menu_name' => __('Ratings', 'marketplace-reviews-for-woocommerce'),
								'all_items' => __('All Ratings', 'marketplace-reviews-for-woocommerce'),
								'edit_item' => __('Edit Rating', 'marketplace-reviews-for-woocommerce'),
								'view_item' => __('View Rating', 'marketplace-reviews-for-woocommerce'),
								'update_item' => __('Update Rating', 'marketplace-reviews-for-woocommerce'),
								'add_new_item' => __('Add New Rating', 'marketplace-reviews-for-woocommerce'),
								'new_item_name' => __('New Rating Name', 'marketplace-reviews-for-woocommerce'),
						],
						'show_in_menu' => true,
						    'show_admin_column' => true,
            'rewrite'      => ['slug' => 'review-rating'],
            // Не используйте show_in_menu для таксономий, это не поддерживается WordPress!
            // Удалите 'show_in_menu' => true,
        ]);

        register_taxonomy('review_product', ['marketplace_review'], [
            'label'        => __('Products', 'marketplace-reviews-for-woocommerce'),
            'public'       => true,
            'hierarchical' => false,
            'show_ui'      => true,
            'show_in_rest' => true,
						'labels'       => [
								'menu_name' => __('Products', 'marketplace-reviews-for-woocommerce'),
								'all_items' => __('All Products', 'marketplace-reviews-for-woocommerce'),
								'edit_item' => __('Edit Product', 'marketplace-reviews-for-woocommerce'),
								'view_item' => __('View Product', 'marketplace-reviews-for-woocommerce'),
								'update_item' => __('Update Product', 'marketplace-reviews-for-woocommerce'),
								'add_new_item' => __('Add New Product', 'marketplace-reviews-for-woocommerce'),
								'new_item_name' => __('New Product Name', 'marketplace-reviews-for-woocommerce'),
						],
						'show_in_menu' => true,
						'show_admin_column' => true,
            'rewrite'      => ['slug' => 'review-product'],
            // Не используйте show_in_menu для таксономий, это не поддерживается WordPress!
        ]);
    }

    public function remove_default_meta_boxes() {
        remove_meta_box('commentsdiv', 'marketplace_review', 'normal');
        remove_meta_box('commentstatusdiv', 'marketplace_review', 'normal');
    }

    public function register_meta_fields() {
        register_post_meta('marketplace_review', 'review_pros', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        register_post_meta('marketplace_review', 'review_cons', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        register_post_meta('marketplace_review', 'product_id', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'integer',
        ]);

        register_post_meta('marketplace_review', 'review_rating', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'integer',
        ]);

        register_post_meta('marketplace_review', 'review_author', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
register_post_meta('marketplace_review', 'review_images', [
    'show_in_rest' => true,
    'single' => true,
    'type' => 'string', // важное отличие!
    'sanitize_callback' => function($value) {
        // Проверяем, что это массив чисел (ID вложений)
        if (is_array($value)) {
            return json_encode(array_map('intval', $value));
        } elseif (is_string($value)) {
            return $value; // уже строка (например, после get_post_meta)
        }
        return '';
    },
    'auth_callback' => function() {
        return current_user_can('edit_posts');
    }
]);


        register_post_meta('marketplace_review', 'moderation_status', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'default' => 'pending',
        ]);
    }
}

//add_action('init', function() {
//    $instance = new Marketplace_Reviews_Post_Types();
//    $instance->register_post_types();
//    $instance->register_taxonomies();
//    $instance->register_meta_fields();
//		remove_post_type_support('marketplace_review', 'comments');
//    remove_post_type_support('marketplace_review', 'trackbacks');
//    $instance->disable_gutenberg_for_reviews(); // <--- вот так!
//});


require_once __DIR__ . '/meta-box-review-images.php';