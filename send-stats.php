<?php

add_action( 'wp_body_open' , 'lap_matomo_http_api_head' );

function lap_matomo_http_api_head() {
  include_once plugin_dir_path( __FILE__ ) .'MatomoTracker.php';

  $options = get_option( 'lap_matomo_http_api_options' );
  if ($options['url'] !== false) {
    global $wp;

    $matomoUrl = ($options['url']);
    $matomoSiteId = ($options['idsite']);
    $authToken = ($options['tokenAuth']);
    $tracking_args = 'rec=1';
    $queuedtracking = 0;
    $wp_url = esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $page_title = esc_html(get_the_title());
    if (is_user_logged_in()) {
      $user_info = get_userdata(get_current_user_id());
      $display_name = $user_info->display_name;
    }
    // echo $display_name;
    if (current_user_can('administrator')) {
      echo '<details>';
      echo '<summary>Debugging</summary>';
      print_r($_SERVER);
      echo '<br>';
      echo 'Server: ' . $_SERVER['REMOTE_ADDR'];
      echo '<br>';
      echo 'User: ' . $display_name;
      echo '<br>';
      echo 'URL: ' . $matomoUrl . '<br>';
      echo 'Site: ' . $matomoSiteId . '<br>';
      echo 'Auth: ' . $authToken;
      echo '</details>';
    }
    $connecting_ip = $_SERVER['REMOTE_ADDR'];

    MatomoTracker::$URL = $url;
    $matomoTracker = new MatomoTracker($matomoSiteId, $matomoUrl );
    $matomoTracker->setRequestTimeout(2);
    $matomoTracker->setTokenAuth($authToken);
    $matomoTracker->setRequestMethodNonBulk('POST');
    if (is_user_logged_in()) {
      $matomoTracker->setUserId($display_name);
    }
    $matomoTracker->setIp($connecting_ip);
    $matomoTracker->doTrackPageView( $page_title );
  }
  if (current_user_can('administrator')) {
    echo '<details>';
    echo '<summary>Debugging 2</summary>';
    echo $matomoTracker->$response;
    echo '</details>';
  }
}