<html>
	<head><title></title>
{STYLESHEETS}
{JAVASCRIPTS}
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css">
    <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
    <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{JQUERY}jquery-ui/ui/jquery-ui.js"></script>
    {tiny}
    <script language="JavaScript">
{init}
    var savehg  = -1;
    var savekat = -1;
    function filesearch() {
        f=open("dokument.php?P=1","File","width=900,height=650,left=200,top=100");
        f.pickup = true;
    }
    function showcontent( data ) { 
        $('#headline').empty().append('[<b>'+data.name+'</b>] '+' Version: <b>'+data.version+'</b> vom <b>'+data.datum+'</b> durch <b>'+data.employee+'</b>');
        $('#wissencontent').empty().append(data.content);
        $("#owener option[value='"+data.owener+"']").attr('selected',true);
        $('#id').val(data.id);
        $('#kat').val(data.categorie);
        showSel();
    }
    function editContent( editor ) {
        id = $('#kat').val();
        if ( editor ) {
            showEdit();
        } else {
            showSel();
        }
        $.get('jqhelp/wissen.php?task=edit&id='+id+"&edit="+editor, function( content ) {
                $('#wissencontent').empty().append(content);
        } );
    }
    function newContent() { 
        $.get('jqhelp/wissen.php?task=neu', function( content ) {
            $('#headline').empty().append('[<b>'+$('#catname').text()+'</b>] Neu');
            $('#wissencontent').empty().append(content);
            showEdit();
            
        } );
    }
    function saveContent() {
        id = $('#kat').val();
        if ( tiny ) {
            content = tinyMCE.activeEditor.contentDocument.body.innerHTML;
        } else {
            content = $('#elm1').val();
        }
        own = $('#owener option:selected').val();
        $.ajax( { 
            url: 'jqhelp/wissen.php?task=savecontent',
            data: { 'content':content, 'kat':id,'owener':own },
            dataType: 'json',
            type: 'post',
            success: function(rc) {
                 if ( rc.cnt == 0) {
                     alert(rc.msg);
                 } else {
                     showcontent(rc);
                 }
            }
        } );
    }

    function history() {
        var id = $('#kat').val();
        var v1, v2, cnt = 0 ;
        $("input[type=checkbox][name=diff]").each( function( ) { 
              if ( $('#'+this.id).attr('checked') == 'checked' ) {
                  cnt++;
                  if ( cnt == 1) v1 = $('#'+this.id).val();
                  if ( cnt == 2) v2 = $('#'+this.id).val();
              }
        } );
        $.get('jqhelp/wissen.php?task=history&id='+id+"&v1="+v1+"&v2="+v2, function( content ) {
                $('#wissencontent').empty().append(content);
        } );
    }
    function suche() {
        wort = $('#wort').val();
        kat  = $('#kat').val();
        $.ajax( { 
            url: 'jqhelp/wissen.php?task=suche&wort='+wort+'&kat='+kat,
            dataType: 'json',
            success: function(rc) {
                if ( rc.cnt == 0 ) {
                    $('#wort').val(rc.msg);
                    $('#headline').empty();
                    $('#wissencontent').empty();
                    showNew();
                } else {
                   if ( rc.cnt  == 1 ) {
                       showcontent( rc );
                   } else {
                       $('#headline').empty().append('Trefferliste');
                       $('#kat').val(-1);
                       $('#neu').hide();
                       $('#history').hide();
                       $('#wissencontent').empty().append('<ul>');
                       $.each(rc.data, function( i, row ) {
                          $('#wissencontent').append( '<li onClick="getFromList('+row.id+');">'+row.id + " " + row.name + "</li>");
                       })
                       $('#wissencontent').append('</ul>');
                   }
                }
            }
        } ) 
    }
    function getFromList( id ) {
        $('#kat').val(id);
        $('#wort').val('');
        suche();
    }
    function hidenewCat() {
        $('#savecat').hide();
        $('#catedit').hide('fast');
        $('#hidenewcat').hide('fast');
        $('#newcat').show();
        $('#editcat').show();
        if ( savehg  != -1 ) $('#hg').val( savehg );
        if ( savekat != -1 ) $('#kat').val( savekat );
    }
    var path = new Array();
    function openpath( hg ) {
        var subhg = $('#'+hg).attr('class');
        path.push(subhg);
        while ( subhg != 'sub0' ) {
            subhg = $('#'+subhg.substr(3)).attr('class');
            path.push(subhg);
        }
        while ( path.length > 0 ) {
            x = path.pop();
            y = "."+x;
            $('.'+x).slideDown("fast");
        }
    }
    function saveCat() {
            var newcat = $('#catneu').val();
            var hg = $('#hg').val();
            var cid = $('#kat').val()
            var kdhelp = ( $('#kdhelp').is(':checked') )?1:0;
            $.get('jqhelp/wissen.php?task=newcat&catname='+newcat+'&hg='+hg+'&kdhelp='+kdhelp+'&cid='+cid, function( rc ) {
                if ( rc != '0' ) {
                    hidenewCat();
                    mkMenu(rc);
                    savehg = -1;
                    savekst = -1;
                } else {
                    alert(rc);
                }
            });
    }
    function newCat() {
        savehg  = $('#hg').val();
        savekat = $('#kat').val();
        $('#newcat').hide();
        $('#editcat').hide();
        $('#savecat').show();
        $('#hg').val($('#kat').val()); 
        $('#kat').val('');
        $('#catedit').show('fast');
        $('#hidenewcat').show('fast');
    }
    function editCat() {
        $('#catneu').val($('#catname').text());
        if ( $('#'+$('#kat').val()).attr('name') == ' +' ) {
            $('#kdhelp').attr ('checked', true);
        } else {  
            $('#kdhelp').attr ('checked', false);
        }
        $('#newcat').hide();
        $('#editcat').hide();
        $('#savecat').show();
        $('#catedit').show('fast');
        $('#hidenewcat').show('fast');
    }
    function checkForEnterSuche(event) {
        if (event.keyCode == 13) {
            $('#wortsuche').click();
        }
    }
    function toggleMenu( hg, id ) {
        $('#kat').val(id);
        $('#hg').val(hg);
        $('#wort').val('');
        $('#catname').empty().append($('#'+id).text());
        $('#editcat').show();
        suche(); 
        if ( $('.sub'+id).is(':hidden') ) {
            $('.sub'+id).slideDown("slow");
        } else {
            $('.sub'+id).hide();
        }
    }
    function mkMenu(fold) {
        $('#headline').empty();
        $('#wissencontent').empty();
        $('#reload').hide();
        $('#savecontent').hide();
        $('#filesearch').hide();
        $('#history').hide();
        $.get('jqhelp/wissen.php?task=getmenu', function( data ) {
            $('#wdbmenu').empty().append( data );
            $.each($('#wdbmenu').find('ul'), function ( i, sub ) {
                if ( sub.getAttribute('name') == 'submenu') $(this).hide(); 
            }) ;
            if ( fold != -1 ) {
                openpath( fold );
                $('#'+fold).click();
            } else {
                $('#kat').val(0);
                $('#hg').val(0);
                $('#catname').empty().append('/');
            };
        })
    }
    </script>
    <script>
    $(function() {
        $("img")
          .button()
          .click(function( event ) {
              event.preventDefault();
              if (this.id == 'filesearch') {
                  filesearch();
                  return;
              } else if (this.id == 'wortsuche') {
                  suche();
              } else if (this.id == 'newcat') {
                  newCat();
              } else if (this.id == 'editcat') {
                  editCat();
              } else if (this.id == 'savecat') {
                  saveCat();
              } else if (this.id == 'hidenewcat') {
                  hidenewCat(false);
              } else if (this.id == 'neu') {
                  newContent(1);
              } else if (this.id == 'edit') {
                  editContent(1);
              } else if (this.id == 'reload') {
                  editContent(0);
              } else if (this.id == 'savecontent') {
                  saveContent();
              } else if (this.id == 'history') {
                  history();
              }
         });
        if ($.browser.mozilla) {
            $('#wort').keypress(checkForEnterSuche);
        } else {
            $('#wort').keydown(checkForEnterSuche);
        }
    });
    function showNew() {
        $('#edit').hide();
        $('#neu').show();
        $('#reload').hide();
        $('#savecontent').hide();
        $('#filesearch').hide();
        $('#history').hide();
        $('#auth').hide();
    }
    function showSel() {
        $('#edit').show();
        $('#neu').hide();
        $('#reload').show();
        $('#savecontent').hide();
        $('#filesearch').hide();
        $('#history').show();
        $('#auth').hide();
    }
    function showEdit() {
        $('#edit').hide();
        $('#neu').hide();
        $('#reload').show();
        $('#savecontent').show();
        $('#filesearch').show();
        $('#auth').show();
    }
    $(document).ready( function() {
        mkMenu(initkat);
        hidenewCat();
        showNew();
        $('#neu').hide();
        $('#editcat').hide();
        $('#wort').focus( function() {
            $('#wort').val('');
        });
    }) ;
    </script>
