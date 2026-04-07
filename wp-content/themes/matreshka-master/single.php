<?php
get_header();
?>
<main class="content-page">
    <div class="content-page__inner">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('entry-article'); ?>>
                <h1 class="entry-article__title"><?php the_title(); ?></h1>
                <div class="entry-article__content"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php
get_footer();

