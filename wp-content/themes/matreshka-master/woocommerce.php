<?php

get_header();

$shop_title = is_singular('product') ? get_the_title() : mm_theme_t('Каталог');
$shop_copy = is_singular('product')
    ? mm_theme_t('Карточка товара с галереей, ценой, описанием и готовностью к покупке через WooCommerce.')
    : mm_theme_t('Готовые изделия, карточки товаров и архитектура WooCommerce, подготовленная для подключения боевого платёжного модуля.');
?>
<main class="shop-shell">
    <section class="shop-hero">
        <p class="eyebrow"><?php echo esc_html(mm_theme_t('Каталог')); ?></p>
        <h1 class="section-title"><?php echo esc_html($shop_title); ?></h1>
        <p class="section-copy"><?php echo esc_html($shop_copy); ?></p>
    </section>
    <div class="shop-content">
        <?php woocommerce_content(); ?>
    </div>
</main>
<?php
get_footer();
