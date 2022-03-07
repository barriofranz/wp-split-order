(function( $ ) {
	'use strict';


	$(document).on("click", ".gf_splitorder-preview-btn", function(e){

		$(".gf-splitoder-modals #a").css("display","block");
		$(".gf-splitoder-modals #b").css("display","flex");
		e.preventDefault();
    });

	jQuery( document ).on( 'updated_checkout', function() {

		reloadModal();

	});

	function reloadModal()
	{
		$('.gf_splitorder-preview-btn').attr('disabled','disabled');
		var thisthis = $('#splitorder');
		if ( $(thisthis).prop('checked') ) {
			var request1 = $.ajax({
				url: gfAjax.ajaxurl,
				cache: false,
				data : {action: "gf_reload_footer", splitOrderVal:1},
				type : "post",
				dataType : "text"
			});

			request1.done(function(result){

				$('.gf_splitorder-preview-btn').removeAttr('disabled');
				$('#gf_splitorder-footer').html(result);
			});
		} else {

		}
	}
	$(document.body).on('change', '#billing_country_field', function(){
		reloadModal();
	});

	$(document).on("change", "#splitorder", function(){

		var splitOrderVal = 0;
		var thisthis = this;

		$(thisthis).attr('disabled','disabled');
		$('.gf_splitorder-preview-btn').attr('disabled','disabled');
		$('#place_order').attr('disabled','disabled');
		if ( $(thisthis).prop('checked') ) {
			splitOrderVal = 1;
			// reloadModal();
		} else {
			splitOrderVal = 0;
		}

		var request = $.ajax({
			url: gfAjax.ajaxurl,
			cache: false,
			data : {action: "set_split_order_backend", splitOrderVal:splitOrderVal},
			type : "post",
         	dataType : "json"
		});

		request.done(function(result){

			$(thisthis).removeAttr('disabled');
			$('#place_order').removeAttr('disabled');
			if ( $(thisthis).prop('checked') ) {
				$('.gf_splitorder-preview-btn').removeAttr('disabled');
			} else {
				$('.gf_splitorder-preview-btn').attr('disabled','disabled');
			}

			$( 'body' ).trigger( 'update_checkout' );

			reloadModal();
		});


	 });

	 $(document).on("click", ".gf-splitoder-modals .cancel", function(){
		$(".gf-splitoder-modals #a").fadeOut();
		$(".gf-splitoder-modals #b").fadeOut();
	 });


})( jQuery );
