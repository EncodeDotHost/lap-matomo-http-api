<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// Add a menu item to the admin menu
add_action( 'admin_menu', 'lap_matomo_http_api_add_settings_menu');
function lap_matomo_http_api_add_settings_menu() {
  add_options_page('Matomo Server Side Tracking', 'Matomo Server Side Tracking', 'manage_options', 'lap_matomo_http_api', 'lap_matomo_http_api_options_page' );
}

// Create the options page
function lap_matomo_http_api_options_page() {
  ?>
  <div class="wrap">
    <h1>Matomo Server Side Tracking</h1>
    <form method="post" action="options.php">
      <?php settings_fields( 'lap_matomo_http_api_options' ); ?>
      <?php do_settings_sections( 'lap_matomo_http_api_plugin' ); ?>
      <?php submit_button('Save Changes', 'primary'); ?>
    </form>
  </div>
  <?php
}

// Register and define the settings
add_action( 'admin_init', 'lap_matomo_http_api_admin_init' );

function lap_matomo_http_api_admin_init() {
  $args = array(
    'type' => 'string',
    'sanitize_callback' => 'lap_matomo_http_api_validate_options',
    'default' => ''
  );

  // Register the settings
  register_setting( 'lap_matomo_http_api_options', 'lap_matomo_http_api_options', $args );

  // Add the settings section
  add_settings_section(
    'lap_matomo_http_api_main',
    'Tracking Settings',
    'lap_matomo_http_api_section_text',
    'lap_matomo_http_api_plugin'
  );

  // Add the settings fields
  add_settings_field(
    'lap_matomo_http_api_tracking_url',
    'Tracking URL',
    'lap_matomo_http_api_setting_tracking_url',
    'lap_matomo_http_api_plugin',
    'lap_matomo_http_api_main'
  );
  
  add_settings_field( 
    'lap_matomo_http_api_tracking_idsite',
    'Tracking ID Site',
    'lap_matomo_http_api_setting_tracking_idsite',
    'lap_matomo_http_api_plugin',
    'lap_matomo_http_api_main'
  );
  add_settings_field( 
    'lap_matomo_http_api_tracking_tokenAuth',
    'Authentication Token',
    'lap_matomo_http_api_setting_tracking_tokenAuth',
    'lap_matomo_http_api_plugin',
    'lap_matomo_http_api_main'
  );
  add_settings_field(
    'lap_matomo_http_api_tracking_debugging',
    'Enable Debugging Mode?',
    'lap_matomo_http_api_setting_tracking_debugging',
    'lap_matomo_http_api_plugin',
    'lap_matomo_http_api_main'
  );
  add_settings_field(
    'lap_matomo_http_api_enable_js',
    'Enable Client-Side JS Tracking?',
    'lap_matomo_http_api_setting_enable_js',
    'lap_matomo_http_api_plugin',
    'lap_matomo_http_api_main'
  );
  add_settings_field(
    'lap_matomo_http_api_track_admins',
    'Track Admin Users?',
    'lap_matomo_http_api_setting_track_admins',
    'lap_matomo_http_api_plugin',
    'lap_matomo_http_api_main'
  );
}

// Draw the section header

function lap_matomo_http_api_section_text() {
  echo '<p>Enter the tracking URL and ID here.</p>';
}


//Display and fill the settings fields

function lap_matomo_http_api_setting_tracking_url() {
  $options = get_option( 'lap_matomo_http_api_options' );
  $url = isset( $options['url'] ) ? $options['url'] : '';
  ?>
  <input type="text" name="lap_matomo_http_api_options[url]" id="url" value="<?php echo esc_attr( $url ); ?>" />
  <p class="description">The full URL to your Matomo instance, including a trailing slash. e.g. <code>https://matomo.example.com/</code></p>
  <?php
}

function lap_matomo_http_api_setting_tracking_idsite() {
  $options = get_option( 'lap_matomo_http_api_options' );
  $idsite = isset( $options['idsite'] ) ? $options['idsite'] : '';
  ?>
  <input type="text" name="lap_matomo_http_api_options[idsite]" id="idsite" value="<?php echo esc_attr( $idsite ); ?>" />
  <p class="description">The numeric Site ID assigned to this website in Matomo. Found under <strong>Administration &rarr; Websites</strong>.</p>
  <?php
}

function lap_matomo_http_api_setting_tracking_tokenAuth() {
  $options = get_option( 'lap_matomo_http_api_options' );
  $tokenAuth = isset( $options['tokenAuth'] ) ? $options['tokenAuth'] : '';
  ?>
  <input type="password" name="lap_matomo_http_api_options[tokenAuth]" id="tokenAuth" value="<?php echo esc_attr( $tokenAuth ); ?>" />
  <p class="description">Your Matomo API authentication token. Found under your Matomo user profile &rarr; <strong>Security &rarr; Auth Tokens</strong>. Required for server-side tracking.</p>
  <?php
}

function lap_matomo_http_api_setting_tracking_debugging() {
  $options = get_option( 'lap_matomo_http_api_options' );
  $debugging = isset( $options['debugging'] ) && $options['debugging'] === '1' ? 'checked' : '';
  ?>
  <input type="checkbox" name="lap_matomo_http_api_options[debugging]" id="debugging" value="1" <?php echo $debugging; ?> />
  <p class="description">Shows a hidden <code>&lt;details&gt;</code> block on the front end, visible only to administrators, displaying the IP address, user, and page title being sent to Matomo.</p>
  <?php
}

function lap_matomo_http_api_setting_enable_js() {
  $options = get_option( 'lap_matomo_http_api_options' );
  $enable_js = isset( $options['enable_js'] ) && $options['enable_js'] === '1' ? 'checked' : '';
  ?>
  <input type="checkbox" name="lap_matomo_http_api_options[enable_js]" id="enable_js" value="1" <?php echo $enable_js; ?> />
  <p class="description">Injects the Matomo JavaScript snippet into <code>&lt;head&gt;</code> for client-side event and link tracking. Disable this if you load the Matomo JS via Google Tag Manager or a headers &amp; footers plugin.</p>
  <?php
}

function lap_matomo_http_api_setting_track_admins() {
  $options = get_option( 'lap_matomo_http_api_options' );
  $track_admins = isset( $options['track_admins'] ) && $options['track_admins'] === '1' ? 'checked' : '';
  ?>
  <input type="checkbox" name="lap_matomo_http_api_options[track_admins]" id="track_admins" value="1" <?php echo $track_admins; ?> />
  <p class="description">When enabled, users with administrator privileges will be tracked on the front end. By default they are excluded to keep analytics data clean.</p>
  <?php
}

function lap_matomo_http_api_validate_options( $input ) {

  $valid['url'] = esc_url_raw( $input['url'] );
  $valid['idsite'] = sanitize_text_field( $input['idsite'] );
  $valid['tokenAuth'] = sanitize_text_field( $input['tokenAuth'] );
  $valid['debugging'] = sanitize_text_field( $input['debugging'] ?? '' );
  $valid['enable_js'] = sanitize_text_field( $input['enable_js'] ?? '' );
  $valid['track_admins'] = sanitize_text_field( $input['track_admins'] ?? '' );
  return $valid;
}