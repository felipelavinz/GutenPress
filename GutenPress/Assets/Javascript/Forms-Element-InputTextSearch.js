;(function($){
	$('input.searchfield').each(function(i, el) {
		el = $(el);
		el.autocomplete({
			source: function(request, response){
				$.get('/wp-admin/admin-ajax.php', { action : el.attr('action') , term: el.val(), posttype: el.attr('posttype'), resultlenght: el.attr('resultlenght') },
					function(recv){
						var data = eval(recv);
						response(data);
					});
			},
			minLength: (el.attr('minlength') != 'undefined') ? el.attr('minlength') : 1,
			select: function(event, ui){
				var input = $('div.tagchecklist.box-text-search.'+el.attr('field')+' input');
				var span = $('div.tagchecklist.box-text-search.'+el.attr('field')+' span');
				span.html('<a id="element-'+ ui.item.id +'" postid="'+ ui.item.id +'" class="ntdelbutton label-text-search">X</a>&nbsp;'+ ui.item.label );
				input.val(ui.item.id);
				el.val(''); return false;
			}
		});
	});
	$(".box-text-search a").on("click", function(event){
		var postid = $(this).attr('postid');
		$( "input[value='"+postid+"']" ).remove();
		$(this).parent().remove();
		event.preventDefault();
	});
})(jQuery);