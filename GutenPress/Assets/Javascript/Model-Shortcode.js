;(function($, window, l10n){
	if ( typeof tinymce !== 'object' )
		return false;
	tinymce.create('tinymce.plugins.GutenPressShortcodePlugin', {
		init: function(ed, url){
			var t = this;
			t.editor = ed;
			ed.addCommand('init_gutenpress_shortcode', t._init_gutenpress_shortcode, t);
			ed.addButton('gutenpress_shortcode', {
				title: l10n.tinymce.button_title,
				cmd: 'init_gutenpress_shortcode',
				image: url + '/../Img/Model-Shortcode.png'
			});
			var dialog = '' +
			'<div class="hidden">'+
				'<div class="gutenpress-dialog-wrap has-actions" id="gutenpress-dialog-shortcode">' +
					'<div class="gutenpress-dialog">' +
						'<div class="gutenpress-dialog-content" id="gutenpress-shortcode-manager">' +
							'<p class="howto">'+ l10n.dialog.text_select +'</p>' +
							'<div id="gutenpress-shortcode-receiver"></div>' +
						'</div>' +
						'<div id="gutenpress-shortcode-actions" class="gutenpress-dialog-actions hidden">' +
							'<div class="gutenpress-dialog-actions-wrap">' +
								'<input type="text" id="gutenpress-shortcode-preview" class="gutenpress-shortcode-preview" readonly>' +
								'<button id="gutenpress-shortcode-create" class="button-primary">'+ l10n.dialog.button_insert +'</button>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>';
			$('body').append( dialog );
		},
		getInfo: function(){
			return {
				longname  : l10n.tinymce.longname,
				author    : 'Felipe Lavín Z.',
				authorurl : 'http://www.yukei.net',
				infourl   : 'http://felipelavinz.github.io/GutenPress',
				version   : '0.1'
			};
		},
		_init_gutenpress_shortcode: function(){
			var setDialogSize = function(){
				var _window = $(window),
					height  = Math.floor( _window.height() * 0.6 ) - 28,
					width   = Math.floor( _window.width() * 0.5 ),
					dialog  = $('#gutenpress-dialog-shortcode');
				dialog.dialog('option', {
					width  : width > 540 ? width : 540,
					height : height > 250 ? height: 280
				}).dialog('option', 'position', { at: 'center' });
				$('#gutenpress-shortcode-manager').css('height', parseInt( $('#gutenpress-dialog-shortcode').outerHeight(), 10) - 65);
			};
			$('#gutenpress-dialog-shortcode').dialog({
				title         : l10n.dialog.title,
				dialogClass   : 'wp-dialog',
				resizable     : false,
				modal         : true,
				draggable     : true,
				closeOnEscape : true,
				open: function(event, ui){
					// does the shortcode accepts internal content?
					var has_content;
					var setSelectionAsContent = function(){
						var selectedContent = tinymce.activeEditor.selection.getContent();
						if ( selectedContent ) {
							has_content.value = selectedContent;
						}
					};
					var previewShortcode = function(){
						var preview     = $('#gutenpress-shortcode-preview'),
							form        = $('#gutenpress-shortcode-composer'),
							fields      = $('#gutenpress-shortcode-fields'),
							sc_content  = has_content ? has_content.value : '',
							base_format = has_content && sc_content ? '[%shortcode%%attributes%]%content%[/%shortcode%]' : '[%shortcode%%attributes%]',
							attributes  = [],
							val         = base_format.replace(/%shortcode%/g, $('#gutenpress-shortcode-select').val());
						fields.find('input, select').each(function(i, obj){
							// @todo deal with multiple-inputs
							// @todo deal with shortcodes with content
							if ( obj.name === 'content' )
								return;
							if ( obj.checkValidity ) {
								if ( obj.value && obj.checkValidity() ) attributes.push(obj.name +'="'+ obj.value +'"');
							} else {
								if ( obj.value ) attributes.push(obj.name +'="'+ obj.value +'"');
							}
						});
						val = attributes.length ? val.replace('%attributes%', ' '+ attributes.join(' ')) : val.replace('%attributes%', '');
						if ( has_content && sc_content ) {
							val = val.replace('%content%', sc_content);
						}
						preview.val( val );
						return val;
					};
					setDialogSize();
					$.get( l10n.settings.ajax_url, {
						action: 'gutenpress_shortcode_get_composer'
					}, function(data){
						$('#gutenpress-shortcode-receiver').html( data );
						$('#gutenpress-shortcode-select').on('change', function(){
							$.get( l10n.settings.ajax_url, {
								action: 'gutenpress_shortcode_get_fields',
								shortcode: $(this).val()
							}, function(data){
								setDialogSize();
								$('#gutenpress-shortcode-fields').html( data ).find('input, select, textarea').on('change', function(){
									previewShortcode();
								});
								has_content = document.getElementById('gutenpress-shortcode-content');
								// ... if the shortcode can use a content, we should use the selected text as value
								if ( has_content ) setSelectionAsContent();
								$('#gutenpress-shortcode-actions').fadeIn();
								$('#gutenpress-shortcode-create').on('click', function(){
									previewShortcode();
									$('#gutenpress-dialog-shortcode').dialog('close');
								});
								previewShortcode();
							});
						});
					} );
				},
				close: function(event, ui){
					// return focus to tinymce
					var generated_shortcode = $('#gutenpress-shortcode-preview').val(),
						editor_content      = tinymce.activeEditor.getContent();
					tinymce.activeEditor.selection.setContent( generated_shortcode );
					tinymce.activeEditor.focus();
				}
			});
			$(window).resize(function(){ setDialogSize(); });
			return true;
		}
	});
	tinymce.PluginManager.add('gutenpress_shortcode', tinymce.plugins.GutenPressShortcodePlugin);
})(jQuery, window, GutenPress_Shortcodes_l10n);