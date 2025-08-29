<?php
/**
 * Plugin Name: NISU - Named Image Size URLs
 * Description: Replaces the image size from image URLs with the size name.
 * Author: Jeremi Hirvensalo
 * Version: 1.0
 */

defined("ABSPATH") || exit;

define("NISU_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("NISU_PLUGIN_URL", plugin_dir_url(__FILE__));

require_once NISU_PLUGIN_PATH . "inc/image-sizes.php";

new NISU\Image_Sizes();
