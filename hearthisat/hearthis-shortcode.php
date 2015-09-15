<?php
/*
Plugin Name: hearthis.at
Plugin URI: http://wordpress.org/extend/plugins/hearthis-shortcode/
Description: Converts hearthis urls with Wordpress Shortcuts from within your content to a hearthis.at widget. Example: [hearthis]http://hearthis.at/crecs/shawne-stadtfest-chemnitz-31082013/[/hearthis]
Version: 0.6.2
Author: Andreas Jenke | REV
Author URI: http://so-ist.es
License: GPLv2

Original version: Benedikt Gro&szlig; <contact@hearthis.com>

*/

// Point to where you downloaded the phar
include(__DIR__.'/httpful.phar');

/**
 * @link    hearthis.at
 * @category  Plugin URI: http://wordpress.org/extend/plugins/hearthis-shortcode/
 * @internal  Converts hearthis WordPress shortcodes to a hearthis.at widget. Example: [hearthis]http://hearthis.at/shawne/shawne-stadtfest-chemnitz-31082013/[/hearthis]
 * @version:  0.6.1 
 * @author    Benedikt Gro&szlig; <contact@hearthis.com> | URL http://hearthis.at
 * @license:  GPLv2
*/

/* Register hearthis.at shortcode
-------------------------------------------------------------------------- */

add_shortcode("hearthis", "hearthis_shortcode");

/**
 * hearthis.at shortcode handler
 * @param  {string|array}  $atts     The attributes passed to the shortcode like [hearthis attr1="value" /].
 *                                   Is an empty string when no arguments are given.
 * @param  {string}        $content  The content between non-self closing [hearthis]…[/hearthis] tags.
 * @return {string}                  Widget embed code HTML
 */
function hearthis_shortcode($atts, $content = null) 
{

  // Custom shortcode options
  $shortcode_options = array_merge(
      array('url' => trim($content)), 
      is_array($atts) ? $atts : array()
  );

  // Turn shortcode option "param" (param=value&param2=value) into array
  $shortcode_params = array();
  if (isset($shortcode_options['params'])) 
      parse_str(html_entity_decode($shortcode_options['params']), $shortcode_params);

  $shortcode_options['params'] = $shortcode_params;

  // User preference options
  $plugin_options = array_filter(
    array(
      'iframe' => hearthis_get_option('player_iframe', true),
      'width'  => hearthis_get_option('player_width'),
      'height' => hearthis_url_has_tracklist($shortcode_options['url']) ? hearthis_get_option('player_height_multi') : hearthis_get_option('player_height'),
      'params' => array_filter(
        array(
          'hcolor' => hearthis_get_option('color'),
          'color'  => hearthis_get_option('color2'),
          'theme'  => hearthis_get_option('theme'),
          'style'  => hearthis_get_option('style'),
          'style_size'  => hearthis_get_option('style_size'),
          'style_space' => hearthis_get_option('style_space'),
        )
      ),
    )
  );

      /*
          function caption_shortcode( $atts, $content = null ) {
            $a = shortcode_atts( array(
              'class' => 'caption',
            ), $atts );

            return '<span class="' . esc_attr($a['class']) . '">' . $content . '</span>';
          }
          [caption class="headline"]My Caption[/caption]
      */

          
  // Needs to be an array
  if (!isset($plugin_options['params'])) 
    $plugin_options['params'] = array(); 

  // plugin options < shortcode options
  $options = array_merge(
    $plugin_options,
    $shortcode_options
  );

  // plugin params < shortcode params
  $options['params'] = array_merge(
    $plugin_options['params'],
    $shortcode_options['params']
  );

  // The "url" option is required
  if (!isset($options['url'])) 
    return '';
  else 
    $options['url'] = trim($options['url']);

  // Both "width" and "height" need to be integers
  if (isset($options['width']) && !preg_match('/^\d+$/', $options['width'])) 
    $options['width'] = 0;
    // set to 0 so oEmbed will use the default 100% and WordPress themes will leave it alone

  if (isset($options['height']) && !preg_match('/^\d+$/', $options['height'])) 
    unset($options['height']);

  return hearthis_iframe_widget($options);

}

/**
 * Plugin options getter
 * @param  {string|array}  $option   Option name
 * @param  {mixed}         $default  Default value
 * @return {mixed}                   Option value
 */
function hearthis_get_option($option, $default = false) 
{
  $value = get_option('hearthis_' . $option);
  return $value === '' ? $default : $value;
}

/**
 * Booleanize a value
 * @param  {boolean|string}  $value
 * @return {boolean}
 */
function hearthis_booleanize($value) 
{
  return is_bool($value) ? $value : $value === 'true' ? true : false;
}

/**
 * Decide if a url has a tracklist
 * @param  {string}   $url
 * @return {boolean}
 */
function hearthis_url_has_tracklist($url) 
{

  $hearthis_url_has_tracklist = false;
  
  $count = 0;
  $url_split = explode('/', $url);

  foreach ($url_split as &$countval) 
  { 
    if(!empty($countval)) 
      $count++; 
  }
  
  if(preg_match('/^(.+?)\/(set)\/(.+?)$/', $url) || ($count == 3)) 
    $hearthis_url_has_tracklist = true; 

  return $hearthis_url_has_tracklist;

}


