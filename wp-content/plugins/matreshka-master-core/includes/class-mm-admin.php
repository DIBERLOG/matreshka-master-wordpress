<?php

if (! defined('ABSPATH')) {
    exit;
}

class MM_Admin
{
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'register_settings_page']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('add_meta_boxes', [__CLASS__, 'register_meta_boxes']);
        add_action('save_post_page', [__CLASS__, 'save_homepage_meta']);
        add_action('save_post_mm_showcase', [__CLASS__, 'save_showcase_meta']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_filter('manage_mm_lead_posts_columns', [__CLASS__, 'lead_columns']);
        add_action('manage_mm_lead_posts_custom_column', [__CLASS__, 'render_lead_columns'], 10, 2);
    }

    public static function enqueue_assets($hook)
    {
        if (in_array($hook, ['post.php', 'post-new.php', 'toplevel_page_mm-site-settings'], true)) {
            wp_enqueue_media();
            wp_enqueue_style('mm-admin', MM_CORE_URL . 'assets/admin.css', [], MM_CORE_VERSION);
            wp_enqueue_script('mm-admin', MM_CORE_URL . 'assets/admin.js', ['jquery'], MM_CORE_VERSION, true);
        }
    }

    public static function register_settings_page()
    {
        add_menu_page(
            __('Matreshka Master', 'matreshka-master'),
            __('Matreshka Master', 'matreshka-master'),
            'manage_options',
            'mm-site-settings',
            [__CLASS__, 'render_settings_page'],
            'dashicons-admin-customizer',
            61
        );
    }

    public static function register_settings()
    {
        register_setting('mm_site_settings_group', 'mm_site_settings', [__CLASS__, 'sanitize_settings']);

        add_settings_section('mm_brand_section', __('Бренд и тема', 'matreshka-master'), '__return_false', 'mm-site-settings');
        add_settings_field('theme_skin', __('Цветовая схема', 'matreshka-master'), [__CLASS__, 'render_select_field'], 'mm-site-settings', 'mm_brand_section', [
            'key'     => 'theme_skin',
            'options' => [
                'midnight-silver' => __('Тёмно-синий + серебро', 'matreshka-master'),
                'porcelain-ink'   => __('Белый + чёрный + серебро', 'matreshka-master'),
            ],
        ]);

        add_settings_section('mm_contact_section', __('Контакты и юридическая информация', 'matreshka-master'), '__return_false', 'mm-site-settings');
        foreach ([
            'phone'         => __('Телефон', 'matreshka-master'),
            'email'         => __('Email', 'matreshka-master'),
            'address'       => __('Адрес', 'matreshka-master'),
            'telegram_url'  => __('Ссылка Telegram', 'matreshka-master'),
            'whatsapp_url'  => __('Ссылка WhatsApp', 'matreshka-master'),
            'instagram_url' => __('Ссылка Instagram', 'matreshka-master'),
            'wechat_url'    => __('Ссылка WeChat', 'matreshka-master'),
            'privacy_url'   => __('URL политики', 'matreshka-master'),
            'delivery_url'  => __('URL оплаты и доставки', 'matreshka-master'),
            'offer_url'     => __('URL оферты', 'matreshka-master'),
        ] as $key => $label) {
            add_settings_field($key, $label, [__CLASS__, 'render_text_field'], 'mm-site-settings', 'mm_contact_section', ['key' => $key]);
        }

        add_settings_section('mm_conversion_section', __('Конверсии и сообщения', 'matreshka-master'), '__return_false', 'mm-site-settings');
        foreach ([
            'catalog_url'     => __('URL каталога', 'matreshka-master'),
            'guide_url'       => __('URL руководства по заказу', 'matreshka-master'),
            'manager_url'     => __('URL личного менеджера', 'matreshka-master'),
            'recipient_email' => __('Email получателя заявок', 'matreshka-master'),
            'success_message' => __('Сообщение об успешной отправке', 'matreshka-master'),
        ] as $key => $label) {
            $callback = $key === 'success_message' ? 'render_textarea_field' : 'render_text_field';
            add_settings_field($key, $label, [__CLASS__, $callback], 'mm-site-settings', 'mm_conversion_section', ['key' => $key]);
        }

        add_settings_section('mm_integrations_section', __('Интеграции', 'matreshka-master'), '__return_false', 'mm-site-settings');
        foreach ([
            'bitrix_webhook'     => __('Bitrix webhook URL', 'matreshka-master'),
            'telegram_bot_token' => __('Telegram bot token', 'matreshka-master'),
            'telegram_chat_id'   => __('Telegram chat ID', 'matreshka-master'),
            'whatsapp_webhook'   => __('WhatsApp webhook URL', 'matreshka-master'),
            'max_webhook'        => __('MAX webhook URL', 'matreshka-master'),
            'generic_webhook'    => __('Generic webhook URL', 'matreshka-master'),
        ] as $key => $label) {
            add_settings_field($key, $label, [__CLASS__, 'render_text_field'], 'mm-site-settings', 'mm_integrations_section', ['key' => $key]);
        }
    }

    public static function sanitize_settings($input)
    {
        $defaults = MM_Helpers::get_site_settings_defaults();
        $sanitized = [];

        foreach ($defaults as $key => $default) {
            $value = isset($input[$key]) ? $input[$key] : $default;

            if (in_array($key, ['success_message', 'address'], true)) {
                $sanitized[$key] = sanitize_textarea_field($value);
            } elseif (in_array($key, ['recipient_email', 'email'], true)) {
                $sanitized[$key] = sanitize_email($value);
            } elseif (str_contains($key, 'url') || str_contains($key, 'webhook')) {
                $sanitized[$key] = esc_url_raw($value);
            } elseif ($key === 'theme_skin') {
                $sanitized[$key] = in_array($value, ['midnight-silver', 'porcelain-ink'], true) ? $value : 'midnight-silver';
            } else {
                $sanitized[$key] = is_string($value) ? sanitize_text_field($value) : $default;
            }
        }

        return $sanitized;
    }

    public static function render_settings_page()
    {
        ?>
        <div class="wrap mm-admin-wrap">
            <h1><?php esc_html_e('Настройки Matreshka Master', 'matreshka-master'); ?></h1>
            <p><?php esc_html_e('Здесь настраиваются глобальные контакты, ссылки, сообщения и интеграционные поля проекта.', 'matreshka-master'); ?></p>
            <form action="options.php" method="post">
                <?php
                settings_fields('mm_site_settings_group');
                do_settings_sections('mm-site-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function render_text_field($args)
    {
        $settings = MM_Helpers::get_site_settings();
        $key = $args['key'];
        ?>
        <input class="regular-text" type="text" name="mm_site_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($settings[$key] ?? ''); ?>">
        <?php
    }

    public static function render_textarea_field($args)
    {
        $settings = MM_Helpers::get_site_settings();
        $key = $args['key'];
        ?>
        <textarea class="large-text" rows="4" name="mm_site_settings[<?php echo esc_attr($key); ?>]"><?php echo esc_textarea($settings[$key] ?? ''); ?></textarea>
        <?php
    }

    public static function render_select_field($args)
    {
        $settings = MM_Helpers::get_site_settings();
        $key = $args['key'];
        ?>
        <select name="mm_site_settings[<?php echo esc_attr($key); ?>]">
            <?php foreach ($args['options'] as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($settings[$key] ?? '', $value); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public static function register_meta_boxes()
    {
        add_meta_box('mm-home-hero', __('Главная: Hero', 'matreshka-master'), [__CLASS__, 'render_home_hero_box'], 'page', 'normal', 'high');
        add_meta_box('mm-home-sections', __('Главная: Секции', 'matreshka-master'), [__CLASS__, 'render_home_sections_box'], 'page', 'normal', 'default');
        add_meta_box('mm-home-elite', __('Главная: Для элиты', 'matreshka-master'), [__CLASS__, 'render_home_elite_box'], 'page', 'normal', 'default');
        add_meta_box('mm-home-workshop', __('Главная: О мастерской', 'matreshka-master'), [__CLASS__, 'render_home_workshop_box'], 'page', 'normal', 'default');
        add_meta_box('mm-home-contact', __('Главная: Контактный блок', 'matreshka-master'), [__CLASS__, 'render_home_contact_box'], 'page', 'side', 'default');
        add_meta_box('mm-showcase-media', __('Медиа витрины', 'matreshka-master'), [__CLASS__, 'render_showcase_box'], 'mm_showcase', 'normal', 'default');
    }

    public static function is_home_builder_page($post)
    {
        if (! $post || $post->post_type !== 'page') {
            return false;
        }

        $template = get_post_meta($post->ID, '_wp_page_template', true);
        return $template === 'page-templates/home-builder.php' || (int) get_option('page_on_front') === (int) $post->ID;
    }

    public static function render_home_hero_box($post)
    {
        if (! self::is_home_builder_page($post)) {
            echo '<p>' . esc_html__('Назначьте шаблон "Homepage Builder" или сделайте страницу статической главной, чтобы редактировать здесь контент главной.', 'matreshka-master') . '</p>';
            return;
        }

        wp_nonce_field('mm_save_homepage_meta', 'mm_homepage_nonce');
        $defaults = MM_Helpers::get_homepage_defaults();
        ?>
        <div class="mm-field-grid">
            <?php self::text_input('hero_eyebrow', __('Подзаголовок', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('hero_title', __('Заголовок', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::textarea_input('hero_subtitle', __('Описание', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('hero_primary_label', __('Текст основной кнопки', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('hero_primary_url', __('URL основной кнопки', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('hero_secondary_label', __('Текст второй кнопки', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('hero_secondary_url', __('URL второй кнопки', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('hero_video_url', __('URL hero-видео', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::media_input('hero_poster_id', __('Постер hero-экрана', 'matreshka-master'), $post->ID, $defaults); ?>
        </div>
        <?php
    }

    public static function render_home_sections_box($post)
    {
        if (! self::is_home_builder_page($post)) {
            echo '<p>' . esc_html__('Поля главной доступны только на странице-конструкторе главной.', 'matreshka-master') . '</p>';
            return;
        }

        $defaults = MM_Helpers::get_homepage_defaults();
        $visibility = get_post_meta($post->ID, 'mm_section_visibility', true);
        $visibility = is_array($visibility) ? $visibility : $defaults['section_visibility'];
        ?>
        <h4><?php esc_html_e('Видимость секций', 'matreshka-master'); ?></h4>
        <div class="mm-checkbox-grid">
            <?php foreach (MM_Helpers::get_homepage_visibility_keys() as $key) : ?>
                <label>
                    <input type="checkbox" name="mm_section_visibility[<?php echo esc_attr($key); ?>]" value="1" <?php checked($visibility[$key] ?? '0', '1'); ?>>
                    <?php echo esc_html(ucfirst($key)); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <hr>
        <div class="mm-field-grid">
            <?php foreach (['prints', 'corporate', 'portrait'] as $section) : ?>
                <div class="mm-field-group">
                    <h4><?php echo esc_html(ucfirst($section)); ?></h4>
                    <?php self::text_input($section . '_title', __('Заголовок', 'matreshka-master'), $post->ID, $defaults); ?>
                    <?php self::textarea_input($section . '_text', __('Описание', 'matreshka-master'), $post->ID, $defaults); ?>
                    <?php self::text_input($section . '_cta_label', __('Текст кнопки', 'matreshka-master'), $post->ID, $defaults); ?>
                    <?php self::text_input($section . '_cta_url', __('URL кнопки', 'matreshka-master'), $post->ID, $defaults); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mm-field-group">
            <h4><?php esc_html_e('Вступление к FAQ', 'matreshka-master'); ?></h4>
            <?php self::text_input('faq_title', __('Заголовок', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::textarea_input('faq_text', __('Описание', 'matreshka-master'), $post->ID, $defaults); ?>
        </div>
        <?php
    }

    public static function render_home_elite_box($post)
    {
        if (! self::is_home_builder_page($post)) {
            echo '<p>' . esc_html__('Поля главной доступны только на странице-конструкторе главной.', 'matreshka-master') . '</p>';
            return;
        }

        $defaults = MM_Helpers::get_homepage_defaults();
        ?>
        <div class="mm-field-grid">
            <?php self::text_input('elite_title', __('Заголовок', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::textarea_input('elite_text', __('Описание', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::textarea_input('elite_features', __('Преимущества, по одному в строке', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('elite_cta_label', __('Текст кнопки', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('elite_cta_url', __('URL кнопки', 'matreshka-master'), $post->ID, $defaults); ?>
        </div>
        <?php
    }

    public static function render_home_workshop_box($post)
    {
        if (! self::is_home_builder_page($post)) {
            echo '<p>' . esc_html__('Поля главной доступны только на странице-конструкторе главной.', 'matreshka-master') . '</p>';
            return;
        }

        $defaults = MM_Helpers::get_homepage_defaults();
        ?>
        <div class="mm-field-grid">
            <?php self::text_input('workshop_title', __('Заголовок секции', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::textarea_input('workshop_text', __('Описание секции', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('museum_title', __('Заголовок музейного блока', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::textarea_input('museum_text', __('Текст музейного блока', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('stat_1_value', __('Значение статистики 1', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('stat_1_label', __('Подпись статистики 1', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('stat_2_value', __('Значение статистики 2', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('stat_2_label', __('Подпись статистики 2', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('stat_3_value', __('Значение статистики 3', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::text_input('stat_3_label', __('Подпись статистики 3', 'matreshka-master'), $post->ID, $defaults); ?>
            <?php self::textarea_input('workshop_advantages', __('Преимущества, по одному в строке', 'matreshka-master'), $post->ID, $defaults); ?>
        </div>
        <?php
    }

    public static function render_home_contact_box($post)
    {
        if (! self::is_home_builder_page($post)) {
            echo '<p>' . esc_html__('Поля главной доступны только на странице-конструкторе главной.', 'matreshka-master') . '</p>';
            return;
        }

        $defaults = MM_Helpers::get_homepage_defaults();
        self::text_input('contact_title', __('Заголовок контактного блока', 'matreshka-master'), $post->ID, $defaults);
        self::textarea_input('contact_text', __('Текст контактного блока', 'matreshka-master'), $post->ID, $defaults);
    }

    public static function render_showcase_box($post)
    {
        wp_nonce_field('mm_save_showcase_meta', 'mm_showcase_nonce');
        ?>
        <div class="mm-field-grid">
            <?php self::media_input('showcase_front_image_id', __('Лицевая сторона', 'matreshka-master'), $post->ID, []); ?>
            <?php self::media_input('showcase_back_image_id', __('Оборотная сторона', 'matreshka-master'), $post->ID, []); ?>
            <?php self::media_input('showcase_before_image_id', __('До', 'matreshka-master'), $post->ID, []); ?>
            <?php self::media_input('showcase_after_image_id', __('После', 'matreshka-master'), $post->ID, []); ?>
            <?php self::text_input('showcase_badge', __('Бейдж', 'matreshka-master'), $post->ID, []); ?>
            <?php self::text_input('card_variant', __('Вариант карточки', 'matreshka-master'), $post->ID, []); ?>
        </div>
        <?php
    }

    public static function text_input($key, $label, $post_id, $defaults)
    {
        $value = get_post_meta($post_id, 'mm_' . $key, true);
        if ($value === '' && isset($defaults[$key])) {
            $value = $defaults[$key];
        }
        ?>
        <p class="mm-field">
            <label for="mm_<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label><br>
            <input class="widefat" type="text" id="mm_<?php echo esc_attr($key); ?>" name="mm_meta[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>">
        </p>
        <?php
    }

    public static function textarea_input($key, $label, $post_id, $defaults)
    {
        $value = get_post_meta($post_id, 'mm_' . $key, true);
        if ($value === '' && isset($defaults[$key])) {
            $value = $defaults[$key];
        }
        ?>
        <p class="mm-field">
            <label for="mm_<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label><br>
            <textarea class="widefat" rows="5" id="mm_<?php echo esc_attr($key); ?>" name="mm_meta[<?php echo esc_attr($key); ?>]"><?php echo esc_textarea($value); ?></textarea>
        </p>
        <?php
    }

    public static function media_input($key, $label, $post_id, $defaults)
    {
        $value = get_post_meta($post_id, 'mm_' . $key, true);
        if ($value === '' && isset($defaults[$key])) {
            $value = $defaults[$key];
        }
        $preview = $value ? wp_get_attachment_image_url((int) $value, 'medium') : '';
        ?>
        <div class="mm-field mm-media-field">
            <label><strong><?php echo esc_html($label); ?></strong></label>
            <input type="hidden" class="mm-media-id" name="mm_meta[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>">
            <div class="mm-media-preview"><?php if ($preview) : ?><img src="<?php echo esc_url($preview); ?>" alt=""><?php endif; ?></div>
            <button type="button" class="button mm-media-upload"><?php esc_html_e('Выбрать изображение', 'matreshka-master'); ?></button>
            <button type="button" class="button-link-delete mm-media-clear"><?php esc_html_e('Очистить', 'matreshka-master'); ?></button>
        </div>
        <?php
    }

    public static function save_homepage_meta($post_id)
    {
        if (! isset($_POST['mm_homepage_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mm_homepage_nonce'])), 'mm_save_homepage_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        $meta = isset($_POST['mm_meta']) ? wp_unslash($_POST['mm_meta']) : [];
        foreach (MM_Helpers::get_homepage_meta_keys() as $key) {
            $raw = $meta[$key] ?? '';
            if (substr($key, -3) === '_id') {
                $value = absint($raw);
            } elseif (str_contains($key, '_url') || $key === 'hero_video_url') {
                $value = esc_url_raw($raw);
            } else {
                $value = sanitize_textarea_field($raw);
            }
            update_post_meta($post_id, 'mm_' . $key, $value);
        }

        $visibility = [];
        $raw_visibility = isset($_POST['mm_section_visibility']) ? wp_unslash($_POST['mm_section_visibility']) : [];
        foreach (MM_Helpers::get_homepage_visibility_keys() as $key) {
            $visibility[$key] = isset($raw_visibility[$key]) ? '1' : '0';
        }

        update_post_meta($post_id, 'mm_section_visibility', $visibility);
    }

    public static function save_showcase_meta($post_id)
    {
        if (! isset($_POST['mm_showcase_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mm_showcase_nonce'])), 'mm_save_showcase_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        $meta = isset($_POST['mm_meta']) ? wp_unslash($_POST['mm_meta']) : [];
        foreach (['showcase_front_image_id', 'showcase_back_image_id', 'showcase_before_image_id', 'showcase_after_image_id', 'showcase_badge', 'card_variant'] as $key) {
            $raw = $meta[$key] ?? '';
            $value = strpos($key, '_id') !== false ? absint($raw) : sanitize_text_field($raw);
            update_post_meta($post_id, 'mm_' . $key, $value);
        }
    }

    public static function lead_columns($columns)
    {
        $columns['form_type'] = __('Тип формы', 'matreshka-master');
        $columns['lead_phone'] = __('Телефон', 'matreshka-master');
        $columns['lead_email'] = __('Email', 'matreshka-master');
        return $columns;
    }

    public static function render_lead_columns($column, $post_id)
    {
        if ($column === 'form_type') {
            echo esc_html(get_post_meta($post_id, 'mm_form_type', true));
        } elseif ($column === 'lead_phone') {
            echo esc_html(get_post_meta($post_id, 'mm_phone', true));
        } elseif ($column === 'lead_email') {
            echo esc_html(get_post_meta($post_id, 'mm_email', true));
        }
    }
}
