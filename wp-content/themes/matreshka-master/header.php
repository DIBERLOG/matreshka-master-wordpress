<?php
$catalog_url = mm_theme_option('catalog_url', '');
if (! $catalog_url && class_exists('WooCommerce')) {
    $shop_id = wc_get_page_id('shop');
    if ($shop_id > 0) {
        $catalog_url = get_permalink($shop_id);
    }
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" data-header>
    <div class="site-header__inner">
        <a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="site-brand__title"><?php bloginfo('name'); ?></span>
            <span class="site-brand__subtitle"><?php echo esc_html(mm_theme_t('мастерская коллекционных матрёшек')); ?></span>
        </a>

        <button class="site-header__toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-nav-toggle>
            <span></span><span></span>
        </button>

        <nav class="site-header__nav" id="primary-navigation" aria-label="<?php echo esc_attr(mm_theme_t('Основная навигация')); ?>" data-nav-panel>
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'site-menu',
                'fallback_cb'    => 'mm_theme_nav_fallback',
            ]);
            ?>
            <div class="site-header__actions">
                <?php get_template_part('template-parts/components/language-switcher'); ?>
                <?php if ($catalog_url) : ?>
                    <a class="button button--ghost" href="<?php echo esc_url($catalog_url); ?>"><?php echo esc_html(mm_theme_t('Каталог')); ?></a>
                <?php endif; ?>
                <button class="button" type="button" data-form-trigger data-form-type="project"><?php echo esc_html(mm_theme_t('Заказ')); ?></button>
            </div>
        </nav>
    </div>
</header>
<div class="site-shell">
