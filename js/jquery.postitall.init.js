function rgb2hex( rgb ){
    rgb = rgb.match( /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/ );
    function hex( x ){
        return( "0" + parseInt( x ).toString( 16 ) ).slice( -2 );
    }
    return "#" + hex( rgb[1] ) + hex( rgb[2] ) + hex( rgb[3] );
}

$.fn.postitall.globals = {
    prefix          : '#PIApostit_',//Id note prefixe
    filter          : 'all',     //Options: domain, page, all
    savable         : true,        //Save postit in storage
    randomColor     : 0,         //Random color in new postits
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
    showInfo        : 0,            //Show info icon
    pasteHtml       : 1,            //Allow paste html in contenteditor
    htmlEditor      : 0,            //Html editor (trumbowyg) doesn't work! css-error
    autoPosition    : 1,            //Automatic reposition of the notes when user resize screen
    addArrow        : 'back'        //Add arrow to notes : none, front, back, all
};


$.fn.postitall.defaults = {
    //Note properties
    id              : "",                       //Note id
    created         : Date.now(),               //Creation date
    domain          : '',                       //Domain in the url
    page            : '',                       //Page in the url
    osname          : '',                       //Browser informtion & OS name,
    content         : '',                       //Content of the note (text or html)
    position        : 'absolute',               //Position absolute or fixed
    posX            : '',                       //x coordinate (from left)
    posY            : '',                       //y coordinate (from top)
    right           : '',                       //x coordinate (from right). This property invalidate posX
    height          : 240,                      //Note total height
    width           : 180,                      //Note total width
    minHeight       : 240,                      //Note resizable min-width
    minWidth        : 180,                      //Note resizable min-height
    oldPosition     : {},                       //Position when minimized/collapsed (internal use)
    //Config note style
    style : {
        tresd           : true,                 //General style in 3d format
        backgroundcolor : rgb2hex( $('.ui-state-highlight').css('background-color') ), //Background color in new postits when randomColor = false
        textcolor       : '#0a0e87',            //Text color
        textshadow      : 0,                    //Shadow in the text
        fontfamily      : 'verdana',            //Default font
        fontsize        : '13px',               //Default font size
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
        element         : '.tools',             //Where to attach
        position        : 'bottom',             //Position relative to elemente : top, right, bottom or left
        fixed           : true,                 //Fix note to element when resize screen
        arrow           : true,                 //Show an arrow in the inverse position
    },
    // Callbacks / Event Handlers
    onCreated: function( id, options, obj ) { return undefined; }, //Triggered after note creation
    onChange:  function( id, options, obj ) { return undefined; }, //Triggered on each change
    onSelect:  function( id ) { return undefined; },               //Triggered when note is clicked, dragged or resized
    onDblClick:function( id ) { return undefined; },               //Triggered on double click
    onRelease: function( id ) { return undefined; },               //Triggered on the end of dragging and resizing of a note
    onDelete:  function( id ) { return undefined; }                //Triggered when a note is deleted
};
