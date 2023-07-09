<?php
// replace "get_the_excerpt" function call with the following code
add_filter( 'get_the_excerpt', function( $excerpt, $post ) {

  // Grab the content and set alt_text for the image to be the title of the post
	$excerpt = "";
  $content = apply_filters('the_content', get_the_content());
	$alt_text = get_the_title($post->ID);
	
  // Get the post thumbnail or the first image from the content
  $thumbnail_url = get_the_post_thumbnail_url($post, 'thumbnail');
  if (!$thumbnail_url) {
        
  	preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].+alt=[\"](?P<alt>.+?)[\"].+>/i', $content, $matches);
    if (isset($matches['src'])) {
      	$thumbnail_url = $matches['src'];
    }
		
    if (isset($matches['alt'])) {
			$alt_text = $matches['alt'];
		}
  }

  // Get all content after a specific h3 heading (in this case "Limerick")
  // Match the first and second h3 tags
  preg_match('/Limerick<\/h3>(.*?)<p><em>/s', $content, $matches);

  // If the h3 tags are not found, return a blank paragraph
  if (!$matches) {
     $paragraphs = "";
  } else {
	   // Extract the content between the h3 tags
     $paragraphs = $matches[1];
	}

  // if we've got a thumbnail, create that as an image with appropriate alt text
	if ($thumbnail_url) { 
		$excerpt .= '<figure class="wp-block-image size-full"><img src="' .  $thumbnail_url. '" alt="' . $alt_text . '"></figure>';
	}
        
	$excerpt .= $paragraphs; 
  	
	return $excerpt;
}, 10, 2 );
?>
