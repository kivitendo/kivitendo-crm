$(document).ready(function() {
    $(".tablesorter")
            .tablesorter({
		        theme : "jui",
		        headerTemplate : "{content} {icon}", //Wird von der Templateengine zerstört
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