<html>
    <head><title></title>
    {STYLESHEETS}
    <link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}"></link>
    <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
    {JAVASCRIPTS}
    <script language="JavaScript">
    <!--
        function showlist(was) {
            uid=document.termedit.caluser.options[document.termedit.caluser.selectedIndex].value;
            mo=document.termedit.Monat.options[document.termedit.Monat.selectedIndex].value;
            ja=document.termedit.Jahr.options[document.termedit.Jahr.selectedIndex].value;
            tg=document.termedit.Tag.options[document.termedit.Tag.selectedIndex].value;
            Termine.location.href="termlist.php?cuid="+uid+"&ansicht="+was+"&datum="+tg+"."+mo+"."+ja; //month="+mo+"&year="+ja+"&day="+tg;
        }
        function suchName() {
            f=open("suchName.php?name="+document.termedit.suchname.value,"Name","width=400,height=200,left=200,top=100");
        }
        function saveT() {
            selall();
            document.termedit.sichern.value=1;
            document.termedit.submit();
        }
        function searchT() {
            tid = document.termedit.caluser.options[document.termedit.caluser.selectedIndex].value;
            document.termedit.uid.value=tid;
            document.termedit.search.value=document.termedit.cause.value;
            document.termedit.submit();
        }
        function subusr() {
            nr=document.getElementById("istusr").selectedIndex;
            document.getElementById("istusr").options[nr]=null
        }
        function addusr() {
            nr=document.termedit.teiln.selectedIndex;
            val=document.termedit.teiln.options[nr].value;
            txt=document.termedit.teiln.options[nr].text;
            NeuerEintrag = new Option(txt,val,false,true);
             document.getElementById("istusr").options[document.getElementById("istusr").length] = NeuerEintrag;
        }
        function selall() {
            len=document.getElementById("istusr").length;
            document.getElementById("istusr").multiple=true;
            for (i=0; i<len; i++) {
                document.getElementById("istusr").options[i].selected=true;
            }
        }
        function kal(fld) {
            mo=document.termedit.Monat.options[document.termedit.Monat.selectedIndex].value;
            ja=document.termedit.Jahr.options[document.termedit.Jahr.selectedIndex].value;
            f=open("terminmonat.php?datum=01."+mo+"."+ja+"&fld="+fld,"Name","width=380,height=385,left=200,top=100");
        }
        function init() {
            /*
            document.termedit.Tag.options[{TT}-1].selected=true;
            document.termedit.Monat.options[{MM}-1].selected=true;
            for (i=0; i<document.termedit.Jahr.length; i++) {
                if (document.termedit.Jahr.options[i].value=={YY})
                    document.termedit.Jahr.options[i].selected=true;
            }*/
            showlist("T");
        }
    //-->
    </script>
    <script type='text/javascript' src='inc/help.js'></script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<!-- Beginn Code ------------------------------------------->
<p class="listtop" onClick="help('Termine');">Termine (?)</p>
<font color="red">{Msg}</font>
<table>
<form name="termedit" action="termin.php" method="post" >
<input type="hidden" name="uid" value="{uid}">
<input type="hidden" name="search" value="">
<input type="hidden" name="sichern" value="">
<tr>
    <td width="*" valign="top">
        {OK}
        <!--input type="button" value="Zeige" onClick="showlist('T')"><hr-->
        <table>
            <tr><td >.:by:.:</td><td ><input type="text" name="vondat" size="9" maxlength="10" value="{VONDAT}">
                                      <a href="#" title='Vondatum' onClick="kal('vondat')" ><img src='image/date.png' align='middle' border="0"></a>
<!--input type="button" value="K" onClick="kal('vondat')"-->
                    <select name="von">
<!-- BEGIN Time1 -->
                        <option value="{tval1}"{tsel1}>{tkey1}</option>
<!-- END Time1 -->
                    </select>
                    <select name="repeat">
<!-- BEGIN repeat -->
                        <option value="{RPTK}"{RPTS}>{RPTV}</option>
<!-- END repeat -->
                    </select>
                </td></tr>
            <tr><td >.:till:.:</td><td ><input type="text" name="bisdat" size="9" maxlength="10" value="{BISDAT}">
                                        <a href="#" title='Bisdatum' onClick="kal('bisdat')" ><img src='image/date.png' align='middle' border="0"></a>
