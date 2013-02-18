<html>
    <head><title></title>
    {STYLESHEETS}
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css">
    <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
    {THEME}
    <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{JQUERY}jquery-ui/ui/jquery-ui.js"></script>
    {JAVASCRIPTS}
    <script language="JavaScript">
        function doInit() {
            {JS}
        }
        function sende() {
            to   = $("#TO").val();
            cc   = $("#CC").val();
            subj = $("#Subject").val();
            if (to == "" && cc == "") {
                alert (".:no_to:.");
                return false;
            }
            if (subj == "") {
                alert(".:no_subject:.");
                return false;
            }
            $("#aktion").val("sendmail");
            $("#mailform").submit();
        }
        function suchMail(wo) {
            doc=eval("document.mailform."+wo);
            val=doc.value;
            f1=open("suchMail.php?name="+val+"&adr="+wo,"suche","width=450,height=200,left=100,top=100");
        }
        function upload() {
            f1=open("mailupload.php","suche","width=350,height=200,left=100,top=100");
        }
        function sign(){
            var txt = $("#BodyText").val();
            var sig = "{Sign}".replace(/<br>/g,"\n");
            $("#BodyText").val(txt+"\n"+sig);
        }
        function setcur(textEl) {
            if(textEl.selectionStart || textEl.selectionStart == '0') {
                     textEl.selectionStart=0;
                     textEl.selectionEnd=0;
            }
        }
        function delTpl() {
            mid = $("#vorlagen option:selected").val();
            $.ajax({
               url: "jqhelp/mailvorlage.php?case=del&template="+mid,
    	       dataType: 'json',
    	       success: function(data){
                           if (data.rc == 'ok') {
                               $("#vorlagen option:selected").remove();
                               docreset();
                               alert('.:delete_ok:.');
                           } else {
                               alert('.:error_del:.');
                           }
                        }
               });

        }
        function saveTpl() {
            mid = $("#vorlagen option:selected").val();
            txt = $("#BodyText").val();
            sub = $("#Subject").val();
            $.ajax({
               url: "jqhelp/mailvorlage.php",
               type: 'POST',
               data: {case:"save", template:mid, subject:sub, bodytxt:txt },
               dataType: 'json',
    	       success: function(data){
                            if (data.rc > 0) {
                                if ( mid > 0 ) {
                                    $("#vorlagen option:selected").text(sub)
                                } else {
                                   $("#vorlagen").append('<option value="'+data.rc+'">'+sub+'</option>');
                                }
                            alert('.:datasave:.');
                            docreset();
                            } else {
                                alert('.:error:. .:save:.');
                            }
                        }
            });
        }
        function docreset() {
            document.mailform.KontaktTO.value = '';
            document.mailform.reset();
            sign();
        }
    </script>

    <script>
    $(document).ready(
        function(){
            $("#vorlagen").change(function(){
                var mid = $("#vorlagen option:selected").val();
                if (mid > 0) {
                    KontaktTO=$("#KontaktTO").value;
    	            $.ajax({
    	                url: "jqhelp/mailvorlage.php?case=get&template="+mid+"&to="+KontaktTO,
    	                dataType: 'json',
    	                success: function(data){
    		            $("#Subject").val(data.subject);
    		            $("#BodyText").val(data.bodytxt);
                            sign();
    	                }
    	            });
                } else {
                    docreset();
                }
            })
        });
        $.widget("custom.catcomplete", $.ui.autocomplete, {
            _renderMenu: function(ul,items) {
                var that = this,
                currentCategory = "";
                $.each( items, function( index, item ) {
                    if ( item.category != currentCategory ) {
                        ul.append( "<li class=\'ui-autocomplete-category\'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    that._renderItemData(ul,item);
                });
             }
         });    
        $(function() {
            $("#TO").catcomplete({
                source: "jqhelp/mailvorlage.php?case=mailsearch",
                minLength: '{acminlen}',
                delay: '{acdelay}',
                select: function(e,ui) {
                    $("#sto").click();
                }
            });
        });    
    </script>
    </head>
<body onLoad="sign();">
{PRE_CONTENT}
{START_CONTENT}
<!-- Beginn Code ------------------------------------------->
<p class="listtop">.:email:. .:send:. <font color="red">{Msg}</font></p>
<div id='contentbox2'>
<center>
<form name="mailform" id="mailform" action="mail.php" enctype='multipart/form-data' method="post" >
<table>
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<INPUT TYPE="hidden" name="QUELLE" value="{QUELLE}">
<INPUT TYPE="hidden" name="KontaktTO" id="KontaktTO" value="{KontaktTO}">
<INPUT TYPE="hidden" name="KontaktCC" id="KontaktCC" value="{KontaktCC}">
<INPUT TYPE="hidden" name="MID" value="{vorlage}">
<INPUT TYPE="hidden" name="aktion" id="aktion" value="">
<tr>
	<td class=" re" width="3em"></td>
	<td class=" re" width="*"></td>
	<td class=" re" width="*"></td>
</tr>
<tr>
	<td class="klein re">.:to:.:</td>
	<td class=""><input type="text" name="TO" id="TO" value="{TO}" size="70" maxlength="125" tabindex="1"> 
	   	     <input type="button" name="sto" value="suchen" onClick="suchMail('TO');" ></td>
	<td rowspan="7" class="le" style="vertical-align:middle;">
			<br><image src='image/mail-send.png' border='0' name="ok"  title=".:send:." onClick="sende();"><br>{btn}
			<br><br><image src='image/save_kl.png' border='0' style="visibility:{hide}" name="save" onClick="saveTpl();"  title=".:template:.
.:save:.">		<br><br><image src='image/eraser.png' border='0' style="visibility:{hide}" name="del" onClick="delTpl();" title=".:template:.
.:delete:.">
	</td>
</tr><tr>
	<td class="klein re" nowrap>B<input type="checkbox" name="bcc" value="1">CC:</td>
	<td class=""><input type="text" name="CC" id="CC" value="{CC}" size="70" maxlength="125" tabindex="2"> 
	   	     <input type="button" name="scc" value=".:search:." onClick="suchMail('CC');"></td>
</tr><tr>
	<td class="klein re">.:template:.:</td>
	<td class=""><select name="vorlagen" id="vorlagen" tabindex="3" style="width:44em;"> 
		<option value=""></option>
<!-- BEGIN Betreff -->
		<option value="{MID}">{CAUSE}</option>
<!-- END Betreff -->
	</select></td>
</tr><tr>
	<td class="klein re">.:subject:.:</td>
	<td class=""><input type="text" name="Subject" id="Subject" value="{Subject}" size="80" maxlength="125" tabindex="3"></td>
</tr><tr>
	<td class="klein re" valign="top">.:body:.:</td>
	<td class="">
	<textarea class="normal" name="BodyText" id="BodyText" cols="100" rows="14" tabindex="4" wrap="hard" onFocus="setcur(this);">{BodyText}</textarea>
	</td>
</tr><tr>
	<td class="klein re">.:file:.:</td>
	<td><input type="file" name="Datei[]" size="48" maxlength="125"></td>
</tr><tr>
	<td class="klein re">.:file:.:</td>
	<td><input type="file" name="Datei[]" size="48" maxlength="125"></td>
</tr><tr>
	<td class="klein re">.:file:.:</td>
	<td><input type="file" name="Datei[]" size="48" maxlength="125"></td>
</tr><tr>
	<td class="klein re"></td>
	<td><span id="rcmsg"></span></td>
</tr>

</form>
</table>
</center>
</div>
<!-- End Code ------------------------------------------->
{END_CONTENT}
</body>
</html>
