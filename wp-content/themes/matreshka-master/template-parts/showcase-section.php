<?php
$args = wp_parse_args($args ?? [], [
    'category'  => 'prints',
    'anchor'    => '',
    'title'     => '',
    'text'      => '',
    'cta_label' => '',
    'cta_url'   => '',
    'form_type' => '',
]);

$query = mm_theme_showcase_query($args['category'], 9);
$is_portrait = $args['category'] === 'portrait';
$fallbacks = [
    'front'  => $args['category'] . '-front.svg',
    'back'   => $args['category'] . '-back.svg',
    'before' => 'portrait-before.svg',
    'after'  => 'portrait-after.svg',
];
?>
<section class="section section--showcase section--<?php echo esc_attr($args['category']); ?>" <?php if ($args['anchor']) : ?>id="<?php echo esc_attr($args['anchor']); ?>"<?php endif; ?> data-reveal>
    <div class="section__intro">
        <p class="eyebrow"><?php echo esc_html($args['category'] === 'prints' ? mm_theme_t('Готовые изделия') : ($args['category'] === 'corporate' ? 'B2B' : mm_theme_t('Портретные заказы'))); ?></p>
        <h2 class="section-title"><?php echo esc_html($args['title']); ?></h2>
        <p class="section-copy"><?php echo esc_html($args['text']); ?></p>
    </div>

    <div class="section__header-actions">
        <?php if ($args['cta_label']) : ?>
            <?php if ($args['form_type']) : ?>
                <button class="button button--ghost" type="button" data-form-trigger data-form-type="<?php echo esc_attr($args['form_type']); ?>"><?php echo esc_html($args['cta_label']); ?></button>
            <?php else : ?>
                <a class="button button--ghost" href="<?php echo esc_url($args['cta_url']); ?>"><?php echo esc_html($args['cta_label']); ?></a>
            <?php endif; ?>
        <?php endif; ?>
        <div class="carousel-controls">
            <button class="carousel-control" type="button" data-carousel-prev><?php echo esc_html(mm_theme_t('Назад')); ?></button>
            <button class="carousel-control" type="button" data-carousel-next><?php echo esc_html(mm_theme_t('Вперёд')); ?></button>
        </div>
    </div>

    <div class="showcase-carousel" data-carousel>
        <div class="showcase-track" data-carousel-track>
            <?php if ($query->have_posts()) : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $post_id = get_the_ID();
                    $primary = $is_portrait
                        ? mm_theme_showcase_image($post_id, 'showcase_before_image_id', $fallbacks['before'])
                        : mm_theme_showcase_image($post_id, 'showcase_front_image_id', $fallbacks['front']);
                    $secondary = $is_portrait
                        ? mm_theme_showcase_image($post_id, 'showcase_after_image_id', $fallbacks['after'])
                        : mm_theme_showcase_image($post_id, 'showcase_back_image_id', $fallbacks['back']);
                    $badge = get_post_meta($post_id, 'mm_showcase_badge', true);
                    ?>
                    <article class="showcase-card">
                        <div class="showcase-card__media">
                            <div class="showcase-card__visual is-active" data-visual="primary">
                                <img src="<?php echo esc_url($primary); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" decoding="async">
                            </div>
                            <div class="showcase-card__visual" data-visual="secondary">
                                <img src="<?php echo esc_url($secondary); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" decoding="async">
                            </div>
                            <div class="showcase-card__switcher">
                                <button type="button" class="is-active" data-visual-trigger="primary"><?php echo esc_html($is_portrait ? mm_theme_t('До') : mm_theme_t('Лицевая')); ?></button>
                                <button type="button" data-visual-trigger="secondary"><?php echo esc_html($is_portrait ? mm_theme_t('После') : mm_theme_t('Оборот')); ?></button>
                            </div>
                        </div>
                        <div class="showcase-card__body">
                            <?php if ($badge) : ?><span class="showcase-card__badge"><?php echo esc_html($badge); ?></span><?php endif; ?>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
