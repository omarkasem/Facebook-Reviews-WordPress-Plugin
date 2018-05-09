<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       omark.me
 * @since      1.0.0
 *
 * @package    Ultimate_Facebook_Reviews
 * @subpackage Ultimate_Facebook_Reviews/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ultimate_Facebook_Reviews
 * @subpackage Ultimate_Facebook_Reviews/public
 * @author     Omar Kasem <omar.kasem207@gmail.com>
 */
class Ultimate_Facebook_Reviews_Public extends WP_Widget {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name = '', $version = '' ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('rest_api_init', array($this,'register_rest_route'));

        parent::__construct(
            'ufr_widget',
            __('Ultimate Facebook Reviews', $this->plugin_name),
            array('description' => __('Ultimate Facebook Reviews Widget', $this->plugin_name),)
        );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ultimate_Facebook_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ultimate_Facebook_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_style('ufr_slick' . $this->plugin_name, plugin_dir_url(__FILE__) . 'css/slick.css', array(), $this->version, 'all');
        wp_enqueue_style('ufr_slick_theme' . $this->plugin_name, plugin_dir_url(__FILE__) . 'css/slick-theme.css', array(), $this->version, 'all');

		
		wp_enqueue_style( 'ufr_fontawesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ultimate-facebook-reviews-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ultimate_Facebook_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ultimate_Facebook_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_script('ufr_slick_carousel' . $this->plugin_name, plugin_dir_url(__FILE__) . 'js/slick.min.js', array('jquery'), $this->version, false);

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ultimate-facebook-reviews-public.js', array( 'jquery' ), $this->version, false );
	}

	private function curl_it($url){
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		$url = curl_exec($curl);
		curl_close($curl);
		return $url;
	}


	public function fb_sdk(){
		if(get_option('ufr_app_id') != '' && get_option('ufr_app_secret')){
			$fb = new \Facebook\Facebook([
			  'app_id' => get_option('ufr_app_id'),
			  'app_secret' => get_option('ufr_app_secret'),
			  'default_graph_version' => 'v2.11',
			]);
			return $fb;
		}
	}

	public function fb_creds(){
		if(get_option('ufr_app_id') && get_option('ufr_app_secret')){
			return true;
		}else{
			return false;
		}
	}

	public function get_array_key($array,$id){
		foreach($array as $key => $val) {
		   if ($val['id'] == $id) {
		       return $key;
		   }
		}
		return null;
	}

	public function get_fb_page_access_token($page_id){
		if(is_array(get_option('ufr_fb_pages')) && !empty(get_option('ufr_fb_pages'))){
			$key = $this->get_array_key(get_option('ufr_fb_pages'),$page_id);
			return get_option('ufr_fb_pages')[$key]['access_token'];
		}
	}

	public function get_fb_reviews($page_id){
		$page_access_token = $this->get_fb_page_access_token($page_id);
		if($page_access_token != '' || $page_id != ''){
			try {
			  $response = $this->fb_sdk()->get(
			    "/$page_id/ratings",
			    "$page_access_token"
			  );
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
			  echo 'Graph returned an error: ' . $e->getMessage();
			  exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
			  echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  exit;
			}
			$body = $response->getDecodedBody();
			return $body['data'];
		}else{
			return 'User access token or Page ID not found';
		}
	}



	private function fb_shortcode_attributes($atts,$id){
        $cached_reviews = $this->get_fb_reviews($atts['page']);
 		if($atts['minimum'] != ''){
 			$cached_reviews = $this->fb_minimum_reviews($cached_reviews,$atts['minimum']);
 		}

 		if($atts['hide_blank'] == 1){
 			$cached_reviews = $this->fb_hide_blank_reviews($cached_reviews);
 		}
 		if($atts['number'] != ''){
 			$cached_reviews = array_slice($cached_reviews, 0, $atts['number']);
 		}
	    return $cached_reviews;
	}


    public function fb_shortcode($atts)
    {
    	$shortcodes = get_option('ufr_shortcodes');
    	$id = intval($atts['id']);
    	$array = $shortcodes["ID_".$id];
        extract(shortcode_atts($array, $atts));
        
        if($atts['id'] != '' && !empty($shortcodes) && is_array($shortcodes)){
        	$atts = $array;
        	$cached_reviews = $this->fb_shortcode_attributes($atts,$id);
 			return $this->fb_html_shortcode($cached_reviews,$atts,$id);
 		}else{
 			return 'ID IS REQUIRED !';
 		}
    }

