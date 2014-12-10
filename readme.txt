=== Plugin Name ===
Contributors: scottsm
Donate link: http://scott.sherrillmix.com/blog/
Tags: comments, avatar, monster, monsterid, gravatar, icon
Requires at least: 1.5
Tested up to: 4.0.1
Stable tag: 3.0

Creates a unique, persistent monster avatar for each commenter based on email address. 

== Description ==

This plugin provides a small randomly assembled monster avatar for each user based on their email address. Think gravatar only without requiring any external site and with monsters. Now with monsters consistent across servers. Based on idea and images by [Andreas Gohr](http://www.splitbrain.org/blog/2007-01/20_monsterid_as_gravatar_fallback) and artwork by [Lemm](http://rocketworm.com/). See the plugin website if you need any help or for an example of the plugin in action.

== Installation ==

1. Unzip `wp_monsterid.zip`. 
1. Upload `wp_monsterid.php` and the `monsterid` folder to `wp-content/plugins`. 
1. Make sure the `monsterid` folder is [writable](http://codex.wordpress.org/Changing_File_Permissions). 
1. Activate the plugin in the Plugins Admin page.
1. Monsters should now appear beside commenters' names. Enjoy. (Advanced users can edit their theme file if they want further control).
1. You can add CSS for `img.monsterid` in your theme's style.css to adjust the appearance of the images or adjust the size in the MonsterID control panel (your old monsters won't be deleted until you clear the cache). You can also turn on Gravatar support or clear the MonsterID image cache in the Control Panel.

== Frequently Asked Questions ==

= Will my monster be the same on different blogs? =

Yes, if they're using the standard version (and I didn't mess up anything).

= Can it generate monsters only for people without gravatars? =

Yes. Just turn on the Gravatar option in the MonsterID options.

= Can I add MonsterIDs to the Recent Comments Widget in my sidebar? =

Yes, this plugin provides a replacement widget to Recent Comments (since the default widget doesn't provide the commenter's email).

= Why should I use this plugin when Gravatars now has Identicons? = 

The gravatar monsters are pretty good but if you want artistic monsters or just want one set of avatars for everyone on your site, then this plugin's for you. Otherwise, sure go with gravatars (just set <code>&default=monsterid</code> in the gravatar url).



== Screenshots ==

1. Example of WP_MonsterIDs.

2. Example of artistic (available in WP_MonsterID 2.0+) WP_MonsterIDs.
