<?php
/*
Plugin Name: hearthis.at
Plugin URI: http://wordpress.org/extend/plugins/hearthis-shortcode/
Description: Converts hearthis urls with Wordpress Shortcuts from within your content to a hearthis.at widget. Example: [hearthis]http://hearthis.at/crecs/shawne-stadtfest-chemnitz-31082013/[/hearthis]
Version: 0.6.5
Author: Andreas Jenke | SIEs
Author URI: http://so-ist.es
License: GPLv2

*/

// Point to where you downloaded the phar
include(__DIR__.'/httpful.phar');

/**
 * @link    hearthis.at
 * @category  Plugin URI: http://wordpress.org/extend/plugins/hearthis-shortcode/
 * @internal  Converts hearthis WordPress shortcodes to a hearthis.at widget. Example: [hearthis]http://hearthis.at/shawne/shawne-stadtfest-chemnitz-31082013/[/hearthis]
 * @version:  0.6.5 
 * @author    Benedikt Gro&szlig; <contact@hearthis.com> | URL http://hearthis.at | upgraded by Andreas Jenke | SIEs 
 * @license:  GPLv2
*/

## Original version: Benedikt Gro&szlig; <contact@hearthis.com>

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

    $content = rtrim($content, '/') . '/';
    
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

    #echo '<pre>'.print_r(hearthis_is_type($shortcode_options['url']) ,true).'</pre>';   

    $defaults = hearthis_code_params();
    $user_defs = $shortcode_options;
    $overwrite_defs = array_merge($defaults,$user_defs);
    
    $options = $overwrite_defs;
    $options['params'] = $shortcode_options['params'];

    $url = '';

    if (!isset($options['url'])) 
    {
      return $url;
    }
    else
    {
      $infos = hearthis_get_url_infos(trim($options['url']));  
      // merge both for getting url
      $url = hearthis_get_iframe_url(array_merge($options,$infos));
      $options['url'] = $url;
    } 

    if( ($infos['type'] === 'set' || $infos['type'] === 'profile') && strtolower($options['liststyle']) !== 'single' )
    {  
      if( ! is_integer($options['height']) || $options['height'] <= 400 )
          $options['height'] = 450;
    }
    elseif( ($infos['type'] === 'set' || $infos['type'] === 'profile') && strtolower($options['liststyle']) === 'single')
    {  
      if( ! is_integer($options['height']) || $options['height'] <= 145 )
          $options['height'] = 145;
    }
   
    if( $infos['type'] === 'track' && $options['background'] == 1)
    {
        if( ! is_integer($options['height']) || $options['height'] <= 400 )
            $options['height'] = 400;

        $options['waveform'] = 0;
    }
    elseif ( $infos['type'] === 'track' && $options['background'] != 1) 
    {
        if( ! is_integer($options['height']) || $options['height'] <= 400 )
            $options['height'] = 145;

        $options['waveform'] = 0;
    }


    if ($infos['type'] === 'track' && $options['waveform'] == 1) 
    {
        if( ! is_integer($options['height']) || $options['height'] <= 145 )
            $options['height'] = 95;

        $options['background'] = 0;
    }

    // fallback
    if( ! hearthis_is_integer($options['height']) )
        $options['height'] = 145;

    
    if(isset($options['width']) && ! hearthis_is_integer($options['width'])) 
      $options['width'] = '100%';

    return hearthis_iframe_widget($options);

  }


  function hearthis_code_params()
  {
    // deafults
    $opts = array(
        'iframe' => get_option('hearthis_player_iframe', true),
        'width' => get_option('hearthis_player_width','100%'),
        'height' => get_option('hearthis_player_height',''),
        'color' => get_option('hearthis_color','#333333'),
        'color2' => get_option('hearthis_color2','#d81900'),
        'cover' => get_option('hearthis_cover',0),
        'autoplay' => get_option('hearthis_autoplay',0),
        'style' => get_option('hearthis_style',1),
        'theme' => get_option('hearthis_theme','transparent'),
        'background' => get_option('hearthis_background',0),
        'waveform' => get_option('hearthis_waveform'),
        'digitized_space' => get_option('hearthis_digitized_space',1),
        'digitized_size' => get_option('hearthis_digitized_size',2),
        'liststyle'  => (get_option('hearthis_liststyle') === 'single') ?  'single' : NULL,
        'css'  => get_option('hearthis_css','')
      );
     return $opts;
  }

  function hearthis_is_integer($input)
  {
    return preg_match('/^\d+$/', $input);
  }

  function hearthis_get_iframe_url($options)
  {
    
    if($options['type'] === 'set' && $options['liststyle'] !== 'single' )
    {
      $url = 'https://hearthis.at'.esc_attr($options['player_url']).'embed/?hcolor='.hearthis_clear_color($options['color'], TRUE).'&color='.hearthis_clear_color($options['color2'], TRUE);
    }
    elseif($options['type'] === 'set' && $options['liststyle'] === 'single')
    {  
      foreach ($options['setlist'] as $tune) 
      {
        # code...
          $href = 'https://hearthis.at/embed/'.esc_attr($tune['tracks']).'/'.$options['theme'].'/?';
          $href .='hcolor='.hearthis_clear_color($options['color'], TRUE).
          '&color='.hearthis_clear_color($options['color2'], TRUE).
          '&style='.$options['style']. 
          '&block_space='.$options['digitized_space'].
          '&block_size='.$options['digitized_size'].
          '&background='.$options['background'].
          '&waveform='.$options['waveform'].
          '&cover='.$options['cover'].
          '&autoplay='.$options['autoplay'];
          //'&css='.$options['css'];
          $u[] = $href;
      }
      $url = $u;
    } 

    if($options['type'] === 'track')
    {
      $url = 'https://hearthis.at/embed/'.esc_attr($options['track_id']).'/'.$options['theme'].'/?';
      $url .='hcolor='.hearthis_clear_color($options['color'], TRUE).
      '&color='.hearthis_clear_color($options['color2'], TRUE).
      '&style='.$options['style']. 
      '&block_space='.$options['digitized_space'].
      '&block_size='.$options['digitized_size'].
      '&background='.$options['background'].
      '&waveform='.$options['waveform'].
      '&cover='.$options['cover'].
      '&autoplay='.$options['autoplay'];
      //'&css='.$options['css'];
    }

    if($options['type'] === 'profile')
      $url = $options['url'].'embed/?hcolor='.hearthis_clear_color($options['color'], TRUE).'&color='.hearthis_clear_color($options['color2'], TRUE);

    return $url;

  }

  /**
   * Decide if a url has a tracklist
   * @param  {string}   $url
   * @return {boolean}
   */
  function hearthis_is_type($url) 
  {
    if(is_string($url))
      $test = hearthis_get_url_types($url);
    if(is_array())
    if(isset($test['type']))
      return $test['type'];
  }

  /**
   * Decide if a url has a tracklist
   * @param  {string}   $url
   * @return {boolean}
   */
  function hearthis_get_url_infos($url) 
  {
    $parts = parse_url($url);
    $url_split = explode('/', $parts['path']);

    if($url_split[2] === 'set') 
    {
      $l = get_headers($url.'embed/',true);
      if(isset($l['Location']))
      {
        $url = str_replace('embed/', '', $l['Location']);
      }
      unset($l);
    }
    return hearthis_get_url_types($url);
  }

  /**
   * Decide if a url has a tracklist
   * @param  {string}   $url
   * @return {boolean}
   */
  function hearthis_get_url_types($url) 
  {
    $parts = parse_url($url);
    $url_split = explode('/', $parts['path']);
    $info = array(
      'type' => 'track',
      'user' => FALSE,
      'player_url' => $parts['path'],
      'setlist' => FALSE,
      'track_id' => FALSE
      );

    unset($url_split[count($url_split)-1]);

    if(count($url_split) === 2)
    {
      $info['type'] = 'profile';
      $info['user'] = $url_split[1];
    }
    elseif(count($url_split) > 2)
    {
      for ($i=0; $i < count($url_split); $i++) 
      { 
        if(strtolower($url_split[$i]) === 'set')
          $info['type'] = 'set';

        if( $i < 2 && $url_split[$i] !== 'set')
          $info['user'] = $url_split[$i];
      }
    }

    $response = \Httpful\Request::get('https://api-v2.hearthis.at'.$parts['path'])->send();

    if(count($response->body) == 1)
    {
        if(isset($response->body->user->permalink) )
            $info['user'] = $response->body->user->permalink;
        if(isset($response->body->id) )
            $info['track_id'] = $response->body->id;
    }
    elseif (count($response->body) > 1 )
    {
      for ($j=0; $j < count($response->body); $j++) 
      { 
        if($info['type'] === 'set')
        {
          $info['setlist'][] = array(
            'users' => $response->body[$j]->user->permalink,
            'tracks' => $response->body[$j]->id
          );
          # unset($info['track_id']); //  = FALSE;
        }
        // $user_ids[] = $response->body[$j]->user->permalink;
        // $track_ids[] = $response->body[$j]->id;
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

    $width = $options['width'];
    $height = $options['height'];
    $return = array();
    $iframe = '';
    $url = $options['url'];
    
    if(is_array($url) && isset($options['liststyle']) && $options['liststyle'] === 'single')
    { 
        foreach($url as $href) 
          $return[] = sprintf('<div><iframe class="hearthis-iframe-widget" width="%s" height="%s" scrolling="no" frameborder="no" src="%s" allowtransparency></iframe></div>', $width, '145', $href);
    } 
    else 
        $return[] = sprintf('<iframe class="hearthis-iframe-widget" width="%s" height="%s" scrolling="no" frameborder="no" src="%s" allowtransparency></iframe>', $width, $height, $url);

    for ($i=0; $i < count($return); $i++) 
    { 
      $iframe .= $return[$i];
    }
    return $iframe;
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
  { if( is_admin() ) 
    {
      add_options_page('hearthis.at Options', 'hearthis.at', 'manage_options', 'hearthis-shortcode', 'hearthis_shortcode_options');
      add_action('admin_init', 'register_hearthis_settings');
    }
  }


  function register_hearthis_settings()
  {

    register_setting('hearthis-settings', 'hearthis_liststyle');
    register_setting('hearthis-settings', 'hearthis_player_iframe');
    register_setting('hearthis-settings', 'hearthis_player_width');
    register_setting('hearthis-settings', 'hearthis_player_height');
    register_setting('hearthis-settings', 'hearthis_player_profile_height');
    register_setting('hearthis-settings', 'hearthis_player_height_multi');
    register_setting('hearthis-settings', 'hearthis_color');
    register_setting('hearthis-settings', 'hearthis_color2');
    register_setting('hearthis-settings', 'hearthis_cover');
    register_setting('hearthis-settings', 'hearthis_autoplay');
    register_setting('hearthis-settings', 'hearthis_style');
    register_setting('hearthis-settings', 'hearthis_theme');
    register_setting('hearthis-settings', 'hearthis_waveform');
    register_setting('hearthis-settings', 'hearthis_background');
    register_setting('hearthis-settings', 'hearthis_digitized_space');
    register_setting('hearthis-settings', 'hearthis_digitized_size');
    register_setting('hearthis-settings', 'hearthis_css');
  }

  /**
   * Function that will check if value is a valid HEX color.
   */
  function hearthis_check_color( $value ) 
  {

    $v = strtolower($value);
    if ( preg_match( '/^#[a-f0-9]{6}$/i', $v ) ) 
    { 
      return true;
    }
    return false;
  }

  /**
   * Function that will check if value is a valid HEX color.
   */
  function hearthis_clear_color( $value , $convert = FALSE)
  {
    $check = hearthis_check_color($value);
    if($check)
    {
       if($convert === TRUE)
          return substr($value, 1);
        else
          return $value;
    }
    else
    {
       $value = '#'.$value;
       if(hearthis_check_color($value))
            return $value;
        else
           return '';
    }
  }


  add_action( 'admin_enqueue_scripts', 'hearthis_add_color_picker' );
  function hearthis_add_color_picker( $hook ) 
  {
    if( is_admin() ) 
    {
      // Add the color picker css file
      wp_enqueue_style( 'wp-color-picker' );

      // Include our custom jQuery file with WordPress Color Picker dependency
      wp_enqueue_script( 'wp-color-picker' );
    }
  }


  function hearthis_shortcode_options() 
  {
    if (!current_user_can('manage_options')) 
      wp_die( __('You do not have sufficient permissions to access this page.') );
    ?>
    <div class="wrap">
    <h2>hearthis.at Default Settings</h2>
    <p>You can always override these settings with your shortcode for each preference. Your shortcode will always overrides these defaults individually. Please note that not every settting does affect. The settings depends on your hearthis.at url and if its a track, playlist or profile link.</p>

    <form method="post" action="options.php">
    <?php settings_fields( 'hearthis-settings' ); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Current Default 'params'</th>
          <td><?php  echo http_build_query(array_filter(array(
            'css'              => get_option('hearthis_css'),
            'width'            => get_option('hearthis_player_width'),
            'height'           => get_option('hearthis_player_height'),
            'profile_height'   => get_option('hearthis_player_profile_height'),
            'multi_height'     => get_option('hearthis_player_height_multi'),
            'color2'           => get_option('hearthis_color2'),
            'color'            => get_option('hearthis_color'),
            'cover'            => get_option('hearthis_cover'),
            'autoplay'         => get_option('hearthis_autoplay'),
            'style'            => get_option('hearthis_style'),
            'theme'            => get_option('hearthis_theme'),
            'waveform'         => get_option('hearthis_waveform'),
            'background'       => get_option('hearthis_background'),
            'block_space'      => get_option('hearthis_digitized_space'), // style_size
            'block_size'       => get_option('hearthis_digitized_size'), // style_space
            ))); ?>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row">Player default width</th>
          <td><input type="text" name="hearthis_player_width" value="<?php echo get_option('hearthis_player_width'); ?>"> (px or %)<br />
            Leave blank to use the default.</td>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Player default height (single track)</th>
          <td>
            <input type="text" name="hearthis_player_height" id="hearthis_player_height" value="<?php echo get_option('hearthis_player_height'); ?>"> (px or %)<br />
            Leave blank to use the default.</td>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Player Height for Profile</th>
          <td><input type="text" name="hearthis_player_profile_height" id="hearthis_player_profile_height" value="<?php echo get_option('hearthis_player_profile_height'); ?>">  (px or %)<br />
            Leave blank to use the default.</td>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Player Height for Sets</th>
          <td><input type="text" name="hearthis_player_height_multi" id="hearthis_player_height_multi" value="<?php echo get_option('hearthis_player_height_multi'); ?>"> (px or %)<br />
            Leave blank to use the default.</td>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Color Waveform</th>
          <td><input type="text" id="hearthis_color2" name="hearthis_color2" value="<?php echo get_option('hearthis_color2'); ?>">
            Defines the default waveform color.</td>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Color Play button </th>
          <td><input type="text" id="hearthis_color" name="hearthis_color" value="<?php echo get_option('hearthis_color'); ?>">
            Defines the color of the play button and the waveform from the passed time.</td>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Show cover image</th>
          <td>
            <input type="checkbox" name="hearthis_cover" id="hearthis_cover" value="<?php echo get_option('hearthis_cover');?>"<?php if(get_option('hearthis_cover') == 1) echo ' checked="checked"'; ?>> 
            <label for="hearthis_cover" style="margin-right: 1em;">show cover, off/on</label><br /> Defines if the player should show the cover image.</td>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Show waveform</th>
          <td>
            <input type="checkbox" name="hearthis_waveform" id="hearthis_waveform" value="<?php echo get_option('hearthis_waveform');?>"<?php if(get_option('hearthis_waveform') == 1) echo ' checked="checked"'; ?>>
            <label for="hearthis_waveform" style="margin-right: 1em;">hide waveform, off/on</label><br /> Defines if the player will show the waveform image.
          </td>
        </tr>

        <tr valign="top">
          <th scope="row">Show background image</th>
          <td>
            <input type="checkbox" name="hearthis_background" id="hearthis_background" value="<?php echo get_option('hearthis_background');?>"<?php if(get_option('hearthis_background') == 1) echo ' checked="checked"'; ?>>
            <label for="hearthis_background" style="margin-right: 1em;">hide background image (if set), off/on</label><br /> Defines if the player will show the background image if it set.
          </td> 
        </tr>

        <tr valign="top">
          <th scope="row">Start Autoplay</th>
          <td>
            <input type="checkbox" name="hearthis_autoplay" id="hearthis_autoplay" value="<?php echo get_option('hearthis_autoplay');?>" <?php if(get_option('hearthis_autoplay') == 1) echo ' checked="checked"'; ?>> 
            <label for="hearthis_autoplay" style="margin-right: 1em;">autoplay, off/on</label><br /> Defines if the player will start autoplay after loading.</td>
          </td>
        </tr>
       
       <tr valign="top">
          <th scope="row">Link to external CSS File</th>
          <td>
            <input type="url" name="hearthis_css" id="hearthis_css" value="<?php echo get_option('hearthis_css');?>"> 
            <label for="hearthis_css" style="margin-right: 1em;">Url to an external CSS File</label><br /> You can provide a CSS File with markups to stlye your individual player.</td>
          </td>
        </tr>
            
        <tr valign="top">
          <th scope="row">Waveform Style</th>
            <td>         
            <select id="hearthis_style" name="hearthis_style">
              <option value="1" <?php if (strtolower(get_option('hearthis_style')) == '1') echo 'selected="selected"'; ?>>Waveform Style: Soft</option>
              <option value="2" <?php if (strtolower(get_option('hearthis_style')) == '2') echo 'selected="selected"'; ?>>Waveform Style: Digitized</option>
            </select>
  
            <div id="template-2" style="display: none;">
              <div style="float: left; width: 48%;">
                <div style="float: left; margin-top: 9px;">Block Size</div>
                <input id="hearthis_digitized_size" name="hearthis_digitized_size" type="range" min="1" max="10" step="1" value="<?php echo (int) get_option('hearthis_digitized_size','2'); ?>" style="float: left; margin-right: 15px;" />                        
  
              </div>
              <div style="float: right; width: 48%;">
                <div style="float: left; margin-top: 9px;">Block Space</div>
                <input id="hearthis_digitized_space" type="range" name="hearthis_digitized_space" min="1" max="10" step="1" value="<?php echo (int) get_option('hearthis_digitized_space','1'); ?>" style="float: left; margin-right: 15px;" />
              </div>
            </div>
            <script>
  
            (function( $ ) {
  
              $("#hearthis_style").change(function() 
              {
                if($(this).val() == 2) 
                {
                  $("#template-2").slideDown(500);
                  style = 2;
                } 
                else 
                {
                  $("#template-2").slideUp(500);
                  style = 1;
                }
              });  
  
              var waveform = 0, background = 0;
  
              var chgFct = function(elm, olm, b) {
                var f = (b == '') ? false : true;
                var el = document.getElementById(elm);
                var o = document.getElementById(olm);
  
                if (el.checked == true ) {
                  el.value = 1;
                  if(el.id == 'hearthis_waveform')
                    waveform = 1;
                  o.value = 0;
                  o.checked = false;
  
                  $(o).prop("disabled", 'disabled');
  
                } else {
  
                  if(el.id == 'hearthis_background')
                    background = 1;
                  el.value = 0;
                  el.checked = false
                    // o.checked = false;
                    $(o).prop("disabled", false);
                }
                changeEmbedPlayer();
              };
  
              $("#hearthis_background").change(function() {
                chgFct('hearthis_background',"hearthis_waveform",false);
              });
  
              $("#hearthis_waveform").change(function() {
                chgFct('hearthis_waveform',"hearthis_background",false);
              });
  
              $(window).on('load', function() 
              {
                chgFct('hearthis_background',"hearthis_waveform", true);
                chgFct('hearthis_waveform',"hearthis_background", true);
              });
  
              $("#hearthis_autoplay").change(function() {
                if (this.checked)
                  this.value = 1;
                else 
                  this.value = 0; 
              });
  
              $("#hearthis_cover").change(function() {
                if (this.checked) {
                  this.value = 1;
                } else {
                  this.value = 0;
                }
              });
                  
  
              $("#hearthis_css").keyup(function() {
  
                css = $(this).val();
                css = css.replace(/(<([^>]+)>)/ig,"");
                  css = css.replace(" ","+");
  
                delay(function(){
                    if(css.length > 2) {
                      changeEmbedPlayer();
                    }
                  }, 500 );
              });
  
  
              $("#hearthis_digitized_size").change(function() {
                this.value = $(this).val();
              });
  
  
              $("#hearthis_digitized_space").change(function() {
                this.value = $(this).val();
              });
  
              function changeEmbedPlayer() {  
  
                $("#hearthis_player_height").val('145');
  
                if(waveform == 1) {
                  $("#hearthis_player_height").val('145');
                }
                if(background == 1) {
                  $("#hearthis_player_height").val('400');
                }
                if(background == 0) {
                  $("#hearthis_player_height").val('145');
                }
                if(waveform == 0 && background == 0) {
                  $("#hearthis_player_height").val('145');
                }
              }
  
              $(function() {
                $('#hearthis_color,#hearthis_color2').wpColorPicker();
              });
  
  
              })( jQuery );
  
            </script>
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
        </table>
        <p class="submit">
          <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
        </p>
      </form>
  </div>
  <?php
}
