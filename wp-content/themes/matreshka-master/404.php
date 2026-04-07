<?php
get_header();
?>
<main class="content-page">
    <div class="content-page__inner not-found">
        <p class="eyebrow"><?php esc_html_e('404', 'matreshka-master'); ?></p>
        <h1 class="section-title"><?php echo esc_html(mm_theme_t('Страница не найдена.')); ?></h1>
        <p><?php echo esc_html(mm_theme_t('Запрошенная страница не существует или была перемещена.')); ?></p>
        <a class="button" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(mm_theme_t('Вернуться на главную')); ?></a>
    </div>
</main>
<?php
get_footer();
