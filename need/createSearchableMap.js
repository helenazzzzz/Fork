// Generate initial map with markers at specified locations
function createSearchableMap(locations = allLocations) {
  var bounds = new google.maps.LatLngBounds();
  var mapOptions = {mapTypeId: 'roadmap'};
  var markers = [];
  var infoWindowContent = [];
  var map = new google.maps.Map(document.getElementById('locations-near-you-map'), mapOptions);
  
  map.setTilt(45);
  // Set marker values from longitude and latitude data
  locations.forEach(function(location) {
    markers.push([location.name, location.lat, location.lng]);
    // Send data to information window for each store
    infoWindowContent.push(['<div class="infoWindow"><h3>' + location.name + 
                            '</h3><p>' + location.address + '<br />' + location.city + 
                            ', ' + location.state + ' ' + location.zip + '</p><p>Phone ' + 
                            location.phone + '</p></div>']);
  });	    

  var infoWindow = new google.maps.InfoWindow(), marker, i;
  
  // Place the markers on the map
  for (i = 0; i < markers.length; i++) {
    var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
    bounds.extend(position);
    marker = new google.maps.Marker({
      position: position,
      map: map,
      title: markers[i][0]
    });
    
    // Add an infoWindow to each marker, and create a closure so that the current marker is always associated with the correct click event listener
    google.maps.event.addListener(marker, 'click', (function(marker, i) {
      return function() {
        infoWindow.setContent(infoWindowContent[i][0]);
        infoWindow.open(map, marker);
      }
    })(marker, i));

    // Only use the bounds to zoom the map if there is more than 1 location shown
    if (locations.length > 1) {
      map.fitBounds(bounds);
    } else {
      var center = new google.maps.LatLng(locations[0].lat, locations[0].lng);
      map.setCenter(center);
      map.setZoom(15);
    }
  }
}

// return a subset of a “locations” array including only elements for which the “is within radius” function returned true
function filterLocations() {
  var userLatLng;
  var geocoder = new google.maps.Geocoder();
  var userAddress = document.getElementById('userAddress').value.replace(/[^a-z0-9\s]/gi, '');
  var maxRadius = parseInt(document.getElementById('maxRadius').value, 10);
  
  if (userAddress && maxRadius) {
    userLatLng = getLatLngViaHttpRequest(userAddress);
  } 

  function getLatLngViaHttpRequest(address) {
    // Set up a request to the Geocoding API
    // Supported address format is City, City + State, just a street address, or any combo
    var addressStripped = address.split(' ').join('+');
    var key = 'AIzaSyAeuC_DNO_mR4CWLDDJWdwjgJCPNF-V3j8';
    var request = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + addressStripped + '&key=' + key;
    
    // Call the Geocoding API using jQuery GET, passing in the request and a callback function 
    // which takes one argument "data" containing the response
    $.get( request, function( data ) {
      var searchResultsAlert = document.getElementById('location-search-alert');

      // Abort if there is no response for the address data
      if (data.status === "ZERO_RESULTS") {
        searchResultsAlert.innerHTML = "Sorry, '" + address + "' seems to be an invalid address.";
        return;
      }

      var userLatLng = new google.maps.LatLng(data.results[0].geometry.location.lat, data.results[0].geometry.location.lng);
      var filteredLocations = allLocations.filter(isWithinRadius);
      
      if (filteredLocations.length > 0) {
        createSearchableMap(filteredLocations);
        createListOfLocations(filteredLocations);
        searchResultsAlert.innerHTML = 'Grocery store locations within ' + maxRadius + ' miles of ' + userAddress + ':';
      } else {
        console.log("nothing found!");
        document.getElementById('locations-near-you').innerHTML = '';
        searchResultsAlert.innerHTML = 'Sorry, no grocery store locations were found within '+ maxRadius + ' miles of ' + userAddress + '.';
      }
      // return true given two latLng objects and a radius if distance between the two objects is less than or equal to the radius
      function isWithinRadius(location) {
        var locationLatLng = new google.maps.LatLng(location.lat, location.lng);
        // return the correct distance in meters between two latLng objects 
        var distanceBetween = google.maps.geometry.spherical.computeDistanceBetween(locationLatLng, userLatLng);

        return convertMetersToMiles(distanceBetween) <= maxRadius;
      }
    });  
  }
}

// return number of miles given meters
function convertMetersToMiles(meters) {
  return (meters * 0.000621371);
}

// Generate list of valid nearby store locations 
function createListOfLocations(locations) {
  var locationsList = document.getElementById('locations-near-you');
  
  // Clear any existing locations from the previous search first
  locationsList.innerHTML = '';
  
  locations.forEach( function(location) {
    var specificLocation = document.createElement('div');
    var locationInfo = "<h4>" + location.name + "</h4><p>" + location.address + "</p>" +
                       "<p>"  + location.city + ", " + location.state + " " + location.zip + "</p><p>" + location.phone + "</p>";
    specificLocation.setAttribute("class", 'location-near-you-box');
    specificLocation.innerHTML = locationInfo;
    locationsList.appendChild(specificLocation);
  });
}

$('#submitLocationSearch').on('click', function(e) {
  e.preventDefault();
  filterLocations();
});