/**
 * Iframe widget embed code
 * @param  {array}   $options  Parameters
 * @return {string}            Iframe embed code
 */
function hearthis_iframe_widget($options) 
{
  $urlSource = parse_url($options['url']);

  $count = 0;
  $url_split = explode('/', $options['url']);

  foreach ($url_split as &$countval) 
  { 
    if(!empty($countval)) 
      $count++; 
  }
  
  $hearthis_url_has_tracklist = false;
  // Merge in "url" value
  $options['params'] = array_merge(
    array( 
      'url' => $options['url']
    ), 
    $options['params']
  );
  
  $width = isset($options['width']) && $options['width'] !== 0 ? $options['width'] : '100%';
  $height = isset($options['height']) && $options['height'] !== 0 ? $options['height'] : '150';
  
  $return = array();
  $extras = '';

  if(preg_match('/^(.+?)\/(set)\/(.+?)$/', $options['url']) || ($count == 3)) 
  {    
      # $url = $options['url'] . 'embed/' . (!empty($options['params']['hcolor']) ? '?hcolor=' . $options['params']['hcolor'] : '');
      $setlist = \Httpful\Request::get('http://api-v2.hearthis.at/'.$urlSource['path'])->send(); 

      // if(_HT_DEBUG)
      // echo '<pre>'. print_r($track->body,true).'</pre>'
       # $url = $options['url'] . 'embed/' . (!empty($options['params']['hcolor']) ? '?hcolor=' . $options['params']['hcolor'] : '');
       
     // $extras = '';
     foreach ($setlist->body as $p => $v) 
     {
          // @todo search in $v->title || $setlist->body->title;

        $url = '//hearthis.at/embed/' . $v->id . '/' . $options['params']['theme'] . '/?style=' . $options['params']['style'] . '' . (!empty($options['params']['style_size']) ? '&block_size=' . $options['params']['style_size'] : '') . '' . (!empty($options['params']['style_space']) ? '&block_space=' . $options['params']['style_space'] : '') . '' . (!empty($options['params']['hcolor']) ? '&hcolor=' . $options['params']['hcolor'] : '') . '' . (!empty($options['params']['color']) ? '&color=' . $options['params']['color'] : '') . '';
        $extras .= '<pre>'. print_r($v,true).'</pre>';
        $return['SL'][] = sprintf('<iframe class="hearthis-iframe-widget" width="%s" height="%s" scrolling="no" frameborder="no" src="%s" allowtransparency></iframe>%s', $width, '450', $url, $extras);
     }
  } 
  else
  {
        // if(isInteger(substr($urlSource['path'],1,-1))) 
        // {
        //   $trackID = substr($urlSource['path'],1,-1);
        // } 
        // else
        //   $trackID = $track->body->id;



      $track = \Httpful\Request::get('http://api-v2.hearthis.at'.$urlSource['path'])->send();
      $url = '//hearthis.at/embed/' . $track->body->id . '/' . $options['params']['theme'] . '/?style=' . $options['params']['style'] . '' . (!empty($options['params']['style_size']) ? '&block_size=' . $options['params']['style_size'] : '') . '' . (!empty($options['params']['style_space']) ? '&block_space=' . $options['params']['style_space'] : '') . '' . (!empty($options['params']['hcolor']) ? '&hcolor=' . $options['params']['hcolor'] : '') . '' . (!empty($options['params']['color']) ? '&color=' . $options['params']['color'] : '') . '';
      $return['SL'][] = sprintf('<iframe class="hearthis-iframe-widget" width="%s" height="%s" scrolling="no" frameborder="no" src="%s" allowtransparency></iframe>%s', $width, $height, $url, $extras);
  }
  
     #  echo '<pre>'. print_r(count($setlist->body),true).'</pre>';
 
     // if(_HT_DEBUG)
      // echo '<pre>'. print_r($track->body,true).'</pre>'
     # echo '<pre>'. print_r($setlist->body,true).'</pre>';
     // foreach ($setlist->body as $p => $v) 
     // {
     //   $extras .= '<pre>'. print_r($p,true).'": "'.print_r($v,true).'</pre>';
     // }

    if(isset($return['SL']))
    {
      for ($i=0; $i < count($return['SL']); $i++) { 
        $_return .= $return['SL'][$i];
      }
      return $_return;
    } 
}

/* Settings
-------------------------------------------------------------------------- */

