$(function () {
    map.markerIconProvider = new MarkerIconProvider
});

function MarkerIconProvider() {
    this.icons = {
        'default' : [
            {
                url: '/img/icon/marker/home.png',
                size: new google.maps.Size(32, 46),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(16, 45)
            },
            {
                url: '/img/icon/marker/home-hov.png',
                size: new google.maps.Size(40, 57),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 55)
            }
        ],
        'building' : [
            {
                url: '/img/icon/marker/building.png',
                size: new google.maps.Size(32, 46),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(16, 45)
            },
            {
                url: '/img/icon/marker/building-hov.png',
                size: new google.maps.Size(40, 57),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 55)
            }
        ],
        'dollar' : [
            {
                url: '/img/icon/marker/dollar.png',
                size: new google.maps.Size(32, 46),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(16, 45)
            },
            {
                url: '/img/icon/marker/dollar-hov.png',
                size: new google.maps.Size(40, 57),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 55)
            }
        ]
    };

    this._getIconSet = function(estate) {
        var type = estate.estateType;
        if (type == 'building')
            return this.icons.building;
        if (type == 'restaurant' || type == 'office')
            return this.icons.dollar;
        return this.icons.default;
    };

    this.getIcon = function(estate) {
        return this._getIconSet(estate)[0];
    };

    this.getIconHovered = function(estate) {
        return this._getIconSet(estate)[1];
    };
}

//Object to manage estate
var EstateManager = {

    data : {
        markerCluster : null,
        markerList : [],
        estateList : [],
        totalFound : null
    },

    mapSettings: {
        markerIcon : {},
        clusterSettings: {
            gridSize: 25,
            styles: [
                {
                    textColor: 'white',
                    url: '/img/icon/redpoint32.png',
                    height: 32,
                    width: 32
                },
                {
                    textColor: 'white',
                    url: '/img/icon/redpoint32.png',
                    height: 32,
                    width: 32
                },
                {
                    textColor: 'white',
                    url: '/img/icon/redpoint32.png',
                    height: 32,
                    width: 32
                }
            ],
            maxZoom: 15
        }
    },

    // Returns estate list and render
    'getEstateList': function (searchCriterias, callback) {

        if (this.data.markerCluster) this.data.markerCluster.removeMarkers(this.data.markerList);
        this.data.markerList.length = 0;
        this.data.estateList.length = 0;

        $.ajax({
            type: "GET",
            url: Routing.generate('get_estates', searchCriterias),
            success: function (result) {
                if (result) {
                    for (var i in result.collection) {
                        var estate = result.collection[i];
                        var tmpLatLng = new google.maps.LatLng(estate.lat, estate.lng);

                        var marker = new google.maps.Marker({
                            map: map,
                            position: tmpLatLng,
                            icon: map.markerIconProvider.getIcon(estate),
                            title : estate.title
                        });

                        estate.marker = marker;
                        marker.estate = estate;
                        marker.infoWindow = new google.maps.InfoWindow({
                            'content' : ' '
                        });
                        marker.infoWindow.estate = estate;

                        google.maps.event.addListener(estate.marker.infoWindow, 'domready', function(){
                            this.setContent(Templating.createMarkerInfoWindow(this.estate));
                            $('.infowindow-lazy').lazy();
                            $('.panorama-btn').on('click', MapInterfaceControls.infowindowStreetviewOpen)
                        });
                        marker.addListener('click', MapInterfaceControls.markerOnClick);
                        marker.addListener('mouseover', MapInterfaceControls.markerMouseOver);
                        marker.addListener('mouseout', MapInterfaceControls.markerMouseOut);


                        var itemIndex = EstateManager.data.estateList.push(estate) -1;
                        EstateManager.data.estateList[itemIndex].estateManagerListId = itemIndex; //save index in array EstateManager.data.estateList

                        EstateManager.data.markerList.push(estate.marker);
                    }

                    EstateManager.data.totalFound = result.totalFound;
                    EstateManager.data.markerCluster = new MarkerClusterer(
                        map,
                        EstateManager.data.markerList,
                        EstateManager.mapSettings.clusterSettings
                    );
                }
                if (callback) callback();
            },
            error: function () {
            }
        });
    },

    'getByMapBorders': function (callback) {
        var result = [];
        var bounds = map.getBounds();
        for (var i in this.data.estateList) {
            object = this.data.estateList[i];
            if (object.lat > bounds.f.f && object.lat < bounds.f.b && object.lng > bounds.b.b && object.lng < bounds.b.f) {
                result.push(object);
            }
        }
        return result;
    },

    'formToObject' : function (form) {
        var formObjects = $(form).serializeArray();
        var result = {};
        for (var i in formObjects) {
            var object = formObjects[i];

            var name = null;
            if (object.name.match(/\[(.*)\]\[\]/) === null) {
                name  = object.name.match(/\[(.*)\]/)[1];
                if (name != '_token') result[name] = object.value;
            } else {
                name  = object.name.match(/\[(.*)\]\[\]/)[1];
                if (result[name]) {
                    result[name] = result[name]+','+object.value;
                } else {
                    result[name] = object.value;
                }
            }


        }
        return result;
    }
};
