<?php
/**
 * Plugin Name: Matreshka Master Core
 * Description: Core content types, homepage admin fields, lead forms, and integration points for the Matreshka Master project.
 * Version: 1.0.0
 * Author: Codex
 * Text Domain: matreshka-master
 */

if (! defined('ABSPATH')) {
    exit;
}

define('MM_CORE_VERSION', '1.0.0');
define('MM_CORE_PATH', plugin_dir_path(__FILE__));
define('MM_CORE_URL', plugin_dir_url(__FILE__));

require_once MM_CORE_PATH . 'includes/class-mm-helpers.php';
require_once MM_CORE_PATH . 'includes/class-mm-content.php';
require_once MM_CORE_PATH . 'includes/class-mm-admin.php';
require_once MM_CORE_PATH . 'includes/class-mm-forms.php';
require_once MM_CORE_PATH . 'includes/class-mm-core.php';

register_activation_hook(__FILE__, ['MM_Core', 'activate']);

MM_Core::bootstrap();

function mm_get_option($key, $default = '')
{
    return MM_Helpers::get_option($key, $default);
}

function mm_get_page_meta($post_id, $key, $default = '')
{
    return MM_Helpers::get_page_meta($post_id, $key, $default);
}

function mm_seed_demo_content()
{
    MM_Content::seed_demo_content();
}

