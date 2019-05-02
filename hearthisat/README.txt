=== hearthis.at ===
Plugin Name:       hearthis.at
Plugin URI:        https://wordpress.org/plugins/hearthisat/
Description:       the hearthis.at plugin allows you to integrate a player widget from hearthis.at into your Wordpress Blog by using a Wordpress shortcodes.
Tags:              hearthis, html5, player, sound, mp3, audio, shortcodes, music, widget
Contributors:      hearthis, dj_force
Donate link:       https://hearthis.at/
Requires at least: 3.1
Tested up to:      4.3.2
Stable tag:        stable
Version:           1.0.2
Author:            Andreas Jenke <ja@so-ist.es>
Author URI:        http://so-ist.es/
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt



== Description ==

The hearthis.at plugin allows you to integrate a player widget from hearthis.at into your Wordpress Blog by using a Wordpress shortcodes.
this is how it works:      
`[hearthis]http://http://hearthis.at/LINK_TO_TRACK_SET_OR_ARTIST/[/hearthis]`  

These Shortcodes do also supports several optional parameters. These parameters will pass its given value as option on to the player widget.  
At the moment the hearthis Shortcode accepts the following parameter and options:

*   `width` define the width of the widget (number value and if <= 100 means in %  and abouve 100 is in pixels. default is 100)
*   `height` define the height of the widget (integer value or empty for default )    
if this value is less 100 it will passed as percent if it is higher than 100 it will parsed as pixels
*   `theme` you can choose between these 2 options transparent (default) or transparent_black
*   `hcolor` button and passed time color for the waveform (not set or a hex color with or without a prependig #)
*   `color` highlight color for the waveform (not set or a hex color with or without a prependig #)
*   `style` style '1' is default waveform and value '2' is the digitized waveform
*   `background` shows the background if set (values not set, 1 or 0, if is 1 the height is 170px)
*   `waveform` hides the waveform if you set to '1', change track height fixed to 95px (values 0 off, 1 on)
*   `autoplay` starts with autoplay (values 0 off, 1 on)
*   `cover` hides the cover image if its set (values 0 off, 1 on)
*   `block_size` size of the waveform blocks (numberr, steps from 1 to 10, default is 2, works only if style is set to 2)
*   `block_space` space size between the waveform blocks (integer, steps from 1 to 10, default is 1, works only if style is set to 2)
*   `liststyle` only available on playlists or profile urls, this will display a list as single tracks buut works only if you provide value 'single'. If you enter a profile uri from hearthis.at this listing is limited by 50 tracks.
*   `css` string, should contain a valid uri that will load an additional css file link the link tag


**IMPORTANT NOTE**

This Version **is now stable** and provides also a fallback for some old hearthis Shortcode params.    
So these params could also being used because they will passed to the their new names.

*   `color2` the old name of the hcolor property
*   `params` a params string with the namend values 
*   `digitized_size` old name of the waveform block size (see block_size)
*   `digitized_space` old name of the waveform block space (see block_space)

== Examples ==

Embed a single track without any params.   
    `[hearthis]https://hearthis.at/shawne/shawne-pornbass-12-06042013-2300-0200-uhr/[/hearthis]`

Embed a playlist or setlist without params.   
    `[hearthis]https://hearthis.at/crecs/set/tbase-feat-charlotte-haining-oscar-michael-unspoken-words-ep/[/hearthis]`

Embed a user profile with a blue hightlight color.   
    `[hearthis hcolor="0000ff"]https://hearthis.at/crecs/[/hearthis]`

Embed a user without color params and autostart.   
    `[hearthis hcolor="#ff5c24" color="#33fd11" autostart="1"]http://hearthis.at/djforce/[/hearthis]`

Embeds a track with a black theme and a background image (if set) and hides the cover.   
    `[hearthis theme="transparent" background="1" cover="1"]https://hearthis.at/djforce/baesser-forcesicht-dnbmix/[hearthis]`

Embeds a track player with 50% width and a green button color.   
    `[hearthis width="50" color="#33fd11"]https://hearthis.at/crec/maverick-krl-c-recordings-guestmix/[/hearthis]`

Embeds a track player with 500px width and hides the waveform and has the transparent_black theme. Remember this will change track height to 95 pixels.    
    `[hearthis width="500" waveform="1" params="color2=00ff00&theme=transparent_black"]https://hearthis.at/djforce/dj-force-is-breaking-the-habit-electrobreaks-bass-dubstep-mix-052014/[/hearthis]`
  
Embeds a playlist or set with 400px height.   
    `[hearthis height="400"]https://hearthis.at/set/51-7/[/hearthis]`
  
This is not a real option, its more like a hook and works only with playlists or profile urls.
So if you want to display a playlist or user profile as single tracks and not as a list you can set this option. 
For users we limited this by 50 tracks. Does only take effect if the value single is inside the liststyle tag.    
    `[hearthis liststyle="single"]https://hearthis.at/djforce/set/dubstep-mixes/[/hearthis]`


== Installation ==

1. Download the Plugin and extract its content. You should see a folder named hearthisat or hearthis.   
2. Upload the complete `hearthisat` folder to the `/wp-content/plugins/` directory   
3. Activate the plugin through the 'Plugins' menu in WordPress   
4. Now you are ready to go and you can place `[hearthis]` Shortcodes to your pages or articles   



== Frequently Asked Questions ==

= What is with httpful Phar file? =

With version 1.0.0 it's removed!

This was included till version 0.6.5. If you use a former version, 
you can update this library by downloading the latest phar file 
from the [distributors website][4]. The latest integrated version we used was version 0.2.19. 

= Where I can get help? =

There is a issue tracker at the github.com website.

So if you will have any problems with the plugin please visit developer site at [github.com/Andrew-Jenkins/wp_plugin_hearthis](https://github.com/Andrew-Jenkins/wp_plugin_hearthis).
You can open a new [issue][3] and ask for help.


== Screenshots ==

This is how the player widget could looks like:  

1. with a single Track 
![track view ](/hearthisat/screenshot_track.png "the view of the hearthis widget with a single track")

2. ... or with a playlist or set 
![playlist view](/hearthisat/screenshot_playlist.png "the view of the widget for a playlist") 

3. ... or if the url is a profile
![profile view](/hearthisat/screenshot_profile.png "the view of the widget for a profile") 


== Changelog ==

= 1.0.2 =
* small bug fix with height option from shortcode 
* bugfix responseBody check 

= 1.0.1 =
* minor bug fix with theme transparent_black option that doens't work, if you use the transparent_black we have to remove the value of the color and to add the share path to the irframe url? maybe it's a lil' bug at hearthis.at. I'll check this later. Right now this work's for all wordpress users. 
* changed some php and readme documenations

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
[3]: https://github.com/Andrew-Jenkins/wp_plugin_hearthis/issues
[4]: http://phphttpclient.com/

