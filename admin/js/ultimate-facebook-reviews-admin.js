jQuery(function($) {
	'use strict';
	 // Switch toggle
	 $(document).ajaxSuccess(function() {
		 $('.wp_switch').on('click',function(){
		 	var input = $(this).find('input');
		 	if(input.is(":checked")){
		 		input.val(1);
		 	}else{
		 		input.val(0);
		 	}
		 });


	 $('.ufr_fb_version_widget').change(function(){
	 	var regular_review_widget = $(this).parent().parent().find('.regular_review');
	 	var slider_review_widget = $(this).parent().parent().find('.slider_review');
	 	
	 	if($(this).val() == 1){
	 		regular_review_widget.show();
	 		slider_review_widget.hide();
	 	}else{
	 		regular_review_widget.hide();
	 		slider_review_widget.show();
	 	}
	 });

		 $('.ufr-review-tab').on('click',function(e){
		 	e.preventDefault();
		 	var parent = $(this).parent();
		 	parent.find('.ufr-review-tab').removeClass('active');
		 	$(this).addClass('active');
		 	if($(this).is(':first-child')){
		 		parent.parent().find('.review-options').show();
		 		parent.parent().find('.review-style').hide();
		 	}else{
		 		parent.parent().find('.review-style').show();
		 		parent.parent().find('.review-options').hide();
		 	}
		 });


	 });

	 $('.ufr-review-tab').on('click',function(e){
	 	e.preventDefault();
	 	var parent = $(this).parent();
	 	parent.find('.ufr-review-tab').removeClass('active');
	 	$(this).addClass('active');
	 	if($(this).is(':first-child')){
	 		parent.parent().find('.review-options').show();
	 		parent.parent().find('.review-style').hide();
	 	}else{
	 		parent.parent().find('.review-style').show();
	 		parent.parent().find('.review-options').hide();
	 	}
	 });


	 $('.wp_switch').on('click',function(){
	 	var input = $(this).find('input');
	 	if(input.is(":checked")){
	 		input.val(1);
	 	}else{
	 		input.val(0);
	 	}
	 });


	 $('.ufr_fb_version').change(function(){
	 	var regular_review_short = $('.regular_review');
	 	var slider_review_short = $('.slider_review');

	 	if($(this).val() == 1){
	 		regular_review_short.show();
	 		slider_review_short.hide();
	 	}else{
	 		regular_review_short.hide();
	 		slider_review_short.show();
	 	}
	 });



	 $('.ufr_fb_version_widget').change(function(){
	 	var regular_review_widget = $(this).parent().parent().find('.regular_review');
	 	var slider_review_widget = $(this).parent().parent().find('.slider_review');

	 	if($(this).val() == 1){
	 		regular_review_widget.show();
	 		slider_review_widget.hide();
	 	}else{
	 		regular_review_widget.hide();
	 		slider_review_widget.show();
	 	}
	 });


	 // SHORTCODES
	 $("#ufr_shortcode_form").submit(function(event){
	 	event.preventDefault();
	 	if($('.ufr_fb_pages').val() == ''){
	 		$('.ufr_fb_pages').focus();
	 		return;
	 	}
	 	var ufr_fb_pages = $(".ufr_fb_pages").val();
	 	var ufr_fb_number = $(".ufr_fb_number").val();ufr_fb_hide_blank
	 	var ufr_fb_minimum = $(".ufr_fb_minimum").val();
	 	var ufr_fb_hide_blank = $(".ufr_fb_hide_blank").val();
	 	var ufr_fb_main_color = $(".ufr_fb_main_color").val();
	 	var ufr_fb_columns = $(".ufr_fb_columns").val();
	 	var ufr_fb_version = $(".ufr_fb_version").val();

	 	var shortcode = {
	 		page:ufr_fb_pages,
	 		number:ufr_fb_number,
	 		minimum:ufr_fb_minimum,
            hide_blank:ufr_fb_hide_blank,
            color:ufr_fb_main_color,
            version:ufr_fb_version,
	 	};

	 	if(ufr_fb_version == 1){
			var ufr_fb_columns = $(".ufr_fb_columns").val();
			shortcode.columns = ufr_fb_columns;
		}else{
			var ufr_fb_slides_to_show = $(".ufr_fb_slides_to_show").val();
			shortcode.slides = ufr_fb_slides_to_show;
		}
	 	console.log(shortcode);

		jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: { action: 'ufr_save_shortcodes', shortcode: shortcode},
		success: function(response){
			$("#ufr_shortcode_div").html(response);
		},
		error: function(error){
			console.log("bad");
		}
		});
	 });



	 // URLS
	 $("#ufr_urls_form").submit(function(event){
	 	event.preventDefault();
	 	if($('.ufr_fb_pages').val() == ''){
	 		$('.ufr_fb_pages').focus();
	 		return;
	 	}
	 	var site_url = ufr_ajax_object.site_url;


	 	var ufr_fb_pages = $(".ufr_fb_pages").val();
	 	var ufr_fb_number = $(".ufr_fb_number").val();
	 	var ufr_fb_minimum = $(".ufr_fb_minimum").val();
	 	var ufr_fb_hide_blank = $(".ufr_fb_hide_blank").val();

	 	var url = site_url+'/wp-json/ufr/fb_reviews/'+ufr_fb_pages+'/'+ufr_fb_number+'/?hide_blank='+ufr_fb_hide_blank+'&minimum='+ufr_fb_minimum+'';

		jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: { action: 'ufr_save_urls', url: url},
		success: function(response){
			$("#ufr_urls_div").html(response);
		},
		error: function(error){
			console.log("bad");
		}
		});
	 });


	 // Copy to clipboard
	 $('.shortcodes_urls_div').on('click', '.copy-button', function(e){
	 	e.preventDefault();
	 	$(this).parent().prev().select();
	 	var copied;
		try
		{
		  // Copy the text
		  copied = document.execCommand('copy');
		} 
		catch (ex)
		{
		  copied = false;  
		}

		if(copied)
		{
			$(this).parent().prev().prev().fadeIn(600).fadeOut(600);
		}
	 });

	



});