    private function fb_html_shortcode($reviews,$atts,$shortcode_id='',$widget_id=''){
    	if($shortcode_id != ''){
	    	echo "<style>";
	    	echo "
				.ufr_slider_s".$shortcode_id." .slick-next:before,.ufr_slider_s".$shortcode_id." .ufr-style2 .star-rating i,.ufr_slider_s".$shortcode_id." .ufr-style2 i.quote{
				  color: #".$atts['color'].";
				  border-top-color: #".$atts['color'].";
				}
	    	";
	    	echo "</style>";
	    	if($atts['version'] == 1){
		    	$output = '<div class="ufr-reviews ufr-row ufr_slider_s'.$shortcode_id.'">';
		    	if(is_array($reviews) && !empty($reviews)){
		    		foreach($reviews as $review){
		    			$output .= '<div itemprop="review" itemscope itemtype="http://schema.org/Review" class="ufr-review ufr-col-md-'.intval($atts['columns']).'">';
		    			$output .= $this->fb_style2($review,$atts);
		    			$output .= "</div>";
		    		}
		    	}
		    	$output .= '</div>';
		    }else{
		    	$slider_columns = intval($atts['slides']);
		    	$output = '<script>';
		    	$output .= 'jQuery(function($) {
								"use strict";
								$(".ufr_slider_s'.$shortcode_id.'").slick({
								  infinite: true,
								  dots:true,
								  arrows: false,
								  autoplay: true,
								  autoplaySpeed: 5000,
								  slidesToShow: '.$slider_columns.',
								});
							});';
		    	$output .= '</script>';
		    	$output .= '<div class="ufr-row reviews ufr_slider_s'.$shortcode_id.'">';

		    	if(is_array($reviews) && !empty($reviews)){
		    		foreach($reviews as $review){
		    			$output .= '<div itemprop="review" itemscope itemtype="http://schema.org/Review" class="ufr-review ufr-col-md-12">';
		    			$output .= $this->fb_style2($review,$atts);
		    			$output .= "</div>";
		    		}
		    	}
		    	$output .= "</div>";
		    }
    	}else{
	    	echo "<style>";
	    	echo "
				.ufr_slider_w".$widget_id." .slick-next:before,.ufr_slider_w".$widget_id." .ufr-style2 .star-rating i,.ufr_slider_w".$widget_id." .ufr-style2 i.quote{
				  color: #".$atts['color'].";
				  border-top-color: #".$atts['color'].";
				}
	    	";
	    	echo "</style>";
	    	if($atts['version'] == 1){
		    	$output = '<div class="ufr-reviews ufr-row ufr_slider_w'.$widget_id.'">';
		    	if(is_array($reviews) && !empty($reviews)){
		    		foreach($reviews as $review){
		    			$output .= '<div itemprop="review" itemscope itemtype="http://schema.org/Review" class="ufr-review ufr-col-md-'.intval($atts['columns']).'">';
		    			$output .= $this->fb_style2($review,$atts);
		    			$output .= "</div>";
		    		}
		    	}
		    	$output .= '</div>';
		    }else{
		    	$slider_columns = intval($atts['slides']);
		    	$output = '<script>';
		    	$output .= 'jQuery(function($) {
								"use strict";
								$(".ufr_slider_w'.$widget_id.'").slick({
								  infinite: true,
								  dots:true,
								  arrows: true,
								  autoplay: true,
								  autoplaySpeed: 5000,
								  slidesToShow: '.$slider_columns.',
								});
							});';
		    	$output .= '</script>';
		    	$output .= '<div class="ufr-row reviews ufr_slider_w'.$widget_id.'">';

		    	if(is_array($reviews) && !empty($reviews)){
		    		foreach($reviews as $review){
		    			$output .= '<div itemprop="review" itemscope itemtype="http://schema.org/Review" class="ufr-review ufr-col-md-12">';
		    			$output .= $this->fb_style2($review,$atts);
		    			$output .= "</div>";
		    		}
		    	}
		    	$output .= "</div>";
		    }
    	}

		echo $output;
    }


    private function fb_style2($review,$atts){
		$output ='<div class="ufr-style2">';

		$output .= '<span style="display:none;" itemprop="itemReviewed">'.get_bloginfo('name').'</span>';

		$output .= '<div class="star-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><span style="display:none;" itemprop="ratingValue">'.$review['rating'].'</span>'.$this->fb_html_stars($review['rating']).'</div>';

		if(array_key_exists('review_text',$review)){
			$output .= '<p itemprop="description">'.$review['review_text'].'</p>';
		}
		$output .='<i itemscope itemtype="http://schema.org/Person" class="fa fa-quote-left quote" aria-hidden="true"></i>
					<strong itemprop="author">'.$review['reviewer']['name'].'</strong>
		';
		$output .= '</div>';
		return $output;
    }


    private function fb_html_stars($rating){
    	$rating = intval($rating);
    	$output = '';
    	if($rating == 5){
    		$output = '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>';
    	}elseif($rating == 4){
    		$output = '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>';
    	}elseif($rating == 3){
    		$output = '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>';
    	}elseif($rating == 2){
    		$output = '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>';
    	}elseif($rating == 1){
    		$output = '<i class="fa fa-star" aria-hidden="true"></i>';
    	}
    	return $output;
    }


    private function fb_minimum_reviews($reviews,$minimum){
    	$new = array();
    	if(is_array($reviews) && !empty($reviews)){
    		foreach($reviews as $review){
	    		if($review['rating'] >= $minimum){
	    			$new[] = $review;
	    		}
    		}
    	}
    	return $new;
    }

    private function fb_hide_blank_reviews($reviews){
    	$new = array();
    	if(is_array($reviews) && !empty($reviews)){
    		foreach($reviews as $review){
	    		if(array_key_exists('review_text',$review)){
	    			$new[] = $review;
	    		}
    		}
    	}
    	return $new;
    }


    public function get_reviews_by_wp_rest($atts){
    	$page = intval($atts['page']);
    	if(!in_array($page,$this->fb_get_pages_list_ids())){
    		return 'Page ID is wrong';
    		exit;
    	}
    	$number = intval($atts['number']);
    	$minimum = '';
    	$hide_blank = '';

    	if(isset($_GET['minimum'])){
    		$minimum = intval($_GET['minimum']);
    	}
    	if(isset($_GET['hide_blank'])){
    		$hide_blank = sanitize_text_field($_GET['hide_blank']);
    	}
    	
        $cached_reviews = $this->get_fb_reviews($page);
 		if($minimum != ''){
 			$cached_reviews = $this->fb_minimum_reviews($cached_reviews,$minimum);
 		}

 		if(intval($hide_blank) == 1){
 			$cached_reviews = $this->fb_hide_blank_reviews($cached_reviews);
 		}

 		if($number != ''){
 			$cached_reviews = array_slice($cached_reviews, 0, $number);
 		}
		return $this->generate_json_array_for_url($cached_reviews,$atts);
    }


    private function fb_get_date($date){
    	return substr($date, 0, strpos($date, "T"));
    }

    public function generate_json_array_for_url($reviews,$data){
    	$array = array();
    	if(!empty($reviews) && is_array($reviews)){
    		foreach($reviews as $review){
    			if(array_key_exists('review_text',$review)){
    				$review_text = $review['review_text'];
    			}else{
    				$review_text = null;
    			}
                $array[] = (object)array(
                    'page' => $this->fb_get_page_name_by_id($data['page']),
                    'name' => $review['reviewer']['name'],
                    'id'=>$review['reviewer']['id'],
                    'review_rating' =>$review['rating'],
                    'review_text'=>$review_text,
                    'date' => $this->fb_get_date($review['created_time']),
                ); 
    		}
    		return $array;
    	}
    }

	public function fb_get_page_name_by_id($page_id){
		if(is_array(get_option('ufr_fb_pages')) && !empty(get_option('ufr_fb_pages'))){
			$key = $this->get_array_key(get_option('ufr_fb_pages'),$page_id);
			return get_option('ufr_fb_pages')[$key]['name'];
		}
	}

	public function fb_get_pages_list_ids(){
		$all_pages_ids =array();
		if(is_array(get_option('ufr_fb_pages')) && !empty(get_option('ufr_fb_pages'))){
			foreach(get_option('ufr_fb_pages') as $page){
				$all_pages_ids[] = $page['id'];
			}
		}
		return $all_pages_ids;
	}


    public function register_rest_route(){
        register_rest_route('ufr', '/fb_reviews/(?P<page>[0-9]+)/(?P<number>[0-9]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_reviews_by_wp_rest'),
        ));
    }


	public function fb_get_pages_list(){
		$list = 'https://graph.facebook.com/v2.9/me/accounts?access_token='.get_option('ufr_user_access_token');
		$list = $this->curl_it($list);
		$list = json_decode($list);
		return $list->data;
	}



    // Widgets
    public function widget($args, $instance){
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];
        if (!empty($title)){
            echo $args['before_title'] . $title . $args['after_title'];
        }
    	$cached_reviews = $this->fb_shortcode_attributes($instance,'');
        $widget_id = preg_replace('/[^0-9]/', '', $args['widget_id']);
        $widget_id = intval($widget_id);
    	echo $this->fb_html_shortcode($cached_reviews,$instance,'',$widget_id);

        echo $args['after_widget'];
    }

    public function form($instance){
    	$title = isset($instance['title']) ? $instance['title'] : '';
    	$page = isset($instance['page']) ? $instance['page'] : '';
    	$number = isset($instance['number']) ? $instance['number'] : '';
    	$minimum = isset($instance['minimum']) ? $instance['minimum'] : '';
    	$hide_blank = isset($instance['hide_blank']) ? $instance['hide_blank'] : 0;
    	$version = isset($instance['version']) ? $instance['version'] : '';
		$columns = isset($instance['columns']) ? $instance['columns'] : '';
    	$slides = isset($instance['slides']) ? $instance['slides'] : 0;
    	$color = isset($instance['color']) ? $instance['color'] : '';
    	?>
    	<div class="ufr-widget-div">
    		<div class="ufr-review-tab-div">
	    		<a href="" class="ufr-review-tab active">Review Options</a>
	    		<a href="" class="ufr-review-tab">Review Styles</a>
    		</div>
    		<div class="review-options">
	            <p>
	                <label
	                    for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', $this->plugin_name); ?></label>
	                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
	                       name="<?php echo $this->get_field_name('title'); ?>" type="text"
	                       value="<?php echo esc_attr($title); ?>"/>
	            </p>
				<?php
					$pages = array();
					if( $this->fb_creds() && !empty(get_option('ufr_fb_pages')) && is_array(get_option('ufr_fb_pages'))){
						$pages = get_option('ufr_fb_pages');
					}else{
						echo '<h3>Please Login with your account first from the access tokens tab.</h3>';
					}
				?>
				<p>
					<label for="<?php echo $this->get_field_id('page'); ?>">
					  <?php _e('List of Pages',$this->plugin_name); ?>
					</label>
					<select name="<?php echo $this->get_field_name('page'); ?>" class="widefat" id="<?php echo $this->get_field_id('page'); ?>">
						<option value="">Choose a Page</option>
						<?php if(!empty($pages) && is_array($pages)){ ?>
							<?php foreach($pages as $page2){ ?>
								<option <?php if($page == $page2['id']){echo 'selected';} ?> value="<?php echo $page2['id']; ?>"><?php echo $page2['name']; ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id('number'); ?>">
					  <?php _e('Number of Reveiws',$this->plugin_name); ?>
					</label>
					<input type="number" name="<?php echo $this->get_field_name('number'); ?>" id="<?php echo $this->get_field_id('number'); ?>" class="widefat" value="<?php if($number == '' ){echo 5;}else{echo $number;} ?>" min="1">
				</p>

				<p>
					<label for="<?php echo $this->get_field_id('minimum'); ?>">
					  <?php _e('Minimum Review Rating',$this->plugin_name); ?>
					</label>
					<select name="<?php echo $this->get_field_name('minimum'); ?>" class="widefat" id="<?php echo $this->get_field_id('minimum'); ?>">
						<option value="">No Filter</option>
						<?php $stars = array(5,4,3,2);
								foreach($stars as $star){
						 ?>
						<option <?php if($minimum == $star){echo 'selected';} ?> value="<?php echo $star; ?>"><?php echo $star; ?> Stars</option>
						<?php } ?>
					</select>
				</p>


				<p>
					<label style="display:block;" for="<?php echo $this->get_field_id('hide_blank'); ?>">
					  <?php _e('Hide Blank Reviews',$this->plugin_name); ?>
					</label>
					<label class="wp_switch">

					  <input type="checkbox" <?php if($hide_blank == 1){echo 'checked="checked"';} ?> name="<?php echo $this->get_field_name('hide_blank'); ?>" id="<?php echo $this->get_field_id('hide_blank'); ?>" value="<?php if($hide_blank == ''){echo 1;}else{echo $hide_blank;} ?>">
					  <span class="wp_slider round"></span>
					</label>
				</p>

			</div>
			
			<div class="review-style" style="display: none;">

			<p>
				<label for="<?php echo $this->get_field_id('version'); ?>">
				  <?php _e('Regular or Slider ?',$this->plugin_name); ?>
				</label>
				<select name="<?php echo $this->get_field_name('version'); ?>" id="<?php echo $this->get_field_id('version'); ?>" class="widefat ufr_fb_version_widget">
					<option <?php if($version == 1){echo 'selected';} ?> value="1">Regular</option>
					<option <?php if($version == 2){echo 'selected';} ?> value="2">Slider</option>
				</select>
			</p>

			<p class="regular_review" <?php if($version == 2){echo 'style="display:none;"';} ?>>
				<label for="<?php echo $this->get_field_id('columns'); ?>">
				  <?php _e('Columns',$this->plugin_name); ?>
				</label>
				<select name="<?php echo $this->get_field_name('columns'); ?>" class="widefat" id="<?php echo $this->get_field_id('columns'); ?>">
					<?php $all_columns = array('12'=>1,'6'=>2,'4'=>3,'3'=>4);
							foreach($all_columns as $key => $value){
					 ?>
					<option <?php if($key == $columns){echo 'selected';} ?> value="<?php echo $key; ?>"><?php echo $value; ?> Columns</option>
					<?php } ?>
				</select>
			</p>

			<p class="slider_review" <?php if($version == 1 || $version == ''){echo 'style="display:none;"';} ?>>
				<label for="<?php echo $this->get_field_id('slides'); ?>">
				  <?php _e('Slides to Show',$this->plugin_name); ?>
				</label>
				<select name="<?php echo $this->get_field_name('slides'); ?>" class="widefat" id="<?php echo $this->get_field_id('slides'); ?>">
					<?php $all_columns = array(1,2,3,4);
							foreach($all_columns as $all_column){
					 ?>
					<option <?php if($all_column == $slides){echo 'selected';} ?> value="<?php echo $all_column; ?>"><?php echo $all_column; ?> Columns</option>
					<?php } ?>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('color'); ?>">
				  <?php _e('Main Color',$this->plugin_name); ?>
				</label>
				<input type="text" name="<?php echo $this->get_field_name('color'); ?>" id="<?php echo $this->get_field_id('color'); ?>" class="widefat jscolor" value="<?php echo $color; ?>">
			</p>

		</div>
		</div>

    <?php }

    public function update($new_instance, $old_instance){
    	$instance = array();
    	$instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
    	$instance['page'] = !empty($new_instance['page']) ? intval($new_instance['page']) : '';
    	$instance['number'] = !empty($new_instance['number']) ? intval($new_instance['number']) : '';
    	$instance['minimum'] = !empty($new_instance['minimum']) ? intval($new_instance['minimum']) : '';
    	$instance['hide_blank'] = !empty($new_instance['hide_blank']) ? intval($new_instance['hide_blank']) : 0;
    	$instance['version'] = !empty($new_instance['version']) ? intval($new_instance['version']) : '';
		$instance['columns'] = !empty($new_instance['columns']) ? intval($new_instance['columns']) : '';
    	$instance['slides'] = !empty($new_instance['slides']) ? intval($new_instance['slides']) : 0;
    	$instance['color'] = !empty($new_instance['color']) ? sanitize_text_field($new_instance['color']) : '';
    	return $instance;
    }


}
