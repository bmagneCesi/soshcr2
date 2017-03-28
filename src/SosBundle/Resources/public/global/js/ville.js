$(document).ready(function(){
	
    // get les nom des villes via google
	google.maps.event.addDomListener(window, 'load', initialize);

    // Au changement de ville on get la Lat & Lnt
    $('#ville').on('change', function(){ 
        var Geocoder = new google.maps.Geocoder();
        var address = document.getElementById('ville').value;

        Geocoder.geocode({ 'address': address }, function (results, status) {

            if (status == google.maps.GeocoderStatus.OK) {
                var latitude = results[0].geometry.location.lat();
                var longitude = results[0].geometry.location.lng();
                $('input[name="ville[latitude]"]').val(latitude);
                $('input[name="ville[longitude]"]').val(longitude);
            }
        });
    });

});

function initialize() {
    var options = {
      componentRestrictions: {country: "fr"}
     };
    var input = document.getElementById('ville');
    var autocomplete = new google.maps.places.Autocomplete(input, options);

}
