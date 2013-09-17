;(function($){
	$.fn.gpWpGallery = function(){
		this.each(function(){
			var $this = $(this),
				$createButton = $this.find('button.gp-wpgallery-create'),
				$addButton = $this.find('button.gp-wpgallery-add'),
				$deleteButton = $this.find('button.gp-wpgallery-delete'),
				$receiver = $this.find('div.gp-wpgallery-receiver'),
				$input = $this.find('input.gp-wpgallery-field');
			if ( gallery_frame ) {
				gallery_frame.open();
				return;
			}
			var gallery_frame = wp.media.frames.gallery_frame = wp.media({
				title: $(this).data( 'uploader_title' ),
				library: { type: 'image' },
				button: {
					text: $(this).data( 'uploader_button_text' )
				},
				multiple: 'add'  // Set to true to allow multiple files to be selected
			});
			gallery_frame.on('ready', function(){
				$('.media-modal').addClass('no-sidebar smaller');
			});
			var sendToMetabox = function( images ){
				var img_holders = [];
				var existing = [];
				$this.find('input[type="hidden"]').each(function(i, obj){
					existing[i] = parseInt( obj.value, 10 );
				});
				for ( var i = 0, q = images.length; i < q; i++ ){
					var attachment = images[i];
					console.log(attachment.id, existing, $.inArray(attachment.id, existing) );
					// skip existing images
					if ( $.inArray(attachment.id, existing) != -1 ) {
						continue;
					}
					var holder = $('<div>').attr({
						'class'  : 'sortable gp-wpgallery-item gp-wpgallery-sortable-item attachment'
					}), thumb = $('<img>').attr({
						'alt'    : attachment.title,
						'src'    : attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.url : attachment.sizes.full.url,
						'title'  : attachment.title,
						'width'  : attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.width : attachment.sizes.full.width,
						'height' : attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.height : attachment.sizes.full.height
					}), control = $('<input>').attr({
						'type'   : 'hidden',
						'name'   : $this.data('name') +'[]',
						'value'  : attachment.id
					});
					holder.css({
						'width' : attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.width : attachment.sizes.full.width,
						'height': attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.height : attachment.sizes.full.height
					}).append(thumb).append(control).append('<a class="close media-modal-icon" href="#" title="Remove"></a>');
					img_holders[i] = holder;
				}
				$receiver.append( img_holders );
				$receiver.sortable().disableSelection();
			};
			$createButton.on('click', function(event){
				gallery_frame.off('select').on('select', function(){
					sendToMetabox( gallery_frame.state().get('selection').toJSON() );
					$createButton.addClass('hidden').hide();
					$addButton.removeClass('hidden').show();
					$deleteButton.removeClass('hidden').show();
				}).open();
				event.preventDefault();
			});
			$addButton.on('click', function(event){
				gallery_frame.off('select').on('select', function(){
					sendToMetabox( gallery_frame.state().get('selection').toJSON() );
				}).open();
				event.preventDefault();
			});
			$deleteButton.on('click', function(event){
				$receiver.find('div.attachment').animate({
					opacity: 0,
					height: 0
				}, 'fast', function(){
					$(this).remove();
					$deleteButton.fadeOut('fast');
					$addButton.fadeOut('fast', function(){
						$createButton.fadeIn('normal');
					});
				});
				event.preventDefault();
			});
			$(this).on('click', 'a.close', function(event){
				event.preventDefault();
				$(this).closest('div.attachment').animate({
					width: 0,
					opacity: 0
				}, 'fast', function() {
					$(this).remove();
				});
			});
			$receiver.sortable().disableSelection();
		});
	};
	$('.gp-wpgallery').gpWpGallery();
})(jQuery);