<?php
/**
 * Review display logic for frontend
 *
 * @package    MarketplaceReviews
 * @subpackage MarketplaceReviews/includes
 */
class Marketplace_Reviews_Display {

    public function __construct() {
        add_filter('woocommerce_screen_ids', [$this, 'add_woocommerce_screen_id']);
        add_filter('get_the_archive_title', [$this, 'custom_archive_title']);
        add_action('init', [$this, 'register_review_page_endpoint']);
        add_filter('template_include', [$this, 'load_review_page_template']);
        add_filter('woocommerce_product_tabs', [$this, 'product_reviews_tab']);
        add_action('admin_notices', [$this, 'admin_notice_redis_status']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('init', [$this, 'register_shortcodes']);
			add_action('wp_footer', [$this, 'render_review_popup_global']);
    }
		 public function render_review_popup_global() {
    // Показывать попап только если опция включена
    if (get_option('marketplace_reviews_popup_reminder', 'yes') !== 'yes') return;

    // Показывать только для залогиненных (можно усложнить логику)
    if (!is_user_logged_in()) return;

    // Можно добавить: не показывать на страницах аккаунта/корзины и т.п.
    // if (is_account_page() || is_cart()) return;

    // Вызов шаблона попапа
    if (class_exists('Marketplace_Reviews_Form')) {
        $form = new Marketplace_Reviews_Form();
        $form->render_review_popup();
    }
}


    public function register_review_page_endpoint() {
        // По ID товара
        add_rewrite_rule('^product-reviews/product/([0-9]+)/?', 'index.php?marketplace_reviews_product_id=$matches[1]', 'top');
        add_rewrite_tag('%marketplace_reviews_product_id%', '([0-9]+)');

        // По слагу товара
        add_rewrite_rule('^product-reviews/product/([^/]+)/?', 'index.php?marketplace_reviews_product_slug=$matches[1]', 'top');
        add_rewrite_tag('%marketplace_reviews_product_slug%', '([^&]+)');

        // Отдельный отзыв по ID
        add_rewrite_rule('^product-reviews/review/([0-9]+)/?', 'index.php?marketplace_review_id=$matches[1]', 'top');
        add_rewrite_tag('%marketplace_review_id%', '([0-9]+)');
    }

    public function load_review_page_template($template) {
        $product_id   = absint(get_query_var('marketplace_reviews_product_id'));
        $product_slug = sanitize_title(get_query_var('marketplace_reviews_product_slug'));
        $review_id    = absint(get_query_var('marketplace_review_id'));
        $archive_page_id = absint(get_option('marketplace_reviews_page_id'));

        // Страница товара: /product-reviews/product/ID/
        if ($product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                global $marketplace_reviews_product;
                $marketplace_reviews_product = $product;
                return MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/product-reviews-page.php';
            }
        }

        // Страница товара: /product-reviews/product/slug/
        if ($product_slug) {
            $product_id_by_slug = $this->get_product_id_by_slug($product_slug);
            $product = $product_id_by_slug ? wc_get_product($product_id_by_slug) : false;
            if ($product) {
                global $marketplace_reviews_product;
                $marketplace_reviews_product = $product;
                return MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/product-reviews-page.php';
            }
        }

        // Страница отдельного отзыва: /product-reviews/review/ID/
        if ($review_id) {
            $review = get_post($review_id);
            if ($review && $review->post_type === 'marketplace_review') {
                global $marketplace_review_single;
                $marketplace_review_single = $review;
                return MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/product-reviews-page.php';
            }
        }

        // Архивная страница: /product-reviews/
        if ($archive_page_id && is_page($archive_page_id)) {
            return MARKETPLACE_REVIEWS_PLUGIN_DIR . 'public/templates/product-reviews-page.php';
        }
        return $template;
    }

    // Получить ID товара по слагу
    private function get_product_id_by_slug($slug) {
        $post = get_page_by_path($slug, OBJECT, 'product');
        return $post ? $post->ID : 0;
    }

