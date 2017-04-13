/**
 * Created by oleg on 09.12.16.
 */

$(function () {
    // Handlebars Extensions
    Handlebars.registerHelper('ifIn', function(elem, list, options) {
        if(list.indexOf(elem) > -1) {
            return options.fn(this);
        }
        return options.inverse(this);
    });

    Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
        switch (operator) {
            case '==':
                return (v1 == v2) ? options.fn(this) : options.inverse(this);
            case '===':
                return (v1 === v2) ? options.fn(this) : options.inverse(this);
            case '!=':
                return (v1 != v2) ? options.fn(this) : options.inverse(this);
            case '<':
                return (v1 < v2) ? options.fn(this) : options.inverse(this);
            case '<=':
                return (v1 <= v2) ? options.fn(this) : options.inverse(this);
            case '>':
                return (v1 > v2) ? options.fn(this) : options.inverse(this);
            case '>=':
                return (v1 >= v2) ? options.fn(this) : options.inverse(this);
            case '&&':
                return (v1 && v2) ? options.fn(this) : options.inverse(this);
            case '||':
                return (v1 || v2) ? options.fn(this) : options.inverse(this);
            default:
                return options.inverse(this);
        }
    });

    Handlebars.registerHelper('random', function (min, max, options) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    });

    Handlebars.registerHelper('round', function(value, options){
        return Math.round(value);
    });

    // Handlebars.registerHelper('round', function(value, c, d, t, options){  //value, c - precision, d - decimal separator, t - thousands separator
    //     var n = value,
    //         c = isNaN(c = Math.abs(c)) ? 2 : c,
    //         d = d == undefined ? "." : d,
    //         t = t == undefined ? "," : t,
    //         s = n < 0 ? "-" : "",
    //         i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
    //         j = (j = i.length) > 3 ? j % 3 : 0;
    //     return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    // });

    HandlebarsIntl.registerWith(Handlebars);
});