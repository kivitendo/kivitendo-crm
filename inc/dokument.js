    var file = "";
    function mkDir() {
        seite = $('#seite').val();
        name  = $('#subdir').val();
        $.get('jqhelp/firmaserver.php?task=newDir&pfad='+pfadleft+'&newdir='+name,function(rc) {
                 if (rc == 'ok') {
                     dateibaum('left',pfadleft);
                     newDir();
                 } else {
                     alert(rc);
                 }
             });
    }
    var downloadfile = "";
    function download() {
        downloadfile=open("download.php?file="+aktfile,"Download","width=250px,height=200px,top=50px,menubar=no,status=no,toolbar=no,dependent=yes");
        window.setTimeout("downloadfile.close()", 30000);
    }
    var onA = true;
    var onL = true;
    function editattribut() {
        if (onA) {
            onA = false;
            $('#attribut').hide();
        } else {
            onL = false;
            $('#fileDel').hide();
            onA = true;
            $('#attribut').show();
        }
    }
    function saveAttribut() {
        name    = $('#docname').val();
        oldname = $('#docoldname').val();
        pfad    = $('#docpfad').val();
        komment = $('#docdescript').val();
        id      = $('#docid').val();
        $.ajax({
               url: "jqhelp/firmaserver.php",
               data: { 'task':'saveAttribut', 'name':name, 'oldname':oldname, 'pfad':pfad, 'komment':komment, 'id':id },
               dataType: 'json',
               success: function(data) { 
                   if ( data.rc > 0 ) {
                       editattribut();
                       dateibaum('left',pfadleft);
                       showFile('left',name);
                   } else {
                       alert('Error');
                   }
               }
        });
    }
    function deletefile() {
        if (onL) {
            onL = false;
            $('#fileDel').hide();
        } else {
            onA = false;
            $('#attribut').hide();
            onL = true;
            name = $('#docname').val();
            $('#delname').empty().append(name);
            $('#fileDel').show();
        }
    }
    function movefile() {
        $.ajax({
               url: 'jqhelp/firmaserver.php?task=moveFile&file='+aktfile+'&pfadleft='+pfadleft,
               dataType: 'json',
               success: function(data) { 
                   if (data.rc == 0) { alert('Error'); }
                   else {
                       dateibaum('left',pfadleft);
                       dateibaum('right',pfadright);
                       aktfile = '';
                   } 
               }
        });

    }
    function filedelete() {
        id   = $('#docid').val();
        name = $('#docname').val();
        pfad = $('#docpfad').val();
        if (!id) id = 0;
        $.get('jqhelp/firmaserver.php?task=delFile&id='+id+'&pfad='+pfad+'&file='+name,function(data) { 
                 if (data == 'ok' ) {
                     dateibaum('left',pfadleft);
                     dateibaum('right',pfadright);
                     deletefile();
                 } else {
                     alert(data);
                 }
              });
    }
    var onD = true;
    function newDir(seite) {
        if (onD) {
            onD = false;
            $('#newwindir').hide();
        } else {
            onD = true;
            $('#newwindir').show();
            $('#subdir').focus();
            $('#seite').val(seite);
        }
    }
    var onF = true;
    function newFile(seite) {
        if (onF) {
            onF = false;
            $('#uploadfr').hide();
        } else {
            onF = true;
            $('#seite').val(seite);
            $('#uploadfr').show();
            frames["frupload"].document.getElementById("upldpath").value=pfadleft;
            frames["frupload"].document.getElementById("caption").focus();
       }
    }
    var pfadleft = "";
    var pfadright = "";
    function showFile(seite,file) {
        $.ajax({
               url: "jqhelp/firmaserver.php",
               data: {'task':'showFile','id':seite,'file':file, 'pfad':(seite=="left")?pfadleft:pfadright},
    	       dataType: 'json',
    	       success: function(data){
                   aktfile = data.docpfad+"/"+data.docname;
                   $('#docname').val(data.docname);
                   $('#docoldname').val(data.docoldname);
                   $('#docpfad').val(data.docpfad);
                   $('#docid').val(data.docid);
                   $('#docdescript').empty().append(data.docdescript);
                   $('#fbright').empty().append(data.fbright);
                   showlinks();
               }
        })
    }
    function lockFile() {
        id   = $('#docid').val();
        name = $('#docname').val();
        pfad = $('#docpfad').val();
        $.get('jqhelp/firmaserver.php?task=lockFile&id='+id+'&pfad='+pfad+'&file='+name,function(rc) { 
                 if (rc == 'lock' || rc == 'unlock') {
                     //dateibaum('left',pfadleft);
                     showFile('left',name);
                 } else {
                     alert(rc);
                 }
             });        
    }
    function hidelinks() {
        $('#subdownload').hide();
        $('#subdelete').hide();
        $('#subedit').hide();
        $('#lock').hide();
        $('#submove').hide();
    }
    function showlinks() {
        $('#subdownload').show();
        $('#subdelete').show();
        $('#subedit').show();
        $('#lock').show();
        $('#submove').show();
    }
    function dateibaum(seite,start) {
        if(seite=="left") { pfadleft=start; }
        else { 
            aktfile = '';
            pfadright=start; 
            hidelinks()
        };
        if (onD) newDir(seite);
        if (onF) newFile(seite);
        if (onL) deletefile();
        if (onA) editattribut();
        $.ajax({
               url: "jqhelp/firmaserver.php?task=showDir&id="+seite+"&dir="+start,
    	       dataType: 'json',
    	       success: function(data){
                   if (data.rc == 1) {
                       $('#path').empty().append(data.path);
                       $('#fb'+seite).empty().append(data.fb);
                   }
               }
        });
        setTimeout("dateibaum('left',pfadleft)",100000) // 100sec
    }

