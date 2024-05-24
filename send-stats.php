<?php

add_action( 'wp_head' , 'lap_matomo_http_api_head' );
function lap_matomo_http_api_head() {
  $options = get_option( 'lap_matomo_http_api_options' );
  if ($options['url'] !== false) {
    global $wp;

    $url = ($options['url']);
    $idsite = ($options['idsite']);
    $tracking_args = 'rec=1';
    $wp_url = esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $page_title = get_the_title();

    $full_url = $url. 'matomo.php?idsite='. $idsite . '&' . $tracking_args . '&url='. $wp_url ; 
    //var_dump($full_url);
    wp_remote_post( $full_url);
  }
} 