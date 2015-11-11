$(document).ready(function() {
    $(".tablesorter")
    .tablesorter({
        theme : "jui",
        headerTemplate : "{content} {icon}",
        widgets : ["uitheme", "zebra"],
        widgetOptions : {
            zebra   : ["even", "odd"]
        }
    }).tablesorterPager({
        container: $(".pager"),
        size: 20,
        positionFixed: false
    });
});