<body >
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:knowhowdb:.</p>

<span style="position:absolute; left:1em; top:7em; width:95%; border: 0px solid black">
	<div style="float:left; width:33%; text-align:left; border: 0px solid red" >
        <strong onClick='mkMenu(0);'>.:categories:.</strong><br>
        <form name="wissen" id='wissen' onSubmit='false'>
            <input type="hidden" id="id"      name="id"      value="">
            <input type="hidden" id="kat"     name="kat"     value="">
            <input type="hidden" id="hg"      name="hg"     value="">
            <span id='wdbmenu'></span><br />
            <span id='popup' style='visibility:{popup};'>
        	<span id='catedit'>
                <input type="text" name="catneu" id="catneu" size=""> <input type="checkbox" name="kdhelp" id="kdhelp" val="1">
            </span>
            <span><br /> .:newcat:. <br>.:below:. &quot;<b><span id='catname'></span></b>&quot;<br /></span>
            <img src='image/save_kl.png' border="0" title='.:save:.'    align="middle" id='savecat'>
		    <img src="image/neu.png"     border="0" title=".:newcat:."  align="middle" id='newcat'>
            <img src="image/edit_kl.png" border="0" title=".:editcat:." align="middle" id='editcat'>
            <img src="image/cancel_kl.png" border="0" title=".:normview:." align="middle" id='hidenewcat'>
            </span>
            <br />
            <br />
            <input type="text" name="wort" id="wort" value="">
		    <img src="image/search.png" border="0" title=".:search:." align="middle" id='wortsuche'><br>
    </div>
	<div style="float:left; width:65%; text-align:left; border: 0px solid blue; padding-left: 10px;" id="wdbfile">
		<span id='headline'></span><br />
		<hr />
		<span id='wissencontent'></span><br />
		<br />
        <img src='image/edit_kl.png'    title='.:edit:.'            id='edit'>
        <img src='image/neu.png'        title='.:new:. .:article:.' id='neu'>
        <img src='image/cancel_kl.png'  title='.:normview:.'        id='reload'>
        <img src='image/save_kl.png'    title='.:save:.'            id='savecontent'>
        <img src='image/file_kl.png'    title='.:picfile:.'         id='filesearch'>
        <img src='image/history_kl.png' title='History'             id='history'>
		<span id="auth" class="klein">.:authority:.
            <select name="owener" id="owener">
<!-- BEGIN OwenerListe -->
                <option value="{OLid}" {OLsel}>{OLtext}</option>
<!-- END OwenerListe -->
            </select> </span> 
	</div>
	</form>
</span>
{END_CONTENT}
</body>
</html>
