<?php
$args = wp_parse_args($args ?? [], [
    'type'        => 'project',
    'title'       => '',
    'description' => '',
    'modal'       => false,
]);

$form_types = class_exists('MM_Helpers') ? MM_Helpers::get_form_types() : [];
$type = $args['type'];
$title = $args['title'] ?: ($form_types[$type] ?? mm_theme_t('Заказать проект'));
$description = $args['description'];
$wrapper_class = $args['modal'] ? 'lead-form-modal' : 'lead-form-panel';
?>
<div class="<?php echo esc_attr($wrapper_class); ?>" <?php if ($args['modal']) : ?>data-form-modal hidden<?php endif; ?>>
    <div class="<?php echo $args['modal'] ? 'lead-form-modal__dialog' : 'lead-form-panel__inner'; ?>">
        <?php if ($args['modal']) : ?>
            <button class="lead-form-modal__close" type="button" aria-label="<?php echo esc_attr(mm_theme_t('Закрыть форму')); ?>" data-form-close>&times;</button>
        <?php endif; ?>

        <div class="lead-form-panel__header">
            <p class="eyebrow"><?php echo esc_html(mm_theme_t('Заявка')); ?></p>
            <h3 data-form-title><?php echo esc_html($title); ?></h3>
            <?php if ($description) : ?><p><?php echo esc_html($description); ?></p><?php endif; ?>
        </div>

        <form class="lead-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="mm_submit_lead">
            <input type="hidden" name="form_type" value="<?php echo esc_attr($type); ?>" data-form-type-input>
            <?php wp_nonce_field('mm_submit_lead', 'mm_form_nonce'); ?>

            <div class="lead-form__grid">
                <label>
                    <span><?php echo esc_html(mm_theme_t('Имя')); ?></span>
                    <input type="text" name="name" required>
                </label>
                <label>
                    <span><?php echo esc_html(mm_theme_t('Телефон')); ?></span>
                    <input type="text" name="phone">
                </label>
                <label>
                    <span><?php esc_html_e('Email', 'matreshka-master'); ?></span>
                    <input type="email" name="email">
                </label>
                <label>
                    <span><?php echo esc_html(mm_theme_t('Компания')); ?></span>
                    <input type="text" name="company">
                </label>
            </div>

            <label class="lead-form__full">
                <span><?php echo esc_html(mm_theme_t('Комментарий')); ?></span>
                <textarea name="comment" rows="5" placeholder="<?php echo esc_attr(mm_theme_t('Опишите задачу, тираж, референсы, дедлайн и географию доставки.')); ?>"></textarea>
            </label>

            <label class="lead-form__full lead-form__file">
                <span><?php echo esc_html(mm_theme_t('Прикрепить изображение или ТЗ')); ?></span>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip">
            </label>

            <label class="lead-form__consent">
                <input type="checkbox" name="consent" value="1" required>
                <span><?php echo esc_html(mm_theme_t('Согласен на обработку персональных данных.')); ?></span>
            </label>

            <button class="button" type="submit"><?php echo esc_html(mm_theme_t('Отправить заявку')); ?></button>
        </form>
    </div>
</div>
