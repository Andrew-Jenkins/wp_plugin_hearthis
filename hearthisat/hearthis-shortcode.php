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

/*[hearthis width="250"]https://hearthis.at/djforce/baesser-forcesicht-dnbmix/[/hearthis]

[hearthis height="350"]https://hearthis.at/djforce/[/hearthis]
[hearthis height="350"]https://hearthis.at/crecs/set/tbase-feat-charlotte-haining-oscar-michael-unspoken-words-ep/[/hearthis]
[hearthis height="350"]http://hearthis.at/set/51-7/[/hearthis]*/


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

  // width="250" height="400" params="hcolor=33e040&theme=transparent_black"
  // echo '<pre>'.print_r($plugin_options,true).'</pre>';   

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

/*
function hearthisat_shortcode( $atts, $content = null ) {
  
  $a = shortcode_atts( array(
    'width' => 'hearthis',
    'height' => 'hearthis',
    'hcolor' => 'hearthis',
    'color' => 'hearthis',
    'background' => 'hearthis',
    'cover' => 'hearthis',
    'waveform' => 'hearthis',
    'theme' => 'hearthis',
    'style' => 'hearthis',
    'block_space' => 'hearthis',
    'block_size' => 'hearthis',
    'autoplay' => 'hearthis',
  ), $atts );

  return '<span class="' . esc_attr($a['class']) . '">' . $content . '</span>';
}

*/

function hearthis_iframe_url($options,$info)
{
    $d = 'https://hearthis.at';

    if($info['type'] == 'track')
        $d .= '/embed/'.$info['track_id'];



    $url = $d.'/'.hearthis_get_option('theme').
          '/?hcolor='.hearthis_get_option('color2').
          '&color='.hearthis_get_option('color').
          '&style='.hearthis_get_option('style'). 
          '&block_size='.hearthis_get_option('digitized_size').
          '&block_space='.hearthis_get_option('digitized_space').
          '&background='.hearthis_get_option('background').
          '&waveform='.hearthis_get_option('waveform').
          '&cover='.hearthis_get_option('cover').
          '&autoplay='.hearthis_get_option('autoplay');

     return $url;

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

  $count = 0;
  $url_split = explode('/', $url);

  foreach ($url_split as &$countval) 
  { 
    if(!empty($countval)) 
      $count++; 
  }
  
  if(preg_match('/^(.+?)\/(set)\/(.+?)$/', $url) || ($count == 3)) 
    return TRUE; 
  else
    return FALSE; 
}


/**
 * Decide if a url has a tracklist
 * @param  {string}   $url
 * @return {boolean}
 */
function hearthis_get_type_from_url($url) 
{


  $count = 0;
  $parts = parse_url($url);
  $url_split = explode('/', $parts['path']);
  

    $info = array(
      'type' => 'track',
      'user' => FALSE,
      'player_url' => $parts['path'],
      'setlist' => FALSE,
      'track_id' => FALSE
    );

    // foreach ($url_split as &$countval) 
    // { 
    //   if(!empty($countval)) 
    //     $count++; 
    // }


  unset($url_split[count($url_split)-1]);
      // echo '<pre>'.print_r(count($url_split),true).'</pre>'; 

  if(count($url_split) === 2)
  {
      $info['type'] = 'profile';
      $info['user'] = $url_split[1];
  }

  if(count($url_split) > 2)
  {
      $response = \Httpful\Request::get('http://api-v2.hearthis.at'.$parts['path'])->send(); 
      $info['user'] = $response->body->user->permalink;
      $info['track_id'] = $response->body->id;
      // echo '<pre>'.print_r(count($response->body),true).'</pre>';  

    for ($i=0; $i < count($url_split); $i++) 
    { 
        if(strtolower($url_split[$i]) === 'set')
              $info['type']  = 'set';

        if( $i < 2 && $url_split[$i] !== 'set')
              $info['user'] = $url_split[$i];
    }

    if (count($response->body) > 1 )
    {
      for ($j=0; $j < count($response->body); $j++) 
      { 
          if($info['type'] === 'set')
          {
              $info['setlist'][] = array(
                'users' => $response->body[$j]->user->permalink,
                'tracks' => $response->body[$j]->id
              );
              
              unset($info['track_id']); //  = FALSE;

              if( empty($info['user']) ) 
                  unset($info['user']); //  = FALSE;
             
          }
          // $user_ids[] = $response->body[$j]->user->permalink;
          // $track_ids[] = $response->body[$j]->id;
      }
    }
  }




    
    

    


   
    return $info;
}


