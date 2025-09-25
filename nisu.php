<?php
/**
 * Plugin Name: NISU - Named Image Size URLs
 * Description: Replaces the image size from image URLs with the size name.
 * Author: Jeremi Hirvensalo
 * Version: 0.1.0
 */

defined("ABSPATH") || exit;

define("NISU_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("NISU_PLUGIN_URL", plugin_dir_url(__FILE__));

foreach(glob(NISU_PLUGIN_PATH . "inc/{**/*,*}.php", GLOB_BRACE) as $filename) {
  require_once $filename;
} 

add_filter("wp_generate_attachment_metadata", "\NISU\Main::rename_image_sizes", 10, 1);
