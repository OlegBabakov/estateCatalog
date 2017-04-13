/**
 * Created by oleg on 29.10.16.
 */
//TODO: edit image, delete image, delete album

var Templating = {
    'data' : {
        cachedSidebarHtml : null,
        compiledTemplates : {
            MarkerInfowindow : null
        }
    },
    //Render album list
    'renderEstateList' : function(estateList) {
        var html = '';
        var templateHtml = $('#estate-list-template').html();
        var template = Handlebars.compile(templateHtml);

        var sidebarListWidth = $('#sidebar-list').outerWidth();
        if (sidebarListWidth >= 330) {
            var frameHeight = Math.floor(sidebarListWidth * 0.32);
        } else {
            var frameHeight = Math.floor(sidebarListWidth * 0.53);
        }

        for (var i in estateList) {
            estate = estateList[i];
            var frameId = 'frameScrub'+i;

            html += template({
                'estate'      : estate,
                'frameId': frameId,
                'frameHeight' : frameHeight,
                'url' : Routing.generate('estate_show', {
                    '_locale' : Routing.locale,
                    'id' : estate.id
                })
            });
        }

        if (html!=this.data.cachedSidebarHtml) {
            $('#sidebar-list-content').html(html);

            $('.lazy').lazy({
                afterLoad: function(element) {
                    // $(element).parent().parent().fadeIn(30);

                    $(".frameScrub").frameScrub({
                        verticalAlignment: 'middle'
                    });
                }
            });

            this.data.cachedSidebarHtml = html;
        }
    },
    'createMarkerInfoWindow' : function (estate) {
        if (!this.data.compiledTemplates.MarkerInfowindow) {
            var templateHtml = $('#marker-infowindow-template').html();
            this.data.compiledTemplates.MarkerInfowindow = Handlebars.compile(templateHtml);
        }

        return this.data.compiledTemplates.MarkerInfowindow({
            'data' : {
                intl: {
                    "locales": "en-US"
                }
            },
            'id': estate.id,
            'area': estate.area,
            'priceSell': estate.priceSell,
            'priceRent': estate.priceRent,
            'image': estate.image,
            'estate': estate,
            'url': Routing.generate('estate_show', {
                '_locale': Routing.locale,
                'id': estate.id
            })
        })
    }
};

