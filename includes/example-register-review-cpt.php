// Пример регистрации кастомного типа записи "review" для отзывов с поддержкой иерархии и без архивной страницы
add_action('init', function() {
    register_post_type('review', [
        'label'         => 'Отзывы',
        'public'        => true,
        'hierarchical'  => true, // Включаем поддержку дочерних записей (ответы)
        'supports'      => ['title', 'editor', 'author'],
        'show_ui'       => true,
        'show_in_menu'  => true,
        'has_archive'   => false, // Отключаем архивную страницу
        'rewrite'       => ['slug' => 'review'],
        'menu_icon'     => 'dashicons-testimonial',
        'labels'        => [
            'name'          => 'Отзывы',
            'singular_name' => 'Отзыв',
            'add_new'       => 'Добавить отзыв',
            'add_new_item'  => 'Добавить новый отзыв',
            'edit_item'     => 'Редактировать отзыв',
            'new_item'      => 'Новый отзыв',
            'view_item'     => 'Просмотреть отзыв',
            'search_items'  => 'Искать отзывы',
            'not_found'     => 'Отзывы не найдены',
        ],
    ]);
});
