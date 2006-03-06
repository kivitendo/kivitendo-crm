tinyMCE.init({
                theme : "advanced",
                mode : "exact",
                elements : "elm1,elm2",
                content_css : "example_advanced.css",
                extended_valid_elements : "a[href|target|name]",
                plugins : "table,emotions",
		theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
                theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "emotions",
                //invalid_elements : "a",
                theme_advanced_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1", // Theme specific setting CSS classes
                //execcommand_callback : "myCustomExecCommandHandler",
                debug : false
        });
// Custom event handler
        function myCustomExecCommandHandler(editor_id, elm, command, user_interface, value) {
                var linkElm, imageElm, inst;

                switch (command) {
                        case "mceLink":
                                inst = tinyMCE.getInstanceById(editor_id);
                                linkElm = tinyMCE.getParentElement(inst.selection.getFocusElement(), "a");

                                if (linkElm)
                                        alert("Link dialog has been overriden. Found link href: " + tinyMCE.getAttrib(linkElm, "href"));
                                else
                                        alert("Link dialog has been overriden.");

                                return true;

                        case "mceImage":
                                inst = tinyMCE.getInstanceById(editor_id);
                                imageElm = tinyMCE.getParentElement(inst.selection.getFocusElement(), "img");

                                if (imageElm)
                                        alert("Image dialog has been overriden. Found image src: " + tinyMCE.getAttrib(imageElm, "src"));
                                else
                                        alert("Image dialog has been overriden.");

                                return true;
                }

                return false; // Pass to next handler in chain
        }

