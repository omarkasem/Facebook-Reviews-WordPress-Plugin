<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       omark.me
 * @since      1.0.0
 *
 * @package    Ultimate_Facebook_Reviews
 * @subpackage Ultimate_Facebook_Reviews/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ultimate_Facebook_Reviews
 * @subpackage Ultimate_Facebook_Reviews/admin
 * @author     Omar Kasem <omar.kasem207@gmail.com>
 */
class Ultimate_Facebook_Reviews_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( 'ufr-bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.css', array(), $this->version, 'all' );


		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ultimate-facebook-reviews-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
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
		wp_enqueue_script( 'ufr-jscolor', plugin_dir_url( __FILE__ ) . 'js/jscolor.js', array( 'jquery'), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ultimate-facebook-reviews-admin.js', array( 'jquery'), $this->version, false );

	    wp_localize_script( $this->plugin_name, 'ufr_ajax_object',
	        array( 
	            'site_url' => get_site_url(),
	        )
	    );

	}


/********************************************************************************
	Facebook Functions
********************************************************************************/

	// Making the sub menu page for facebook.
	public function ufr_page(){
		add_options_page('Ultimate Facebook Reviews','Ultimate Facebook Reviews','manage_options',$this->plugin_name.'.php',array($this, 'ufr_display'));
	}

	// Making tabs for the facebook page.
	private function ufr_tabs(){
		return array(
			'access-token'=>'Facebook Credentials',
			'shortcodes'=>'Shortcodes',
			'developers'=>'Developers',
		);
	}

	// The display function that's required by the add_submenu_page function, it displays the HTML in the page.
	public function ufr_display(){ ?>
		<div class="bootstrap-iso">
			<div class="container-fluid">
			<div class="panel panel-default main_panel">
			  <div class="panel-body no-pad-bot">
				<div class="row">
					<div class="col-md-8">
						<h3>Ultimate Facebook Reviews</h3>
						<h5>Version <?php echo $this->version; ?></h5>
						<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'access-token'; ?>
						<ul class="nav nav-tabs" role="tablist">
							<?php foreach($this->ufr_tabs() as $key => $value){ ?>
								<li class="<?php echo $active_tab == $key ? 'active' : ''; ?>" role="presentation"><a href="?page=<?php echo $this->plugin_name ?>.php&tab=<?php echo $key ?>"><?php echo _e($value,$this->plugin_name); ?></a></li>
							<?php } ?>
						</ul>
					</div>
					<div class="col-md-4">
						<h3>Other Awesome Plugins !</h3>
						<a target="_blank" href="https://wordpress.org/plugins/ultimate-twitter-feed">
							<img src="<?php echo plugin_dir_url(dirname(__FILE__)).'admin/images/' ?>tweet.jpg">
						</a>
					</div>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-body">
				<?php foreach($this->ufr_tabs() as $key => $value){
					if($active_tab == $key){
						echo '<div class="row">';
						include_once('partials/'.$key.'-display.php');
						echo '</div>';
					}
				} ?>
			  </div>
			</div>



				
			</div>
		</div>
	<?php }

	
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

	public function login_with_fb(){
	  $fb = $this->fb_sdk();
	  $helper = $fb->getJavaScriptHelper();
	  try {
	      $accessToken = $helper->getAccessToken();
	  } catch (Facebook\Exceptions\FacebookResponseException $e) {
	      echo 'Graph returned an error: ' . $e->getMessage();
	      exit;
	  } catch (Facebook\Exceptions\FacebookSDKException $e) {
	      echo 'Facebook SDK returned an error: ' . $e->getMessage();
	      exit;
	  }
	  if (!isset($accessToken)) {
	      echo 'No cookie set or no OAuth data could be obtained from cookie.';
	      exit;
	  }

	  $fb_accessToken = $accessToken->getValue();
	  $oAuth2Client = $fb->getOAuth2Client();
	  try {
	      $longLiveAccessToken = $oAuth2Client->getLongLivedAccessToken($fb_accessToken);
	  } catch (Facebook\Exceptions\FacebookSDKException $e) {
	      echo "Error getting long-lived access token: " . $e->getMessage() . "\n\n";
	      exit;
	  }

	  $parameter = [
	      'access_token' => $longLiveAccessToken->getValue(),
	      'fields' => 'access_token,id,name,perms',
	  ];

	  try {
	      $responses = $fb->get('/me/accounts?' . http_build_query($parameter));
	  } catch (Facebook\Exceptions\FacebookResponseException $e) {
	      echo 'Graph returned an error: ' . $e->getMessage();
	      exit;
	  } catch (Facebook\Exceptions\FacebookSDKException $e) {
	      echo 'Facebook SDK returned an error: ' . $e->getMessage();
	      exit;
	  }

	  $result = $responses->getDecodedBody();
	  if (! $result['data']) {
	      echo "You have no pages!";
	      exit;
	  }

	  $pages = array();
	  foreach($result['data'] AS $item) {
          $pages[] = array(
              'access_token' => $item['access_token'],
              'name' => $item['name'],
              'id'=>$item['id'],
          );
	  }

	  update_option('ufr_fb_pages',$pages);
	}




	// Register Facebook Settings
	public function ufr_register_settings(){
		// Keys and tokens
		register_setting( 'ufr_facebook_group', 'ufr_app_id');
		register_setting( 'ufr_facebook_group', 'ufr_app_secret');
	}


	public function fb_get_pages_list(){
		$list = 'https://graph.facebook.com/v2.9/me/accounts?access_token='.get_option('ufr_user_access_token');
		$list = $this->curl_it($list);
		$list = json_decode($list);
		return $list->data;
	}



	public function ufr_save_shortcodes(){
		$shortcode = $_POST['shortcode'];
		$shortcodes = get_option('ufr_shortcodes');
		if(!empty($shortcodes) && is_array($shortcodes)){
			$count = count($shortcodes);
			$count++;
			$shortcodes['ID_'.$count] = $shortcode;
			update_option('ufr_shortcodes',$shortcodes);
		}else{
			add_option('ufr_shortcodes',array('ID_1'=>$shortcode));
		}
		$shortcodes = get_option('ufr_shortcodes');
		$shortcodes = array_reverse($shortcodes);
		$output = '';
		foreach($shortcodes as $key => $value){
			$key = str_replace("ID_",'',$key);
			$output .= '
			  <div class="input-group">
			  <span class="copied">Copied !</span>
			    <input readonly type="text" class="form-control"
			        value="[UFR_FB id='.$key.']" class="text-to-copy">
			    <span class="input-group-btn">
			      <button class="btn btn-danger copy-button" type="button"
			          data-toggle="tooltip" data-placement="button"
			          title="Copy to Clipboard">
			        Copy
			      </button>
			    </span>
			  </div>';
		}
		echo $output;
		wp_die();
	}


	public function ufr_save_urls(){
		$url = esc_url($_POST['url']);
		$urls = get_option('ufr_urls');
		if($urls != "" && is_array($urls)){
			$urls[] = $url;
			update_option('ufr_urls',$urls);
		}else{
			add_option('ufr_urls',array($url));
		}
		$urls = get_option('ufr_urls');
		$urls = array_reverse($urls);
		foreach($urls as $url){
			$output .= '
			  <div class="input-group">
			  <span class="copied">Copied !</span>
			    <input readonly type="text" class="form-control"
			        value="'.esc_url($url).'" class="text-to-copy">
			    <span class="input-group-btn">
			      <button class="btn btn-danger copy-button" type="button"
			          data-toggle="tooltip" data-placement="button"
			          title="Copy to Clipboard">
			        Copy
			      </button>
			    </span>
			  </div>';
		}
		echo $output;
		wp_die();
	}



	public function load_widget() {
		register_widget( 'Ultimate_Facebook_Reviews_Public' );
	}

}
