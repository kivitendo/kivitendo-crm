$( document ).ready( function(){
    $( ".tools" ).before(
        '<div class="calculator_dialog"><div class="calculator"></div></div>' +
        '<div class="postit_dialog"><div class="postit">Notizblock</div></div>' +
        '<div class="translator_dialog"><div class="translator"><input class="translator_input" style="margin-right: 10";>' +
        '<button class="translator_button">translate</button><button class="translator_swap"></button>' +
        '<div><table class="result_table tablesorter" style="visibility: hidden"><thead></thead><tbody class="tbody"></tbody></table></div></div></div>' +
        '<div class="tools" style="position:absolute; top: +20; left:900;">' +
        '<img src="tools/rechner.png" class="calculator_img" title=".:simple calculator:.">' +
        '<img src="tools/notiz.png" class="postit_img" title=".:postit notes:." style="margin-left: 20;">' +
        '<img src="tools/kalender.png" class="calendar_img" title=".:calendar:." style="margin-left: 20;">' +
        '<img src="tools/leo.png" class="translator_img" title="LEO .:english/german:." style="margin-left: 20;"></div>'
    );
    var langpair = 'en|de';
    $( ".translator_swap" ).append('<img  src="image/swap.gif" />').button().on( 'click', function(){
        var title = $( ".translator_dialog" ).dialog( "option", "title"  );
        $( ".translator_dialog" ).dialog( "option", "title", title ==  'Übersetzer en -> de' ? 'Translator de -> en' : 'Übersetzer en -> de');
        langpair = langpair == 'en|de' ? 'de|en' : 'en|de';
    }) ;
    $( '.translator_input' ).addClass( "ui-widget ui-widget-content ui-corner-all" ).keypress( function( e ){
        if( e.which == 13 ) $( '.translator_button' ).click();
    });
    $( '.translator_button' ).button().on( 'click', function(){
        $( ".tbody" ).empty();
        $.getJSON( 'http://mymemory.translated.net/api/get?q=' + $( '.translator_input' ).val() + '&langpair=' + langpair, function( data ) {
            $.each( data.matches, function( index, value ){
                $( ".tbody" ).append( "<tr><td style='font-size: 0.88em';background-color: #abc; >" +  value.segment + "</td><td style='font-size: 0.88em'>" + value.translation + "</td></tr>" );
            });
        }).done( function(){ $( '.result_table' ).css('visibility', 'visible').tablesorter().trigger( 'update' ) });
    });
    $( ".calculator" ).calculator({
        useThemeRoller: true,
        layout: $.calculator.scientificLayout,
    });
    $( ".calculator_dialog" ).dialog({
        autoOpen: false,
        title: 'Calcutator',
        width: "360px",
        resizable: false
    });
    $( ".postit_dialog" ).dialog({
        autoOpen: false,
        title: 'Post it!',
        width:'auto',
        resizable: false
    });
    $( ".translator_dialog" ).dialog({
        autoOpen: false,
        title: 'Übersetzer en -> de',
        width:'390px',
        resizable: false
    });
    $( ".calculator_img" ).on("click", function(){
        $( ".calculator_dialog" ).dialog( "open" );
    });
    $( ".postit_img" ).on( "click", function(){
        $( ".postit_dialog" ).dialog( "open" );
    });
    $( ".calendar_img" ).on("click", function(){
        window.location.href = "calendar.phtml";
    });
    $( ".translator_img" ).on("click", function(){
        $( ".translator_dialog" ).dialog( "open" );
    });
});