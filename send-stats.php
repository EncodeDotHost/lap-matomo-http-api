<?php

add_action( 'wp_body_open' , 'lap_matomo_http_api_head' );

function lap_matomo_http_api_head() {
  include_once plugin_dir_path( __FILE__ ) .'MatomoTracker.php';

  $options = get_option( 'lap_matomo_http_api_options' );
  if ($options['url'] !== false) {
    global $wp;

    $url = ($options['url']);
    $idsite = ($options['idsite']);
    $tracking_args = 'rec=1';
    $queuedtracking = 0;
    $wp_url = esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $page_title = esc_html(get_the_title());
    $user_info = get_userdata(get_current_user_id());
    $display_name = $user_info->display_name;
    // echo $display_name;

    MatomoTracker::$URL = $url;
    $matomoTracker = new MatomoTracker( $idSite = $idsite );
    $matomoTracker->doTrackPageView( $page_title );
    $matomoTracker->setUserId($display_name);
    $matomoTracker->ip = $_SERVER['REMOTE_ADDR'];
  }
}