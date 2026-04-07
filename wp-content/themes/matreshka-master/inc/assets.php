<?php

if (! defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'matreshka-master-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'matreshka-master-main',
        get_template_directory_uri() . '/assets/css/main.css',
        ['matreshka-master-fonts'],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'matreshka-master-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );

    wp_localize_script('matreshka-master-main', 'mmTheme', [
        'successMessage' => function_exists('mm_get_option')
            ? mm_get_option('success_message', __('Спасибо. Мы свяжемся с вами в ближайшее время.', 'matreshka-master'))
            : __('Спасибо. Мы свяжемся с вами в ближайшее время.', 'matreshka-master'),
        'formTypes'      => function_exists('MM_Helpers') ? MM_Helpers::get_form_types() : [],
    ]);
});
