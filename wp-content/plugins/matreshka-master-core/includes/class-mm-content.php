<?php

if (! defined('ABSPATH')) {
    exit;
}

class MM_Content
{
    public static function register_content_types()
    {
        register_post_type('mm_showcase', [
            'labels' => [
                'name'          => __('Витрины', 'matreshka-master'),
                'singular_name' => __('Карточка витрины', 'matreshka-master'),
            ],
            'public'       => true,
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-images-alt2',
            'supports'     => ['title', 'editor', 'thumbnail', 'page-attributes', 'excerpt'],
            'rewrite'      => ['slug' => 'showcase'],
        ]);

        register_taxonomy('mm_showcase_category', 'mm_showcase', [
            'labels' => [
                'name'          => __('Категории витрин', 'matreshka-master'),
                'singular_name' => __('Категория витрины', 'matreshka-master'),
            ],
            'public'       => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite'      => ['slug' => 'showcase-category'],
        ]);

        register_post_type('mm_faq', [
            'labels' => [
                'name'          => __('FAQ', 'matreshka-master'),
                'singular_name' => __('Вопрос FAQ', 'matreshka-master'),
            ],
            'public'       => false,
            'show_ui'       => true,
            'show_in_rest'  => true,
            'menu_icon'     => 'dashicons-editor-help',
            'supports'      => ['title', 'editor', 'page-attributes'],
        ]);

        register_post_type('mm_lead', [
            'labels' => [
                'name'          => __('Заявки', 'matreshka-master'),
                'singular_name' => __('Заявка', 'matreshka-master'),
            ],
            'public'              => false,
            'show_ui'             => true,
            'menu_icon'           => 'dashicons-email-alt',
            'supports'            => ['title', 'editor'],
            'exclude_from_search' => true,
        ]);
    }

    public static function seed_demo_content()
    {
        self::seed_showcase_categories();
        self::seed_homepage_defaults();
        self::seed_showcases();
        self::seed_faq();

        if (class_exists('WooCommerce')) {
            self::seed_products();
        }
    }

    public static function seed_showcase_categories()
    {
        $terms = [
            'prints'    => __('Printed collections', 'matreshka-master'),
            'corporate' => __('Corporate gifts', 'matreshka-master'),
            'portrait'  => __('Portrait commissions', 'matreshka-master'),
        ];

        foreach ($terms as $slug => $label) {
            if (! term_exists($slug, 'mm_showcase_category')) {
                wp_insert_term($label, 'mm_showcase_category', ['slug' => $slug]);
            }
        }
    }

    public static function seed_homepage_defaults()
    {
        $front_page_id = (int) get_option('page_on_front');
        if (! $front_page_id) {
            return;
        }

        $defaults = MM_Helpers::get_homepage_defaults();
        foreach ($defaults as $key => $value) {
            $meta_key = 'mm_' . $key;
            if (get_post_meta($front_page_id, $meta_key, true) === '') {
                update_post_meta($front_page_id, $meta_key, $value);
            }
        }

        $visibility = get_post_meta($front_page_id, 'mm_section_visibility', true);
        if (! is_array($visibility) || empty($visibility)) {
            update_post_meta($front_page_id, 'mm_section_visibility', $defaults['section_visibility']);
        }
    }

    public static function seed_showcases()
    {
        $items = [
            ['Императорский цветок', 'prints', 'Премиальный набор с насыщенной росписью, лаковой глубиной и коллекционной подачей.', 1],
            ['Серебряная зима', 'prints', 'Сдержанный подарочный комплект в эстетике quiet luxury и музейной аккуратности.', 2],
            ['Музейный noir', 'prints', 'Контрастная коллекционная серия для интерьеров, подарков и частных собраний.', 3],
            ['Executive Crest', 'corporate', 'Корпоративная серия с логотипом, представительской упаковкой и акцентом на бренд.', 1],
            ['Heritage Summit', 'corporate', 'VIP-решение для делегаций, форумов, руководителей и стратегических партнёров.', 2],
            ['Signature Protocol', 'corporate', 'Подарочный набор для топ-менеджмента, событий и церемониальных вручений.', 3],
            ['Портретное наследие', 'portrait', 'Индивидуальная портретная матрёшка по фотографии с точной передачей образа.', 1],
            ['Семейная история', 'portrait', 'Многофигурный набор с тёплой подачей и детальной художественной работой.', 2],
            ['Коллекционный персонаж', 'portrait', 'Лимитированный портретный заказ с акцентом на характер и премиальную отделку.', 3],
        ];

        foreach ($items as $item) {
            [$title, $term_slug, $excerpt, $order] = $item;

            $existing = get_page_by_title($title, OBJECT, 'mm_showcase');
            if ($existing) {
                $post_id = $existing->ID;
            } else {
                $post_id = wp_insert_post([
                    'post_type'    => 'mm_showcase',
                    'post_status'  => 'publish',
                    'post_title'   => $title,
                    'post_excerpt' => $excerpt,
                    'post_content' => $excerpt,
                    'menu_order'   => $order,
                ]);
            }

            if ($post_id && ! is_wp_error($post_id)) {
                wp_set_object_terms($post_id, [$term_slug], 'mm_showcase_category');
                update_post_meta($post_id, 'mm_card_variant', $term_slug);
                self::seed_showcase_media($post_id, $term_slug);
            }
        }
    }

