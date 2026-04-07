<?php
$catalog_url = mm_theme_option('catalog_url', '');
if (! $catalog_url && class_exists('WooCommerce')) {
    $shop_id = wc_get_page_id('shop');
    if ($shop_id > 0) {
        $catalog_url = get_permalink($shop_id);
    }
}

$hero_poster_id = (int) mm_theme_home_meta('hero_poster_id', 0);
$hero_poster = $hero_poster_id ? wp_get_attachment_image_url($hero_poster_id, 'full') : '';
if (! $hero_poster) {
    $hero_poster = mm_theme_placeholder_uri('hero-poster.svg');
}

$status = isset($_GET['mm_form_status']) ? sanitize_text_field(wp_unslash($_GET['mm_form_status'])) : '';
?>
<main class="homepage">
    <section class="hero" style="--hero-poster:url('<?php echo esc_url($hero_poster); ?>');">
        <?php if (mm_theme_home_meta('hero_video_url', '')) : ?>
            <video class="hero__video" autoplay muted loop playsinline poster="<?php echo esc_url($hero_poster); ?>">
                <source src="<?php echo esc_url(mm_theme_home_meta('hero_video_url', '')); ?>" type="video/mp4">
            </video>
        <?php endif; ?>
        <div class="hero__overlay"></div>
        <div class="hero__content" data-reveal>
            <p class="eyebrow"><?php echo esc_html(mm_theme_home_meta('hero_eyebrow', '')); ?></p>
            <h1 class="hero__title"><?php echo esc_html(mm_theme_home_meta('hero_title', '')); ?></h1>
            <p class="hero__copy"><?php echo esc_html(mm_theme_home_meta('hero_subtitle', '')); ?></p>
            <div class="hero__actions">
                <a class="button" href="<?php echo esc_url($catalog_url ?: '#catalog'); ?>"><?php echo esc_html(mm_theme_home_meta('hero_primary_label', mm_theme_t('Каталог'))); ?></a>
                <button class="button button--ghost" type="button" data-form-trigger data-form-type="project"><?php echo esc_html(mm_theme_home_meta('hero_secondary_label', mm_theme_t('Заказать проект'))); ?></button>
            </div>
        </div>
    </section>

    <?php if (mm_theme_visibility('prints')) : ?>
        <?php get_template_part('template-parts/showcase-section', null, [
            'category'    => 'prints',
            'anchor'      => 'catalog',
            'title'       => mm_theme_home_meta('prints_title', ''),
            'text'        => mm_theme_home_meta('prints_text', ''),
            'cta_label'   => mm_theme_home_meta('prints_cta_label', ''),
            'cta_url'     => $catalog_url ?: mm_theme_home_meta('prints_cta_url', '#catalog'),
            'form_type'   => '',
        ]); ?>
    <?php endif; ?>

    <?php if (mm_theme_visibility('corporate')) : ?>
        <?php get_template_part('template-parts/showcase-section', null, [
            'category'    => 'corporate',
            'anchor'      => 'corporate',
            'title'       => mm_theme_home_meta('corporate_title', ''),
            'text'        => mm_theme_home_meta('corporate_text', ''),
            'cta_label'   => mm_theme_home_meta('corporate_cta_label', ''),
            'cta_url'     => mm_theme_option('guide_url', mm_theme_home_meta('corporate_cta_url', '#contact')),
            'form_type'   => 'corporate',
        ]); ?>
    <?php endif; ?>

    <?php if (mm_theme_visibility('portrait')) : ?>
        <?php get_template_part('template-parts/showcase-section', null, [
            'category'    => 'portrait',
            'anchor'      => 'portrait',
            'title'       => mm_theme_home_meta('portrait_title', ''),
            'text'        => mm_theme_home_meta('portrait_text', ''),
            'cta_label'   => mm_theme_home_meta('portrait_cta_label', ''),
            'cta_url'     => mm_theme_home_meta('portrait_cta_url', '#contact'),
            'form_type'   => 'photo',
        ]); ?>
    <?php endif; ?>

    <?php if (mm_theme_visibility('elite')) : ?>
        <section class="section section--elite" data-reveal>
            <div class="section__intro">
                <p class="eyebrow"><?php echo esc_html(mm_theme_t('Эксклюзивные заказы')); ?></p>
                <h2 class="section-title"><?php echo esc_html(mm_theme_home_meta('elite_title', '')); ?></h2>
            </div>
            <div class="elite-grid">
                <p class="section-copy"><?php echo esc_html(mm_theme_home_meta('elite_text', '')); ?></p>
                <div class="elite-card">
                    <ul class="elite-list">
                        <?php foreach (class_exists('MM_Helpers') ? MM_Helpers::parse_lines(mm_theme_home_meta('elite_features', '')) : [] as $item) : ?>
                            <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button class="button" type="button" data-form-trigger data-form-type="manager"><?php echo esc_html(mm_theme_home_meta('elite_cta_label', mm_theme_t('Связаться с личным менеджером'))); ?></button>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (mm_theme_visibility('workshop')) : ?>
        <section class="section section--workshop" data-reveal>
            <div class="section__intro">
                <p class="eyebrow"><?php echo esc_html(mm_theme_t('Мастерство')); ?></p>
                <h2 class="section-title"><?php echo esc_html(mm_theme_home_meta('workshop_title', '')); ?></h2>
                <p class="section-copy"><?php echo esc_html(mm_theme_home_meta('workshop_text', '')); ?></p>
            </div>

            <div class="workshop-layout">
                <div class="workshop-story">
                    <h3><?php echo esc_html(mm_theme_home_meta('museum_title', '')); ?></h3>
                    <p><?php echo esc_html(mm_theme_home_meta('museum_text', '')); ?></p>
                </div>

                <div class="workshop-stats">
                    <?php foreach ([1, 2, 3] as $index) : ?>
                        <div class="workshop-stat">
                            <strong><?php echo esc_html(mm_theme_home_meta('stat_' . $index . '_value', '')); ?></strong>
                            <span><?php echo esc_html(mm_theme_home_meta('stat_' . $index . '_label', '')); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="workshop-advantages">
                    <?php foreach (class_exists('MM_Helpers') ? MM_Helpers::parse_lines(mm_theme_home_meta('workshop_advantages', '')) : [] as $item) : ?>
                        <div class="workshop-advantage"><?php echo esc_html($item); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="section section--contact" id="contact" data-reveal>
        <div class="section__intro">
            <p class="eyebrow"><?php echo esc_html(mm_theme_t('Формы захвата')); ?></p>
            <h2 class="section-title"><?php echo esc_html(mm_theme_home_meta('contact_title', '')); ?></h2>
            <p class="section-copy"><?php echo esc_html(mm_theme_home_meta('contact_text', '')); ?></p>
        </div>

        <?php if ($status === 'success') : ?>
            <div class="form-message is-success"><?php echo esc_html(mm_theme_option('success_message', mm_theme_t('Спасибо. Мы свяжемся с вами в ближайшее время.'))); ?></div>
        <?php elseif ($status === 'error') : ?>
            <div class="form-message is-error"><?php echo esc_html(mm_theme_t('Укажите имя, хотя бы один контакт и подтвердите согласие.')); ?></div>
        <?php endif; ?>

        <div class="contact-grid">
            <div class="contact-actions">
                <button class="button" type="button" data-form-trigger data-form-type="project"><?php echo esc_html(mm_theme_t('Заказать проект')); ?></button>
                <button class="button button--ghost" type="button" data-form-trigger data-form-type="photo"><?php echo esc_html(mm_theme_t('Узнать стоимость по фото')); ?></button>
                <button class="button button--ghost" type="button" data-form-trigger data-form-type="corporate"><?php echo esc_html(mm_theme_t('Корпоративное предложение')); ?></button>
                <button class="button button--ghost" type="button" data-form-trigger data-form-type="manager"><?php echo esc_html(mm_theme_t('Личный менеджер')); ?></button>
            </div>

            <div class="contact-panel">
                <?php get_template_part('template-parts/forms/lead-form', null, [
                    'type'        => 'project',
                    'title'       => mm_theme_t('Оставить заявку'),
                    'description' => mm_theme_t('Эта форма подходит для общего запроса. Остальные CTA открывают профильные варианты той же формы.'),
                    'modal'       => false,
                ]); ?>
            </div>
        </div>
    </section>

    <?php if (mm_theme_visibility('faq')) : ?>
        <section class="section section--faq" id="faq" data-reveal>
            <div class="section__intro">
                <p class="eyebrow"><?php esc_html_e('FAQ', 'matreshka-master'); ?></p>
                <h2 class="section-title"><?php echo esc_html(mm_theme_home_meta('faq_title', '')); ?></h2>
                <p class="section-copy"><?php echo esc_html(mm_theme_home_meta('faq_text', '')); ?></p>
            </div>
            <?php
            $faq_query = mm_theme_faq_query();
            if ($faq_query->have_posts()) :
                ?>
                <div class="faq-list">
                    <?php while ($faq_query->have_posts()) : $faq_query->the_post(); ?>
                        <article class="faq-item">
                            <button class="faq-item__toggle" type="button" aria-expanded="false">
                                <span><?php the_title(); ?></span>
                                <span class="faq-item__icon">+</span>
                            </button>
                            <div class="faq-item__content">
                                <div class="faq-item__inner"><?php the_content(); ?></div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php
                wp_reset_postdata();
            endif;
            ?>
        </section>
    <?php endif; ?>
</main>
