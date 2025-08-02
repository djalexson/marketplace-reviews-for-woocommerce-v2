<?php
/**
 * Fired during plugin deactivation
 *
 * @package MarketplaceReviews
 */

class Marketplace_Reviews_Deactivator {

    public static function deactivate() {
        // Flush rewrite rules to remove custom endpoints
        flush_rewrite_rules();
    }
}
