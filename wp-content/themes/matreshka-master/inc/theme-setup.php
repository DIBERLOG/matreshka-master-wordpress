<?php

if (! defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    load_theme_textdomain('matreshka-master', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', [
        'height'      => 96,
        'width'       => 320,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus([
        'primary' => __('Primary Menu', 'matreshka-master'),
        'footer'  => __('Footer Menu', 'matreshka-master'),
    ]);
});

add_filter('body_class', function ($classes) {
    $skin = function_exists('mm_get_option') ? mm_get_option('theme_skin', 'midnight-silver') : 'midnight-silver';
    $classes[] = 'skin-' . sanitize_html_class($skin);

    if (is_front_page()) {
        $classes[] = 'is-front-page';
    }

    return $classes;
});

