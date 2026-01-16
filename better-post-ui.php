<?php

/**
 * Plugin Name:       Better Post UI
 * Plugin URI:        (#plugin_url#)
 * Description:       Improves the UI and UX of the WordPress admin post form.
 * Version: 3.1.0
 * Author:            Kristoffer Svanmark
 * Author URI:        (#plugin_author_url#)
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       better-post-ui
 * Domain Path:       /languages
 */
use WpService\Implementations\NativeWpService;
use WpUtilService\WpUtilService;

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('BETTERPOSTUI_PATH', plugin_dir_path(__FILE__));
define('BETTERPOSTUI_URL', plugins_url('', __FILE__));
define('BETTERPOSTUI_TEMPLATE_PATH', BETTERPOSTUI_PATH . 'templates/');

load_plugin_textdomain('better-post-ui', false, plugin_basename(dirname(__FILE__)) . '/languages');

// Autoload from plugin
if (file_exists(BETTERPOSTUI_PATH . 'vendor/autoload.php')) {
    require_once BETTERPOSTUI_PATH . 'vendor/autoload.php';
}
require_once BETTERPOSTUI_PATH . 'Public.php';

$wpService = new NativeWpService();
$wpUtilService = new WpUtilService($wpService);

// Start application
new BetterPostUi\App($wpUtilService->enqueue(__DIR__));
