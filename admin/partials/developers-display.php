<div class="col-md-7">
<form method="post" class="form-horizontal" action="options.php" id="ufr_urls_form">
	<?php
		$pages = array();
		if( $this->fb_creds() && !empty(get_option('ufr_fb_pages')) && is_array(get_option('ufr_fb_pages'))){
			$pages = get_option('ufr_fb_pages');
		}else{
			echo '<h3>Please Login with your account first from the access tokens tab.</h3>';
		}
	?>

  <div class="form-group">
    <label for="ufr_fb_pages" class="col-sm-4 control-label">
      <?php _e('List of Pages',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<select name="ufr_fb_pages" class="ufr_fb_pages form-control">
			<option value="">Choose a Page</option>
			<?php if(!empty($pages) && is_array($pages)){ ?>
				<?php foreach($pages as $page){ ?>
					<option value="<?php echo $page['id']; ?>"><?php echo $page['name']; ?></option>
				<?php } ?>
			<?php } ?>
		</select>
    </div>
  </div>


  <div class="form-group">
    <label for="ufr_fb_number" class="col-sm-4 control-label">
      <?php _e('Number of Reveiws',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<input type="number" class="ufr_fb_number form-control" value="<?php if(get_option('ufr_fb_number') != ''){echo get_option('ufr_fb_number');}else{echo 5;} ?>">
    </div>
  </div>


  <div class="form-group">
    <label for="ufr_fb_minimum" class="col-sm-4 control-label">
      <?php _e('Minimum Review Rating',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<select name="ufr_fb_minimum" class="ufr_fb_minimum form-control">
			<option value="">No Filter</option>
			<option value="5">5 Stars</option>
			<option value="4">4 Stars</option>
			<option value="3">3 Stars</option>
			<option value="2">2 Stars</option>
		</select>
    </div>
  </div>


  <div class="form-group">
    <label for="ufr_fb_hide_blank" class="col-sm-4 control-label">
      <?php _e('Hide Blank Reviews',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<label class="wp_switch">
		  <input type="checkbox" checked="checked" name="ufr_fb_hide_blank" class="ufr_fb_hide_blank" value="1">
		  <span class="wp_slider round"></span>
		</label>
    </div>
  </div>





	<input type="submit" id="generate_button" class="btn btn-primary" value="<?php _e('Generate URL',$this->plugin_name); ?>">

</form>
</div>


<div class="col-md-5">
	<div class="panel panel-default main_panel">
	  <div class="panel-body">
		<h3>Generated WP REST URLS</h3>
		<div id="ufr_urls_div" class="shortcodes_urls_div">
	<?php
		if(isset($_POST['reset_urls']) && check_admin_referer( 'ufr_reset_urls_nonce') && current_user_can('manage_options')){
			delete_option('ufr_urls');
		}
		$urls = get_option('ufr_urls');
		$output = "";
		if($urls != "" && is_array($urls)){
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
			 ?>
		<?php } ?>
		</div>


	<?php if($urls != "" && is_array($urls)){ ?>
	<form action="" method="post">
		<?php wp_nonce_field( 'ufr_reset_urls_nonce'); ?>
		<input type="submit" class="btn btn-danger" value="<?php _e('Reset URLS',$this->plugin_name); ?>" name="reset_urls">
	</form>
	<?php } ?>


	  </div>
	</div>
</div>


