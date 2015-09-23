== hearthis.at ===
Contributors: hearthis, dj_force
Donate link: http://hearthis.at
Tags: hearthis, html5, player, shortcode, widgets, music, sound
Requires at least: 3.1.0
Tested up to: 4.3.x
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The hearthis.at Shortcode plugin allows you to integrate a player widget from hearthis.at into your Wordpress Blog by using a Wordpress shortcodes.

== Description ==

Use it in your blog post or pages by adding this Shortcode to your content:  
      `[hearthis]http://hearthis.at/LINK_TO_TRACK_SET_OR_ARTIST/[/hearthis]`.

The Plugin also supports optional parameters. By now these are width, height and params.
The "params" parameter will pass the given options on to the player widget. The hearthis 
player accepts the following parameter options:

* theme  			 = you can choose between these 2 options __transparent__ (default) or __transparent_black__
* width  			 = define the width of the widget (integer value or % value or empty string '', default is 100%)   
* height           =  define the height of the widget (integer value or empty string '', default is 145)  
* profile_height   =  define the height of the profile view (integer value or %, default is 400)  
* multi_height     =  define the height of the playlist view (integer value or empty string '', default is 450)  
* color2           =  highlight color for the waveform (not set or a hex color string with prependig #)  
* color            =  button and passed time color for the waveform (not set or a hex color string with prependig #)  
* cover            =  hides the cover img (values not set, 1 or 0)  
* autoplay         =  starts with autoplay (values not set, 1 or 0)  
* style            =  style 1 or 2  
* waveform         =  hide the waveform (values not set, 1 or 0)  
* background       =  shows the background if set (values not set, 1 or 0, if is 1 the height is 400px)  
* digitized_space  =  space between the waveform blocks (integer, steps from 1 to 10, works only if style is set to 2)
* digitized_size   =  size of the waveform blocks (integer, steps from 1 to 10, works only if style is set to 2)
* liststyle        =  only aviable on playlists and will also works only with 'single' as value 
* css              =  only aviable on tracks and you can provide a link to an external css file to style your player 


== Examples ==

Embed a single track without params.  
      `[hearthis]https://hearthis.at/shawne/shawne-pornbass-12-06042013-2300-0200-uhr/[/hearthis]`

Embed a playlist or set without params.  
      `[hearthis]https://hearthis.at/crecs/set/tbase-feat-charlotte-haining-oscar-michael-unspoken-words-ep/[/hearthis]`

Embed a user without color params and autostart.  
      `[hearthis color="#ff5c24" color2="#33fd11" autostart="1"]http://hearthis.at/djforce/[/hearthis]`

Embeds a track with a black theme and a bachground image (if set).  
      `[hearthis theme="transparent_black" background="1" ]https://hearthis.at/djforce/baesser-forcesicht-dnbmix/[hearthis]`

Embeds a track player with 300px width and a green button color.  
      `[hearthis width="300" color="#33fd11"]https://hearthis.at/crec/maverick-krl-c-recordings-guestmix/[/hearthis]`
      
Embeds a playlist or set with 400px height.  
      `[hearthis height="400"]https://hearthis.at/set/51-7/[/hearthis]`

I embeds a hook so if you have a playlist and do set the liststyle="single" option, it will parse all tracks from this set as single tracks.  
      `[hearthis liststyle="single"]https://hearthis.at/crecs/set/tbase-feat-charlotte-haining-oscar-michael-unspoken-words-ep/[/hearthis]`

Embeds a CSS File to style your hearthis Widgets individually 
      `[hearthis css="http://_LINK_TO_YOUR_CSS_FILE_"]https://hearthis.at/shawne/[/hearthis]`
      
== Installation ==

Download the Plugin and extract the content. You should see a folder
named hearthisat. Move or upload this folder to your Wordpress installation
plugins directory. By default the wordpress plugin folder is under /wp-content/plugins/. 

After you done this go to your Wordpress Backend and activate the Plugin. 
Now you are ready to go and can insert the hearthis Shortcodes.

== Frequently Asked Questions ==

**whats about httpfull**

It is already included and if you will update the library you can do this by downloading a new version from the developer site. You can overwrite the phar file without any problems.


== Screenshots ==

with a single Track  
![Track view ](/hearthisat/screenshot_track.png "the hearthis widget with a single track")

with a playlist or set  
![playlist view](/hearthisat/screenshot_playlist.png "the widget with a playlist widget")  

1. This is how the player looks.

== Changelog ==

**latest version is 0.6.5**

= version 0.6.5 =
+ several bugfixes
+ added the option and param for the css property

= version 0.6.4 =
+ add a trailing slash to hearthis url if its not exists

= version 0.6.3 =
+ add a shortcode option as a hook to parse all tracks from a playlist as single tracks 

= version 0.6.2 =
+ the plugin was originaly written by Benedikt GroÃŸ the founder of hearthis.at and this release fixes old or wrong options and bug so that you can use it with the latest wordpress version and you will have the full controll of all original hearthis params 


