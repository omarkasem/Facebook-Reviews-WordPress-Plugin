<div class="col-md-7">
<form method="post" class="form-horizontal" action="options.php" id="ufr_shortcode_form">
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
		<input type="number" class="ufr_fb_number form-control" value="3" min="1">
    </div>
  </div>


  <div class="form-group">
    <label for="ufr_fb_minimum" class="col-sm-4 control-label">
      <?php _e('Minimum Review Rating',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<select name="ufr_fb_minimum" class="ufr_fb_minimum form-control">
			<option value="">No Filter</option>
			<?php $stars = array(5,4,3,2);
					foreach($stars as $star){
			 ?>
			<option value="<?php echo $star; ?>"><?php echo $star; ?> Stars</option>
			<?php } ?>
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


  <div class="form-group">
    <label for="ufr_fb_version" class="col-sm-4 control-label">
      <?php _e('Regular or Slider ?',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<select name="ufr_fb_version" class="ufr_fb_version form-control">
			<option value="1">Regular</option>
			<option value="2">Slider</option>
		</select>
    </div>
  </div>


    <div class="form-group regular_review">
    <label for="ufr_fb_columns" class="col-sm-4 control-label">
      <?php _e('Columns',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<select name="ufr_fb_columns" class="ufr_fb_columns form-control">
			<option value="12">1 Column</option>
			<option value="6">2 Columns</option>
			<option value="4">3 Columns</option>
			<option value="3">4 Columns</option>
		</select>
    </div>
  </div>


    <div class="form-group slider_review" style="display:none;">
    <label for="ufr_fb_slides_to_show" class="col-sm-4 control-label">
      <?php _e('Slides to Show',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<select name="ufr_fb_slides_to_show" class="ufr_fb_slides_to_show form-control">
			<option value="1">1 Column</option>
			<option value="2">2 Columns</option>
			<option value="3">3 Columns</option>
			<option value="4">4 Columns</option>
		</select>
    </div>
  </div>



  <div class="form-group">
    <label for="ufr_fb_main_color" class="col-sm-4 control-label">
      <?php _e('Main Color',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
		<input type="text" name="ufr_fb_main_color" class="jscolor ufr_fb_main_color" value="3b5998">
    </div>
  </div>



	<input type="submit" id="generate_button" class="btn btn-primary" value="<?php _e('Generate Shortcode',$this->plugin_name); ?>">

</form>
</div>


<div class="col-md-5">
	<div class="panel panel-default main_panel">
	  <div class="panel-body">
		<h3>Generated Shortcodes</h3>
		<div id="ufr_shortcode_div" class="shortcodes_urls_div">
			<?php
				if(isset($_POST['reset_shortcodes']) && check_admin_referer( 'ufr_reset_shortcodes_nonce') && current_user_can('manage_options')){
					delete_option('ufr_shortcodes');
				}
				$shortcodes = get_option('ufr_shortcodes');
				$output = "";
				if($shortcodes != "" && is_array($shortcodes)){
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

					?>
				<?php } ?>
		</div>


	<?php if($shortcodes != "" && is_array($shortcodes)){ ?>
	<form action="" method="post">
		<?php wp_nonce_field( 'ufr_reset_shortcodes_nonce'); ?>
		<input type="submit" class="btn btn-danger" value="<?php _e('Reset Shortcodes',$this->plugin_name); ?>" name="reset_shortcodes">
	</form>
	<?php } ?>


	  </div>
	</div>
</div>