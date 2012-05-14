![FLI logo](https://github.com/brasofilo/featured-link-image/raw/master/logo.png)

# Tuit Cycler
WordPress plugin for displaying a Widget with x numbers of Tweets from a specific User. Displays one tweet at a time, and rotates them using the jQuery Cycle Plugin.

## Description
Based in this [WordPress Question](http://wordpress.stackexchange.com/q/52074/12615), by Anriëtte Combrink.

Uses [jQuery Cycle Plugin](http://jquery.malsup.com/cycle/) and [jQuery Easing] (http://gsgd.co.uk/sandbox/jquery/easing/).

Function for grabbing the Tweets grabbed from the plugin [Twitter Hash Tag Shortcode] (http://wordpress.org/extend/plugins/twitter-hash-tag-shortcode/), by Bainternet.

A little trick for registering the Javascript files only when Widget active from this [WordPress Answer](http://wordpress.stackexchange.com/a/48385/12615), by One Trick Pony.

A little touch of style in the Widget admin area by this [WordPress Q&A](http://wordpress.stackexchange.com/q/3003/12615), by Jan Fabry.

## Screenshot
![Tuit Cycler](https://github.com/brasofilo/tuit-cycler/raw/master/screenshot.png)

##FAQ
Check [jQuery Cycle documentation](http://jquery.malsup.com/cycle/options.html) and configure the file /js/tuit-widget.js

The CSS for the frontend Widget is inside the function tuit_print_stylesheet (line 54) of the plugin.

The individual twitts are printed in the last lines of the plugin.
Line 183 displays the user image.
Line 184 is the actual twitt.

## Installation
### Requirements
* WordPress version 3.3 and later (not tested in previous versions)

### Installation
1. Unpack the download-package
1. Upload the file to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


## Other Notes
### Licence
Released under GPL, you can use it free of charge on your personal or commercial blog.