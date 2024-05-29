<?php
/*
 * Plugin Name:       LAP Matomo HTTP Tracking
 * Description:       Handle sending the page tracking to Matomo via HTTP rather than JavaScript.
 * Version:           1.1.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            EncodeDotHost
 * Author URI:        https://encode.host/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lap-matomo-http-api
 * Domain Path:       /languages
 */

 if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

include_once plugin_dir_path( __FILE__ ) .'options-page.php';
include_once plugin_dir_path( __FILE__ ) .'send-stats.php';