    // Проверка, включён ли Redis-кеш для отзывов
    public static function is_redis_enabled() {
        $use_redis = get_option('marketplace_reviews_enable_redis', 'no') === 'yes';
        if (!$use_redis) return false;
        if (function_exists('wp_cache_get') && defined('WP_CACHE') && WP_CACHE) return true;
        if (class_exists('Redis')) {
            try {
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379, 1);
                if ($redis->ping() === '+PONG') return true;
            } catch (Exception $e) {}
        }
        return false;
    }

    public static function cache_get($key) {
        if (self::is_redis_enabled()) {
            if (function_exists('wp_cache_get')) return wp_cache_get($key, 'marketplace_reviews');
            if (class_exists('Redis')) {
                static $redis = null;
                if (!$redis) {
                    $redis = new Redis();
                    $redis->connect('127.0.0.1', 6379, 1);
                }
                $value = $redis->get($key);
                return $value !== false ? unserialize($value) : false;
            }
        }
        static $cache = [];
        return isset($cache[$key]) ? $cache[$key] : false;
    }

    public static function cache_set($key, $value, $expire = 300) {
        if (self::is_redis_enabled()) {
            if (function_exists('wp_cache_set')) return wp_cache_set($key, $value, 'marketplace_reviews', $expire);
            if (class_exists('Redis')) {
                static $redis = null;
                if (!$redis) {
                    $redis = new Redis();
                    $redis->connect('127.0.0.1', 6379, 1);
                }
                return $redis->setex($key, $expire, serialize($value));
            }
        }
        static $cache = [];
        $cache[$key] = $value;
        return true;
    }

    public static function get_reviews_for_product($product_id) {
        $cache_key = 'marketplace_reviews_' . (int)$product_id;
        $cached = self::cache_get($cache_key);
        if ($cached !== false) return $cached;
        $reviews = get_posts([
            'post_type' => 'marketplace_review',
            'meta_query' => [
                [
                    'key' => 'product_id',
                    'value' => $product_id,
                    'compare' => '='
                ],
                [
                    'key' => 'moderation_status',
                    'value' => 'approved',
                    'compare' => '='
                ]
            ],
            'posts_per_page' => -1,
            'fields' => 'ids'
        ]);
        self::cache_set($cache_key, $reviews, 300);
        return $reviews;
    }
