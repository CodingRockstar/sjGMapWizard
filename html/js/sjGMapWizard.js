 
/**
 * @copyright  Stephan Jahrling (Stephan Jahrling - Softwarelösungen), 2011
 * @author     Stephan Jahrling <info@jahrling-software.de>
 * @license    commercial
 */
  
var map;
var geocoder;
var address;


window.addEvent('domready', function() {
	
	
    if (GBrowserIsCompatible()) {
	  
        map = new GMap2(document.getElementById("begmap"));
        
        if (ptMapCenter)
        	center = ptMapCenter;
        else
        	center = new GLatLng(53.871996,10.404629);
        	
        
		if( $('ctrl_' + fieldName + '[geocodes]').get('value').length <= 0 ){
			map.setCenter(center, 6);
		} else {
			var latlng = $('ctrl_' + fieldName + '[geocodes]').get('value').split(',');
			map.setCenter(new GLatLng( latlng[0], latlng[1] ), 15);
			
			// set marker
			var marker = new GMarker( new GLatLng( latlng[0], latlng[1] ) );
            map.addOverlay(marker);
		}
		
		map.addControl(new GSmallMapControl());
		
		
		if (GMapIsClickable)
			GEvent.addListener(map, "click", getAddress);
		
			
		geocoder = new GClientGeocoder();
		
		// add event to button
		$('sjGetGeocodes').addEvent('click', function(event) {
		
			event.stop();
		
			if( $('ctrl_' + fieldName + '[city]').get('value').length > 0 
				   && $('ctrl_' + fieldName + '[country]').get('value').length > 0 ){
				
				var address_str = $('ctrl_' + fieldName + '[city]').get('value') + ", " + $('ctrl_' + fieldName + '[country]').getSelected()[0].get('text');
				
				if ( $('ctrl_' + fieldName + '[street]') && $('ctrl_' + fieldName + '[street]').get('value').length > 0 )
					address_str = $('ctrl_' + fieldName + '[street]').get('value') + ", " + address_str;
					
				if ( $('ctrl_' + fieldName + '[postal]') && $('ctrl_' + fieldName + '[postal]').get('value').length > 0 )
					address_str = $('ctrl_' + fieldName + '[postal]').get('value') + " " + address_str;
				
				geocodeAddress( address_str );			
			
			} else {
				alert(strWrongAddress);
			}
		});

		
	}
	
});


function getAddress(overlay, latlng) {
  if (latlng != null) {
    address = latlng;
    geocoder.getLocations(latlng, showAddress);
  }
}



function showAddress(response) {
  
  map.clearOverlays();
  
  if (!response || response.Status.code != 200) {
  
    alert("Status Code:" + response.Status.code);
  
  } else {
    
    place = response.Placemark[0];
    point = new GLatLng(place.Point.coordinates[1],place.Point.coordinates[0]);
    marker = new GMarker(point);
    map.addOverlay(marker);
	
	var geocode_str = place.Point.coordinates[1] + "," + place.Point.coordinates[0];
	
    marker.openInfoWindowHtml(
        '<b>' + strKoordinates + ':</b> ' + place.Point.coordinates[1] + "," + place.Point.coordinates[0] + '<br>' +
        '<b>' + strAddress + ':</b>  ' + place.address + '<br>' +
        '<b>' + strCountry + '</b> ' + place.AddressDetails.Country.CountryNameCode + '<br><br>' +
		'<input type="button" name="addGeoCodes" value="' + strUseAddress + ' &gt;&gt;" onClick="$(\'ctrl_' + fieldName + '[geocodes]\').set(\'value\', \'' + geocode_str + '\');"><br>&nbsp;') ;
  
  }
 
}


function geocodeAddress(address) {

    if (geocoder) {
        geocoder.getLatLng(
          address,
          function(point) {
            if (!point) {
              alert(address + " not found");
            } else {
              map.setCenter(point, 13);
              var marker = new GMarker(point);
              map.addOverlay(marker);
              marker.openInfoWindowHtml(address);
			  
			  document.getElementById('ctrl_' + fieldName + '[geocodes]').value = point.lat() + "," + point.lng();
            }
          }
        );
    }
	
}
