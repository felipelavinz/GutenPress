;(function($){
	$('.gp-ui-datepicker').each(function(){
		var $this    = $(this),
			instanceConfig = $this.data('instanceconfig');
		var params = $.extend({
			dateFormat: 'yy-mm-dd'
		}, instanceConfig );
		$(this).datepicker( params );
	});
})(jQuery);