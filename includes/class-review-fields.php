<?php
/**
 * Simple helper for review custom fields.
 *
 * @package MarketplaceReviews
 */
class Marketplace_Review_Fields {
    public static function get($post_id, $key) {
        return get_post_meta($post_id, $key, true);
    }

    public static function update($post_id, $key, $value) {
        return update_post_meta($post_id, $key, $value);
    }
}
