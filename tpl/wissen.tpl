<html>
	<head><title></title>
        {STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
        {JAVASCRIPTS}

    <script language="JavaScript">
    function filesearch() {
        f=open("dokument.php?P=1","File","width=900,height=650,left=200,top=100");
    }
    function go(was) {
        document.wissen.aktion.value=was;
        document.wissen.submit();
    }
    </script>
	{tiny}
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:knowhowdb:.</p>

<span style="position:absolute; left:1em; top:7em; width:95%; border: 0px solid black">
<!-- Hier beginnt die Karte  ------------------------------------------->
	<form name="wissen" action="wissen.php" method="post" onSubmit="return false">
	<div style="float:left; width:33%; text-align:left; border: 0px solid red" >
		<input type="hidden" name="id" value="{id}">
		<input type="hidden" name="m" value="{menuitem}">
		<input type="hidden" name="version" value="{version}">
		<input type="hidden" name="aktion" value="">
        <input type="hidden" name="kat" value="{m}">
		<strong><a href='wissen.php?m=0'>.:categories:.</a></strong><br>
		{menu}
	 	<br />
		{catinput}
		<span> .:newcat:. <br>.:below:. &quot;<b>{catname}</b>&quot;</span>
		<img src="image/neu.png" border="0" title=".:newcat:." align="middle" onClick="go('newcat')">
		<img src="image/edit_kl.png" border="0" title=".:editcat:." align="middle" onClick="go('editcat')">
        <br />
        <input type="text" name="wort" value="{notfound}"> 
		<img src="image/search.png" border="0" title=".:search:." align="middle" onClick="go('suche')">
	</div>
	<div style="float:left; width:65%; text-align:left; border: 0px solid blue; padding-left: 10px;" id="wdbfile">
		{headline}<br />
		<hr />
		{pre}{content}{post}<br />
		{button1} &nbsp; {button2}
	</div>
	</form>
<!-- Hier endet die Karte ------------------------------------------->
</span>
{END_CONTENT}
</body>
</html>
