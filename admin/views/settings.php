<?php
/**
 * Admin settings page template
 *
 * @package MarketplaceReviews
 */

// Подключаем стили и скрипты для страницы настроек (если не подключаются глобально)
wp_enqueue_style('marketplace-reviews-admin-settings');
wp_enqueue_script('marketplace-reviews-admin-settings');
?>
<div class="wrap marketplace-reviews-settings">
    <h1><?php echo esc_html__('Marketplace Reviews Settings', 'marketplace-reviews-for-woocommerce'); ?></h1>
    
    <!-- Навигация по вкладкам -->
    <nav class="nav-tab-wrapper marketplace-nav-tabs">
        <?php foreach ($this->tabs as $tab_key => $tab_data): ?>
            <a href="<?php echo admin_url('admin.php?page=marketplace_reviews_settings&tab=' . $tab_key); ?>" 
               class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                <span class="tab-icon"><?php echo $tab_data['icon']; ?></span>
                <?php echo esc_html__($tab_data['title'], 'marketplace-reviews-for-woocommerce'); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Содержимое вкладки -->
    <div class="tab-content">
        <form method="post" action="options.php">
            <?php
            // --- изменено: использовать группу по табу ---
            settings_fields('marketplace_reviews_settings_group_' . $active_tab);
            do_settings_sections('marketplace_reviews_settings_' . $active_tab);
										submit_button(__('Save Settings', 'marketplace-reviews-for-woocommerce'), 'primary save-button', 'submit', true);
            ?>
        </form>
    </div>
</div>
