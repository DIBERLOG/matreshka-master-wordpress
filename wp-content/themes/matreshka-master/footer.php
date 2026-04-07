<?php
$privacy_url = mm_theme_option('privacy_url', '');
$delivery_url = mm_theme_option('delivery_url', '');
$offer_url = mm_theme_option('offer_url', '');
?>
</div>
<footer class="site-footer">
    <div class="site-footer__grid">
        <div>
            <p class="site-footer__label"><?php echo esc_html(mm_theme_t('Контакты')); ?></p>
            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', mm_theme_option('phone', ''))); ?>"><?php echo esc_html(mm_theme_option('phone', '+7 (495) 000-00-00')); ?></a>
            <a href="mailto:<?php echo esc_attr(mm_theme_option('email', 'atelier@example.com')); ?>"><?php echo esc_html(mm_theme_option('email', 'atelier@example.com')); ?></a>
            <p><?php echo esc_html(mm_theme_option('address', 'Moscow, Russia')); ?></p>
        </div>
        <div>
            <p class="site-footer__label"><?php echo esc_html(mm_theme_t('Разделы')); ?></p>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'footer-menu',
                'fallback_cb'    => 'mm_theme_footer_nav_fallback',
            ]);
            ?>
        </div>
        <div>
            <p class="site-footer__label"><?php echo esc_html(mm_theme_t('Мессенджеры')); ?></p>
            <div class="site-footer__socials">
                <a href="<?php echo esc_url(mm_theme_option('telegram_url', '#')); ?>">Telegram</a>
                <a href="<?php echo esc_url(mm_theme_option('whatsapp_url', '#')); ?>">WhatsApp</a>
                <a href="<?php echo esc_url(mm_theme_option('instagram_url', '#')); ?>">Instagram</a>
                <a href="<?php echo esc_url(mm_theme_option('wechat_url', '#')); ?>">WeChat</a>
            </div>
        </div>
        <div>
            <p class="site-footer__label"><?php echo esc_html(mm_theme_t('Юридическая информация')); ?></p>
            <?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>"><?php echo esc_html(mm_theme_t('Политика конфиденциальности')); ?></a><?php endif; ?>
            <?php if ($offer_url) : ?><a href="<?php echo esc_url($offer_url); ?>"><?php echo esc_html(mm_theme_t('Оферта / условия')); ?></a><?php endif; ?>
            <?php if ($delivery_url) : ?><a href="<?php echo esc_url($delivery_url); ?>"><?php echo esc_html(mm_theme_t('Оплата и доставка')); ?></a><?php endif; ?>
        </div>
    </div>
    <div class="site-footer__bottom">
        <p>&copy; <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?></p>
    </div>
</footer>
<?php get_template_part('template-parts/forms/lead-form', null, ['type' => 'project', 'modal' => true]); ?>
<?php wp_footer(); ?>
</body>
</html>