    public static function seed_faq()
    {
        $items = [
            __('Доставляете ли вы по всему миру?', 'matreshka-master') => __('Да. Мы подбираем безопасный формат международной доставки с учётом страны, таможенных требований и ценности заказа.', 'matreshka-master'),
            __('Какие сроки изготовления?', 'matreshka-master') => __('Срок зависит от сложности, тиража и упаковки. После брифа мы подтверждаем понятный календарь производства и контрольные этапы.', 'matreshka-master'),
            __('Есть ли гарантия качества?', 'matreshka-master') => __('Каждый заказ проходит внутренний контроль на этапах эскиза, росписи, лакировки и упаковки перед отправкой.', 'matreshka-master'),
            __('Как заказать портретную матрёшку?', 'matreshka-master') => __('Пришлите фотографии, пожелания, тираж, размер и дедлайн. Мы изучим задачу, предложим концепцию и вернёмся с расчётом.', 'matreshka-master'),
            __('Как оформляется корпоративный заказ?', 'matreshka-master') => __('Сначала уточняем цель, аудиторию, бюджет, тираж и брендирование, затем готовим концепцию, расчёт и план реализации.', 'matreshka-master'),
            __('Есть ли разработка макета?', 'matreshka-master') => __('Да. Мы можем подготовить эскизы, композицию, адаптацию фирменного стиля и концепцию упаковки.', 'matreshka-master'),
            __('Как проходит согласование?', 'matreshka-master') => __('Сначала утверждаем концепцию, затем согласовываем цвет, композицию и ключевые детали перед переходом в тираж.', 'matreshka-master'),
            __('Возможна ли индивидуальная упаковка?', 'matreshka-master') => __('Да. Премиальные коробки, ложементы, обложки и подарочная подача входят в архитектуру проекта.', 'matreshka-master'),
        ];

        $order = 1;
        foreach ($items as $question => $answer) {
            $existing = get_page_by_title($question, OBJECT, 'mm_faq');
            if (! $existing) {
                wp_insert_post([
                    'post_type'    => 'mm_faq',
                    'post_status'  => 'publish',
                    'post_title'   => $question,
                    'post_content' => $answer,
                    'menu_order'   => $order,
                ]);
            }

            $order++;
        }
    }

    public static function seed_products()
    {
        if (! class_exists('WC_Product_Simple')) {
            return;
        }

        $categories = [
            'collectible-sets' => __('Коллекционные наборы', 'matreshka-master'),
            'corporate-gifts'  => __('Корпоративные подарки', 'matreshka-master'),
            'portrait-orders'  => __('Портретные заказы', 'matreshka-master'),
        ];

        foreach ($categories as $slug => $name) {
            if (! term_exists($slug, 'product_cat')) {
                wp_insert_term($name, 'product_cat', ['slug' => $slug]);
            }
        }

        $products = [
            ['Imperial Bloom Set', 'collectible-sets', '16500', 'Коллекционный цветочный набор с лаковой отделкой и премиальной подачей.'],
            ['Winter Silver Set', 'collectible-sets', '18900', 'Премиальная коллекционная серия с серебряными деталями и подарочной подачей.'],
            ['Executive Crest Gift', 'corporate-gifts', '22900', 'Корпоративное решение для брендов, делегаций и руководителей.'],
            ['Protocol Summit Edition', 'corporate-gifts', '24900', 'Представительский набор для форумов, церемоний и VIP-партнёров.'],
            ['Portrait Heirloom Commission', 'portrait-orders', '32000', 'Портретная матрёшка по фото с согласованием образа и композиции.'],
            ['Family Legacy Commission', 'portrait-orders', '44500', 'Семейный портретный набор с индивидуальной подачей и упаковкой.'],
        ];

        foreach ($products as $entry) {
            [$name, $category, $price, $description] = $entry;
            $existing = get_page_by_title($name, OBJECT, 'product');
            if ($existing) {
                $product_id = $existing->ID;
            } else {
                $product = new WC_Product_Simple();
                $product->set_name($name);
                $product->set_status('publish');
                $product->set_catalog_visibility('visible');
                $product->set_regular_price($price);
                $product->set_price($price);
                $product->set_short_description($description);
                $product->set_description($description);
                $product->save();

                $product_id = $product->get_id();
            }

            if ($product_id) {
                wp_set_object_terms($product_id, [$category], 'product_cat');
                self::seed_product_media($product_id, $category);
            }
        }
    }

