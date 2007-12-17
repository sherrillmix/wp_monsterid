=== Plugin Name ===
Contributors: scottsm
Donate link: http://scott.sherrillmix.com/blog/
Tags: comments, avatar, monster, monsterid, gravatar, icon
Requires at least: 1.5
Tested up to: 2.3.1
Stable tag: 0.61

Creates a unique, persistent monster avatar for each commenter based on email address. 

== Description ==

This plugin provides a small randomly assembled monster avatar for each user based on their email address. Think gravatar only without requiring any external site and with monsters. Now with monsters consistent across servers. Based on idea and images by [Andreas Gohr](http://www.splitbrain.org/blog/2007-01/20_monsterid_as_gravatar_fallback). See the plugin website if you need any help or for an example of the plugin in action.

== Installation ==

1. Unzip `wp_monsterid.zip`. 
1. Upload `wp_monsterid.php` and the `monsterid` folder to `wp-content/plugins`. 
1. Make sure the `monsterid` folder is [writable](http://codex.wordpress.org/Changing_File_Permissions). 
1. You can now add a monster to any comment with `monsterid_build_monster($comment->comment_author_email, $comment->comment_author)`. I don’t think there’s a convenient way to make Wordpress automatically add pictures to comments so now you’re going to have to get your hands slightly dirty. Find the `comments.php` of your current theme (it should be in the folder `wp-content/themes/currentThemeName/`). Open it up and look for something similar to `foreach ($comments as $comment)`. Inside this loop there should be code that displays the comment author’s name or metadata like `<p class="comment-author">` or `<p class="comment-metadata">`. Just before all this enter `<?php if (function_exists("monsterid_build_monster")) {echo monsterid_build_monster($comment->comment_author_email,$comment->comment_author); } ?>`
1.You can add CSS for `img.monsterid` in your theme’s style.css to adjust the appearance of the images or adjust the size in the MonsterID control panel (your old monsters won’t be deleted until you clear the cache). You can also clear the MonsterID image cache in the Control Panel.

== Frequently Asked Questions ==

= Will my monster be the same on different blogs? =

Yes, if they're using the standard version (and I didn't mess up anything).


== Screenshots ==

1. An example of WP_MonsterID in action.