/* Add settings link on plugin page */
add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'hearthis_settings_link');
function hearthis_settings_link($links)
{
  $settings_link = '<a href="options-general.php?page=hearthis-shortcode">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

/* Add admin menu */
add_action('admin_menu', 'hearthis_shortcode_options_menu');
function hearthis_shortcode_options_menu() 
{
  add_options_page('hearthis.at Options', 'hearthis.at', 'manage_options', 'hearthis-shortcode', 'hearthis_shortcode_options');
  add_action('admin_init', 'register_hearthis_settings');
}

function register_hearthis_settings()
{
  register_setting('hearthis-settings', 'hearthis_player_height');
  register_setting('hearthis-settings', 'hearthis_player_height_multi');
  register_setting('hearthis-settings', 'hearthis_player_width ');
  register_setting('hearthis-settings', 'hearthis_color');
  register_setting('hearthis-settings', 'hearthis_color2');
  register_setting('hearthis-settings', 'hearthis_theme');
  register_setting('hearthis-settings', 'hearthis_style');
  register_setting('hearthis-settings', 'hearthis_style_size');
  register_setting('hearthis-settings', 'hearthis_style_space');
}

function isInteger($input)
{
  return(ctype_digit(strval($input)));
}

function hearthis_shortcode_options() 
{
  if (!current_user_can('manage_options')) 
    wp_die( __('You do not have sufficient permissions to access this page.') );
  ?>
  <div class="wrap">
    <h2>hearthis.at Default Settings</h2>
    <p>You can always override these settings on a per-shortcode basis. Setting the 'params' attribute in a shortcode overrides these defaults individually.</p>

    <form method="post" action="options.php">
      <?php settings_fields( 'hearthis-settings' ); ?>
      <table class="form-table">

        <tr valign="top">
          <th scope="row">Player Height for Sets</th>
          <td>
            <input type="text" name="hearthis_player_height_multi" value="<?php echo get_option('hearthis_player_height_multi'); ?>" /> (no unit, or %)<br />
            Leave blank to use the default.
          </td>
        </tr>

        <tr valign="top">
          <th scope="row">Player Width</th>
          <td>
            <input type="text" name="hearthis_player_width" value="<?php echo get_option('hearthis_player_width'); ?>" /> (no unit, or %)<br />
            Leave blank to use the default.
          </td>
        </tr>

        <tr valign="top">
          <th scope="row">Current Default 'params'</th>
          <td>
            <?php echo http_build_query(array_filter(array(
              'hcolor'      => get_option('hearthis_color'),
              'color'       => get_option('hearthis_color2'),
              'style'       => get_option('hearthis_style'),
              'style_size'  => get_option('hearthis_style_size'),
              'style_space' => get_option('hearthis_style_space'),
              'theme'       => get_option('hearthis_theme'),
              ))) ?>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Waveform Default Color</th>
            <td>
              <input type="text" name="hearthis_color2" value="<?php echo get_option('hearthis_color2'); ?>" /> (color hex code e.g. ff6699)<br />
              Defines the default waveform color.
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Waveform Highlight Color</th>
            <td>
              <input type="text" name="hearthis_color" value="<?php echo get_option('hearthis_color'); ?>" /> (color hex code e.g. ff6699)<br />
              Defines the color to paint the play button, waveform and selections.
            </td>
          </tr>


          <tr valign="top">
            <th scope="row">Theme Color (Tracks Only)</th>
            <td>         
              <input type="radio" id="hearthis_theme_color_light"  name="hearthis_theme" value="transparent"  <?php if (strtolower(get_option('hearthis_theme')) === 'transparent')  echo 'checked'; ?> />
              <label for="hearthis_theme_color_light"  style="margin-right: 1em;">Light</label>
              <input type="radio" id="hearthis_theme_color_dark" name="hearthis_theme" value="transparent_black" <?php if (strtolower(get_option('hearthis_theme')) === 'transparent_black') echo 'checked'; ?> />
              <label for="hearthis_theme_color_dark" style="margin-right: 1em;">Dark</label>

            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Waveform Style</th>
            <td>         
              <select id="track-share-embed-style"  name="hearthis_style">
                <option value="1" <?php if (strtolower(get_option('hearthis_style')) === '1') echo 'checked'; ?>>Waveform Style: Soft</option>
                <option value="2" <?php if (strtolower(get_option('hearthis_style')) === '2') echo 'checked'; ?>>Waveform Style: Digitized</option>
              </select>

              <div id="template-2" style="display: none;">
                <div style="float: left; width: 48%;">
                  <div style="float: left; margin-top: 9px;">Block Size</div>
                  <input id="track-share-embed-style2-block-size" name="hearthis_digitized_size" type="range" min="1" max="20" step="1" value="5" style="float: left; margin-right: 15px;" />                        

                </div>
                <div style="float: right; width: 48%;">
                  <div style="float: left; margin-top: 9px;">Block Space</div>
                  <input id="track-share-embed-style2-block-space" type="range" name="hearthis_digitized_space" min="0" max="10" step="1" value="1" style="float: left; margin-right: 15px;" />
                </div>
              </div>
              <script>
              $("#track-share-embed-style").change(function() 
              {
                if(jQuery(this).val() == 2) 
                {
                  jQuery("#template-2").slideDown(500);
                  style = 2;
                } 
                else 
                {
                  jQuery("#template-2").slideUp(500);
                  style = 1;
                }
              });         
              </script>
            </td>
          </tr>
        </table>
        <p class="submit">
          <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
    </div>
    <?php
  }
?>
