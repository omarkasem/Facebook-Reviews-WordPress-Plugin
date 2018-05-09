<div class="col-md-12">

<div class="step">
  <h3>Step 1</h3>

<form class="form-horizontal" method="post" action="options.php">
  <?php
    settings_fields( 'ufr_facebook_group' );
    do_settings_sections( 'ufr_facebook_group' );
  ?>

  <div class="form-group">
    <label for="ufr_app_id" class="col-sm-2 control-label">
      <?php _e('Facebook App ID',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
      <input type="text" class="form-control" name="ufr_app_id" value="<?php echo get_option('ufr_app_id'); ?>">
      <p class="description"><a href="https://developers.facebook.com/apps">Create your application from here</a></p>
    </div>
  </div>
  
  <div class="form-group">
    <label for="ufr_app_secret" class="col-sm-2 control-label">
      <?php _e('Facebook App Secret',$this->plugin_name); ?>
    </label>
    <div class="col-sm-6">
      <input type="text" class="form-control" name="ufr_app_secret" value="<?php echo get_option('ufr_app_secret'); ?>">
      <p class="description"><a href="https://developers.facebook.com/apps">Create your application from here</a></p>
    </div>
  </div>
  <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Save Changes">
</form>
</div>




<div class="step other">
  <h3>Step 2</h3>
  <?php if($this->fb_creds()){ ?>
    <button type="submit" class="ufr-fb-login" onClick="logInWithFacebook()"><img src="<?php echo plugin_dir_url(dirname(__DIR__)).'admin/images/' ?>fb-sign.png" alt="fb-login"></button>
    <script>
      logInWithFacebook = function() {
          FB.login(function(response) {
              if (response.authResponse) {
                  var url = window.location.href;
                  window.location.replace(url+"&logged_in=true");
              }
          }, {scope: 'manage_pages'});
          return false;
      };
      window.fbAsyncInit = function() {
          FB.init({
              appId: <?php echo get_option('ufr_app_id') ?>,
              cookie: true,
              version: 'v2.11'
          });
      };

      (function(d, s, id){
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) {return;}
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/sdk.js";
          fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
  <?php }else{ ?>
    <h4>Fill the facebook application ID and Secret.</h4>
  <?php } ?>

    <?php if(isset($_GET['logged_in']) && $_GET['logged_in'] == true){
      $this->login_with_fb();
     }
    ?>
  </div>


  <div class="step other">
    <h3>Error in Step 2 ?</h3>
      <ol>
        <li><a target="_blank" href="https://developers.facebook.com/apps/<?php echo get_option('ufr_app_id') ?>/fb-login/">Click Here</a></li>
        <li>Add your site URL in the field 'Valid OAuth redirect URIs' Then Click 'Save Settings'.</li>
        <li>Try 'Step 2' Again.</li>
      </ol>
  </div>


    <div class="step other">
      <h3>Loaded Pages</h3>
      <?php if( $this->fb_creds() && !empty(get_option('ufr_fb_pages')) && is_array(get_option('ufr_fb_pages'))){ ?>
          <ul class="list-group">
            <?php foreach(get_option('ufr_fb_pages') as $page){
              echo '<li class="list-group-item">'.$page['name'].'</li>';
            } ?>
          </ul>
      <?php }else{ ?>
        <h4>Fill the facebook application ID and Secret.</h4>
      <?php } ?>
    </div>

</div>
