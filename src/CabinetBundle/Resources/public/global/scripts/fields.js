/**
 * Created by oleg on 14.12.16.
 */

//Кастомизация полей ввода
$(function () {

    $('.select2').select2({
        'width': '100%'
    });

    $('.multiselect').multiSelect({
        'width': '100%'
    });

    $.fn.datepicker.dates['qtrs'] = {
        days: ["Sunday", "Moonday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
        daysShort: ["Sun", "Moon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
        months: ["Q1", "Q2", "Q3", "Q4", "", "", "", "", "", "", "", ""],
        monthsShort: ["Jan&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Feb&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mar", "Apr&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;May&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jun", "Jul&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aug&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sep", "Oct&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nov&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dec", "", "", "", "", "", "", "", ""],
        today: "Today",
        clear: "Clear",
        format: "mm/dd/yyyy",
        titleFormat: "MM yyyy",
        /* Leverages same syntax as 'format' */
        weekStart: 0
    };

    $('.datepicker-quarter').datepicker({
        format: "MM yyyy",
        minViewMode: 1,
        autoclose: true,
        language: "qtrs",
        forceParse: false
    }).on("show", function(event) {
        $(".month").each(function(index, element) {
            if (index > 3) {
                $(element).hide()
            } else {
                $(element).css('width', '100%');
            }
        });
        // $(this).css('top', $(this).css('top'));
    });



});
