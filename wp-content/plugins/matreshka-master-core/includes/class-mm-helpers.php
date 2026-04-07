<?php

if (! defined('ABSPATH')) {
    exit;
}

class MM_Helpers
{
    public static function translate($string)
    {
        if (function_exists('pll__')) {
            return pll__($string);
        }

        return __($string, 'matreshka-master');
    }

    public static function get_site_settings_defaults()
    {
        return [
            'theme_skin'          => 'midnight-silver',
            'phone'               => '+7 (495) 000-00-00',
            'email'               => 'atelier@example.com',
            'address'             => 'Moscow, Russia',
            'telegram_url'        => '#',
            'whatsapp_url'        => '#',
            'instagram_url'       => '#',
            'wechat_url'          => '#',
            'privacy_url'         => '',
            'delivery_url'        => '',
            'offer_url'           => '',
            'catalog_url'         => '',
            'guide_url'           => '#contact',
            'manager_url'         => '#contact',
            'recipient_email'     => get_option('admin_email'),
            'success_message'     => self::translate('Спасибо. Мы свяжемся с вами в ближайшее время.'),
            'bitrix_webhook'      => '',
            'telegram_bot_token'  => '',
            'telegram_chat_id'    => '',
            'whatsapp_webhook'    => '',
            'max_webhook'         => '',
            'generic_webhook'     => '',
        ];
    }

    public static function get_site_settings()
    {
        $saved = get_option('mm_site_settings', []);

        if (! is_array($saved)) {
            $saved = [];
        }

        return wp_parse_args($saved, self::get_site_settings_defaults());
    }

    public static function ensure_default_options()
    {
        $saved = get_option('mm_site_settings', null);
        if ($saved === null || ! is_array($saved)) {
            update_option('mm_site_settings', self::get_site_settings());
        }
    }

    public static function get_option($key, $default = '')
    {
        $settings = self::get_site_settings();

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        return $default;
    }

    public static function get_homepage_defaults()
    {
        return [
            'section_visibility' => [
                'prints'    => '1',
                'corporate' => '1',
                'portrait'  => '1',
                'elite'     => '1',
                'workshop'  => '1',
                'faq'       => '1',
            ],
            'hero_eyebrow'         => self::translate('Музейная мастерская из России'),
            'hero_title'           => self::translate('Матрёшка №1 в мире'),
            'hero_subtitle'        => self::translate('Ручная роспись, коллекционные изделия, корпоративные подарки и портретные заказы для частных клиентов и B2B-сектора.'),
            'hero_primary_label'   => self::translate('Каталог'),
            'hero_primary_url'     => '#catalog',
            'hero_secondary_label' => self::translate('Заказать проект'),
            'hero_secondary_url'   => '#contact',
            'hero_video_url'       => '',
            'hero_poster_id'       => '',
            'prints_title'         => self::translate('Матрёшки с принтами'),
            'prints_text'          => self::translate('Готовые премиальные наборы с коллекционной подачей, подарочной эстетикой и выразительным визуалом.'),
            'prints_cta_label'     => self::translate('Каталог'),
            'prints_cta_url'       => '#catalog',
            'corporate_title'      => self::translate('Корпоративные подарки'),
            'corporate_text'       => self::translate('Подарочные решения для брендов, HR, маркетинга и первых лиц: логотипы, фирменная подача и премиальное исполнение.'),
            'corporate_cta_label'  => self::translate('Руководство по заказу'),
            'corporate_cta_url'    => '#contact',
            'portrait_title'       => self::translate('Портретные матрёшки'),
            'portrait_text'        => self::translate('Высокая детализация, узнаваемость образа и художественная подача по фотографиям клиента.'),
            'portrait_cta_label'   => self::translate('Узнать стоимость по фото'),
            'portrait_cta_url'     => '#contact',
            'elite_title'          => self::translate('Для элиты'),
            'elite_text'           => self::translate('Частные заказы для VIP-клиентов, дипломатических подарков и представительских коллекций с индивидуальной упаковкой и редкими материалами.'),
            'elite_features'       => "Индивидуальная упаковка\nЦенные породы дерева\nДекоративные металлы и камни\nПерсональное художественное сопровождение",
            'elite_cta_label'      => self::translate('Связаться с личным менеджером'),
            'elite_cta_url'        => '#contact',
            'workshop_title'       => self::translate('О мастерской'),
            'workshop_text'        => self::translate('Matreshka Master соединяет музейный уровень ручной работы с производственной дисциплиной и точным соблюдением сроков.'),
            'museum_title'         => self::translate('Мастерская с музейным характером'),
            'museum_text'          => self::translate('От разработки эскиза и токарной основы до лакировки и упаковки, все этапы остаются под контролем одной команды.'),
            'stat_1_value'         => '12+',
            'stat_1_label'         => self::translate('лет опыта'),
            'stat_2_value'         => '100 000+',
            'stat_2_label'         => self::translate('расписанных матрёшек'),
            'stat_3_value'         => '360°',
            'stat_3_label'         => self::translate('производство полного цикла'),
            'workshop_advantages'  => "Собственная технология производства\nПортфолио музейного уровня\nСвоя лакировочная\nРазработка эскиза\nУпаковка под ключ\nСоблюдение сроков",
            'faq_title'            => self::translate('Часто задаваемые вопросы'),
            'faq_text'             => self::translate('Ключевые ответы для частных клиентов, корпоративных заказчиков и коллекционеров.'),
            'contact_title'        => self::translate('Обсудить проект'),
            'contact_text'         => self::translate('Отправьте задачу, фото, референсы и желаемые сроки. Мы вернёмся с концепцией, расчётом и следующими шагами.'),
        ];
    }

    public static function get_page_meta($post_id, $key, $default = '')
    {
        $defaults = self::get_homepage_defaults();
        $fallback = array_key_exists($key, $defaults) ? $defaults[$key] : $default;
        $value = get_post_meta($post_id, 'mm_' . $key, true);

        if ($value === '' || $value === null) {
            return $fallback;
        }

        return $value;
    }

    public static function parse_lines($value)
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $value);
        $lines = array_map('trim', $lines);

        return array_values(array_filter($lines));
    }

    public static function get_homepage_meta_keys()
    {
        return [
            'hero_eyebrow', 'hero_title', 'hero_subtitle', 'hero_primary_label', 'hero_primary_url',
            'hero_secondary_label', 'hero_secondary_url', 'hero_video_url', 'hero_poster_id',
            'prints_title', 'prints_text', 'prints_cta_label', 'prints_cta_url',
            'corporate_title', 'corporate_text', 'corporate_cta_label', 'corporate_cta_url',
            'portrait_title', 'portrait_text', 'portrait_cta_label', 'portrait_cta_url',
            'elite_title', 'elite_text', 'elite_features', 'elite_cta_label', 'elite_cta_url',
            'workshop_title', 'workshop_text', 'museum_title', 'museum_text',
            'stat_1_value', 'stat_1_label', 'stat_2_value', 'stat_2_label', 'stat_3_value', 'stat_3_label',
            'workshop_advantages', 'faq_title', 'faq_text', 'contact_title', 'contact_text',
        ];
    }

    public static function get_homepage_visibility_keys()
    {
        return ['prints', 'corporate', 'portrait', 'elite', 'workshop', 'faq'];
    }

    public static function get_form_types()
    {
        return [
            'project'   => self::translate('Заказать проект'),
            'photo'     => self::translate('Узнать стоимость по фото'),
            'corporate' => self::translate('Запросить корпоративное предложение'),
            'manager'   => self::translate('Связаться с личным менеджером'),
        ];
    }
}
