<html>
    <head><title></title>
{STYLESHEETS}
{CRMCSS}
{JAVASCRIPTS}
{THEME}
{ELFINDER}
<script type="text/javascript">
    $(document).ready(function() {
        var language = kivi.myconfig.countrycode;
        start = function( lng ){
            $('#elfinder').elfinder({
                url : 'jquery-plugins/elFinder/php/connector.minimal.php',
                height : 600,
                defaultView: 'list',
                rememberLastDir : true,
                lang : lng
            });
        }
        if( language != 'en' ){
            $.ajax({
                url : 'jquery-plugins/elFinder/js/i18n/elfinder.' + language + '.js',
                cache : true,
                dataType : 'script'
            }).done( function(){
                start( language );
            }).fail( function(){
                start( 'en' );
            });
        }
        else start( language );
    });
</script>
</head>
<body>
{PRE_CONTENT}
{START_CONTENT}
<div class="ui-widget-content" style="height:700px">
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">.:Documents:. </p>
<div id="elfinder"></div>
</div>
{END_CONTENT}
</body>
</html>
