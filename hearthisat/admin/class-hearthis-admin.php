<?php

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @link       http://so-ist.es
	 * @since      1.0.0
	 *
	 * @package    Hearthis
	 * @subpackage Hearthis/admin
	 */

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    Hearthis
	 * @subpackage Hearthis/admin
	 * @author     Andreas Jenke <deraj@outlook.de>
	 */
	class Hearthis_Admin {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $plugin_name The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $version The current version of this plugin.
		 */
		private $version;



		/**
		 * Holds the values to be used in the fields callbacks
		 */
		private $options;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 *
		 * @param      string $plugin_name The name of this plugin.
		 * @param      string $version The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version     = $version;
			add_filter("plugin_action_links_" . plugin_basename(__FILE__), array($this, 'hearthis_plugin_settings_link'));
		}


		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {
			// Add the color picker css file
			wp_enqueue_style( 'wp-color-picker' );
			// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hearthis-admin.css', array(), $this->version, 'all' );
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			// Include our custom jQuery file with WordPress Color Picker dependency
			wp_enqueue_script( 'wp-color-picker' );
		}


		/**
		 * Register the Setting.
		 *
		 * @since    1.0.0
		 */
		public function init_hearthis_settings() {
			foreach (Hearthis::$settings as $name )
			{
				register_setting( 'hearthis-settings',
				                  $this->plugin_name . '_' . $name );
				                  //, array($this, 'callback'));
			}
		}

		/**
		 * shows the options page for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function hearthis_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hearthis-admin.js',
						                   array( 'jquery' ), $this->version, FALSE );
			?>
			<div class="wrap">
				<h2>hearthis.at Default Settings</h2>

				<p>You can always change these settings with your shortcode options property.
					With your shortcode options you'll always override each of these defaults individually. If
					you won't use the shortcode options than a fallback to these defaults is taken.
					Please note that not every settings does take affect or will changed with your selections. Some
					settings depends on your hearthis.at url and if its a track, playlist or profile link.</p>
				<form method="post" action="options.php">
					<?php settings_fields( 'hearthis-settings' ); ?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">Current Default 'params'</th>
							<td>
							<?php

								echo http_build_query(
											array_filter( 
									             array(
	                                               'width'       => get_option( 'hearthis_width' ),
	                                               'height'      => get_option( 'hearthis_height' ),
	                                               'theme'       => get_option( 'hearthis_theme' ),
	                                               'hcolor'      => get_option( 'hearthis_hcolor' ),
	                                               'color'       => get_option( 'hearthis_color' ),
	                                               'style'       => get_option( 'hearthis_style' ),
	                                               'background'  => get_option( 'hearthis_background' ),
	                                               'waveform'    => get_option( 'hearthis_waveform' ),
	                                               'autoplay'    => get_option( 'hearthis_autoplay' ),
	                                               'cover'       => get_option( 'hearthis_cover' ),
	                                               'block_size'  => get_option( 'hearthis_block_size' ),
	                                               'block_space' => get_option( 'hearthis_block_space' ),
	                                               'css'         => get_option( 'hearthis_css' ) 
	                                               )
												)
											);
								/*
								echo  'each setting name without prefix<br>';
								foreach ( Hearthis::$settings as $name ) 
								{
									echo  $name . '<br>';
								}
									echo '<br><pre>filtered saved options'.print_r(
									array_filter( array(
	                                               'width'       => get_option( 'hearthis_width' ),
	                                               'height'      => get_option( 'hearthis_height' ),
	                                               'theme'       => get_option( 'hearthis_theme' ),
	                                               'hcolor'      => get_option( 'hearthis_hcolor' ),
	                                               'color'       => get_option( 'hearthis_color' ),
	                                               'style'       => get_option( 'hearthis_style' ),
	                                               'background'  => get_option( 'hearthis_background' ),
	                                               'waveform'    => get_option( 'hearthis_waveform' ),
	                                               'autoplay'    => get_option( 'hearthis_autoplay' ),
	                                               'cover'       => get_option( 'hearthis_cover' ),
	                                               'block_size'  => get_option( 'hearthis_block_size' ),
	                                               'block_space' => get_option( 'hearthis_block_space' ),
	                                               'css'         => get_option( 'hearthis_css' ) )
											), true	).'</pre>';
								echo '<br><pre>filtered saved options'.print_r(
									array_filter( array(
	                                               'width'       => get_option( 'hearthis_width' ),
	                                               'height'      => get_option( 'hearthis_height' ),
	                                               'theme'       => get_option( 'hearthis_theme' ),
	                                               'hcolor'      => get_option( 'hearthis_hcolor' ),
	                                               'color'       => get_option( 'hearthis_color' ),
	                                               'style'       => get_option( 'hearthis_style' ),
	                                               'background'  => get_option( 'hearthis_background' ),
	                                               'waveform'    => get_option( 'hearthis_waveform' ),
	                                               'autoplay'    => get_option( 'hearthis_autoplay' ),
	                                               'cover'       => get_option( 'hearthis_cover' ),
	                                               'block_size'  => get_option( 'hearthis_block_size' ),
	                                               'block_space' => get_option( 'hearthis_block_space' ),
	                                               'css'         => get_option( 'hearthis_css' ) )
											), true	).'</pre>';

											// 'player_width',
											// 'player_height',
											// 'player_height_list',
											// 'theme',
											// 'color',
											// 'hcolor',
											// 'style',
											// 'background_show',
											// 'waveform_hide',
											// 'autoplay',
											// 'cover_hide',
											// 'block_size',
											// 'block_space',
											// 'liststyle',
											// 'css'
*/
									?>


							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Player default width</th>
							<td><input type="text" min="0" maxlength="5" placeholder="100" id="hearthis_width" name="hearthis_width"
							           value="<?php echo get_option( 'hearthis_width' ); ?>"> A number below 100 this would be interpreted as percent! If you enter a number larger than 100 you will set up this in pixels.<br/>
								Leave blank to use the default.
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Player height</th>
							<td>
								<input type="text" min="0" maxlength="3" placeholder="140"  id="hearthis_height" name="hearthis_height"
								       value="<?php echo get_option( 'hearthis_height' ); ?>"> This setting is only in pixels. Leave it blank to use the default. <br>
								       This value depends on your track url and could be overwritten.
								So if your shortcode url will show a profile we will detect that an this value will be overwritten by a defaukt value of 350. On other hands 
								if you like to set the option below, which will hide the waveform image, this value will immediatly change to a value of 95.
								<br/>
								Leave blank to use the default.
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Waveform Color</th>
							<td><input type="text" id="hearthis_color" name="hearthis_color" value="<?php echo get_option( 'hearthis_color' ); ?>">
								Defines the default waveform and buttons color.
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Highlight Waveform Color</th>
							<td><input type="text" id="hearthis_hcolor" name="hearthis_hcolor" value="<?php echo get_option( 'hearthis_hcolor' ); ?>">
								The highlight color is shown in waveform in the passed time or on hover effects in the player. On User Profiles it's define also the normal wavform color.
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Theme Color (Tracks Only)</th>
							<td>
								<input type="radio" id="hearthis_theme_color_light" name="hearthis_theme"
								       value="transparent" <?php if ( strtolower( get_option( 'hearthis_theme' ) ) === 'transparent' ) {echo 'checked';} ?> />
								<label for="hearthis_theme_color_light" style="margin-right: 1em;">Light</label>
								<input type="radio" id="hearthis_theme_color_dark" name="hearthis_theme"
								       value="transparent_black" <?php if ( strtolower( get_option( 'hearthis_theme' ) ) === 'transparent_black' ) {echo 'checked';} ?> />
								<label for="hearthis_theme_color_dark" style="margin-right: 1em;">Dark</label>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Hide cover image</th>
							<td>
								<input type="checkbox" name="hearthis_cover" id="hearthis_cover"
								value="<?php echo get_option('hearthis_cover');?>"<?php echo get_option( 'hearthis_cover' ) == '1'  ? ' checked="checked"' : '' ;?>>
								<label for="hearthis_cover" style="margin-right: 1em;">Hide cover,
									on/off</label><br/> The player will not show the cover image from this tracks.
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Hide waveform</th>
							<td>
								<input type="checkbox" name="hearthis_waveform" id="hearthis_waveform"
								       value="<?php echo get_option( 'hearthis_waveform' );?>"<?php if(get_option( 'hearthis_background' ) == '1') echo ' disabled="disabled"';?>>

								      <label for="hearthis_waveform" style="margin-right: 1em;">hide waveform,
									on/off</label><br/> The player will not show the waveform of any track.
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Show background image</th>
							<td>
								<input type="checkbox" name="hearthis_background" id="hearthis_background" 
								value="<?php echo get_option( 'hearthis_background', '0' );?>"<?php if(get_option( 'hearthis_waveform' ) == '1') echo ' disabled="disabled"';?>>
								<label for="hearthis_background_show" style="margin-right: 1em;">
									Show background image (if it has one)</label>
								<br/> The player will show the background image if it has one. This selection depends on the hide/show waveform property. So if you activate this, you'll be not able to hide the waveform.
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">Start Autoplay</th>
							<td>
								<input type="checkbox" name="hearthis_autoplay" id="hearthis_autoplay"
								       value="<?php echo get_option( 'hearthis_autoplay' );?>"<?php echo ( get_option( 'hearthis_autoplay' ) == 1 ) ? ' checked="checked"' : '';?>>Should the player starts with autoplay after loading?
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Waveform Style</th>
							<td>
								<select id="hearthis_style" name="hearthis_style">
									<option value="1"<?php echo ( get_option( 'hearthis_style' )  == '1' )? ' selected="selected"' : '';?>>Waveform Style: Soft
									</option>
									<option value="2"<?php echo ( get_option( 'hearthis_style' )  == '2' )? ' selected="selected"' : '';?>>Waveform Style: Digitized
									</option>
								</select>

								<div id="style-tpl" style="display: none;">
									<div style="float: left; width: 25%;margin-top: 15px;">
										<div style="float: left; padding:5px;">Block Size</div>
										<input id="hearthis_block_size" name="hearthis_block_size" type="range" min="1"
										       max="10" step="1"
										       value="<?php echo ( get_option( 'hearthis_block_size' ) ==  '') ? '1' :  (int) get_option( 'hearthis_block_size' ); ?>"
										       style="float: left; margin-right: 15px;"/>
									</div>
									<div style="float: left; width: 25%;margin-top: 15px;">
										<div style="float: left; padding: 5px;">Block Space</div>
										<input id="hearthis_block_space" type="range" name="hearthis_block_space"
										       min="1" max="10" step="1"
										       value="<?php echo ( get_option( 'hearthis_block_space' ) ==  '') ? '2' :  (int) get_option( 'hearthis_block_space' ); ?>"
										       style="float: left; margin-right: 15px;"/>
									</div>
								</div>
								<div style="clear:both"></div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Link to CSS File</th>
							<td>
								<input type="url" style="min-width: 300px;" name="hearthis_css" placeholder="http://your-domain.com/stylesheet.css" id="hearthis_css" value="<?php echo get_option('hearthis_css');?>">
								<label for="hearthis_css" style="margin-right: 1em;">Url to an external CSS File</label><br/>
								You can provide a external CSS File with markups to stlye your individual player.
							</td>
						</tr>
					</table>
					<p class="submit">
						<?php submit_button(); ?>
					</p>
				</form>
			</div>
		<?php
		}

		public function hearthis_admin_menu()
		{
			add_menu_page( 'hearthis.at Einstellungen', 'hearthis.at', 'manage_options', 'hearthis',
			               array( $this, 'hearthis_settings_page'), plugin_dir_url( __FILE__ ).'img/icon.png', $position=67 );
			add_options_page( 'hearthis.at Options', 'hearthis.at', 'manage_options', 'hearthis-shortcode-options',
			                  array( $this, 'hearthis_settings_page' ) );
		}

		public function hearthis_plugin_settings_link( $links )
		{
	        $settings_link = '<a href="options-general.php?page=hearthis-shortcode-options">'.__('Settings').'</a>';
	        array_unshift( $links, $settings_link );
	        return $links;
		}

		/**
		* Function that will check if value is a valid HEX color.
		*/
		public function hearthis_clear_color( $value , $convert = FALSE)
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

	}
