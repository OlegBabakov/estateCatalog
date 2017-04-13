/**
 * Created by oleg on 07.09.16.
 */

$(function(){

    $('.multiselect').multiSelect({
        'width': '100%'
    });

    $.fn.select2.defaults.set( "theme", "bootstrap" );
    $( ".select2" ).select2({
        'width': '100%'
        // theme: "bootstrap"
    });




    // $('.spinner').spinner({
    //     step: 0.01,
    //     numberFormat: "n"
    // });

});
