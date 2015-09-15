# Overview

This is a Wordpress Plugin which allows you to easily integrate a player widget for a track, set, playlist or from hearthis.at[1] into your Wordpress Blog by using Wordpress Shortcodes.

Requirements
==========

 * Wordpress[2] Blog in version 3.1.0  
 * httpful [3] [4]

Description
-----------------

Use it in your blog post or pages by adding this Shortcode to your content:
`[hearthis]http://hearthis.at/LINK_TO_TRACK_SET_OR_ARTIST/[/hearthis]`.

The Plugin also supports optional parameters. By now these are width, height and params.
The "params" parameter will pass the given options on to the player widget. The hearthis 
player accepts the following parameter options:

* hcolor = (hex color codes) will show the play button, waveform and selections in this color
* theme  = you can choose between these 2 options "transparent" (default) or "transparent_black"


Examples
--------------

Embed a single track without params.
`[hearthis]https://hearthis.at/shawne/shawne-pornbass-12-06042013-2300-0200-uhr/[/hearthis]`

Embed a playlist or set without params.
`[hearthis]https://hearthis.at/crecs/set/tbase-feat-charlotte-haining-oscar-michael-unspoken-words-ep/[/hearthis]`

Embed a user without params.
`[hearthis]http://hearthis.at/djforce/[/hearthis]`

Embeds a track with a green highlight color and black theme.
`[hearthis params="hcolor=33e040&theme=transparent_black"]https://hearthis.at/shawne/shawne-stadtfest-chemnitz-31082013/[/hearthis]`

Embeds a track player with 250px width.
`[hearthis width="250"]https://hearthis.at/djforce/baesser-forcesicht-dnbmix/[/hearthis]`

Embeds a playlist or set with 400px height.
`[hearthis height="400"]https://hearthis.at/set/51-7/[/hearthis]`


Installation
------------------

Download the Plugin and extract the content. You should see a folder
named hearthis. Move or upload this folder to your Wordpress installation
plugins directory. By default the wordpress plugin folder is under /wp-content/plugins/. 

After you done this go to your Wordpress Backend and activate the Plugin. 
Now you are ready to go and can insert the hearthis Shortcodes.


Frequently Asked Questions
--------------------------------

I have to write this :)


Screenshots
-----------------

1. This is how the player looks. btw: this follows at next

Changelog
---------------

latest version is 0.6.2

the plugin was originaly written by Benedikt Groß the founder of hearthis.at,
I rewrite this plugin but it is still in development, so please wait for the stable 
release which will come soon.

thx and please support hearthis.at

[1]: https://hearthis.at/
[2]: https://de.wordpress.org/
[3]: http://phphttpclient.com/
[4]: https://github.com/nategood/httpful

