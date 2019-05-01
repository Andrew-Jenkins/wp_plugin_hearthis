<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://so-ist.es
 * @since      1.0.0
 *
 * @package    Hearthis
 * @subpackage Hearthis/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hearthis
 * @subpackage Hearthis/public
 * @author     Andreas Jenke <ja@so-ist.es>
 */



class Hearthis_Public {

    /**
     * the api URL root
     *
     * @since    1.0.0
     * @var      const    HEARTHIS_API_URL    the api URL root
     */
    const HEARTHIS_API_URL = 'http://api-v2.hearthis.at';
    
    /**
     *  The placeholder for all vars we want to use
     *
     * @since    1.0.0
     * @access   protected
     * @var      mixed    $vars    The placeholder for all vars we want to use
     */
    protected $vars;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     *  the iframe dependable vars
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $url_options_tpl    the iframe dependable vars
     */
    private static $url_options_tpl = array(
        'hcolor',
        'color',
        'style',
        'block_size',
        'block_space',
        'background',
        'waveform',
        'cover',
        'autoplay',
        'css'
    ); 


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_shortcode( 'hearthis', array( $this, 'hearthis_shortcode_function' ) );
    }

    /**
     * hearthis.at shortcode handler
     *
     * @since   1.0.0
     * @param  string|array    $atts       The attributes passed to the shortcode like [hearthis attr1="value" /].
     *                                     Is an empty string when no arguments are given.
     * @param  string          $content    The content between non-self closing [hearthis]…[/hearthis] tags.
     * @return string                      Widget embed code HTML
     */
    public function hearthis_shortcode_function( $atts, $content = '' )
    {
        // add trailing slash to the url if its doesn't have one
        $url = rtrim(trim($content), '/') . '/';

        // clear all vars
        $this->clearVar();
        // then set the url parts 
        $this->set_url_parts($url);

        // than we check if the given url belongs to hearthis.at 
        // if this fails than we return NULL 
        // this also sets the VARS 'URL' and 'PATH'
        if( $this->_check_url() === FALSE) 
        {
            // clear vars again
            $this->clearVar();
            return NULL;
        }

        $atts = shortcode_atts(
            is_array($atts) ? $atts : array(), 
            'hearthis'
        );

        if (isset($atts['liststyle']) && strtolower($atts['liststyle']) === 'single')
        {
            $as_single = TRUE;
        }

        // get the default settings and the old shortcode attributes
        $atts = $this->get_fallback_atts($atts);
        // and merge it to options
        $atts = array_merge($this->get_hearthis_settings(),$atts); 
        // set colors 
        $atts['hcolor'] = $this->hearthis_color($atts['hcolor'], TRUE);
        $atts['color'] = $this->hearthis_color($atts['color'], TRUE);
        // filter out all options the have value FALSE
        $atts = array_filter($atts);
        $this->setVar('ATTS', $atts);
        // now set TYPE, USER and SETLIST
        $this->_url_info($atts, $as_single);
        
        # var_dump( $this->getVar());
        $widget = $this->hearthis_iframe_widget();
        return do_shortcode($widget);
    }


    /**
     *
     * @since    1.0.0
     * @access   private
     * @param    string     $url    the given url
     * @return   void
     */
    private function set_url_parts($url)
    {
        $parts = parse_url($url);
        $parts['_input_'] = $url;
        $this->setVar('URL_PARTS', $parts);
    } 


    /**
     *  check if the shortcode url is refering to hearthis.at
     *
     * @since    1.0.0
     * @access   protected
     * @return   bool   TRUE/FALSE
     */
    protected function _check_url()
    {
        $parts = $this->getVar('URL_PARTS');
        
        if( ! isset($parts['host']) OR strtolower($parts['host']) !== 'hearthis.at' ) 
        {
            return FALSE;
        }

        $this->setVar('URL',$parts['_input_']);
        $this->setVar('PATH', $parts['path']);
        $this->clearVar('URL_PARTS');
        return TRUE;
    }


    /**
     *  gets all the url infos for the widget
     *
     * @since    1.0.0
     * @access   protected
     * @param    array     $atts        settings and attributes 
     * @param    bool      $as_single   if a url should parsed as single tune this is true else false
     * @return   void   
     */
    protected function _url_info($atts, $as_single = FALSE)
    {
        $parts = array_filter(preg_split('/\\//', substr($this->get_url_path(),1,-1)));
        $this->setVar('TYPE','TRACK');
        $api_url = $this->build_api_url($this->getVar('URL'));
        $num_parts = count($parts);
        
        if(count($parts) === 1) 
        {
            $this->setVar('TYPE','PROFILE');
            $api_url = FALSE;
            if($as_single)
            {
                $this->setVar('TYPE','AS_TRACKS');
                $api_url = self::HEARTHIS_API_URL.'/'.$parts[0].'/?type=tracks&count=50';
            }
        }
        else if($num_parts === 2 && $parts[0] == "embed")
	    {
	        $urls[] = $parts[1];
            $this->setVar('SETLIST', $urls);
	        return;
	    }

        if(strtolower($parts[0]) === 'set') 
        {
            $this->setVar('TYPE','SET');
            $api_url = FALSE;
            if($as_single)
            {
                $type = $this->setVar('TYPE','AS_TRACKS');
                $api_url = $this->build_api_url($this->getVar('URL'));
            }
        }

        if(strtolower($parts[1]) === 'set') 
        {
            $this->setVar('TYPE','SET');
            $api_url = FALSE;
            if($as_single)
            {
                $type = $this->setVar('TYPE','AS_TRACKS');
                $api_url = $this->build_api_url($this->get_setlist_url($this->getVar('URL')));
            }
        }

        if($api_url !== FALSE)
        {
            $this->_set_setlist_urls($api_url);
        }
        
    }


    /**
     *  fallback to correct the old params
     *
     * @since    1.0.0
     * @access   private
     * @param    array     $atts        settings and attributes 
     * @return   array     $atts        correct the old params
     */
    private function get_fallback_atts( array $atts)
    {
        // first merge the old params string to atts 
        if(isset($atts['params']))
        {
            $params = array();
            parse_str(html_entity_decode($atts['params']), $params);
            $atts = array_merge($atts,$params); 
            unset($atts['params']);   
            #var_dump($params);
        }

        if(isset($atts['digitized_size'])) 
        {
            $atts['block_size'] = $atts['digitized_size'];
            unset($atts['digitized_size']);
        }
        if(isset($atts['digitized_space']))
        {
            $atts['block_space'] = $atts['digitized_space'];
            unset($atts['digitized_space']);
        }
        if(isset($atts['color2'])) 
        {
            // $atts['hcolor'] = isset($atts['color']) ? $atts['color'] : $atts['color2'];
            $atts['color'] = $atts['color2'];
            unset($atts['color2']);
        }
       
        return $atts;
    }

   
    
    /**
     * this creates the Iframe widget embed code
     *
     * @access public 
     * @return string            Iframe embed code
     */
    public function hearthis_iframe_widget() 
    {
        $urls = $this->get_iframe_urls();
        $width = $this->get_player_width();
        $height = $this->get_player_height();
        $return = array();
     
        foreach($urls as $href) 
        {
            $return[] = sprintf('<div class="hearthis-widget"><iframe class="hearthis-iframe" width="%s" height="%s" scrolling="no" frameborder="no" src="%s" allowtransparency></iframe></div>'."\n", $width, $height, $href);
        }

        $widget = '';
        for ($i=-1; $i < count($return); $i++) 
        { 
            $widget .= $return[$i]."\n";
        }

        return $widget;
    }




   
    /**
     * this will return the api url for the given url
     *
     * @since    1.0.0
     * @access   private
     * @param    string   $url    the url from hearthis 
     * @return   string   $apiURL return the api url for th given url
     */
    private function build_api_url($url)
    {
        $apiURL = str_replace(array('http://hearthis.at','https://hearthis.at','http://wwww.hearthis.at','https://wwww.hearthis.at'), self::HEARTHIS_API_URL, $this->getVar('URL'));
        return $apiURL;
    }


    /**
     *  return the path or void
     *
     * @since    1.0.0
     * @access   private
     * @param    string   $uri  the uri of the setlist
     * @return   string   $url  return the response header location from a set url or empty string
     */
    private function get_setlist_url($uri)
    {
        $url = '';
        $response_header = get_headers($uri.'embed/',true);
        if(isset($response_header['Location']))
        {
            $url = str_replace('embed/', '', $response_header['Location']);
        }
        unset($response_header);
        return $url;
    }


    /**
     *  return the path or void
     *
     * @since    1.0.0
     * @access   private
     * @return   string|void   return the path or void
     */
    private function get_url_path()
    {
        if($this->hasVar('PATH'))
            return $this->getVar('PATH');
    }


    /**
     * get the plugin setting with default values
     *
     * @since    1.0.0
     * @access   protected
     * @return   array     $defaults
     */
    protected function get_hearthis_settings()
    {
        // set default values if the user hasn't set the plugin settings
        // if any plugin settings is set the get_option function overwrites the
        // default 2nd param, at least these params can be overwritten by the
        // users shortcode params, so the keys are also the possible params 
        $defaults = array_filter( array(
            'width' => get_option('hearthis_width'),
            'height' => get_option('hearthis_height'),
            'theme' => get_option('hearthis_theme'),
            'color' => get_option('hearthis_color'),
            'hcolor' => get_option('hearthis_hcolor'),
            'style' => get_option('hearthis_style'),
            'background' => get_option('hearthis_background'),
            'waveform' => get_option('hearthis_waveform'),
            'autoplay' => get_option('hearthis_autoplay'),
            'cover' => get_option('hearthis_cover'),
            'block_size' => get_option('hearthis_block_size'),
            'block_space' => get_option('hearthis_block_space'),
            'css' => get_option('hearthis_css')
        ));
        return $defaults;
    }


    /**
     * function that checks if the value is a valid HEX color.
     *
     * @since    1.0.0
     * @access   private
     * @param    string   $value      the color value 
     * @return   bool     TRUE/FALSE  TRUE if it is a hex color else FALSE
     */
    private function hearthis_check_color( $value ) 
    {
        if ( preg_match( '/^#[a-f0-9]{6}$/i', strtolower($value) ) ) 
        { 
            return TRUE;
        }
        return FALSE;
    }


    /**
     * this return the value if it is a valid hexcolor 
     * if the 2nd param is given it will return the valuew without the #
     * this provides also a fallback if the given value is without the #
     * than will set it automaticly
     *
     * @since    1.0.0
     * @access   private
     * @param    string   $value      the color to check 
     * @param    bool     $only_hex   true to get color without #
     * @return   string   $value      the color or an empty string
     */
    private function hearthis_color( $value , $only_hex = FALSE)
    {
        if(substr($value,0,1) !== '#')
            $value = '#'.$value;

        if( $this->hearthis_check_color( $value ) )
        {
            if($only_hex === TRUE)
                $value = substr($value, 1);

            return $value;
        }
        return FALSE;
    }


    /**
     * returns the array with the urls which will be passed to the iframe(s)
     * 
     * @since    1.0.0
     * @access   private
     * @return   array  $hrefs  the array with the urls
     */
    private function get_iframe_urls()
    {
        $hrefs = array();
        $atts = $this->getVar('ATTS');
        $theme = 'transparent/';
        if(isset($atts['theme']) && $atts['theme'] === 'transparent_black')
        {
            /**
            *
            *@todo check why this is required to use the tranpsparent_black theme
            *@author this doesn't work without this share param ? and with an given color
            *        only the hcolor property is accepted by hearthis 
            */
            $theme = 'transparent_black/share/';
            if(isset($atts['color']))
                $this->clearVar(array('ATTS'=> 'color'));
        }
        if(isset($atts['background']) && $atts['background'] == '1')
        {
            $theme = NULL;
        }

        switch ($this->getVar('TYPE')) 
        {
            case 'PROFILE':
                $hrefs[] = $this->getVar('URL').'embed/'.$this->build_options_string(array('color','hcolor'));
                break;

            case 'SET':
                $hrefs[] = $this->getVar('URL').'embed/'.$this->build_options_string(array('color','hcolor'));
                break;

            case 'AS_TRACKS':
            case 'TRACK':
                foreach($this->getVar('SETLIST') as $trackid) 
                {
                    $hrefs[] = 'https://hearthis.at/embed/'.esc_attr($trackid).'/'.$theme.$this->build_options_string(self::$url_options_tpl);
                }
                break;
            
            default:
                break;
        }

        return $hrefs;
    }


    /**
     * this returns the query string which will set the look of the iframe widget
     * of the for param is set to LIST it only return the hcolor param as string
     * because this its the only one which taka effect on Profiles sr Setlist
     *
     * @todo http_build_query returns false if allow_url_fopen is off
     *
     * @since    1.0.0
     * @access   private
     * @param    array    $for           these are the options
     * @return   string   $url_options   query string for the irframe url
     */
    private function build_options_string($for)
    {
        $style = NULL;
        $params = array();
        for ($i=0; $i < count($for); $i++) 
        {   
            if(isset($this->getVar('ATTS')[$for[$i]]))
            {
                $params[$for[$i]] = $this->getVar('ATTS')[$for[$i]];
            }
            array_filter($params);
            #var_dump($params);
        }
        if(isset($params['style']))
        {
            $style ='style='.$params['style'].'&';
            unset($params['style']);
        }
        if( ! empty($params))
        {
            $query = http_build_query($params);
            return urldecode('?'.$style.$query);
        }
        else
        {
            return $style;
        }       
    }

    /**
     *
     * removes the key value pair of an array 
     * if the key was not found it return FALSE
     *
     * @author      NOT USED 
     * @since    1.0.0
     * @access   private
     * @return   bool|array   
     */
    private function array_remove()
    {
        if ($stack = func_get_args()) 
        {
            $input = array_shift($stack);
            foreach ($stack as $key) 
            {
                unset($input[$key]);
            }
            return $input;
        }
        return FALSE;
    }

    /**
     * unshifts a value with a named key in the given array
     * its like array_unshift but for assoc array types
     *
     * @author      NOT USED 
     * @since    1.0.0
     * @access   private
     * @param    array    $arr           the array where to unshift 
     * @param    array    $key           the keaýname to push
     * @param    array    $val           the value to push
     * @return   array   
     */

    private function array_unshift_assoc(&$arr, $key, $val) 
    { 
        $arr = array_reverse($arr, true); 
        $arr[$key] = $val; 
        return array_reverse($arr, true); 
    } 


    /**
     * returns true if widget is with an background image otherwise false
     *
     * @since    1.0.0
     * @access   private
     * @return   bool   TRUE/FALSE 
     */
    private function has_bg_img()
    {
        if( isset($this->getVar('ATTS')['background']) && $this->getVar('ATTS')['background'] == '1')
            return TRUE;

        return FALSE;
    }


    /**
     * returns true if widget is without waveform otherwise false
     *
     * @since    1.0.0
     * @access   private
     * @return   bool   TRUE/FALSE 
     */
    private function has_no_wave()
    {
        if( isset($this->getVar('ATTS')['waveform']) && $this->getVar('ATTS')['waveform'] == '1' )
            return TRUE;

        return FALSE;
    }
    

    /**
     * returns the player width in px or percent
     *
     * @since    1.0.0
     * @access   private
     * @return   string|integer   $_width   the iframe width
     */
    private function get_player_width()
    {   
        // set deafult
        $_width = 100;
         // get width from options
        if(isset($this->getVar('ATTS')['width']))
        {
            // replace some size types
            $user_width = str_replace(array('px','em','%'),'', $this->getVar('ATTS')['width']);
            // check if is numeric
            if(is_numeric($user_width))
            {
                // convert to integer
                $width = (int) $user_width;
                // if it within these params set it to width
                if( ($width >= 10) OR ($width <= 1180) )
                    $_width = $width;
            }
        }
    
        // if is less than 100 as percent if its larger than parse as pixels
        return ($_width <= 100) ?  $_width.'%' : $_width;
    }


    /**
     * returns the player height in px
     *
     * @since    1.0.0
     * @access   private
     * @return   string|integer    $_height   the iframe height
     */
    private function get_player_height()
    {
        $atts = $this->getVar('ATTS');
        // set default
        if(isset($atts['height']))
        {
            // get height from options ând check if is numeric
            $user_height = str_replace(array('px','em','%'),'', $atts['height']);
            if(is_numeric($user_height))
            {
                // set to integer
                $height = (int) $user_height;
            }
        }
        else
            $height = 130;

        if($this->getVar('TYPE') === 'PROFILE' OR $this->getVar('TYPE') === 'SET' )
        {
            // min height for a set or profile
            if($height < 350)
                $_height = 350;
            else
                $_height = $height;
        }

        if($this->getVar('TYPE') === 'TRACK' OR $this->getVar('TYPE') === 'AS_TRACKS' )
        {
            // min height with background img
            if( $this->has_bg_img() && $height < 170)
                $_height = 170;
            else
                $_height = $height;     

            // fixed height without waveform
            if( $this->has_no_wave() )
                $_height = 95;
        }

        // set height if its to large/less
        if($height > 450 || $height < 95)
            $_height = 130;

        return $_height;
    }


   /**
     * returns an array with the urls which should be shown on for the shortcode
     *
     * @since    1.0.0
     * @access   private
     * @return   void   sets the urls array the the SETLIST var which is used by listings for tracks
     */
    private function _set_setlist_urls($url)
    {
        $response = $this->curl_get($url);
        $responseBody = json_decode($response);
        $urls = array();
        if(isset($responseBody->id))
        {
            $urls[] = $responseBody->id;
        }
        // else { }
        
        $this->setVar('SETLIST', $urls);
    }


    /**
     * sets a var to the vars with the given key, 
     * if key is an object we will covert it to an array
     * the given array/object keys will passed to the vars
     *
     * @since    1.0.0
     * @access   private
     * @param mixed $key key could be a string, array or object 
     * @param string $value Value
     */
    private function setVar($key, $value = null) 
    {
        if ( (is_array($key) || is_object($key)) && is_null($value) ) 
        {
            $key = (array) $key;
            foreach ($key as $k => $v) 
            {
                $this->vars[(string)$k] = $v;
            }
        }
        else 
        {
            $this->vars[$key] = $value;
        }
    }


    /**
     * checks if a var in the vars has been set.
     *
     * @since    1.0.0
     * @access   private
     * @param string $key Key
     * @return bool Variable status
     */
    private function hasVar($key) 
    {
        return isset($this->vars[$key]);
    }


    /**
     * gets a var if no key given it will return all vars
     * if a key is given it will return the entry from vars or NULL
     * if the key doesn't exist
     *
     * @since    1.0.0
     * @access   private
     * @param string $key Key
     * @return mixed
     */
    private function getVar($key = null) 
    {
        if ($key === null) return $this->vars;
        return isset($this->vars[$key]) ? $this->vars[$key] : FALSE;
    }


    /**
     * unsets a var. if no key is given it unsets all var 
     * if an array is given it unset all vars from 
     * given array_keys if they exists 
     *
     * @since    1.0.0
     * @access   private
     * @param string|array $key key could be null or a string or an assco array 
     * @return void
     */
    private function clearVar($key = null) 
    {
        if( is_null($key) )
        { 
            $this->vars = array();
        }
        elseif( is_string($key) && isset($this->vars[$key]) )
        {
             unset($this->vars[$key]);
        }
        elseif(is_array($key))
        {
            foreach ($key as $k => $v) 
            {
                if(isset($this->vars[$k][$v]))
                    unset($this->vars[$k][$v]);
            }
        }
    }

    /**
     *  check if the curl functions exists
     *
     * @since    1.0.0
     * @access   protected
     * @return   bool   TRUE/FALSE
     */
    protected function check_curl_basic_functions()
    {
        if( ! function_exists("curl_init") &&
            ! function_exists("curl_setopt") &&
            ! function_exists("curl_exec") &&
            ! function_exists("curl_close") ) 
            return FALSE;
        else 
            return TRUE;
    }


    /**
     * Send a POST requst using cURL
     *
     *
     * @since    1.0.0
     * @access   protected
     * @param string $url to request
     * @param array $post values to send
     * @return string
     */
    private function curl_post($url, array $post = array())
    {
        $options = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10,
            // http_build_query returns false if allow_url_fopen is off
            CURLOPT_POSTFIELDS => (is_array($post)) ? http_build_query($post) : $post
        );
        if($this->check_curl_basic_functions())
        {
            $ch = curl_init();
            curl_setopt_array($ch, $options);

            if( ! $result = curl_exec($ch))
            {
                error_log("hearthis Plugin CURL POST ERROR |> No. ".curl_error($ch)."\n");
            }
            curl_close($ch);
            return $result;
        }
        else
        {
            $postdata = http_build_query($post);

            $opts = array('http' =>
                array(
                    'method'  => 'POST',
                    'content' => $postdata,
                    'ignore_errors' => 1,
                    'header' => array(
                        'Connection: Keep-Alive',
                        'Keep-Alive: timeout=10',
                        'Content-type: application/x-www-form-urlencoded;charset=UTF-8'
                    )
                )
            );

            $context = stream_context_create($opts);
            return file_get_contents($url, false, $context);
        }
    }


    /**
     * Send a GET requst using cURL
     *
     * @since    1.0.0
     * @access   protected
     * @param string $url to request
     * @param array $get values to send but ignored here
     * @return string
     */
    protected function curl_get($url, array $get = array())
    {
        $options = array( 
            CURLOPT_URL => $url, 
            CURLOPT_HEADER => 0, 
            CURLOPT_HTTPGET => 1,
            CURLOPT_SSL_VERIFYHOST => 1, 
            CURLOPT_RETURNTRANSFER => 1, 
            CURLOPT_TIMEOUT => 6 
        ); 

        if($this->check_curl_basic_functions())
        {
            $ch = curl_init();
            curl_setopt_array($ch, $options);

            if( ! $result = curl_exec($ch))
            {
                error_log("hearthis Plugin CURL GET ERROR |> No. ".curl_error($ch)."\n");
            }
            curl_close($ch);
            return $result;
        }
        else
        {
            $opts = array('http' =>
                array(
                    'method' => 'GET',
                    'protocol' => 1.1, 
                    'max_redirects' => '1',
                    'ignore_errors' => 1,
                    'header' => array(
                        'Keep-Alive: timeout=5',
                        'Content-Type: application/json',
                        'Connection: close'
                    )
                )
            );

            $context = stream_context_create($opts);
            $stream = fopen($url, 'r', false, $context);
            // actual data at $url
            $result = stream_get_contents($stream);
            fclose($stream);
            return $result;
        }
    }

}

