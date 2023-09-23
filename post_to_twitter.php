<?php
// depends on the TwitterOAuth library which can be found at https://github.com/abraham/twitteroauth
require ABSPATH . 'vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

// only show this to logged in users who have permission to edit posts
if (current_user_can('edit_posts')) {

    // Set up Twitter API credentials - REPLACE THESE WITH YOUR OWN VALUES
		$consumer_key = '';
		$consumer_secret = '';
		$access_token = '';
		$access_token_secret = '';
  
	  // Get the stuff we wish to share

  	if (isset($_GET['twitterpostid'])) {
    		$post_id = $_GET['twitterpostid'];
	
    		echo "<!--- Post id from GET param is " . $post_id . "--->";
    
    		$post_title = get_the_title($post_id);
    		$post_content = get_post_field('post_content', $post_id);
    		$post_excerpt = '';
    		$post_thumbnail = '';
    		$post_thumbnail_alt = '';
    
    		//echo "post content is " . $post_content;
    		
    		// replace excerpt with Limerick
    		preg_match('/Limerick<\/h3>(.*?)<p><em>/s', $post_content, $matches);
    
    		// If the h3 are found the excerpt will be the limerick
    		if ($matches) {
    			// Extract the content between the h3 tags
    			$post_excerpt = $matches[1];
    		}

    		// replace <br> tags with newline character and then strip remaining
    		$post_excerpt = str_ireplace('<br>',"\n",$post_excerpt);
    		$post_excerpt = strip_tags($post_excerpt);
    		
    		// get the image and alt text
    		preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].+alt=[\"](?P<alt>.+?)[\"].+>/i', $post_content, $matches);
    		if (isset($matches['src'])) {
    			$post_thumbnail = $matches['src'];
    		}
    		if (isset($matches['alt'])) {
    			$post_thumbnail_alt = $matches['alt'];
    		}
    
    		// thumbnail image URL must be a local file for the TwitterOAuth library to work, so replace
    		preg_match('/onebread.co.uk\/(?P<imagepath>.+)$/i', $post_thumbnail, $matches);
    		if (isset($matches['imagepath'])) {
    			$post_thumbnail = ABSPATH . $matches['imagepath'];
    		}

    		// Create TwitterOAuth object...')
    		$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    		
    		$content = $connection->get("account/verify_credentials");
    		
    		var_dump($content);
    		echo "<br>";
    		echo $connection->getLastHttpCode();
    		echo "<br>";
    		//echo $connection->getLastBody();
    		
    		// Define tweet text and image URL
    		$tweet_text = $post_title . " summarised by OpenAI (text-davinci-003 and dall-e) as: \n\n" . $post_excerpt;
    		$image_url = $post_thumbnail;

    		echo "<br>Post tweet text is " . $tweet_text;
    		echo "<br>image url is " . $image_url;
    		echo "<br>Tweet text length is " . strlen($tweet_text);
    		
    		// Upload image to Twitter
    		$media = $connection->upload('media/upload', ['media' => $image_url]);
    		//echo $connection->getLastHttpCode();
    		//echo $connection->getLastBody();

    		echo "<br>Media id string is " . $media->media_id_string;
    		
    		// Add in the alt text to the image
    		$metadata_payload = [
    			'media_id' => $media->media_id_string,
    			'alt_text' => [
    				'text' => $post_thumbnail_alt,
    			],
    		];
    		$result = $connection->post('media/metadata/create', $metadata_payload, true);
    
    		echo "<br>" . $connection->getLastHttpCode();
    		echo "<br>";
    		var_dump($connection->getLastBody());
	
    		// set ApiVersion to be 2 for the posting of the tweet (no v2 for media... yet)
    		$connection->setApiVersion('2');
    		
    		// Post tweet with text and attached image
    		$parameters = [
    		  'text' => $tweet_text,
    		  'media' => [
    			  'media_ids' => [
    				  $media->media_id_string
    				  ]
    			  ]
    		];
    		$result = $connection->post('tweets', $parameters, true);

    		echo "<br>";
    		echo $connection->getLastHttpCode();
    		echo "<br>";
    		
    		// Check for errors
    		if ($connection->getLastHttpCode() == 200 || $connection->getLastHttpCode() == 201) {
    		  echo "Tweet posted successfully!" . var_dump($result);
    		} else {
    		  echo "Error posting tweet: " . var_dump($result);
    		}
    		
    		echo "<br>";
    		echo "<br>";
	  }
}
?>
