<?php
/*
Plugin Name: Tuit Cycler
Plugin URI: http://rodbuaiz.com
Description: Display x number of tweets in a rotating fashion
Version: 1.0.0
Author: brasofilo
Author URI: http://rodbuaiz.com
*/

class TuitCyclerWidget extends WP_Widget {
	protected static $did_script = false;
	var $urlpath = '';
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->urlpath = plugins_url('', __FILE__);	
		
		parent::__construct(
	 		'tuit_cycler_widget', // Base ID
			'Tweet Cycler', // Name
			array( 'description' => __( 'Cycle tweets one at a time', 'tcwidget' ) ) // Args
		);

		add_action('wp_enqueue_scripts', array($this, 'scripts'));
	}
	
	/**
	 * From the question: How to enqueue script if widget is displayed on page?
	 * Answer from: One Trick Pony
	 * Url: http://wordpress.stackexchange.com/a/48385/12615
	 */
	function scripts(){
	    if(!self::$did_script && is_active_widget(false, false, $this->id_base, true)){
		  add_action( 'wp_print_styles', array($this, 'tuit_print_stylesheet') );
		  wp_enqueue_script('jquery');
		  wp_register_script('tuit-cycler-js', $this->urlpath . '/js/jquery.cycle.all.js');
		  wp_register_script('tuit-easing-js', $this->urlpath . '/js/jquery.easing.1.3.js');
		  wp_register_script('tuit-widget-js', $this->urlpath . '/js/tuit-widget.js');
	      wp_enqueue_script('tuit-cycler-js');
	      wp_enqueue_script('tuit-easing-js');
	      wp_enqueue_script('tuit-widget-js');
	      self::$did_script = true;
	    }           
	  }


	/**
	 * Print Widget stylesheet in the <head>
	 * Too small to deserve a file
	 */
	function tuit_print_stylesheet() { ?>
	    <style type="text/css">
	    #tuit-show div { background-color:#ffffff; display:none; }
		#tuit-show div.first { display: block }
	    </style>
	    <?php
	}
	
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$get_tuit = array();
		$get_tuit['from'] = apply_filters( 'widget_title', $instance['tuit_user'] );
		$get_tuit['number'] = apply_filters( 'widget_title', $instance['tuit_nums'] );
		$the_tuits = $this->ba_tweets_by_hashtag_9867($get_tuit);
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo $the_tuits;
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['tuit_user'] = strip_tags( $new_instance['tuit_user'] );
		$instance['tuit_nums'] = strip_tags( $new_instance['tuit_nums'] );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : '';
		$tuit_user = ( isset( $instance[ 'tuit_user' ] ) ) ? $instance[ 'tuit_user' ] : '';
		$tuit_nums = ( isset( $instance[ 'tuit_nums' ] ) ) ? $instance[ 'tuit_nums' ] : '';
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'tuit_user' ); ?>"><?php _e( 'Twitter user name:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'tuit_user' ); ?>" name="<?php echo $this->get_field_name( 'tuit_user' ); ?>" type="text" value="<?php echo esc_attr( $tuit_user ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'tuit_nums' ); ?>"><?php _e( 'Number of tweets:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'tuit_nums' ); ?>" name="<?php echo $this->get_field_name( 'tuit_nums' ); ?>" type="text" value="<?php echo esc_attr( $tuit_nums ); ?>" />
		</p>
		<?php 
	}
	
	/*
	Plugin Name: Twitter Hash Tag Shortcode
	Plugin URI: http://en.bainternet.info
	Description: Small adaptation from bainternet's plugin. Hashtag converted to User. Corrected $image
	Version: 0.3
	Author: bainternet
	Author URI: http://www.bainternet.info
	*/
	private function ba_tweets_by_hashtag_9867($atts, $content = null){
         extract(shortcode_atts(array(
                "from" => 'default_user_name',
                "number" => 5,
                ), $atts));
        $api_url = 'http://search.twitter.com/search.json';
        $raw_response = wp_remote_get("$api_url?q=from%3A$from&rpp=$number");

        if ( is_wp_error($raw_response) ) {
            $output = "<p>Failed to update from Twitter!</p>\n";
            $output .= "<!--{$raw_response->errors['http_request_failed'][0]}-->\n";
            //$output .= get_option('twitter_hash_tag_cache');
        } else {
            if ( function_exists('json_decode') ) {
                $response = get_object_vars(json_decode($raw_response['body']));
                for ( $i=0; $i < count($response['results']); $i++ ) {
                    $response['results'][$i] = get_object_vars($response['results'][$i]);
                }
            } else {
                include(ABSPATH . WPINC . '/js/tinymce/plugins/spellchecker/classes/utils/JSON.php');
                $json = new Moxiecode_JSON();
                $response = @$json->decode($raw_response['body']);
            }
			
			$counter = 1;
            $output = "<div id='tuit-show' class='twitter-hash-tag'>\n";
            foreach ( $response['results'] as $result ) {
                $text = $result['text'];
                $user = $result['from_user'];
                $image = $result['profile_image_url'];
                $user_url = "http://twitter.com/$user";
                $source_url = "$user_url/status/{$result['id']}";

                $text = preg_replace('|(https?://[^\ ]+)|', '<a href="$1">$1</a>', $text);
                $text = preg_replace('|@(\w+)|', '<a href="http://twitter.com/$1">@$1</a>', $text);
                $text = preg_replace('|#(\w+)|', '<a href="http://search.twitter.com/search?q=%23$1">#$1</a>', $text);
				
                // Will hide all divs but the first
                if($counter == 1) {
                	$class = ' class="tuit-item first'.$counter.'">';
                } else {
                	$counter++;
                	$class = ' class="tuit-item">';
                }
                $output .= "<div".$class;

                if ( isset($image) ) $output .= "<a href='$user_url'><img src='$image' alt='$user' /></a>";
                $output .= "<a href='$user_url'>$user</a>: $text <a href='$source_url'>&raquo;</a></div>\n";
            }
            $output .= "</div>\n";
            //$output .= "<div class='view-all'><a href='http://search.twitter.com/search?q=%23$hashtag'>" . __('View All') . "</a></div>\n";
        }

        return $output;
	}
}

function monkeyman_tuit_cycler_widgets_style() {
    echo <<<EOF
<style type="text/css">
div.widget[id*=_tuit_cycler_widget] .widget-title {
    color: #2191bf;
}
</style>
EOF;
}

add_action('admin_print_styles-widgets.php', 'monkeyman_tuit_cycler_widgets_style');
add_action( 'widgets_init', create_function('', 'return register_widget("TuitCyclerWidget");') );