$( document ).ready( function(){

    var headline = $(".tools");
    var myposition = headline.position();
    myposition.top += 21;
    //var localStorage = <?php print_r( $_global );?>;
    //alert(JSON.stringify(kivi.global.baseurl));
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + '/';
    //alert( baseUrl );
    $( ".tools" ).before(
        '<div class="calculator_dialog"><div class="calculator"></div></div>' +
        '<div class="translator_dialog"><div class="translator"><input class="translator_input" style="margin-right: 10px";>' +
        '<button class="translator_button">translate</button><button class="translator_swap"></button>' +
        '<div><table class="result_table tablesorter" style="visibility: hidden"><thead></thead><tbody class="tbody"></tbody></table></div></div></div>' +
        '<div class="toolsbuttons" style="position:absolute; top:' + myposition.top + 'px; left:900px;">' +
        '<img src="' + baseUrl + 'crm/tools/rechner.png" class="calculator_img" title=".:simple calculator:.">' +
        '<img src="' + baseUrl + 'crm/tools/notiz.png" class="postit_img" title=".:postit notes:." style="margin-left: 20px;">' +
        '<img src="' + baseUrl + 'crm/tools/kalender.png" class="calendar_img" title=".:calendar:." style="margin-left: 20px;">' +
        '<img src="' + baseUrl + 'crm/tools/leo.png" class="translator_img " title="LEO .:english/german:." style="margin-left: 20px;"></div>'
    );

    var langpair = 'en|de';
    $( ".translator_swap" ).append('<img  src="' + baseUrl + '/crm/image/swap.gif" />').button().on( 'click', function(){
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

    //one Note on dblclick
    var clicks = 0, timer = null;
    $( '.postit_img' ).on( "click", function( e ){
        clicks++;
        if( clicks === 1 ){
            timer = setTimeout( function(){
                $.PostItAll.new({
                    features: {
                        savable : true
                    }
                });
                clicks = 0;
            }, 500 );
        }
        else{
            clearTimeout( timer );
            $.PostItAll.new({
                features: {
                    savable : true
                }
            });
            clicks = 0;
        }
    }).on("dblclick", function(e){
        e.preventDefault();  //cancel system double-click event
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
        window.location.href = baseUrl + "crm/calendar.phtml";
    });

    $( ".translator_img" ).on("click", function(){
        $( ".translator_dialog" ).dialog( "open" );
    });
});
