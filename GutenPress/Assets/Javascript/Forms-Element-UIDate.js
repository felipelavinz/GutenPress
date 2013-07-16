;(function($){
	$('.gp-ui-datepicker').each(function(){
		var $this    = $(this),
			instance = {
				dateFormat: $this.data('dateFormat')
			};
		var params = $.extend({
			dateFormat: 'yy-mm-dd'
		}, instance );
		$(this).datepicker( params );
	});
})(jQuery);