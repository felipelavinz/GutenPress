;(function($){
	jQuery(document).ready(function(){
		$('.clone-fieldset').on('click', function(event){
			var el = $(this),
				fieldset = el.closest('fieldset'),
				clone = fieldset.clone( true );
				clone.find('input, select').val('');
			fieldset.after( clone ).next().find('input, select').filter(':first').focus();
			event.preventDefault();
		});
		$('.remove-fieldset').on('click', function(event){
			var el = $(this),
				fieldset = el.closest('fieldset');
			if ( fieldset.siblings('fieldset').length ) {
				fieldset.remove();
			}
			event.preventDefault();
		});
		$('.clone-fieldset').closest('form').on('submit', function(){
			$('div.fieldset-multiple-wrap').each(function(){
				$(this).find('fieldset').each(function(i_group, obj_group){
					$(this).find('input, select').each(function(i, obj){
						obj.name = $(this).data('nameformat').replace('__i__', i_group);
					});
				});
			});
		});
	});
})(jQuery);