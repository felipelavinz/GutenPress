;(function($){
	$.fn.gpWpImage = function(){
		this.each(function(){
			var $this = $(this),
				$mediaButton = $this.find('button.gp-wpimage-upload'),
				$deleteButton = $this.find('button.gp-wpimage-delete'),
				$receiver = $this.find('div.gp-wpimage-receiver'),
				$input = $this.find('input.gp-wpimage-field');
			$mediaButton.on('click', function(event){
				// props to @mikejolley and @hugosolar for this
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					file_frame.open();
					return;
				}
				// Create the media frame.
				var file_frame = wp.media.frames.file_frame = wp.media({
					title: $(this).data( 'uploader_title' ),
					button: {
						text: $(this).data( 'uploader_button_text' )
					},
					multiple: false  // Set to true to allow multiple files to be selected
				});
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					var attachment = file_frame.state().get('selection').first().toJSON();
						var thumb = $('<img>').attr({
							'alt'    : attachment.title,
							'src'    : attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.url : attachment.sizes.full.url,
							'title'  : attachment.title,
							'width'  : attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.width : attachment.sizes.full.width,
							'height' : attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.height : attachment.sizes.full.height
						}),
						entry_id = attachment.id;
					$receiver.html( thumb );
					$input.val(entry_id);
					$deleteButton.removeClass('hidden').show();
				});
				file_frame.open();
				event.preventDefault();
			});
			$deleteButton.on('click', function(event){
				$receiver.find('img').fadeOut('normal', function(){
					$input.val('');
				});
				$(this).fadeOut('fast');
				event.preventDefault();
			});
		});
	};
	$('.gp-wpimage').gpWpImage();
})(jQuery);