public function render_reviews_for_product($product_id) {
    $paged = max(1, (int)get_query_var('paged', 1));
    $per_page = 10;

    $all_review_ids = self::get_reviews_for_product($product_id);
    $total = count($all_review_ids);
    $total_pages = $total > 0 ? ceil($total / $per_page) : 1;
    $review_ids = array_slice($all_review_ids, ($paged - 1) * $per_page, $per_page);

    if ($total === 0) {
        echo '<p>' . __('There are no reviews yet.', 'marketplace-reviews-for-woocommerce') . '</p>';
        return;
    }

    echo '<div class="marketplace-reviews-list">';
    foreach ($review_ids as $review_id) {
        $this->render_single_review($review_id);
    }
    echo '</div>';

    $pagination_enabled = get_option('marketplace_reviews_shortcode_pagination', 'yes') === 'yes';
    $showall_enabled = get_option('marketplace_reviews_shortcode_showall_button', 'no') === 'yes';

    if ($pagination_enabled && $total_pages > 1) {
        echo paginate_links([
            'total'   => $total_pages,
            'current' => $paged,
            'format'  => '?paged=%#%',
            'show_all'=> false,
            'type'    => 'plain',
        ]);
    } elseif ($showall_enabled && $total_pages > 1 && $total > $per_page) {
        // "Показать все" ведёт на ту же страницу, но с параметром показать все
        $url = add_query_arg(['showall' => '1']);
        echo '<div class="marketplace-reviews-showall"><a href="' . esc_url($url) . '" class="button">' . __('Show all reviews', 'marketplace-reviews-for-woocommerce') . '</a></div>';
    }

    // Если нажали "Показать все", вывести все отзывы без пагинации
    if (isset($_GET['showall']) && $_GET['showall'] == '1') {
        echo '<div class="marketplace-reviews-list-all">';
        foreach ($all_review_ids as $review_id) {
            $this->render_single_review($review_id);
        }
        echo '</div>';
    }
}

 public function render_single_review($review_id) {
    $review_id = absint($review_id);
    $review = get_post($review_id);
    if (!$review || $review->post_type !== 'marketplace_review') {
        echo '<p>' . __('Review not found.', 'marketplace-reviews-for-woocommerce') . '</p>';
        return;
    }
    $rating = get_post_meta($review_id, 'review_rating', true);
    $pros = get_post_meta($review_id, 'review_pros', true);
    $cons = get_post_meta($review_id, 'review_cons', true);
    $author = get_post_meta($review_id, 'review_author', true);
    $images = get_post_meta($review_id, 'review_images', true);

    // Настройки
    $enable_pros_cons = get_option('marketplace_reviews_enable_pros_cons', 'yes') === 'yes';
    $pros_label = get_option('marketplace_reviews_pros_label', __('Pros', 'marketplace-reviews-for-woocommerce'));
    $cons_label = get_option('marketplace_reviews_cons_label', __('Cons', 'marketplace-reviews-for-woocommerce'));
    $star_style = get_option('marketplace_reviews_star_style', 'default');
    $star_svg = get_option('marketplace_reviews_star_svg', '');

    echo '<div class="review">';
    echo '<div class="review-meta">';
    echo '<strong>' . esc_html($author) . '</strong><br>';
    echo '<span class="review-rating">';
    for ($i = 1; $i <= 5; $i++) {
        switch ($star_style) {
            case 'svg':
                if ($star_svg) {
                    echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">' . $star_svg . '</span>';
                } else {
                    echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">★</span>';
                }
                break;
            case 'font':
                // Требует FontAwesome на сайте!
                echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '"><i class="fa fa-star"></i></span>';
                break;
            default:
                echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">★</span>';
        }
    }
    echo '</span>';
    echo '</div>';
    echo '<div class="review-content">' . wpautop($review->post_content) . '</div>';

    if ($enable_pros_cons) {
        if (!empty($pros)) {
            echo '<p class="review-pros"><strong>' . esc_html($pros_label) . ':</strong> ' . esc_html($pros) . '</p>';
        }
        if (!empty($cons)) {
            echo '<p class="review-cons"><strong>' . esc_html($cons_label) . ':</strong> ' . esc_html($cons) . '</p>';
        }
    }

    if (is_array($images) && !empty($images)) {
        echo '<div class="review-images">';
        foreach ($images as $image_id) {
            echo wp_get_attachment_image($image_id, 'thumbnail');
        }
        echo '</div>';
    }
    echo '</div>';
}

    public function add_woocommerce_screen_id($ids) {
        $ids[] = 'marketplace_review';
        return $ids;
    }

    public function custom_archive_title($title) {
        if (is_post_type_archive('marketplace_review')) {
            return '';
        }
        return $title;
    }

    public function enqueue_styles() {
        wp_enqueue_style('marketplace-reviews-styles', MARKETPLACE_REVIEWS_PLUGIN_URL . 'public/css/reviews.css', [], MARKETPLACE_REVIEWS_VERSION);
        wp_enqueue_style('marketplace-reviews-popup', MARKETPLACE_REVIEWS_PLUGIN_URL . 'public/css/popup.css', [], MARKETPLACE_REVIEWS_VERSION);
    }

    public function enqueue_scripts() {
        wp_enqueue_script('marketplace-reviews-script', MARKETPLACE_REVIEWS_PLUGIN_URL . 'public/js/review-form.js', ['jquery'], MARKETPLACE_REVIEWS_VERSION, true);
        wp_enqueue_script('marketplace-reviews-popup', MARKETPLACE_REVIEWS_PLUGIN_URL . 'public/js/popup.js', ['jquery'], MARKETPLACE_REVIEWS_VERSION, true);

        wp_localize_script('marketplace-reviews-script', 'MarketplaceReviewsData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('marketplace_review_submission'),
            'shouldRemind' => (is_user_logged_in() && get_option('marketplace_reviews_popup_reminder') === 'yes' && $this->has_unreviewed_delivered_products()) ? 'yes' : 'no'
        ]);
    }

    private function has_unreviewed_delivered_products() {
        $user_id = get_current_user_id();
        if (!$user_id) return false;

        $orders = wc_get_orders([
            'customer_id' => $user_id,
            'status' => ['wc-completed'],
            'limit' => -1,
        ]);

        foreach ($orders as $order) {
            if (get_post_meta($order->get_id(), '_order_delivered', true) !== 'yes') {
                continue;
            }
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $existing = new WP_Query([
                    'post_type' => 'marketplace_review',
                    'author' => $user_id,
                    'meta_query' => [[
                        'key' => 'product_id',
                        'value' => $product_id,
                        'compare' => '='
                    ]],
                    'posts_per_page' => 1
                ]);
                if (!$existing->have_posts()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function register_shortcodes() {
        add_shortcode('marketplace_product_reviews', [$this, 'render_product_reviews']);
    }

    // SHORTCODE: [marketplace_product_reviews product_id="ID"] или product_slug / review_id
    public function render_product_reviews($atts = []) {
        ob_start();
        if (!empty($atts['review_id'])) {
            $this->render_single_review(absint($atts['review_id']));
        } elseif (!empty($atts['product_id'])) {
            $this->render_reviews_for_product(absint($atts['product_id']));
        } elseif (!empty($atts['product_slug'])) {
            $product_id = $this->get_product_id_by_slug(sanitize_title($atts['product_slug']));
            if ($product_id) {
                $this->render_reviews_for_product($product_id);
            } else {
                echo '<p>' . __('Product not found.', 'marketplace-reviews-for-woocommerce') . '</p>';
            }
        } else {
            echo '<p>' . __('No product specified.', 'marketplace-reviews-for-woocommerce') . '</p>';
        }
        return ob_get_clean();
    }

    public function product_reviews_tab($tabs) {
        if (get_option('marketplace_reviews_enable_tab', 'yes') !== 'yes') return $tabs;
        $tab_title = get_option('marketplace_reviews_tab_title');
        if (!$tab_title) $tab_title = __('Marketplace Reviews', 'marketplace-reviews-for-woocommerce');
        $tabs['marketplace_reviews'] = [
            'title'    => $tab_title,
            'priority' => 50,
            'callback' => [$this, 'render_product_reviews_tab'],
        ];
        return $tabs;
    }

    public function render_product_reviews_tab() {
        global $product;
        if ($product instanceof WC_Product) {
            $product_id = $product->get_id();
            $this->render_reviews_for_product($product_id);
        }
    }

    // Admin notice, если Redis включён в настройках, но не работает реально
    public function admin_notice_redis_status() {
        if (get_option('marketplace_reviews_enable_redis', 'no') === 'yes' && !self::is_redis_enabled()) {
            echo '<div class="notice notice-error"><p><strong>Marketplace Reviews:</strong> Redis-кеш включён в настройках, но не работает на сервере!</p></div>';
        }
    }
}
