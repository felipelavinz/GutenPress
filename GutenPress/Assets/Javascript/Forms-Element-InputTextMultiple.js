;(function($){
	$(".clone-parent").on("click", function(event){
		var el = $(this),
			clone = el.parent().clone( true );
			clone.find("input").val("");
		el.parent().after( clone ).next().find("input").focus();
		event.preventDefault();
	});
	$(".remove-parent").on("click", function(event){
		var el = $(this),
			parent = el.parent();
		if ( parent.siblings("p.input-text-multiple").length ) {
			parent.remove();
		}
		event.preventDefault();
	});
})(jQuery);