$Id$

About Gutenberg
===============
Gutenberg is a raw (unstyled) theme intended for use with CSS-based Movable Type layout styles. It gets Drupal close enough to MT's raw HTML output that a standard Movable Type style (like the ones available for download at <a href="http://www.thestylecontest.com">The Style Contest</a>) can be dropped in and used as sub-themes without modification. There are some mismatches -- page titles in particular have no corresponding class in a Movable Type layout, so we've fudged by using the date-header class.

Using Gutenberg
===============
While it's possible to install Gutenberg without any drop-in styles, it will look ugly: its output is raw HTML. You'll want to visit an archive of Movable Type styles, like http://www.sixapart.com/movabletype/styles/library or http://www.thestylearchive.com. Each style, and its supporting files (images, etc) will need to be placed in a subdirectory underneath the main Gutenberg directory. In addition, Movable Type styles may name their base CSS file anything from 'style.css' to 'theme-themename.css' to 'themename.css'. For Drupal's theme engine to recognize them, change the name of that CSS file to style.css. You'll also need to make sure they include a copy of base-weblog.css, as most Movable Type styles depend on it.

Once you've copied the styles into the gutenberg directory, and renamed their main style sheets to style.css, you should be ready to rock. Log on to your Drupal site, then hit admin/build/themes. The MT styles should show up as standard Drupal themes.