<?php
get_header();
?>
<main class="content-page">
    <div class="content-page__inner">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('entry-card'); ?>>
                    <h1 class="entry-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                    <div class="entry-card__content"><?php the_excerpt(); ?></div>
                </article>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e('Nothing found.', 'matreshka-master'); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();