/**
 * Iframe widget embed code
 * @param  {array}   $options  Parameters
 * @return {string}            Iframe embed code
 */
function hearthis_iframe_widget($options) 
{
  $urlSource = parse_url($options['url']);

 

  if(isset($urlSource['path']) )
  {
    // && hearthis_url_has_tracklist($options['url']) === TRUE
    $infos = hearthis_get_type_from_url($options['url']);

    $url = hearthis_iframe_url($options,$infos);

  }

      echo '<pre>'.print_r($infos,true).'</pre>';

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

  if(isset($urlSource['path']) && hearthis_url_has_tracklist($options['url']) === TRUE)
  {
      $setlist = \Httpful\Request::get('http://api-v2.hearthis.at/'.$urlSource['path'])->send(); 
      # echo '<pre>'.print_r($setlist->body[0]->user->permalink,true).'</pre>';
      /* foreach ($setlist->body as $p => $v) 
       {
            // @todo search in $v->title || $setlist->body->title;

          $url = '//hearthis.at/embed/' . $v->id . '/' . $options['params']['theme'] . '/?style=' . $options['params']['style'] . '' . (!empty($options['params']['style_size']) ? '&block_size=' . $options['params']['style_size'] : '') . '' . (!empty($options['params']['style_space']) ? '&block_space=' . $options['params']['style_space'] : '') . '' . (!empty($options['params']['hcolor']) ? '&hcolor=' . $options['params']['hcolor'] : '') . '' . (!empty($options['params']['color']) ? '&color=' . $options['params']['color'] : '') . '';
          $extras .= '<pre>'. print_r($v,true).'</pre>';
          $return['SL'][] = sprintf('<iframe class="hearthis-iframe-widget" width="%s" height="%s" scrolling="no" frameborder="no" src="%s" allowtransparency></iframe>%s', $width, '450', $url, $extras);
       }
       */
  } 
  else 
  {


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

/*https://hearthis.at
/HEARTHIS_USERNAME
/embed
/
https://hearthis.at
/set
/HEARTHIS_SETID
/embed
/
https://hearthis.at/embed
/HEARTHIS_TRACKID 
/hearthis_theme
/
*/
// TRACK

/*
https://hearthis.at/embed
/HEARTHIS_TRACKID 
/hearthis_theme
/
  ?
  hcolor = hearthis_color2
  &color = hearthis_color
  &style = hearthis_style 
  &block_size = hearthis_digitized_size
  &block_space = hearthis_digitized_space
  &background = hearthis_background
  &waveform = hearthis_waveform
  &cover = hearthis_cover
  &autoplay = hearthis_autoplay
  // &css= null/string
*/

// hearthis_trackid => url->id,
// hearthis_theme => transparent/transparent_black
// hearthis_color => color null/hex without #
// hearthis_color2 => hcolor  null/hex without #
// hearthis_player_width 100% px
// hearthis_player_height => 100
// hearthis_digitized_space => 1,10
// hearthis_digitized_size => 1,10
// hearthis_style => 1 ignores blocksize / 2 digitized
// hearthis_background => 1 (0,1) 1 => waveform disable 0
// hearthis_waveform => 0 (0,1) 1 => background disable 0, 
// hearthis_cover => 1 (0,1), 
// hearthis_autoplay => 1 (0,1), 


// PROFILE
/*
https://hearthis.at
/HEARTHIS_USERNAME
/embed
/ 
  ?
  hcolor=hearthis_color2

hearthis_player_profile_height => 350 px
*/


// SET
/*
https://hearthis.at
/set
/HEARTHIS_SETID
/embed
/
  ?
  hcolor = hearthis_color2
  &autoplay = hearthis_autoplay

hearthis_player_height_multi => 350 px
*/


  // ALL

/*
  HEARTHIS_TRACKID
  HEARTHIS_USERNAME
  HEARTHIS_SETID

hearthis_theme
hearthis_color
hearthis_color2
hearthis_player_width
hearthis_player_height
hearthis_digitized_space
hearthis_digitized_size
hearthis_style
hearthis_background
hearthis_waveform
hearthis_cover
hearthis_autoplay
hearthis_player_profile_height
hearthis_player_height_multi

*/

// block_space => (0,10 > +-1) hearthis_digitized_space
// block_size => (1,20 > +-1) hearthis_digitized_size
// style => hearthis_style
// theme =>  hearthis_theme bool ? : transparent : transparent_black
// waveform_highlight => hearthis_color
// waveform => hearthis_color2
// width => hearthis_player_width
// set_height => hearthis_player_height_multi

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
              jQuery("#track-share-embed-style").change(function() 
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
