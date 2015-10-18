=== hearthis.at ===
Plugin Name:       hearthis.at
Plugin URI:        https://wordpress.org/plugins/hearthisat/
Description:       the hearthis.at plugin allows you to integrate a player widget from hearthis.at into your Wordpress Blog by using a Wordpress shortcodes.
Tags: hearthis,    html5, player, shortcodes, music, widget
Contributors:      hearthis, dj_force
Donate link:       http://hearthis.at
Requires at least: 3.1
Tested up to:      4.3
Stable tag:        stable
Version:           1.0.0
Author:            Andreas Jenke <ja@so-ist.es>
Author URI:        http://so-ist.es/
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:       hearthis
Domain Path:       /languages 
License:           GPL-2.0+ or later
Tags:              hearthis, html5, player, sound, mp3, audio, shortcodes, music, widget
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

The hearthis.at plugin allows you to integrate a player widget from hearthis.at into your Wordpress Blog by using a Wordpress shortcodes.


== Description ==

The hearthis.at plugin allows you to integrate a player widget from hearthis.at into your Wordpress Blog by using a Wordpress shortcodes.
this is how it works:      
`[hearthis]http://http://hearthis.at/LINK_TO_TRACK_SET_OR_ARTIST/[/hearthis]`  

These Shortcodes do also supports several optional parameters. These parameters will pass its given value as option on to the player widget.  
At the moment the hearthis Shortcode accepts the following parameter and options:

*   `width` define the width of the widget (integer value or % value or empty string '', default is 100%)
*   `height` define the height of the widget (integer value or empty for default )    
if this value is less 100 it will passed as percent if it is higher than 100 it will parsed as pixels
*   `theme` you can choose between these 2 options transparent (default) or transparent_black
*   `hcolor` button and passed time color for the waveform (not set or a hex color string with prependig #)
*   `color` highlight color for the waveform (not set or a hex color string with prependig #)
*   `style` style 1 or 2
*   `background` shows the background if set (values not set, 1 or 0, if is 1 the height is 400px)
*   `waveform` hide the waveform if it set to 1 (values not set, 1 or 0)
*   `autoplay` starts with autoplay (values not set, 1 or 0)
*   `cover` hides the cover img if it set to 1 (values not set, 1 or 0)
*   `block_size` size of the waveform blocks (integer, steps from 1 to 10, default is 2, works only if style is set to 2)
*   `block_space` size of the spaces between the waveform blocks (integer, steps from 1 to 10, default is 1, works only if style is set to 2)
*   `liststyle` only available on playlists and will also works only with 'single' as value
*   `css` string, should contain a valid uri that will load an additional css file link the link tag


**IMPORTANT NOTE**

This Version **is now stable** and provides also a fallback for some old hearthis Shortcode params.    
So these params could also being used because they will passed to the their new names.

*   `color2` the old name of the hcolor property
*   `param` a params string with the namend values 
*   `digitized_size` old name of the waveform block size (see block_size)
*   `digitized_space` old name of the waveform block space (see block_space)

== Examples ==

Embed a single track without params.   
      `[hearthis]https://hearthis.at/shawne/shawne-pornbass-12-06042013-2300-0200-uhr/[/hearthis]`

Embed a playlist or set without params.   
      `[hearthis]https://hearthis.at/crecs/set/tbase-feat-charlotte-haining-oscar-michael-unspoken-words-ep/[/hearthis]`

Embed a user without color params and autostart.   
      `[hearthis color="#ff5c24" color="#33fd11" autostart="1"]http://hearthis.at/djforce/[/hearthis]`

Embeds a track with a black theme and a bachground image (if set).   
      `[hearthis theme="transparent_black" background="1" ]https://hearthis.at/djforce/baesser-forcesicht-dnbmix/[hearthis]`

Embeds a track player with 300px width and a green button color.   
      `[hearthis width="300" color="#33fd11"]https://hearthis.at/crec/maverick-krl-c-recordings-guestmix/[/hearthis]`

Embeds a track player with 300px width and waveform and highlight color and the theme transparent_black.   
      `[hearthis width="300" params="color=33e040&color2=00ff00&theme=transparent_black"]https://hearthis.at/djforce/dj-force-is-breaking-the-habit-electrobreaks-bass-dubstep-mix-052014/[/hearthis]`
  
Embeds a playlist or set with 400px height.   
      `[hearthis height="400"]https://hearthis.at/set/51-7/[/hearthis]`
  
I embeds a hook so if you have a playlist and do set the liststyle="single" option, it will parse all tracks from this set as single tracks.  
      `[hearthis liststyle="single"]https://hearthis.at/crecs/set/tbase-feat-charlotte-haining-oscar-michael-unspoken-words-ep/[/hearthis]


== Installation ==

1. Download the Plugin and extract its content. You should see a folder named hearthisat or hearthis.   
2. Upload the complete `hearthis` folder to the `/wp-content/plugins/` directory   
3. Activate the plugin through the 'Plugins' menu in WordPress   
4. Now you are ready to go and you can place `[hearthis]` Shortcodes to your pages or articles   



== Frequently Asked Questions ==


= What is about httpful ? =

With version 1.0.0 this was removed.

This was included till version 0.6.5. If you use a former version, 
you can update this library by downloading the latest phar file 
from the distributors [website][5]. The latest integrated version of [httpful][3] was v 0.2.19.
For more informations please visit the [httpful developer site][4].


== Screenshots ==

This is how the player widget could looks like:  

1. with a single Track 
![track view ](/hearthisat/screenshot_track.png "the view of the hearthis widget with a single track")

2. ... or with a playlist or set 
![playlist view](/hearthisat/screenshot_playlist.png "the view of the widget for a playlist") 

3. ... or if the url is a profile
![profile view](/hearthisat/screenshot_profile.png "the view of the widget for a profile") 


== Changelog ==

= 1.0.0 =
* removed the httpful Phar file so that this works completly without this  
* now based on the [Plugin API](http://codex.wordpress.org/Plugin_API), [Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards), and [Documentation Standards](http://make.wordpress.org/core/handbook/inline-documentation-standards/php-documentation-standards/).
* all classes, functions, and variables are documented so that you know what you need to be changed.
* includes a `.pot` file for internationalization but I don't used it.
* added the latest hearthis.at API functions for the shortcode params
* new backend/wp-admin Menu with color pickers  

= 0.6.5 =
* several bugfixes and reformating code to object base programming standard 

= 0.6.4 =
* added a trailing slash to an URL if its not exist

= 0.6.3 =
* added a Shortcodes option as a hook to transform a playlist url into single widgets for each track from this set instead of displaying a list view.

= 0.6.2 =
* fix and reformating the original code which was written by Benedikt Groß the founder of hearthis.at a year ago. This release fixes deprecated or wrong options and removes errors so now you will be able to use this plugin in the latest wordpress version. Now you will have full control about the latest original hearthis parameters. 


== special thanks and credits ==

= hearthis.at - music is our passion =

**thx for using and supporting** [hearthis.at][1]

[1]: https://hearthis.at/
[2]: https://de.wordpress.org/
[3]: https://github.com/nategood/httpful
[4]: http://phphttpclient.com/
[5]: http://phphttpclient.com/downloads/httpful.phar

