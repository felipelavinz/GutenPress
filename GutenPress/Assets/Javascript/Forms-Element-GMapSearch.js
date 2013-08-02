;(function($){
	$.fn.gpGMapSearch = function(){
		// Defaults
		var defaults = {
			'zoom' : 16,
			'bounds' : false
		};
		this.each( function(){
			var obj;
			obj = this;
			$obj = $(obj);
			obj.init = function(){
				var mapID = $obj.find('.mapCanvas').attr('id');
				obj.mapCanvas = $obj.find('#'+mapID);
				obj.mapAddress = $obj.find('.mapAddress');
				obj.mapSearch = $obj.find('.mapSearch');
				obj.mapLat = $obj.find('.mapLat');
				obj.mapLng = $obj.find('.mapLng');
				obj.mapError = $obj.find('.mapError');
				obj.mapReset = $obj.find('.mapReset');
			};
			obj.initMap = function(){
				if( obj.getLat() && obj.getLng() ){
					// Set Options to center
					obj.setOptions();
					// Create marker
					obj.setMarker();
				} else {
					obj.mapCanvas.gmap({ 'zoom': defaults.zoom });
				}
			};
			obj.setOptions = function(){
				var inputPosition = new google.maps.LatLng( obj.getLat(), obj.getLng() );
				obj.mapCanvas.gmap('get','map').setOptions({ 'zoom': defaults.zoom, 'center': inputPosition });
			};
			obj.getLat = function(){
				return ( obj.mapLat.val().length ? obj.mapLat.val() : false );
			};
			obj.getLng = function(){
				return ( obj.mapLng.val().length ? obj.mapLng.val() : false );
			};
			obj.triggerSearch = function(){
				obj.mapSearch.on('click', function(){
					obj.searchAddress();
				});
			};
			obj.searchAddress = function(){
				var geocoder = new google.maps.Geocoder();
				var address = obj.mapAddress.attr('value');
				if (geocoder) {
					geocoder.geocode({ 'address': address }, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							// Clear error
							obj.mapError.text('');
							// Clear markers if exist
							obj.mapCanvas.gmap('clear', 'markers');
							// Save LatLng hidden inputs
							obj.setHiddenLatLng( results[0].geometry.location );
							// Set Options to center
							obj.setOptions();
							// Create new marker
							obj.setMarker();
						}
						else {
							obj.mapError.text("No se encontró esta dirección. Prueba con el nombre de la ciudad.");
						}
					});
				}
			};
			obj.resetMap = function(){
				obj.mapReset.on('click', function(){
					obj.resetAddress();
				});
			};
			obj.resetAddress = function(){
				obj.mapAddress.val('');
				obj.mapLat.val('');
				obj.mapLng.val('');
			};
			obj.setMarker = function(){
				var inputPosition = new google.maps.LatLng( obj.getLat(), obj.getLng() );
				obj.mapCanvas
					.gmap('addMarker', { 'position': inputPosition, 'bounds': defaults.bounds, 'draggable': true } )
					.dragend( function(event) {
						// Save LatLng hidden inputs if Drag Point
						obj.setHiddenLatLng( event.latLng );
						// Set Options to center
						obj.setOptions();
					});
			};
			obj.setHiddenLatLng = function( location ){
				obj.mapLat.val( location.jb );
				obj.mapLng.val( location.kb );
			};
			// Init Load
			obj.init();
			obj.initMap();
			obj.triggerSearch();
			obj.resetMap();
		});
	};
	$(document).ready( function($){
		$('.gp-gmapsearch').gpGMapSearch();
	});
})(jQuery);