<?php

namespace Plugins\googlemaps;

use Typemill\Events\OnShortcodeFound;
use \Typemill\Plugin;

/**
 * Google Maps
 */
class googlemaps extends Plugin {

  protected $settings;
  protected $pluginData;

  /**
   * subscribe typemill events
   *
   * @return string[]
   */
  public static function getSubscribedEvents() {
    return [
      'onShortcodeFound' => 'onShortcodeFound',
      'onSettingsLoaded' => 'onSettingsLoaded',
    ];
  }

  /**
   * @param $settings
   *
   * @return void
   */
  public function onSettingsLoaded($settings) {
    $this->settings   = $settings->getData();
    $this->pluginData = $this->settings['settings']['plugins']['googlemaps'];
  }

  /**
   * Detect and replace shortcode
   *
   * @param OnShortcodeFound $shortcode
   *
   * @return void
   */
  public function onShortcodeFound($shortcode) {
    $shortcodeArray = $shortcode->getData();

    // check if it is the shortcode name that we where looking for
    if (is_array($shortcodeArray) && $shortcodeArray['name'] == 'GOOGLEMAPS') {
      // we found our shortcode, so stop firing the event to other plugins
      $shortcode->stopPropagation();
      $settings = $this->pluginData;

      # Of course you should validate the user input here, but let us skip it to keep it easy ...
      $address = $shortcodeArray['params']['address'] ?? 'Berlin, Germany';
      $zoom    = (int)($shortcodeArray['params']['zoom'] ?? $settings["1zoom"]);

      // and return a html-snippet that replaces the shortcode on the page.
      $shortcode->setData($this->generateFrame($settings, $address, $zoom));
    }
  }

  /**
   * Create google maps iframe code
   *
   * @return string
   */
  protected function generateFrame(array $settings, string $address, int $zoom) {
    $width  = $settings["1width"] ?? "";
    $height = $settings["1height"] ?? "";
    $type   = $settings["1type"] ?? "";

    $html = '<iframe width="%s" height="%s" src="https://maps.google.com/maps?width=%s&amp;height=%s&amp;hl=en&amp;q=%s&amp;ie=UTF8&amp;t=%s&amp;z=%d&amp;iwloc=B&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>';

    return sprintf($html, $width, $height, $width, $height, urlencode($address), $type, $zoom);

  }
}