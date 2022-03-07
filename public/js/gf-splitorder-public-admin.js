(function( $ ) {
	'use strict';


	$(document).on("click", ".gf-split-backorder-btn", function(e){

		var confirmText = 'Confirm to split order by backorder?';

		if (confirm(confirmText)) {
			var order_id = $(this).attr('data-id');
			$(this).attr('disabled','disabled');

			ajax_split_order(order_id);

		} else {

		}
		e.preventDefault();
	});

	function ajax_split_order(order_id)
	{
		var request = $.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				'order_id':order_id,
				'action':'admin_split_order'
			},
			dataType: 'json'
		});
		request.done(function(jsonData) {
			location.reload();
		});
	}
})( jQuery );
