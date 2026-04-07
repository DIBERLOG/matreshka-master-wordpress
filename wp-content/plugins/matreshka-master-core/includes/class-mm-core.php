<?php

if (! defined('ABSPATH')) {
    exit;
}

class MM_Core
{
    public static function bootstrap()
    {
        add_action('init', ['MM_Content', 'register_content_types']);
        add_action('init', [__CLASS__, 'register_polylang_strings']);
        add_filter('pll_get_post_types', [__CLASS__, 'register_polylang_post_types'], 10, 2);
        add_filter('pll_get_taxonomies', [__CLASS__, 'register_polylang_taxonomies'], 10, 2);
        add_action('plugins_loaded', [__CLASS__, 'load_textdomain']);
        MM_Admin::init();
        MM_Forms::init();
        MM_Helpers::ensure_default_options();
    }

    public static function activate()
    {
        MM_Helpers::ensure_default_options();
        MM_Content::register_content_types();
        MM_Content::seed_showcase_categories();
        flush_rewrite_rules();
    }

    public static function load_textdomain()
    {
        load_plugin_textdomain('matreshka-master', false, basename(dirname(MM_CORE_PATH)) . '/languages');
    }

    public static function register_polylang_strings()
    {
        if (! function_exists('pll_register_string')) {
            return;
        }

        $settings = MM_Helpers::get_site_settings();
        foreach (['success_message', 'address'] as $key) {
            pll_register_string('matreshka_master_' . $key, $settings[$key] ?? '', 'matreshka-master');
        }

        $strings = [
            'мастерская коллекционных матрёшек',
            'Основная навигация',
            'Каталог',
            'Заказ',
            'Главная',
            'Корпоративные',
            'Портретные',
            'Контакты',
            'Разделы',
            'Мессенджеры',
            'Юридическая информация',
            'Политика конфиденциальности',
            'Оферта / условия',
            'Оплата и доставка',
            'FAQ',
            'Страница не найдена.',
            'Запрошенная страница не существует или была перемещена.',
            'Вернуться на главную',
            'Эксклюзивные заказы',
            'Мастерство',
            'Формы захвата',
            'Укажите имя, хотя бы один контакт и подтвердите согласие.',
            'Заказать проект',
            'Узнать стоимость по фото',
            'Корпоративное предложение',
            'Личный менеджер',
            'Оставить заявку',
            'Эта форма подходит для общего запроса. Остальные CTA открывают профильные варианты той же формы.',
            'Готовые изделия',
            'B2B',
            'Портретные заказы',
            'Назад',
            'Вперёд',
            'Лицевая',
            'Оборот',
            'До',
            'После',
            'Заявка',
            'Имя',
            'Телефон',
            'Компания',
            'Комментарий',
            'Опишите задачу, тираж, референсы, дедлайн и географию доставки.',
            'Прикрепить изображение или ТЗ',
            'Согласен на обработку персональных данных.',
            'Отправить заявку',
            'Закрыть форму',
            'Каталог',
            'Карточка товара с галереей, ценой, описанием и готовностью к покупке через WooCommerce.',
            'Готовые изделия, карточки товаров и архитектура WooCommerce, подготовленная для подключения боевого платёжного модуля.',
        ];

        foreach ($strings as $string) {
            pll_register_string('matreshka_master_ui_' . md5($string), $string, 'matreshka-master');
        }
    }

    public static function register_polylang_post_types($post_types, $is_settings)
    {
        $post_types['mm_showcase'] = 'mm_showcase';
        $post_types['mm_faq'] = 'mm_faq';
        return $post_types;
    }

    public static function register_polylang_taxonomies($taxonomies, $is_settings)
    {
        $taxonomies['mm_showcase_category'] = 'mm_showcase_category';
        return $taxonomies;
    }
}
