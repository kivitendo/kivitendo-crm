<?php
	require_once("inc/stdLib.php");
    $head = mkHeader();
?>
<html>
<head><title></title>
    <style>
    .progress { position:relative; width:400px; border: 1px solid #bbb; padding: 1px; border-radius: 3px; }
    .bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
    .percent { position:absolute; display:inline-block; top:3px; left:48%; }
    </style>
<?php echo $head['JQUERY']; ?>
<?php echo $head['JQUERYUI']; ?>
<?php echo $head['JQFILEUP']; ?>
<?php echo $head['JQWIDGET']; ?>
<?php echo $head['JQDATE']; ?>
<script>
$(document).ready(
$(function () {
    $('#fileupload').fileupload({
        dataType: 'json',
        add: function (e, data) {
            if( !data.files[0].name.match(/\.(tex)|(odt)|(swf)|(sxw)$/) ) {  //derzeit kein RTF
                alert ('Falsches Dateiformat');
                return false;
            }
            $('#uplfile').empty().append(data.files[0].name+' ');
            $('#uplfile').append(data.files[0].size+' ');
            $('#progress .bar').css('width','0%');
            $('#uplfile').append($('<button/>').text('Upload')
                                   .click(function () {
                                       $('#msg').empty().append('Uploading...');
                                       data.submit();
                                   })
            );
        },
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                if ( file.error != undefined ) { alert(file.error); return; };
                $('#uplfile').empty().append(file.name+' done');
                $.ajax({
                    url: 'jqhelp/serien.php',
                    dataType: 'json',
                    type: 'post',
                    data : { 'datum': $('#formdate').val(), 'subject': $('#subject').val(), 
                             'body': $('#body').val(), 'src': $('#src').val(), 
                             'filename':file.name, 'task': 'brief' },
                    success: function(rc){
                            if ( !rc.rc ) {
                                alert(rc.msg);
                                return;
                            } else {
                                f1=open('mkserdocs.php?src='+$('#src').val(),'SerDoc','width=600,height=100')
                            }
                    }
                })
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
            //$('#percent').replaceWith(progress+' %');
            
        },
    });
})
);
</script>
<script>    
        $(function() {
            $( "#formdate" ).datepicker($.datepicker.regional[ "de" ]);
        });
</script>    
</head>
<body>
Daten f&uuml;r den Serienbrief:<br />
<form name="serdoc" method="post">
<input type="hidden" name="src" id="src" value='<?php echo $_GET["src"]; ?>'>
Datum: <input type="text" name="formdate" id="formdate" size="12" value=""><br />
Betreff: <input type="text" name="subject" id="subject" size="30" value=""><br />
Zusatztext:<br />
<textarea name="body" id="body" cols="50" rows="8"></textarea><br />
Datei: <input id="fileupload" type="file" name="files[]" data-url="jqhelp/uploader.php">
</form>
<div id="progress" class="progress" >
    <div class="bar" id='bar' style="width: 0%;"></div>
    <!--div class='percent' id='percent'>0 %</div-->
</div>
<div id="uplfile"><div>
<div id="msg"><div>
</body>
</html>