<!--input type="button" value="K" onClick="kal('bisdat')"-->
                    <select name="bis">
<!-- BEGIN Time2 -->
                        <option value="{tval1}"{tsel2}>{tkey1}</option>
<!-- END Time2 -->
                    </select>
                    <span class="klein">nur Arbeitstage</span><input type="checkbox" name="ft" value="1" {FT}>
                </td></tr>
            <tr><td colspan="2"><input type="text" name="cause" size="35" maxlength="75" value="{GRUND}">
                         &nbsp;&nbsp;
                         <input type="button" class="anzeige" value=".:search:." onClick="searchT()"><br />
                         <!--input type="submit" class="anzeige" name="search" value=".:search:."><br /-->
                        <span class="klein">.:cause:.</span></td></tr>
            <tr><td colspan="2"><input type="text" name="location" size="35" maxlength="75" value="{LOCATION}"><br />
                <span class="klein">.:location:.</span></td></tr>
            <tr>
                <td>.:private date:.: </td> 
                <td><input type="checkbox" name="privat" value="1" {CHKPRIVAT}> .:categorie:.: <select name="kategorie">
<!-- BEGIN Kat -->
                        <option value="{catid}"{catsel}>{catname}</option>
<!-- END Kat -->
                    </select>
            </tr>
            <tr><td colspan="2"><textarea name="c_cause" cols="60" rows="4">{LANG}</textarea>
                        <br><span class="klein">.:remarks:.</span>
            <tr><td colspan="2">
                    <input type="text" name="suchname" size="20" maxlength="25" value=""><input type="button" value=".:search:. .:member:." onClick="suchName()">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" class="sichern" name="sich" value=".:save:." onClick="saveT()"></td></tr>
            <tr><td colspan="2"><table>
                    <td ><select name="teiln" id="kannusr" size="5">
<!-- BEGIN User -->
                        <option value="{USRID}">{USRNAME}</option>
<!-- END User -->
                    </select><br><span class="klein">CRM-User</span></td>
                    <td ><input type="button" value="&lt;--" onClick="subusr()" title="entfernen"><br>
                            <br><input type="button" value="--&gt;" onClick="addusr()" title="dazunehmen"></td>
                    <td ><select name="user[]" id="istusr" size="5">
<!-- BEGIN Selusr -->
                        <option value="{UID}">{UNAME}</option>
<!-- END Selusr -->
                    </select><br><span class="klein">.:member:.</span></td>

                    </tr></table><br />
                    <a href="termin.php"><input type="reset" class="clear" name="clear" value=".:clear:."></a>
            </td></tr>
        </table>
    </td>
    <td width="5em"></td>
    <td width="*" class="li">
            <a href="#" title='suchfld' onClick="kal('suchfld')" ><img src='image/date.png' align='middle' border="0"></a>
        <select name="Tag" style="width:4em">
<!-- BEGIN Tage -->
            <option value="{TV}"{TS}>{TK}</option>
<!-- END Tage -->
        </select>
        <select name="Monat" style="width:8em">
<!-- BEGIN Monat -->
            <option value="{MV}"{MS}>{MK}</option>
<!-- END Monat -->
        </select>
        <select name="Jahr" style="width:5em">
<!-- BEGIN Jahre -->
            <option value="{JV}"{JS}>{JK}</option>
<!-- END Jahre -->
        </select>
            <br />
            <select name="caluser" id="calusr" size="1">
<!-- BEGIN CalUser -->
            <option value="{CUID}" {CUIDSEL}>{CUNAME}</option>
<!-- END CalUser -->
        </select>
        <input type="button" class="anzeige" value=".:show:." onClick="showlist('T')"><br />
        <!--input type="button" value="Woche" onClick="showlist('W')">
        <input type="button" value="Monat" onClick="showlist('M')"-->
        <iframe src="termlist.php?cuid={uid}&ansicht={ANSICHT}&datum={DATUM}" name="Termine" style="width:40em; height:38em" marginheight="0" marginwidth="0" align="left">
            <p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
        </iframe>
    </td>
</tr>
<input type="hidden" name="tid" value="{TID}">
</form>
</table>
</center>
<!-- End Code ------------------------------------------->
<!--/td></tr></table-->
{END_CONTENT}
</body>
</html>
