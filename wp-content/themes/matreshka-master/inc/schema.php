<?php

if (! defined('ABSPATH')) {
    exit;
}

add_action('wp_head', function () {
    $title = wp_get_document_title();
    $description = '';
    $image = '';

    if (is_front_page()) {
        $description = mm_theme_home_meta('hero_subtitle', '');
        $poster_id = (int) mm_theme_home_meta('hero_poster_id', 0);
        $image = $poster_id ? wp_get_attachment_image_url($poster_id, 'full') : mm_theme_placeholder_uri('hero-poster.svg');
    } elseif (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            $description = has_excerpt($post) ? wp_strip_all_tags(get_the_excerpt($post)) : wp_trim_words(wp_strip_all_tags($post->post_content), 26);
            if (has_post_thumbnail($post)) {
                $image = get_the_post_thumbnail_url($post, 'full');
            }
        }
    }

    if (! $description) {
        $description = get_bloginfo('description');
    }

    if ($description) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    }

    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr(is_singular('product') ? 'product' : 'website') . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url((is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    if ($image) {
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => get_bloginfo('name') ?: 'Matreshka Master',
        'url'      => home_url('/'),
        'email'    => mm_theme_option('email', ''),
        'telephone'=> mm_theme_option('phone', ''),
        'address'  => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => mm_theme_option('address', ''),
            'addressCountry'  => 'RU',
        ],
        'sameAs'   => array_values(array_filter([
            mm_theme_option('telegram_url', ''),
            mm_theme_option('whatsapp_url', ''),
            mm_theme_option('instagram_url', ''),
            mm_theme_option('wechat_url', ''),
        ])),
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
}, 99);
