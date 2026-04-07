<?php
$languages = mm_theme_languages();
?>
<div class="language-switcher" aria-label="<?php esc_attr_e('Language switcher', 'matreshka-master'); ?>">
    <?php foreach ($languages as $language) : ?>
        <a class="language-switcher__item <?php echo $language['current'] ? 'is-current' : ''; ?>" href="<?php echo esc_url($language['url']); ?>">
            <?php echo esc_html($language['slug']); ?>
        </a>
    <?php endforeach; ?>
</div>
