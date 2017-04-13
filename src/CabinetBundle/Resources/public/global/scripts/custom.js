/**
 * Created by oleg on 14.12.16.
 */

$(function () {
    //Инициализация карты при переключении таба
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) { //переключение табов
        var target = $(this).data('tab-target');
        if (target == 'address-map') addressMap.handleGoogleMapApiReady();
    });

});