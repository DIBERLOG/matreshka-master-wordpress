<?php

if (! defined('ABSPATH')) {
    exit;
}

function mm_theme_front_page_id()
{
    if (is_front_page() && get_queried_object_id()) {
        return get_queried_object_id();
    }

    return (int) get_option('page_on_front');
}

function mm_theme_home_meta($key, $default = '')
{
    $page_id = mm_theme_front_page_id();

    if ($page_id && function_exists('mm_get_page_meta')) {
        return mm_get_page_meta($page_id, $key, $default);
    }

    if (class_exists('MM_Helpers')) {
        $defaults = MM_Helpers::get_homepage_defaults();
        return $defaults[$key] ?? $default;
    }

    return $default;
}

function mm_theme_visibility($key)
{
    $page_id = mm_theme_front_page_id();
    $defaults = class_exists('MM_Helpers') ? MM_Helpers::get_homepage_defaults() : ['section_visibility' => []];
    $visibility = $page_id ? get_post_meta($page_id, 'mm_section_visibility', true) : [];
    $visibility = is_array($visibility) ? wp_parse_args($visibility, $defaults['section_visibility'] ?? []) : ($defaults['section_visibility'] ?? []);

    return ($visibility[$key] ?? '0') === '1';
}

function mm_theme_option($key, $default = '')
{
    return function_exists('mm_get_option') ? mm_get_option($key, $default) : $default;
}

function mm_theme_t($string)
{
    if (function_exists('pll__')) {
        return pll__($string);
    }

    return __($string, 'matreshka-master');
}

function mm_theme_placeholder_uri($name)
{
    return get_template_directory_uri() . '/assets/img/' . ltrim($name, '/');
}

function mm_theme_showcase_query($category, $limit = 6)
{
    return new WP_Query([
        'post_type'      => 'mm_showcase',
        'posts_per_page' => $limit,
        'orderby'        => ['menu_order' => 'ASC', 'date' => 'ASC'],
        'tax_query'      => [
            [
                'taxonomy' => 'mm_showcase_category',
                'field'    => 'slug',
                'terms'    => $category,
            ],
        ],
    ]);
}

function mm_theme_faq_query()
{
    return new WP_Query([
        'post_type'      => 'mm_faq',
        'posts_per_page' => -1,
        'orderby'        => ['menu_order' => 'ASC', 'date' => 'ASC'],
    ]);
}

function mm_theme_showcase_image($post_id, $field, $fallback)
{
    $image_id = (int) get_post_meta($post_id, 'mm_' . $field, true);

    if ($image_id) {
        $image = wp_get_attachment_image_url($image_id, 'large');
        if ($image) {
            return $image;
        }
    }

    return mm_theme_placeholder_uri($fallback);
}

function mm_theme_languages()
{
    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(['raw' => 1]);
        if (is_array($languages) && $languages) {
            return array_map(function ($language) {
                return [
                    'slug'    => strtoupper($language['slug']),
                    'name'    => $language['name'],
                    'url'     => $language['url'],
                    'current' => ! empty($language['current_lang']),
                ];
            }, $languages);
        }
    }

    if (function_exists('icl_get_languages')) {
        $languages = icl_get_languages();
        if (is_array($languages) && $languages) {
            return array_map(function ($language) {
                return [
                    'slug'    => strtoupper($language['language_code']),
                    'name'    => $language['native_name'],
                    'url'     => $language['url'],
                    'current' => ! empty($language['active']),
                ];
            }, $languages);
        }
    }

    return [
        ['slug' => 'RU', 'name' => 'Russian', 'url' => '#', 'current' => true],
        ['slug' => 'EN', 'name' => 'English', 'url' => '#', 'current' => false],
        ['slug' => 'ZH', 'name' => 'Chinese', 'url' => '#', 'current' => false],
    ];
}

function mm_theme_is_catalog_url($url)
{
    if (! $url) {
        return false;
    }

    return str_starts_with($url, '#');
}

function mm_theme_nav_fallback()
{
    $catalog_url = mm_theme_option('catalog_url', '');
    if (! $catalog_url && class_exists('WooCommerce')) {
        $shop_id = wc_get_page_id('shop');
        if ($shop_id > 0) {
            $catalog_url = get_permalink($shop_id);
        }
    }

    $links = [
        ['label' => mm_theme_t('Главная'), 'url' => home_url('/')],
        ['label' => mm_theme_t('Каталог'), 'url' => $catalog_url ?: '#catalog'],
        ['label' => mm_theme_t('Корпоративные'), 'url' => home_url('/#corporate')],
        ['label' => mm_theme_t('Портретные'), 'url' => home_url('/#portrait')],
        ['label' => mm_theme_t('Контакты'), 'url' => home_url('/#contact')],
    ];

    echo '<ul class="site-menu">';
    foreach ($links as $link) {
        echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['label']) . '</a></li>';
    }
    echo '</ul>';
}

function mm_theme_footer_nav_fallback()
{
    $links = [
        ['label' => mm_theme_t('Главная'), 'url' => home_url('/')],
        ['label' => mm_theme_t('Каталог'), 'url' => home_url('/#catalog')],
        ['label' => 'FAQ', 'url' => home_url('/#faq')],
        ['label' => mm_theme_t('Контакты'), 'url' => home_url('/#contact')],
    ];

    echo '<ul class="footer-menu">';
    foreach ($links as $link) {
        echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['label']) . '</a></li>';
    }
    echo '</ul>';
}
