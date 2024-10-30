/**
 * Conditional Content is a solution which helps you to insert conditional content
 * on the WordPress pages/posts/sidebars
 *
 * Copyright (c) 2017 EXTREME IDEA LLC. All Rights Reserved.
 * This software is the proprietary information of EXTREME IDEA LLC.
 *
 * Author URI: http://www.extreme-idea.com/
 * License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

$(document).ready(function () {
    $("#uploadTab").alpaca({
        "schema": {
            "type": "object",
            "title": "Upload Configuration File",
            "properties": {
                "jsonConfigComp": {
                    "title": "Please select a file to upload",
                    "type": "string"
                },
                "uploadConfigFile": {
                    "type": "string"
                },
                "loggingLevel": {
                    "title": "Logging Level",
                    "default": $('#conditional-content-loggingLevel').text(),
                    "enum": ["all", "fatal", "error", "warn", "info", "debug", "trace"]
                },
                "version": {
                    "default": "1.0.1",
                    "title": "Version",
                    "type": "string"
                }
            }
        },
        "options": {
            "form": {
                "attributes": {
                    "method": "post",
                    "enctype": "multipart/form-data"
                },
                "buttons": {
                    "submit": {
                        "title": "Upload Settings",
                        "styles": "btn btn-primary"
                    }
                }
            },
            "fields": {
                "jsonConfigComp": {
                    "type": "file",
                    "helper": "Only '*.json' file is supported."
                },
                "uploadConfigFile": {
                    "default": 1,
                    "type": "hidden"
                },
                "loggingLevel": {
                    "type": "select",
                    "optionLabels": ["All", "Fatal", "Error", "Warning", "Info", "Debug", "Trace"],
                    "helper": "Logs stored in folder $WORDPRESS_HOME/wp-content/upload/logs/",
                    "removeDefaultNone": true,
                    "id": "Logging_Level"
                },
                "version": {
                    "readonly": true
                }

            }
        },
        "postRender": function(control) {
            control.childrenByPropertyId["loggingLevel"].on("change", function () {
                var loggingLevel = $("<input type='hidden' name='loggingLevel'/>");
                loggingLevel.val($("#Logging_Level").val());
                $('<form method="post"><input type="hidden" name="loggingLevel_save" value="1"></form>')
                    .append(loggingLevel)
                    .appendTo('body')
                    .submit();
            });
        },
        "view": "bootstrap-edit"
    });
});



