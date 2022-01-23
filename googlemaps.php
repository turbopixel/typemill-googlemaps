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
    if (is_array($shortcodeArray) && $shortcodeArray['name'] == 'GMAPS_FRAME1') {
      // we found our shortcode, so stop firing the event to other plugins
      $shortcode->stopPropagation();

      // and return a html-snippet that replaces the shortcode on the page.
      $shortcode->setData($this->generateFrame());
    }
  }

  /**
   * Create google maps iframe code
   *
   * @return string
   */
  protected function generateFrame() {
    $settings = $this->pluginData;

    $address = $settings["1address"] ?? "";
    $width   = $settings["1width"] ?? "";
    $height  = $settings["1height"] ?? "";
    $type    = $settings["1type"] ?? "";
    $zoom    = $settings["1zoom"] ?? "";

    if (empty($address)) {
      return "ERROR: missing address";
    }

    $html = '<iframe width="%s" height="%s" src="https://maps.google.com/maps?width=%s&amp;height=%s&amp;hl=en&amp;q=%s&amp;ie=UTF8&amp;t=%s&amp;z=%d&amp;iwloc=B&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>';

    return sprintf($html, $width, $height, $width, $height, urlencode($address), $type, $zoom);

  }
}