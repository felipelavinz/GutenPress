// mediaControl for wp.media in widget
// uses jquery data attached to object
// 		uploader-title 	: Title for the wordpress media windows
//		button-text 	: Text for the button
//		targetid		: target ID of the hidden input that contains the attachment id of the selected picture
// @hugosolar
function bindEventWidgetFile(id) {
	var obj = jQuery('#'+id);
	//mediaControl.init();

	// Create the media frame.
	file_frame  = wp.media({
		title: obj.data( 'uploader_title' ),
		button: {
			text: obj.data( 'uploader_button_text' )
		},
		multiple: false  // Set to true to allow multiple files to be selected
	});
	// When an image is selected, run a callback.
	file_frame.on( 'select', function() {
		//var img_obj = obj.data('targetimg');
		var img_html = obj.data('receiver_id');
		var img_id = obj.data('target_id');
		attachment = file_frame.state().get('selection').first().toJSON();
		var file_selected = '<strong>'+attachment.filename+'</strong>';

		jQuery('#'+img_html).html(file_selected);

		jQuery('#'+img_id).val(attachment.id);
	});

	file_frame.open();

	return false;
}
;(function($){
	$.fn.gpWpFile = function(){
		this.each(function(){
			var $this = $(this),
				$mediaButton = $this.find('button.gp-wpfile-upload'),
				$deleteButton = $this.find('button.gp-wpfile-delete'),
				$receiver = $this.find('div.gp-wpfile-receiver'),
				$input = $this.find('input.gp-wpfile-field');
			$deleteButton.on('click', function(event){
				$receiver.find('strong').fadeOut('normal', function(){
					$input.val('');
				});
				$(this).fadeOut('fast');
				event.preventDefault();
			});
		});
	};
	$('.gp-wpfile').gpWpFile();
})(jQuery);