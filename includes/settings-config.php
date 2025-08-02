<?php

return [
    'settings_config' => [
             // Основные настройки
        'marketplace_reviews_admin_notifications' => [
            'type' => 'checkbox', 'default' => 'yes', 'label' => 'Notify Admin on New Reviews', 'tab' => 'general'
        ],
        'marketplace_reviews_review_trigger_status' => [
            'type' => 'status_select', 'default' => 'wc-completed', 'label' => 'Order Status to Trigger Review', 'tab' => 'general'
        ],
        'marketplace_reviews_popup_reminder' => [
            'type' => 'checkbox', 'default' => 'yes', 'label' => 'Remind User to Leave Review via Popup', 'tab' => 'general'
        ],

        // Интеграции
        'marketplace_reviews_elementor_support' => [
            'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Elementor Support', 'tab' => 'integrations'
        ],
        'marketplace_reviews_enable_redis' => [
            'type' => 'checkbox', 'default' => 'no', 'label' => 'Enable Redis Cache for Reviews', 'tab' => 'integrations'
        ],

        // Страницы
        'marketplace_reviews_archive_page_id' => [
            'type' => 'page_select', 'default' => 0, 'label' => 'Archive Reviews Page', 'tab' => 'pages'
        ],
        'marketplace_reviews_product_page_id' => [
            'type' => 'page_select', 'default' => 0, 'label' => 'Product Reviews Page', 'tab' => 'pages'
        ],

        // Плюсы и минусы
        'marketplace_reviews_enable_pros' => [
            'type' => 'checkbox', 
						'default' => 'yes', 
						'label' => 'Enable Pros Block', 
						'tab' => 'features',
						'hide_fields' => [
								'marketplace_reviews_pros_label',
						]
        ],
        'marketplace_reviews_enable_cons' => [
            'type' => 'checkbox', 
						'default' => 'yes', 
						'label' => 'Enable Cons Block', 
						'tab' => 'features',
						'hide_fields' => [
								'marketplace_reviews_cons_label',	
						]
        ],
        'marketplace_reviews_pros_label' => [
            'type' => 'text', 'default' => 'Pros', 'label' => 'Label for Pros Block', 'multilang' => true, 'tab' => 'features'
        ],
        'marketplace_reviews_cons_label' => [
            'type' => 'text', 'default' => 'Cons', 'label' => 'Label for Cons Block', 'multilang' => true, 'tab' => 'features'
        ],

        // Вкладки
        'marketplace_reviews_enable_tab' => [
            'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Product Tab', 'tab' => 'display'
        ],
        'marketplace_reviews_tab_title' => [
            'type' => 'text', 'default' => 'Marketplace Reviews', 'label' => 'Tab Title', 'multilang' => true, 'tab' => 'display'
        ],

        // Рейтинг и звёзды
        'marketplace_reviews_show_average_rating' => [
            'type' => 'checkbox', 'default' => 'yes', 'label' => 'Show Average Product Rating', 'tab' => 'display'
        ],
        'marketplace_reviews_star_style' => [
            'type' => 'star_style_select',
            'default' => 'default',
            'label' => 'Star Icon Style',
            'tab' => 'display'
        ],
        'marketplace_reviews_star_svg' => [
            'type' => 'svg_input',
            'default' => '',
            'label' => 'Upload SVG for Stars (Paste SVG Code)',
            'tab' => 'display',
            'show_if' => [
                [
                    'field' => 'marketplace_reviews_star_style',
                    'value' => 'svg'
                ],
      
            ]
        ],

        // Пагинация
        'marketplace_reviews_shortcode_pagination' => [
            'type' => 'checkbox', 'default' => 'yes', 'label' => 'Enable Pagination in Shortcode/Tab', 'tab' => 'display'
        ],
        'marketplace_reviews_shortcode_showall_button' => [
            'type' => 'checkbox', 'default' => 'no', 'label' => 'Show "Show All" Button in Shortcode/Tab', 'tab' => 'display'
        ],

        // Попап
        'marketplace_reviews_popup_title' => [
            'type' => 'text', 'default' => 'Leave a Review', 'label' => 'Popup Title', 'multilang' => true, 'tab' => 'popup'
        ],
        'marketplace_reviews_popup_button' => [
            'type' => 'text', 'default' => 'Submit Review', 'label' => 'Popup Button Text', 'multilang' => true, 'tab' => 'popup'
        ],
        'marketplace_reviews_popup_button_pul' => [
            'type' => 'text', 'default' => 'Submitting...', 'label' => 'Popup Button Loading Text', 'multilang' => true, 'tab' => 'popup'
        ],
        'marketplace_reviews_popup_rating_label' => [
            'type' => 'text', 'default' => 'Rating', 'label' => 'Popup Rating Label', 'multilang' => true, 'tab' => 'popup'
        ],

        // Фото
        'marketplace_reviews_enable_photos' => [
            'type' => 'checkbox',
            'default' => 'yes',
            'label' => 'Enable Photo Uploads',
            'tab' => 'features',
            // Управление скрытием других полей:
            'hide_fields' => [
                'marketplace_reviews_max_images',
                'marketplace_reviews_upload_label'
            ]
        ],
        'marketplace_reviews_max_images' => [
            'type' => 'number', 'default' => 5, 'label' => 'Max Images', 'tab' => 'features'
        ],
        'marketplace_reviews_upload_label' => [
            'type' => 'text', 'default' => 'Upload Images (optional)', 'label' => 'Upload Block Label', 'multilang' => false, 'tab' => 'features'
        ],

        // Тексты кнопок и сообщений
        'marketplace_reviews_text_pending' => [
            'type' => 'text', 'default' => 'Review pending moderation', 'label' => 'Text: Review Pending', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_view_review' => [
            'type' => 'text', 'default' => 'View review', 'label' => 'Text: View Review', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_submitted' => [
            'type' => 'text', 'default' => 'Review submitted', 'label' => 'Text: Review Submitted', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_not_completed' => [
            'type' => 'text', 'default' => 'Available after order is completed', 'label' => 'Text: Order Not Completed', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_leave' => [
            'type' => 'text', 'default' => 'Leave a review', 'label' => 'Text: Leave Review', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_already' => [
            'type' => 'text', 'default' => 'Already reviewed', 'label' => 'Text: Already Reviewed', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_security_fail' => [
            'type' => 'text', 'default' => 'Security check failed.', 'label' => 'Text: Security Failed', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_login_required' => [
            'type' => 'text', 'default' => 'You must be logged in to submit a review.', 'label' => 'Text: Login Required', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_cannot_leave' => [
            'type' => 'text', 'default' => 'You cannot leave a review for this product.', 'label' => 'Text: Cannot Leave Review', 'multilang' => true, 'tab' => 'texts'
        ],
        'marketplace_reviews_text_submit_success' => [
            'type' => 'text', 'default' => 'Thank you! Your review has been submitted and is awaiting moderation.', 'label' => 'Text: Review Submitted Successfully', 'multilang' => true, 'tab' => 'texts'
        ],

        // Email notifications
        'marketplace_reviews_email_subject' => [
            'type' => 'text',
            'default' => '[{site_name}] New Product Review: {product}',
            'label' => 'Email Subject Template',
            'multilang' => true,
            'tab' => 'email'
        ],
        'marketplace_reviews_email_body' => [
            'type' => 'textarea',
            'default' => "A new review has been submitted for {product}.\n\nRating: {rating}/5\nAuthor: {author}\n\nReview: {content}\n\nManage this review: {edit_link}",
            'label' => 'Email Body Template',
            'multilang' => true,
            'tab' => 'email'
        ],

        // Service (Служебная кнопка для создания страниц)
        'marketplace_reviews_create_pages' => [
            'type' => 'create_pages_button',
            'label' => 'Create Default Pages',
            'tab' => 'pages'
        ],

        // --- Новые настройки для выбора товара в отзыве ---
        'marketplace_reviews_product_select_limit' => [
            'type' => 'number',
            'default' => 8,
            'label' => 'Max Products in Review Select',
            'tab' => 'features'
        ],
        'marketplace_reviews_product_select_placeholder' => [
            'type' => 'text',
            'default' => '— Select Product —',
            'label' => 'Product Select Placeholder',
            'multilang' => true,
            'tab' => 'features'
        ],
        'marketplace_reviews_product_label' => [
            'type' => 'text',
            'default' => 'Product',
            'label' => 'Product ',
            'multilang' => true,
            'tab' => 'features'
        ],
        'marketplace_reviews_product_mess' => [
            'type' => 'text',
            'default' => 'Product message',
            'label' => 'Select one or more products from the list or enter IDs separated by commas.',
            'multilang' => true,
            'tab' => 'features'
        ],
    ],
    'tabs' => [
        'general' => [
            'title' => 'General Settings',
            'icon' => '⚙️'
        ],
        'display' => [
            'title' => 'Display Settings',
            'icon' => '🎨'
        ],
        'features' => [
            'title' => 'Features',
            'icon' => '✨'
        ],
        'popup' => [
            'title' => 'Popup Settings',
            'icon' => '💬'
        ],
        'pages' => [
            'title' => 'Pages',
            'icon' => '📄'
        ],
        'texts' => [
            'title' => 'Text Labels',
            'icon' => '📝'
        ],
        'email' => [
            'title' => 'Email Settings',
            'icon' => '📧'
        ],
        'integrations' => [
            'title' => 'Integrations',
            'icon' => '🔗'
        ]
    ]
];
