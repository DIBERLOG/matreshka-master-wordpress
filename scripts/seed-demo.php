<?php
if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('wp_insert_post')) {
    return;
}

$home = get_page_by_path('home');
if (! $home) {
    $home_id = wp_insert_post([
        'post_type'   => 'page',
        'post_status' => 'publish',
        'post_title'  => 'Главная',
        'post_name'   => 'home',
    ]);
} else {
    $home_id = $home->ID;
}

if ($home_id) {
    update_option('page_on_front', $home_id);
    update_post_meta($home_id, '_wp_page_template', 'page-templates/home-builder.php');
}

$privacy = get_page_by_path('privacy-policy');
if (! $privacy) {
    $privacy_id = wp_insert_post([
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => 'Политика конфиденциальности',
        'post_name'    => 'privacy-policy',
        'post_content' => 'Замените этот текст на финальную политику конфиденциальности перед публикацией сайта.',
    ]);
} else {
    $privacy_id = $privacy->ID;
}

if ($privacy_id) {
    update_option('wp_page_for_privacy_policy', $privacy_id);
}

$delivery = get_page_by_path('payment-and-delivery');
if (! $delivery) {
    $delivery_id = wp_insert_post([
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => 'Оплата и доставка',
        'post_name'    => 'payment-and-delivery',
        'post_content' => 'Замените этот текст на финальные условия оплаты, доставки и возврата.',
    ]);
} else {
    $delivery_id = $delivery->ID;
}

if (! function_exists('mm_get_option')) {
    return;
}

$settings = get_option('mm_site_settings', []);
if (! is_array($settings)) {
    $settings = [];
}

if ($privacy_id) {
    $settings['privacy_url'] = get_permalink($privacy_id);
}

if ($delivery_id) {
    $settings['delivery_url'] = get_permalink($delivery_id);
}

if (class_exists('WooCommerce')) {
    $shop_id = wc_get_page_id('shop');
    if ($shop_id && $shop_id > 0) {
        $settings['catalog_url'] = get_permalink($shop_id);
    }
}

update_option('mm_site_settings', wp_parse_args($settings, MM_Helpers::get_site_settings_defaults()));

if (function_exists('mm_seed_demo_content')) {
    mm_seed_demo_content();
}

echo "Seed complete.\n";
