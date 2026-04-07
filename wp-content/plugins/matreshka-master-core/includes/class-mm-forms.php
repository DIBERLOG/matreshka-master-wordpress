<?php

if (! defined('ABSPATH')) {
    exit;
}

class MM_Forms
{
    public static function init()
    {
        add_action('admin_post_nopriv_mm_submit_lead', [__CLASS__, 'handle_submission']);
        add_action('admin_post_mm_submit_lead', [__CLASS__, 'handle_submission']);
        add_shortcode('matreshka_master_form', [__CLASS__, 'shortcode']);
    }

    public static function shortcode($atts)
    {
        $atts = shortcode_atts([
            'type'  => 'project',
            'title' => '',
        ], $atts);

        ob_start();
        get_template_part('template-parts/forms/lead-form', null, $atts);
        return ob_get_clean();
    }

    public static function handle_submission()
    {
        if (! isset($_POST['mm_form_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mm_form_nonce'])), 'mm_submit_lead')) {
            self::redirect_with_status('error');
        }

        $type = isset($_POST['form_type']) ? sanitize_text_field(wp_unslash($_POST['form_type'])) : 'project';
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $company = isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '';
        $comment = isset($_POST['comment']) ? sanitize_textarea_field(wp_unslash($_POST['comment'])) : '';
        $consent = isset($_POST['consent']) ? '1' : '0';

        if (! $name || (! $phone && ! $email) || $consent !== '1') {
            self::redirect_with_status('error');
        }

        $attachment_id = 0;
        if (! empty($_FILES['attachment']['name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attachment_id = media_handle_upload('attachment', 0);
            if (is_wp_error($attachment_id)) {
                $attachment_id = 0;
            }
        }

        $lead_id = wp_insert_post([
            'post_type'    => 'mm_lead',
            'post_status'  => 'private',
            'post_title'   => sprintf('%s: %s', ucfirst($type), $name),
            'post_content' => $comment,
        ]);

        if (is_wp_error($lead_id) || ! $lead_id) {
            self::redirect_with_status('error');
        }

        update_post_meta($lead_id, 'mm_form_type', $type);
        update_post_meta($lead_id, 'mm_name', $name);
        update_post_meta($lead_id, 'mm_phone', $phone);
        update_post_meta($lead_id, 'mm_email', $email);
        update_post_meta($lead_id, 'mm_company', $company);
        update_post_meta($lead_id, 'mm_comment', $comment);
        update_post_meta($lead_id, 'mm_consent', $consent);
        if ($attachment_id) {
            update_post_meta($lead_id, 'mm_attachment_id', $attachment_id);
        }

        $payload = [
            'id'         => $lead_id,
            'form_type'  => $type,
            'name'       => $name,
            'phone'      => $phone,
            'email'      => $email,
            'company'    => $company,
            'comment'    => $comment,
            'attachment' => $attachment_id ? wp_get_attachment_url($attachment_id) : '',
            'created_at' => current_time('mysql'),
        ];

        self::dispatch_email($payload);
        self::dispatch_integrations($payload);
        do_action('matreshka_master_form_submitted', $payload, $lead_id);
        self::redirect_with_status('success');
    }

    public static function dispatch_email($payload)
    {
        $recipient = mm_get_option('recipient_email', get_option('admin_email'));
        if (! $recipient) {
            return;
        }

        $subject = sprintf('[Matreshka Master] %s request', ucfirst($payload['form_type']));
        $message = implode("\n", [
            'Form type: ' . $payload['form_type'],
            'Name: ' . $payload['name'],
            'Phone: ' . $payload['phone'],
            'Email: ' . $payload['email'],
            'Company: ' . $payload['company'],
            'Comment: ' . $payload['comment'],
            'Attachment: ' . $payload['attachment'],
        ]);

        wp_mail($recipient, $subject, $message);
    }

    public static function dispatch_integrations($payload)
    {
        foreach ([mm_get_option('bitrix_webhook'), mm_get_option('whatsapp_webhook'), mm_get_option('max_webhook'), mm_get_option('generic_webhook')] as $url) {
            if (! $url) {
                continue;
            }

            wp_remote_post($url, [
                'timeout' => 10,
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => wp_json_encode($payload),
            ]);
        }

        $telegram_token = mm_get_option('telegram_bot_token');
        $telegram_chat_id = mm_get_option('telegram_chat_id');

        if ($telegram_token && $telegram_chat_id) {
            $endpoint = sprintf('https://api.telegram.org/bot%s/sendMessage', rawurlencode($telegram_token));
            $message = implode("\n", [
                'New Matreshka Master lead',
                'Type: ' . $payload['form_type'],
                'Name: ' . $payload['name'],
                'Phone: ' . $payload['phone'],
                'Email: ' . $payload['email'],
                'Company: ' . $payload['company'],
                'Comment: ' . $payload['comment'],
            ]);

            wp_remote_post($endpoint, [
                'timeout' => 10,
                'body'    => [
                    'chat_id' => $telegram_chat_id,
                    'text'    => $message,
                ],
            ]);
        }
    }

    public static function redirect_with_status($status)
    {
        $url = wp_get_referer();
        if (! $url) {
            $url = home_url('/');
        }

        wp_safe_redirect(add_query_arg('mm_form_status', $status, $url));
        exit;
    }
}