    public static function seed_showcase_media($post_id, $category)
    {
        $map = [
            'prints' => [
                'showcase_front_image_id' => 'prints-front.svg',
                'showcase_back_image_id'  => 'prints-back.svg',
                'showcase_badge'          => 'Готово',
            ],
            'corporate' => [
                'showcase_front_image_id' => 'corporate-front.svg',
                'showcase_back_image_id'  => 'corporate-back.svg',
                'showcase_badge'          => 'B2B',
            ],
            'portrait' => [
                'showcase_before_image_id' => 'portrait-before.svg',
                'showcase_after_image_id'  => 'portrait-after.svg',
                'showcase_badge'           => 'Индивидуально',
            ],
        ];

        if (! isset($map[$category])) {
            return;
        }

        foreach ($map[$category] as $meta_key => $value) {
            if (str_ends_with($meta_key, '_id')) {
                if (! get_post_meta($post_id, 'mm_' . $meta_key, true)) {
                    $attachment_id = self::ensure_demo_attachment($value);
                    if ($attachment_id) {
                        update_post_meta($post_id, 'mm_' . $meta_key, $attachment_id);
                    }
                }
            } elseif (! get_post_meta($post_id, 'mm_' . $meta_key, true)) {
                update_post_meta($post_id, 'mm_' . $meta_key, $value);
            }
        }
    }

    public static function seed_product_media($product_id, $category)
    {
        $map = [
            'collectible-sets' => ['prints-front.svg', 'prints-back.svg'],
            'corporate-gifts'  => ['corporate-front.svg', 'corporate-back.svg'],
            'portrait-orders'  => ['portrait-after.svg', 'portrait-before.svg'],
        ];

        if (! isset($map[$category])) {
            return;
        }

        $attachment_ids = array_values(array_filter(array_map([__CLASS__, 'ensure_demo_attachment'], $map[$category])));
        if (! $attachment_ids) {
            return;
        }

        if (! get_post_thumbnail_id($product_id)) {
            set_post_thumbnail($product_id, $attachment_ids[0]);
        }

        if (count($attachment_ids) > 1 && ! get_post_meta($product_id, '_product_image_gallery', true)) {
            update_post_meta($product_id, '_product_image_gallery', implode(',', array_slice($attachment_ids, 1)));
        }
    }

    public static function ensure_demo_attachment($filename)
    {
        $existing = get_posts([
            'post_type'      => 'attachment',
            'posts_per_page' => 1,
            'meta_key'       => 'mm_demo_asset_slug',
            'meta_value'     => $filename,
            'fields'         => 'ids',
        ]);

        if (! empty($existing[0])) {
            return (int) $existing[0];
        }

        $source = get_theme_file_path('assets/img/' . $filename);
        if (! file_exists($source)) {
            return 0;
        }

        $upload_dir = wp_upload_dir();
        if (! empty($upload_dir['error'])) {
            return 0;
        }

        wp_mkdir_p($upload_dir['path']);
        $target = trailingslashit($upload_dir['path']) . wp_unique_filename($upload_dir['path'], basename($filename));
        if (! copy($source, $target)) {
            return 0;
        }

        $filetype = wp_check_filetype(basename($target), null);
        $attachment_id = wp_insert_attachment([
            'post_title'     => pathinfo($filename, PATHINFO_FILENAME),
            'post_mime_type' => $filetype['type'] ?: 'image/svg+xml',
            'post_status'    => 'inherit',
        ], $target);

        if (! $attachment_id || is_wp_error($attachment_id)) {
            return 0;
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        $metadata = wp_generate_attachment_metadata($attachment_id, $target);
        if (is_array($metadata)) {
            wp_update_attachment_metadata($attachment_id, $metadata);
        }

        update_post_meta($attachment_id, 'mm_demo_asset_slug', $filename);
        return (int) $attachment_id;
    }
}
