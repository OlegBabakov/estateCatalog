var CommandFlags = {
    'renderItemsInMapBounds' : false,
    'lockFilterFormHandler' : false
};

var filterChangedElement = null;

/**
 * Created by oleg on 01.11.16.
 */

MapInterfaceControls = {
    infowindowStreetviewOpen : function () {
        panorama = map.getStreetView();
        panorama.setPosition({
            'lat' : $(this).data('lat'),
            'lng' : $(this).data('lng')
        });
        panorama.setPov(/** @type {google.maps.StreetViewPov} */({
            heading: 265,
            pitch: 0
        }));
        panorama.setVisible(true);
    },
    markerOnClick : function () {
        if (map.infoWindow) map.infoWindow.close();
        map.infoWindow = this.infoWindow;
        this.infoWindow.open(map, this);
    },
    markerMouseOver : function () {
        MapInterfaceControls.resetHoveredMarker();
        MapInterfaceControls.hoverMarker(this);
        $(".sidebar-list-item[data-estate-list-id='"+ this.estate.estateManagerListId +"']").addClass('hover');
        map.markerHovered = this;
    },
    markerMouseOut : function () {
        MapInterfaceControls.resetHoveredMarker();
        $(".sidebar-list-item[data-estate-list-id='"+ map.markerHovered.estate.estateManagerListId +"']").removeClass('hover');
        map.markerHovered = null;
    },
    resetHoveredMarker : function () {
        if (map.markerHovered) map.markerHovered.setIcon(
            map.markerIconProvider.getIcon(map.markerHovered.estate)
        );
    },
    hoverMarker : function (marker) {
        marker.setIcon(
            map.markerIconProvider.getIconHovered(marker.estate)
        );
    }

};

$(function () {

    if (!$('#page-map-catalog').length) return 0;

    $(document).ajaxComplete(function() {
        //GalleryEventHandler.onAjaxReload();
    });

    // Map Events
    google.maps.event.addListener(map, "click", function(event) {
        if (map.infoWindow) map.infoWindow.close();
    });

    google.maps.event.addListener(map, 'bounds_changed', function () {
        CommandFlags.renderItemsInMapBounds = true;
    });

    setInterval(
        function() {
            if (CommandFlags.renderItemsInMapBounds) {
                Templating.renderEstateList(EstateManager.getByMapBorders());
                CommandFlags.renderItemsInMapBounds = false;
            }
        },
        1000
    );
    
    // Filter Controls--------------------------------------------------------------------------------------------------
    $('.filter-show-btn').on('click', function () {
        $('#sidebar-gray').fadeIn(500);

        var sidebarFilter = $("#sidebar-filter");
        var offset = $(document).width() - sidebarFilter.outerWidth();
        sidebarFilter.animate(
            {
                left: offset+'px'
            },
            300
        );
    });

    var hideFilter = function () {
        $('#sidebar-gray').fadeOut(500);
        $("#sidebar-filter").animate({left: '100%'}, 300);
        $('#filterPopover').hide();
    };

    $('#filter-hide-btn, #sidebar-gray, #filterPopover').on('click', hideFilter);
    $('#sidebar-filter .scrollable-container').on('click', function (e) {
        if (this == e.target) hideFilter();
    });

    function showFilterPopover() {
        var popover = $("#filterPopover");
        var foundStr = Translator.trans('found') +':&nbsp;<b>'+ EstateManager.data.totalFound + '</b>';
        var row = null;

        var facilitiesModalMessage = $('#facilities-modal .message');
        var facilitiesIsChanged = document.getElementById('map_filter_facilities') == filterChangedElement;

        if (facilitiesIsChanged) {
            row = $('.facilities-row');
        } else {
            row = $(filterChangedElement).parents('.row').eq(0);
        }

        popover.hide();
        popover.find('.popover-content').html(foundStr);

        facilitiesModalMessage.html('');
        if (facilitiesIsChanged)
            facilitiesModalMessage.html(foundStr);

        var topPosition  = Math.round( row.offset().top + row.outerHeight()/2 - $('#sidebar-filter').offset().top - popover.outerHeight()/2 + $('.scrollable-container').scrollTop());
        var rightPosition = Math.round( row.outerWidth() +12);
        popover.css('top', topPosition+'px');
        popover.css('right', rightPosition+'px');
        popover.fadeIn(500);
    }

    function clearForm(formContainer) {
        CommandFlags.lockFilterFormHandler = true;
        formContainer.find('input[type="checkbox"]').prop('checked',false);
        formContainer.find('select').val('').trigger('change');
        formContainer.find('input[type="text"]').val('');
        formContainer.find('.facilities-multiselect').multiSelect('deselect_all');
        CommandFlags.lockFilterFormHandler = false;
    }

    //Очистка формы
    $('#clear-btn').click(function () {
       clearForm($('form[name=map_filter]'));
    });

    //Подтверждение формы поиска
    $('#search-btn').click(function () {
        var searchParams = EstateManager.formToObject($('form[name=map_filter]'));
        EstateManager.getEstateList(
            searchParams,
            function () {
                Templating.renderEstateList(EstateManager.getByMapBorders());
                hideFilter();
            }
        );
    });

    $('form[name=map_filter]').on('change', 'input, select', null, function() {
        if (CommandFlags.lockFilterFormHandler) return false;
        var searchParams = EstateManager.formToObject($('form[name=map_filter]'));
        filterChangedElement = this;
        EstateManager.getEstateList(
            searchParams,
            function () {
                Templating.renderEstateList(EstateManager.getByMapBorders());
                showFilterPopover();
            }
        );
    });

    $('#sidebar-list-content').on('mouseenter mouseleave','.sidebar-list-item', null, function () {
        MapInterfaceControls.resetHoveredMarker();
        var curMarker = EstateManager.data.estateList[ $(this).data('estate-list-id') ].marker;
        if (event.type === 'mouseover') { //hover
            MapInterfaceControls.hoverMarker(curMarker);
            map.markerHovered = curMarker;
        }
        // if (event.type === 'mouseout') {}
    });

    //Filter form controls----------------------------------------------------------------------------------------------

    $('.build-year-choice').change(function () {
        if ($(this).val() == 'interval') {
            $('.build-year-interval-container').show()
        } else {
            $('.build-year-interval-container').hide()
        }
    });

    $('.build-year-min').change(function () {
        var startYear = parseInt($(this).val());
        if (startYear) {
            var endYear = (new Date()).getFullYear() + 9;
            var maxYearChoice = $('.build-year-max');
            var currentMaxYear = parseInt(maxYearChoice.val());

            maxYearChoice.html('<option value></option>');
            for (var year = startYear; year <= endYear; year++) {
                maxYearChoice.append('<option value="'+year+'">'+year+'</option>');
            }

            if (startYear < currentMaxYear) maxYearChoice.val(currentMaxYear);
        }
    });

    //TODO: Сделать показ видео по нажатию на видео кнопку в sidebar
    // $('#sidebar').on('click', '.sidebar-video-play', null,function (event) {
    //     if (!event) event = window.event;
    //     event.cancelBubble = true;
    //     if (event.stopPropagation) event.stopPropagation();
    //
    //     event.stopPropagation();
    //     $('#map-modal').modal('show');
    // });


});