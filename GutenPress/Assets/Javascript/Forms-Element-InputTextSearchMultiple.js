;(function($){
	$('input.searchmultiplefield').each(function(i, el) {
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
				var input = $('div.tagchecklist.box-text-search-multiple.'+el.attr('field')+' input').first().clone();
				var span = $('div.tagchecklist.box-text-search-multiple.'+el.attr('field')+' span').first().clone();
				span.html('<a id="element-'+ ui.item.id +'" postid="'+ ui.item.id +'" class="ntdelbutton label-text-search-multiple">X</a>&nbsp;'+ ui.item.label );
				input.val(ui.item.id);
				$('div.tagchecklist.box-text-search-multiple.'+el.attr('field')).append(input).append(span);
				el.val(''); return false;
			}
		});
	});
	$(".box-text-search-multiple a").on("click", function(event){
		var postid = $(this).attr('postid');
		$( "input[value='"+postid+"']" ).remove();
		$(this).parent().remove();
		event.preventDefault();
	});
})(jQuery);