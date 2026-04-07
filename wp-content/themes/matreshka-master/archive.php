<?php
get_header();
?>
<main class="content-page">
    <div class="content-page__inner">
        <header class="archive-header">
            <p class="eyebrow"><?php esc_html_e('Archive', 'matreshka-master'); ?></p>
            <h1 class="section-title"><?php the_archive_title(); ?></h1>
            <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
        </header>
        <div class="archive-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('entry-card'); ?>>
                        <h2 class="entry-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="entry-card__content"><?php the_excerpt(); ?></div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <?php the_posts_pagination(); ?>
    </div>
</main>
<?php
get_footer();

