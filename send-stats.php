<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_action( 'wp_head', 'lap_matomo_http_api_js' );
add_action( 'wp_body_open' , 'lap_matomo_http_api_head' );

function lap_matomo_http_api_js() {
  if ( is_admin() ) {
    return;
  }

  $options = get_option( 'lap_matomo_http_api_options' );

  if ( current_user_can( 'manage_options' ) && empty( $options['track_admins'] ) ) {
    return;
  }

  if ( empty( $options['enable_js'] ) || $options['enable_js'] !== '1' ) {
    return;
  }

  if (!empty($options['url']) && !empty($options['idsite'])) {
    $url    = esc_js( trailingslashit( $options['url'] ) );
    $idsite = esc_js( $options['idsite'] );
    ?>
<!-- Matomo -->
<script>
  var _paq = window._paq = window._paq || [];
  _paq.push(["disableCookies"]);
  _paq.push(['enableLinkTracking']);
  _paq.push(['enableHeartBeatTimer', 15]);
  (function() {
    var u="<?php echo $url; ?>";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '<?php echo $idsite; ?>']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->
    <?php
  }
}

function lap_matomo_http_api_head() {
  if ( is_admin() ) {
    return;
  }

  include_once plugin_dir_path( __FILE__ ) .'MatomoTracker.php';

  $options = get_option( 'lap_matomo_http_api_options' );

  if ( current_user_can( 'manage_options' ) && empty( $options['track_admins'] ) ) {
    return;
  }
  if (!empty($options['url']) && !empty($options['idsite']) && !empty($options['tokenAuth'])) {
    $matomoUrl = $options['url'];
    $matomoSiteId = $options['idsite'];
    $authToken = $options['tokenAuth'];
    $debugging = $options['debugging'];
    $page_title = esc_html(wp_get_document_title());
    $display_name = '';
    if (is_user_logged_in()) {
      $user_info = get_userdata(get_current_user_id());
      $display_name = $user_info->display_name;
    }

    if (current_user_can('manage_options') && $debugging === '1') {
      echo '<details>';
      echo '<summary>Debugging</summary>';
      echo 'Server: ' . esc_html($_SERVER['REMOTE_ADDR']);
      echo '<br>';
      echo 'User: ' . esc_html($display_name);
      echo '<br>';
      echo 'URL: ' . esc_html($matomoUrl) . '<br>';
      echo 'Site: ' . esc_html($matomoSiteId) . '<br>';
      echo 'Page Title: '. esc_html($page_title). '<br>';
      echo '</details>';
    }
    $connecting_ip = $_SERVER['REMOTE_ADDR'];
    if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
      $forwarded_ips = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
      $connecting_ip = trim( $forwarded_ips[0] );
    } elseif ( array_key_exists( 'HTTP_CF_CONNECTING_IP', $_SERVER ) ) {
      $connecting_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    try {
      $matomoTracker = new MatomoTracker($matomoSiteId, $matomoUrl);
      $matomoTracker->setRequestTimeout(2);
      $matomoTracker->setTokenAuth($authToken);
      $matomoTracker->setRequestMethodNonBulk('POST');
      if (is_user_logged_in()) {
        $matomoTracker->setUserId($display_name);
      }
      $matomoTracker->setIp($connecting_ip);
      $matomoTracker->doTrackPageView( $page_title );
    } catch (Exception $e) {
      error_log('Matomo tracking error: ' . $e->getMessage());
      if (current_user_can('manage_options') && $debugging === '1') {
        echo '<details>';
        echo '<summary>Debugging</summary>';
        echo 'Message: ' . esc_html($e->getMessage());
        echo '</details>';
      }

    }
  }

}