<?php
/**
 * Fired during plugin activation
 *
 * @package MarketplaceReviews
 */
class Marketplace_Reviews_Activator {

    public static function activate() {
        // Register CPTs and taxonomies before flushing
        $post_types = new Marketplace_Reviews_Post_Types();
        $post_types->register_post_types();

        if (method_exists($post_types, 'register_taxonomies')) {
            $post_types->register_taxonomies();
        }

        if (method_exists($post_types, 'register_meta_fields')) {
            $post_types->register_meta_fields();
        }

        // Только одна страница-архив
        $page_title = 'Product Reviews';
        $slug = 'product-reviews';
        $existing = get_page_by_path($slug);

        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title'   => $page_title,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '[marketplace_reviews_archive]', // Ставим архивный шорткод
            ]);

            if (!is_wp_error($page_id)) {
                update_option('marketplace_reviews_page_id', $page_id);
            }
        } else {
            update_option('marketplace_reviews_page_id', $existing->ID);
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
