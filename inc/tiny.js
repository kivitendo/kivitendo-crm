tinyMCE.init({
                theme                   : "advanced",
                mode                    : "exact",
                elements                : "elm1",
                //content_css             : "example_advanced.css",
                extended_valid_elements : "a[href|target|name]",
                plugins                 : "table,emotions",
		theme_advanced_buttons2_add        : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
                theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add        : "emotions",
                //invalid_elements : "a",
                theme_advanced_styles   : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1", // Theme specific setting CSS classes
                //execcommand_callback  : "myCustomExecCommandHandler",
                debug                   : false
        });
