# Wordpress
This repo contains a variety of Wordpress code snippets that I use with the WPcode Wordpress plugin:

## replace_excerpt.php
This replaces the standard excerpt with an image (the featured image, or if there isn't one, the first image in the blog post) and a paragraph under a specified heading (in this example, an h3 heading with word "Limerick")
Set this to run everywhere, so that any time the excerpt appears, it is replaced with this excerpt

## post_meta_preview.php
This generates meta tags within the page header so that if other sites link through to this site, the appropriate content for the preview is shown on that site.  This creates meta tags for Twitter and open graph, including image and alt text, title and description
Set this to run everywhere, so that it is generated within the header of every page

## post_to_twitter.php
This uses the https://github.com/abraham/twitteroauth library to post some of the extracted content of a Wordpress post (taken from a GET request argument "twitterpostid") to Twitter (some of the text of the post plus image and alt text).  This currently has a bug in where it creates two duplicate tweets!
Set this to run with a shortcode so it doesn't appear everywhere
