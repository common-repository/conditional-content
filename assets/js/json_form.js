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

    var data = $('#conditional-content-jsonConfig').text();

    if (!data) {
        data = '{}';
    }

    var jsonLoadConfig = $.parseJSON(data);

    $("#form").alpaca({
        "data": jsonLoadConfig,
        "schema": {
            "title": "JSON Configuration",
            "properties": {
                "head": {
                    "title": "Head section",
                    "items": {
                        "properties": {
                            "condition": {
                                "title": "Define the output condition",
                                "properties": {
                                    "function": {
                                        "type": "string",
                                        "title": "Function"
                                    }
                                },
                                "required": [
                                    "function"
                                ],
                                "type": "object"
                            },
                            "output": {
                                "title": "Define the output type of content",
                                "properties": {
                                    "from_text": {
                                        "title": "From text:",
                                        "type": "string"
                                    },
                                    "from_text_array": {
                                        "title": "From text array:",
                                        "type": "array",
                                        "items": {
                                            "type": "string"
                                        }
                                    },
                                    "from_file": {
                                        "title": "From file",
                                        "type": "string"
                                    },
                                    "from_php_func": {
                                        "title": "From php function",
                                        "type": "string"
                                    },
                                    "from_php_inc": {
                                        "title": "From php file",
                                        "type": "string"
                                    }
                                },
                                "anyOf": [
                                    {"required": ["from_file"]},
                                    {"required": ["from_text"]},
                                    {"required": ["from_text_array"]},
                                    {"required": ["from_php_func"]},
                                    {"required": ["from_php_inc"]}
                                ],
                                "type": "object"
                            }
                        },
                        "required": [
                            "output",
                            "condition"
                        ],
                        "type": "object"
                    },
                    "type": "array"
                },
                "sidebars": {
                    "title": "Sidebars section",
                    "items": {
                        "properties": {
                            "name": {
                                "title": "Available sidebars on your WordPress",
                                "type": "string",
                                "enum": $.parseJSON($('#conditional-content-sidebars_id').text())

                            },
                            "condition": {
                                "title": "Define the output condition",
                                "properties": {
                                    "function": {
                                        "type": "string",
                                        "title": "Function"
                                    }
                                },
                                "required": [
                                    "function"
                                ],
                                "type": "object"
                            },
                            "output": {
                                "title": "Define the output type of content",
                                "properties": {
                                    "from_text": {
                                        "title": "From text:",
                                        "type": "string"
                                    },
                                    "from_text_array": {
                                        "title": "From text array:",
                                        "type": "array",
                                        "items": {
                                            "type": "string"
                                        }
                                    },
                                    "from_file": {
                                        "title": "From file",
                                        "type": "string"
                                    },
                                    "from_php_func": {
                                        "title": "From php function",
                                        "type": "string"
                                    },
                                    "from_php_inc": {
                                        "title": "From php file",
                                        "type": "string"
                                    }
                                },
                                "anyOf": [
                                    {"required": ["from_file"]},
                                    {"required": ["from_text"]},
                                    {"required": ["from_text_array"]},
                                    {"required": ["from_php_func"]},
                                    {"required": ["from_php_inc"]}
                                ],
                                "type": "object"
                            },
                            "position": {
                                "title": "Define output position",
                                "type": "integer"
                            }
                        },
                        "required": [
                            "name",
                            "position",
                            "condition",
                            "output"
                        ],
                        "type": "object"
                    },
                    "type": "array"
                }
            },
            "type": "object"
        },
        "options": {
            "form": {
                "attributes": {
                    "method": "post"
                },
                "buttons": {
                    "resetSettings": {
                        "type": "button",
                        "value": "Reset JSON Configuration",
                        "styles": "btn btn-danger array-item-remove",
                        "click": function () {
                            if (confirm('Are you sure you want to reset all settings?')) {
                                $("<form method='post'><input type='hidden' name='resetJsonConfig' value='1'></form>")
                                    .appendTo('body')
                                    .submit();
                            }
                        }
                    },
                    "exportSettings": {
                        "title": "Export The Configuration",
                        "styles": "btn btn-success",
                        "click": function () {
                            $('<form method="post"><input type="hidden" name="exportConfiguration" value="1"></form>')
                                .appendTo('body')
                                .submit();
                        }
                    },
                    "submit": {
                        "title": "Save Settings",
                        "styles": "btn btn-primary",
                        "click": function () {
                            var value = this.getValue();
                            var json = JSON.stringify(value, null, 2);
                            var jsonConfig = $("<input type='hidden' name='json'/>");
                            jsonConfig.val(json);

                            $('<form method="post"><input type="hidden" name="jsonConfig_save" value="1"></form>')
                                .append(jsonConfig)
                                .appendTo('body')
                                .submit();
                        }
                    }
                }
            },
            "helper": "Add the display rules for conditional content",
            "fields": {
                "sidebars": {
                    "items": {
                        "fields": {
                            "name": {
                                "type": "select",
                                "helper": "Choose the sidebar for content output",
                                "optionLabels": $.parseJSON($('#conditional-content-sidebars_name').text()),
                                "noneLabel": "-- Select sidebar --",
                                "removeDefaultNone": false
                            },
                            "condition": {
                                "fields": {
                                    "function": {
                                        "helper": "Specify the function name, which will be called and depending on result (true/false) will display the output content."
                                    }
                                }
                            },
                            "output": {
                                "helper": "Responsible field for outputting the content.",
                                "fields": {
                                    "from_text": {
                                        "type": "textarea",
                                        "helper": "Enter the output text"
                                    },
                                    "from_text_array": {
                                        "helper": "Enter the output text"
                                    },
                                    "from_file": {
                                        "helper": "Enter the file path for the content to insert into WordPress page header. All text file types are supported."
                                    },
                                    "from_php_func": {
                                        "helper": "Enter the function name (must return string), the output content will be displayed in sidebar."
                                    },
                                    "from_php_inc": {
                                        "helper": "Enter path to *.php file. This file will be executed and its result will be displayed."
                                    }
                                }
                            },
                            "position": {
                                "helper": "Specifies the content position towards the sidebar."
                            }
                        }
                    }
                },
                "head": {
                    "items": {
                        "fields": {
                            "condition": {
                                "fields": {
                                    "function": {
                                        "helper": "Specify the function name, which will be called and depending on result (true/false) will display the output content."
                                    }
                                }
                            },
                            "output": {
                                "fields": {
                                    "from_text": {
                                        "type": "textarea",
                                        "helper": "This text will be inserted to a header of the WordPress page."
                                    },
                                    "from_text_array": {
                                        "helper": "This text will be inserted to a header of the WordPress page."
                                    },
                                    "from_file": {
                                        "helper": "Enter the file path for the content to insert into WordPress page header. All text file types are supported."
                                    },
                                    "from_php_func": {
                                        "helper": "Enter the function name (must return string), the output content will be displayed in head of page."
                                    },
                                    "from_php_inc": {
                                        "helper": "Enter path to *.php file. This file will be executed and its result will be displayed."
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        "view": "bootstrap-edit",
        "postRender": function(control) {
            control.childrenByPropertyId["head"].getFieldEl().css("background-color", "#e5e5e5");
            control.childrenByPropertyId["sidebars"].getFieldEl().css("background-color", "#cecece");
        }
    });

    $(function() {
        $("#tabs").tabs();
    });

});



