<?php
/*
 * Plugin Name:       Matomo Server Side Tracking
 * Description:       Handle sending the page tracking to Matomo via HTTP rather than JavaScript.
 * Version:           1.2.0
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

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'lap_matomo_http_api_action_links' );
function lap_matomo_http_api_action_links( $links ) {
  $settings_link = '<a href="' . admin_url( 'options-general.php?page=lap_matomo_http_api' ) . '">Settings</a>';
  array_unshift( $links, $settings_link );
  return $links;
}