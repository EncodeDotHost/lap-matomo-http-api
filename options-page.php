<?php

// Add a menu item to the admin menu
add_action( 'admin_menu', 'lap_matomo_http_api_add_settings_menu');
function lap_matomo_http_api_add_settings_menu() {
  add_options_page('LAP Matomo HTTP Tracking', 'LAP Matomo HTTP Tracking', 'manage_options', 'lap_matomo_http_api', 'lap_matomo_http_api_options_page' );
}

// Create the options page
function lap_matomo_http_api_options_page() {
  ?>
  <div class="wrap">
    <h1>LAP Matomo HTTP Tracking</h1>
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
}

// Draw the section header

function lap_matomo_http_api_section_text() {
  echo '<p>Enter the tracking URL and ID here.</p>';
}


//Display and fill the settings fields

function lap_matomo_http_api_setting_tracking_url() {
  $options = get_option( 'lap_matomo_http_api_options' );
  $url = (isset($options['url'])) ? $options['url'] : ''; // Use empty string if not set
  ?>
  <input type="text" name="lap_matomo_http_api_options[url]" id="url" value="<?php echo $url; ?>" />
  <?php
}

function lap_matomo_http_api_setting_tracking_idsite() {
  $options = get_option( 'lap_matomo_http_api_options' );
  $idsite = (isset($options['idsite'])) ? $options['idsite'] : ''; // Use empty string if not set
  ?>
  <input type="text" name="lap_matomo_http_api_options[idsite]" id="idsite" value="<?php echo $idsite; ?>" />
  <?php
}

function lap_matomo_http_api_validate_options( $input ) {

	// Only allow letters and spaces for the name
  $valid['url'] = sanitize_url( $input['url'] );
  $valid['idsite'] = sanitize_text_field( $input['idsite'] );
  return $valid;
}