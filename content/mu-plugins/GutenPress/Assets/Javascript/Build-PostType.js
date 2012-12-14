jQuery(document).ready(function($){
	var Form = $('#gp-build-post_type');
	// set defaults
	Form.on('change', 'input[name="gp-build-post_type[public]"]', function(event){
		var el = $(this),
			val = el.val(),
			relatedSelects = Form.find('select[data-public]'),
			relatedRadios = Form.find('input[data-public]');
		relatedSelects.each(function(){
			var el = $(this);
			if ( el.data('public') === 'direct' ) {
				el.val( val );
			} else {
				if ( val === '1' ) {
					el.val('0');
				} else {
					el.val('1');
				}
			}
		});
		relatedRadios.each(function(){
			var el = $(this);
			if ( el.data('public') === 'direct' && val === el.val() ) {
				el.attr('checked', 'checked');
			}
			if ( el.data('public') === 'reverse' && val !== el.val() ) {
				el.attr('checked', 'checked');
			}
		});
	}).on('keyup', '#gp-build-post_type-post_type', function(){
		var val = $(this).val().toLowerCase();
		$(this).val( val );
		$('#gp-build-post_type-capability-type-1, #gp-build-post_type-rewrite-1').val( val );
	}).on('keyup', '#gp-build-post_type-label', function(){
		var el = $(this),
			val = el.val();
		Form.find('input[data-number="plural"], #gp-build-post_type-labels-1').each(function(){
			var el = $(this),
				format = el.data('format');
			el.val( format.replace('%s', val) );
		});
	}).on('keyup', 'input[name="gp-build-post_type[labels][singular_name]"]', function(){
		var el = $(this),
			val = el.val();
		Form.find('input[data-number="singular"]').each(function(){
			var el = $(this),
				format = el.data('format');
			el.val( format.replace('%s', val) );
		});
	}).on('change', 'select[name="gp-build-post_type[rewrite_enable]"]', function(){
		var el = $(this),
			val = $(this).val();
		if ( val == '1' ) {
			Form.find('.rewrite-opt').removeAttr('disabled').parent().fadeTo(100, 1);
		} else {
			Form.find('.rewrite-opt').attr('disabled', 'disabled').parent().fadeTo(300, 0.4);
		}
	});
	$('#gp-build-post_type-public-1').trigger('click');
});