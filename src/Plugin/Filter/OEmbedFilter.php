<?php 

namespace Drupal\gutenberg\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

define('OEMBED_DEFAULT_PROVIDER', 'http://open.iframe.ly/api/oembed');

/**
 * @Filter(
 *   id = "filter_oembed",
 *   title = @Translation("Gutenberg oEmbed filter"),
 *   description = @Translation("Embeds media for URL that supports oEmbed standard."),
 *   settings = {
 *   "oembed_maxwidth" = 800,
 *   "oembed_providers" = "#https?://(www\.)?youtube.com/watch.*#i | http://www.youtube.com/oembed | true
#https?://youtu\.be/\w*#i | http://www.youtube.com/oembed | true 
#https?://(www\.)?vimeo\.com/\w*#i | http://vimeo.com/api/oembed.json | true
#http://(www\.)?hulu\.com/watch/.*#i | http://www.hulu.com/api/oembed.json | true 
#https?://(www\.)?twitter.com/.+?/status(es)?/.*#i | https://api.twitter.com/1/statuses/oembed.json | true 
#https?://(www\.)?instagram.com/p/.*#i | https://api.instagram.com/oembed | true
#https?:\/\/(www\.)?google\.com\/maps\/embed\?pb\=.*#i | http://open.iframe.ly/api/oembed | true
#https?://maps.google.com/maps.*#i | google-maps | LOCAL
#https?://docs.google.com/(document|spreadsheet)/.*#i | google-docs | LOCAL"
 *   },
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class OEmbedFilter extends FilterBase {
  public function process($text, $langcode) {

    $lines = explode("\n", $text);

    $lines = preg_replace_callback('#^(<figure.*?>)?\s*(https?://\S+?)\s*(</figure>)?$#', array($this, 'embed'), $lines);

    $text = implode("\n", $lines);

    return new FilterProcessResult($text);
  }


  /**
   * Callback function to process each URL
   */
  private function embed($match) {

    static $providers = [];

    if (empty($providers)) {
      $providers_string = $this->settings['oembed_providers'];
      $providers_line = explode("\n", $providers_string);
      foreach ($providers_line as $value) {
        $items = explode(" | ", $value);
        $key = array_shift($items);
        $providers[$key] = $items;
      }
    }

    $url = $match[2];

    foreach ($providers as $matchmask => $data) {
      list($providerurl, $regex) = $data;

      $regex = preg_replace('/\s+/', '', $regex);

      if ($regex == 'false') {
        $regex = false;
      }

      if (!$regex) {
        $matchmask = '#' . str_replace( '___wildcard___', '(.+)', preg_quote(str_replace('*', '___wildcard___', $matchmask), '#')) . '#i';
      }
      if (preg_match($matchmask, $url)) {
        $provider = $providerurl;
        break;
      }
    }

    if ($regex === 'LOCAL' && !empty($provider)) {
      $output = $this->getContents($provider, $url);
    }
    elseif (!empty($provider)) {
      $client = \Drupal::httpClient();
      $response = '';

      try {
        $request = $client->get($provider . '?url=' . $url . '&origin=drupal&format=json&maxwidth=' . $this->settings['oembed_maxwidth']);
        $response = $request->getBody();
      }

      catch (RequestException $e) {
        watchdog_exception('oembed', $e->getMessage());
      }
      
      if (!empty($response)) {
        $embed = json_decode($response);
        if (!empty($embed->html)) {
         $output = $embed->html;
        }
        elseif ($embed->type == 'photo') {
          $output = '<img src="' . $embed->url . '" title="' . $embed->title . '" style="width: 100%" />';
          $output = '<a href="' . $url . '">' . $output .'</a>';
        }
      }
    }
    else {
      $client = \Drupal::httpClient();
      $response = '';

      try {
        $request = $client->get(OEMBED_DEFAULT_PROVIDER . '?origin=drupal&url=' . $url);
        $response = $request->getBody();
      }

      catch (RequestException $e) {
        watchdog_exception('oembed', $e->getMessage());
      }
      
      if (!empty($response)) {
        $embed = json_decode($response);
        if (!empty($embed->html)) {
         $output = $embed->html;
        }
        elseif ($embed->type == 'photo') {
          $output = '<img src="' . $embed->url . '" title="' . $embed->title . '" style="width: 100%" />';
          $output = '<a href="' . $url . '">' . $output .'</a>';
        }
      }
    }

    $output = empty($output) ? $url : $output;

    if (count($match) > 3) {
      $output = $match[1] . $output . $match[3]; // Add <figure> and </figure> back.
    }

    return $output;
  }

  /**
   * Locally create HTML after pattern study for sites that don't support oEmbed.
   */
  private function getContents($provider, $url) {
    $width = $this->settings['oembed_maxwidth']; // variable_get('oembed_maxwidth', 0);

    switch ($provider) {
      case 'google-maps':
        //$url    = str_replace('&', '&amp;', $url); Though Google encodes ampersand, it seems to work without it.
        $height = (int)($width / 1.3);
        $embed  = "<iframe width='{$width}' height='{$height}' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='{$url}&output=embed'></iframe><br /><small><a href='{$url}&source=embed' style='color:#0000FF;text-align:left'>View Larger Map</a></small>";
        break;
      case 'google-docs':
        $height = (int)($width * 1.5);
        $embed  = "<iframe width='{$width}' height='{$height}' frameborder='0' src='{$url}&widget=true'></iframe>";
        break;
      default:
        $embed = $url;
    }

    return $embed;
  }

  /**
   * Define settings for text filter.
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['oembed_providers'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Providers'),
      '#default_value' => $this->settings['oembed_providers'],
      '#description' => $this->t('A list of oEmbed providers. Add your own by adding a new line and using this pattern: [Url to match] | [oEmbed endpoint] | [Use regex (true or false)]'),
    );
    $form['oembed_maxwidth'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Maximum width of media embed'),
      '#default_value' => $this->settings['oembed_maxwidth'],
      '#description' => $this->t('Set the maximum width of an embedded media. The unit is in pixels, but only put a number in the textbox.'),
    );
    return $form;
  }

}

