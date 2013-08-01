;(function($){
	$(".clone-parent").on("click", function(event){
		var el = $(this),
			clone = el.parent().clone( true );
			clone.find("select option:selected").removeAttr("selected");
		el.parent().after( clone ).next().find("select").focus();
		event.preventDefault();
	});
	$(".remove-parent").on("click", function(event){
		var el = $(this),
			parent = el.parent();
		if ( parent.siblings("p.select-multiple").length ) {
			parent.remove();
		}
		event.preventDefault();
	});
})(jQuery);