<?php

if (! defined('ABSPATH')) {
    exit;
}

add_filter('woocommerce_show_page_title', function ($show) {
    if (is_shop() || is_product_taxonomy()) {
        return false;
    }

    return $show;
});

add_filter('woocommerce_catalog_orderby', function ($options) {
    return [
        'menu_order' => 'Сортировка по умолчанию',
        'popularity' => 'По популярности',
        'rating'     => 'По рейтингу',
        'date'       => 'Сначала новые',
        'price'      => 'Сначала дешевле',
        'price-desc' => 'Сначала дороже',
    ];
});

add_filter('woocommerce_product_tabs', function ($tabs) {
    if (isset($tabs['description'])) {
        $tabs['description']['title'] = 'Описание';
    }

    if (isset($tabs['reviews'])) {
        $count = 0;
        global $product;
        if ($product instanceof WC_Product) {
            $count = (int) $product->get_review_count();
        }
        $tabs['reviews']['title'] = sprintf('Отзывы (%d)', $count);
    }

    if (isset($tabs['additional_information'])) {
        $tabs['additional_information']['title'] = 'Характеристики';
    }

    return $tabs;
}, 20);

add_filter('gettext', function ($translated, $text, $domain) {
    if ('woocommerce' !== $domain) {
        return $translated;
    }

    $map = [
        'Showing all %d result'                           => 'Показан %d товар',
        'Showing all %d results'                          => 'Показано %d товаров',
        'Showing the single result'                       => 'Показан 1 товар',
        'Showing %1$d–%2$d of %3$d results'              => 'Показаны товары %1$d–%2$d из %3$d',
        'Default sorting'                                 => 'Сортировка по умолчанию',
        'Sort by popularity'                              => 'По популярности',
        'Sort by average rating'                          => 'По рейтингу',
        'Sort by latest'                                  => 'Сначала новые',
        'Sort by price: low to high'                      => 'Сначала дешевле',
        'Sort by price: high to low'                      => 'Сначала дороже',
        'Add to cart'                                     => 'В корзину',
        'Description'                                     => 'Описание',
        'Reviews'                                         => 'Отзывы',
        'Related products'                                => 'Похожие товары',
        'There are no reviews yet.'                       => 'Отзывов пока нет.',
        'Your rating *'                                   => 'Ваша оценка *',
        'Your review *'                                   => 'Ваш отзыв *',
        'Name *'                                          => 'Имя *',
        'Email *'                                         => 'Email *',
        'Submit'                                          => 'Отправить',
        'Your review'                                     => 'Ваш отзыв',
        'Your rating'                                     => 'Ваша оценка',
        'Name'                                            => 'Имя',
        'Email'                                           => 'Email',
        'Save my name, email, and website in this browser for the next time I comment.' => 'Сохранить моё имя, email и адрес сайта в этом браузере для следующих комментариев.',
        'Your email address will not be published. Required fields are marked %s'       => 'Ваш email не будет опубликован. Обязательные поля отмечены %s',
        'Be the first to review &ldquo;%s&rdquo;'         => 'Оставьте первый отзыв о товаре «%s»',
        'Category:'                                       => 'Категория:',
        'Categories:'                                     => 'Категории:',
    ];

    return $map[$text] ?? $translated;
}, 20, 3);

add_action('wp', function () {
    if (! class_exists('WooCommerce')) {
        return;
    }

    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    add_action('woocommerce_before_shop_loop', 'mm_theme_woocommerce_result_count', 20);
});

function mm_theme_woocommerce_result_count()
{
    if (! woocommerce_products_will_display()) {
        return;
    }

    $total        = (int) wc_get_loop_prop('total');
    $per_page     = (int) wc_get_loop_prop('per_page');
    $current_page = max(1, (int) wc_get_loop_prop('current_page'));
    $first        = ($per_page * ($current_page - 1)) + 1;
    $last         = min($total, $per_page * $current_page);

    if ($total <= 0) {
        return;
    }

    if ($total <= $per_page || -1 === $per_page) {
        $label = sprintf('Показано %d товаров', $total);
    } else {
        $label = sprintf('Показаны товары %1$d–%2$d из %3$d', $first, $last, $total);
    }

    echo '<p class="woocommerce-result-count">' . esc_html($label) . '</p>';
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
