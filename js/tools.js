$( document ).ready( function(){
    var headline = $(".tools");
    var myposition = headline.position();
    console.log( "test" );
    myposition.top += 21;
    $( ".tools" ).before(
        '<div class="calculator_dialog"><div class="calculator"></div></div>' +
        '<div class="translator_dialog"><div class="translator"><input class="translator_input" style="margin-right: 10";>' +
        '<button class="translator_button">translate</button><button class="translator_swap"></button>' +
        '<div><table class="result_table tablesorter" style="visibility: hidden"><thead></thead><tbody class="tbody"></tbody></table></div></div></div>' +
        '<div class="tools" style="position:absolute; top:' + myposition.top + '; left:900;">' +
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
    $( '.postit_img' ).on( 'click', function(){
        $.PostItAll.new({
            features: {
                savable : true
            }
        });
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

    $.fn.postitall.globals = {
    prefix          : '#PIApostit_',//Id note prefixe
    filter          : 'all',     //Options: domain, page, all
    savable         : true,        //Save postit in storage
    randomColor     : 1,         //Random color in new postits
    toolbar         : true,         //Show or hide toolbar
    autoHideToolBar : true,         //Animation efect on hover over postit shoing/hiding toolbar options
    removable       : true,         //Set removable feature on or off
    askOnDelete     : 0,         //Confirmation before note remove
    draggable       : true,         //Set draggable feature on or off
    resizable       : true,         //Set resizable feature on or off
    editable        : true,         //Set contenteditable and enable changing note content
    changeoptions   : true,         //Set options feature on or off
    blocked         : true,         //Postit can not be modified
    minimized       : true,         //true = minimized, false = maximixed
    expand          : true,         //Expand note
    fixed           : true,         //Allow to fix the note in page
    addNew          : true,         //Create a new postit
    showInfo        : 0,         //Show info icon
    pasteHtml       : 0,         //Allow paste html in contenteditor
    htmlEditor      : 0,         //Html editor (trumbowyg)
    autoPosition    : 0,         //Automatic reposition of the notes when user resize screen
    addArrow        : 'back'        //Add arrow to notes : none, front, back, all
};

    $.fn.postitall.defaults = {
    //Note properties
    id              : "",                       //Note id
    created         : Date.now(),               //Creation date
    domain          : window.location.origin,   //Domain in the url
    page            : window.location.pathname, //Page in the url
    osname          : navigator.appVersion,     //Browser informtion & OS name,
    content         : '',                       //Content of the note (text or html)
    position        : 'absolute',               //Position absolute or fixed
    posX            : '10px',                   //x coordinate (from left)
    posY            : '120px',                   //y coordinate (from top)
    right           : '15px',                       //x coordinate (from right). This property invalidate posX
    height          : 240,                      //Note total height
    width           : 180,                      //Note total width
    minHeight       : 240,                      //Note resizable min-width
    minWidth        : 180,                      //Note resizable min-height
    oldPosition     : {},                       //Position when minimized/collapsed (internal use)
    //Config note style
    style : {
        tresd           : true,                 //General style in 3d format
        backgroundcolor : '#FFFA3C',            //Background color in new postits when randomColor = false
        textcolor       : '#333333',            //Text color
        textshadow      : true,                 //Shadow in the text
        fontfamily      : 'verdana',            //Default font
        fontsize        : 'small',              //Default font size
        arrow           : 'none',               //Default arrow : none, top, right, bottom, left
    },
    //Enable / Disable features
    features : $.fn.postitall.globals,          //By default, copy of global defaults
    //Note flags
    flags : {
        blocked         : false,                //If true, the note cannot be edited
        minimized       : false,                //true = Collapsed note / false = maximixed
        expand          : false,                //true = Expanded note / false = normal
        fixed           : false,                //Set position fixed
        highlight       : false,                //Higlight note
    },
    //Attach the note to al html element
    attachedTo : {
        element         : '',                   //Where to attach
        position        : 'right',              //Position relative to elemente : top, right, bottom or left
        fixed           : true,                 //Fix note to element when resize screen
        arrow           : true,                 //Show an arrow in the inverse position
    },
    // Callbacks / Event Handlers
    onCreated: function(id, options, obj) {
        console.log( obj );
        //alert(JSON.stringify(obj))
        return undefined;
    },    //Triggered after note creation
    onChange: function (id, options, obj) {
        //alert(JSON.stringify(id))
        return undefined;
     },                  //Triggered on each change
    onSelect: function (id) { return undefined; },                  //Triggered when note is clicked, dragged or resized
    onDblClick: function (id) { return undefined; },                //Triggered on double click
    onRelease: function (id) { return undefined; },                 //Triggered on the end of dragging and resizing of a note
    onDelete: function (id) { return undefined; }                   //Triggered when a note is deleted
};

});
