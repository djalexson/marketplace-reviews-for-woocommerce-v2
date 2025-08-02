<?php 

 $star_style = get_option('marketplace_reviews_star_style', 'default');
    $star_svg = get_option('marketplace_reviews_star_svg', '');

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

?>