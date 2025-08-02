<?php
/**
 * YITH WooCommerce Ajax Filter Integration
 *
 * @package MarketplaceReviews
 */

class Marketplace_Reviews_YITH {

    public function register_review_rating_filter($taxonomies) {
        $taxonomies[] = 'review_rating';
        return $taxonomies;
    }
}
