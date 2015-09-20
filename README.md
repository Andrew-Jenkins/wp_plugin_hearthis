# Overview

This is a Wordpress Plugin which allows you to easily integrate a player widget for a track, set or playlist or a pofile from [hearthis.at][1] into your Wordpress Blog by using Shortcodes.

Requirements
==========

 * [Wordpress][2], version >= 3.1.0  
 * PHP 5.3+. 
 * [httpful][3] / [php http client][4] already included, but you may update it by your self

Description
-----------------

Use it in your blog post or pages by adding this Shortcode to your content:  
      `[hearthis]http://hearthis.at/LINK_TO_TRACK_SET_OR_ARTIST/[/hearthis]`.

The Plugin also supports optional parameters. By now these are width, height and params.
The "params" parameter will pass the given options on to the player widget. The hearthis 
player accepts the following parameter options:

* theme  = you can choose between these 2 options __transparent__ (default) or __transparent_black__
* width  = define the width of the widget (integer value or % value or empty string '', default is 100%)   
* height           =  define the height of the widget (integer value or empty string '', default is 145)  
* profile_height   =  define the height of the profile view (integer value or %, default is 400)  
* multi_height     =  define the height of the playlist view (integer value or empty string '', default is 450)  
* color2            =  highlight color for the waveform (not set or a hex color string with prependig #)  
* color            =  button and passed time color for the waveform (not set or a hex color string with prependig #)  
* cover            =  hides the cover img (values not set, 1 or 0)  
* autoplay         =  starts with autoplay (values not set, 1 or 0)  
* style            =  style 1 or 2  
* waveform         =  hide the waveform (values not set, 1 or 0)  
* background       =  shows the background if set (values not set, 1 or 0, if is 1 the height is 400px)  
* digitized_space      =  size of the spaces between the waveform blocks (integer, steps from 1 to 10, works only if style is set to 2)  
* digitized_size       =  size waveform blocks (integer, steps from 1 to 10, works only if style is set to 2)  
      

Examples
--------------

Embed a single track without params.  
      `[hearthis]https://hearthis.at/shawne/shawne-pornbass-12-06042013-2300-0200-uhr/[/hearthis]`

Embed a playlist or set without params.  
      `[hearthis]https://hearthis.at/crecs/set/tbase-feat-charlotte-haining-oscar-michael-unspoken-words-ep/[/hearthis]`

Embed a user without params.  
      `[hearthis]http://hearthis.at/djforce/[/hearthis]`

Embeds a track with a black theme and a bachground image (if set).  
      `[hearthis theme="transparent_black" background="1" ]https://hearthis.at/djforce/baesser-forcesicht-dnbmix/[hearthis]`

Embeds a track player with 300px width and a green button color.  
      `[hearthis width="300" color="#33fd11" ]https://hearthis.at/crec/maverick-krl-c-recordings-guestmix/[/hearthis]`
      
Embeds a playlist or set with 400px height.  
      `[hearthis height="400"]https://hearthis.at/set/51-7/[/hearthis]`


Installation
------------------

Download the Plugin and extract the content. You should see a folder
named hearthisat. Move or upload this folder to your Wordpress installation
plugins directory. By default the wordpress plugin folder is under /wp-content/plugins/. 

After you done this go to your Wordpress Backend and activate the Plugin. 
Now you are ready to go and can insert the hearthis Shortcodes.


Frequently Asked Questions
--------------------------------

I have to write this :)


Screenshots
-----------------

This is how the player widget will looks like:  

with a single Track  
![Track view ](/hearthisat/screenshot_track.png "the hearthis widget with a single track")

with a playlist or set  
![playlist view](/hearthisat/screenshot_playlist.png "the widget with a playlist widget")  

Changelog
---------------

**latest version is 0.6.2**

the plugin was originaly written by Benedikt Groß the founder of hearthis.at,
I rewrite this plugin but it is still in development, so please wait for the stable 
release which will come soon.

**thx and support [hearthis.at][1]**

[1]: https://hearthis.at/
[2]: https://de.wordpress.org/
[3]: https://github.com/nategood/httpful
[4]: http://phphttpclient.com/

