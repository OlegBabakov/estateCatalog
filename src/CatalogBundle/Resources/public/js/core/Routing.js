/**
 * Created by oleg on 01.11.16.
 */

//Adding to common JsRouting bundle URL hash generate function
Routing.generateHash = function (route, params) {
    var hash = Routing.generate(route, params);
    return hash
        .replace('/app_dev.php','')
        .replace(/\/api\/v\d\//,'');
};