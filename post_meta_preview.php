<?php
function generate_social_link_preview() {
	
    // Get the stuff we wish to share
    $post_id = get_queried_object_id();
	
	  echo "<!--- Queried object id is " . $post_id . "--->";

    // get the stuff we want to put in the preview
    $post_title = get_the_title($post_id);
    $post_permalink = get_permalink($post_id);
	  $post_content = get_the_content($post_id);
    $post_excerpt = get_the_excerpt($post_id);
    $post_thumbnail = get_the_post_thumbnail_url($post_id, 'thumbnail');
	
	  // see if we can replace excerpt with the stuff under an h3 heading with the word "Limerick"
    preg_match('/Limerick<\/h3>(.*?)<p><em>/s', $post_content, $matches);

    // If the h3 tags are found the excerpt will be the limerick
    if ($matches) {
		  // Extract the content between the h3 tags
    	$post_excerpt = $matches[1];
	  }

    // see if we've got a post thumbnail - if we don't, then grab the first image from the post, along with alt text
	  if (!$post_thumbnail) {
		    preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].+alt=[\"](?P<alt>.+?)[\"].+>/i', $post_content, $matches);
        if (isset($matches['src'])) {
        	$post_thumbnail = $matches['src'];
        }
		    if (isset($matches['alt'])) {
			    $post_thumbnail_alt = $matches['alt'];
		    }
	  }

    // generate first the Twitter Card, then the open graph 
    if ($post_title && $post_permalink) {
        // Twitter Card meta tags
        echo '<meta name="twitter:card" content="summary_large_image">';
        echo '<meta name="twitter:title" content="' . esc_attr($post_title) . '">';
        echo '<meta name="twitter:description" content="' . esc_attr($post_excerpt) . '">';
        echo '<meta name="twitter:url" content="' . esc_url($post_permalink) . '">';

        if ($post_thumbnail) {
			    if ($post_thumbnail_alt) {
				    echo '<meta name="twitter:image" content="' . esc_url($post_thumbnail) . '" alt="' . esc_attr($post_thumbnail_alt) . '">';
			    } else {
				    echo '<meta name="twitter:image" content="' . esc_url($post_thumbnail) . '">';
          }     
        }

        // standard open graph meta tags
        echo '<meta property="og:title" content="' . esc_attr($post_title) . '">';
        echo '<meta property="og:description" content="' . esc_attr($post_excerpt) . '">';
        echo '<meta property="og:url" content="' . esc_url($post_permalink) . '">';

        if ($post_thumbnail) {
            if ($post_thumbnail_alt) {
      				echo '<meta name="og:image" content="' . esc_url($post_thumbnail) . '" alt="' . esc_attr($post_thumbnail_alt) . '">';
      			} else {
      				echo '<meta name="og:image" content="' . esc_url($post_thumbnail) . '">';
      			}
        }
    }
}
// fire this action when the wordpress head is called, to insert this code into the heading of every page
add_action('wp_head', 'generate_social_link_preview');
?>
