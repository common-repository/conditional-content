<?php
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
namespace com\extremeidea\wordpress\plugin\conditional\content\classes\form;

/**
 * Class AdminSettingsForm
 *
 * @package com\extremeidea\wordpress\plugin\conditional\content\classes\core
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AdminSettingsForm {

    /**
     * Admin settings form
     *
     * @param array $settings with settings
     *
     * @return string
     */
    public function displayForm($settings) {
        $pluginUrl = plugins_url() . '/conditional-content';
        $json = htmlspecialchars($settings['json']);
        $sidebars = $GLOBALS['wp_registered_sidebars'];
        $sidebarId = array();
        $sidebarName = array();
        foreach ($sidebars as $sidebar) {
            $sidebarId[] = $sidebar['id'];
            $sidebarName[] = $sidebar['name'];
        }
        $sidebarId = json_encode($sidebarId);
        $sidebarName = json_encode($sidebarName);


        echo " 
         <!-- JSON FORM -->
            <link rel=\"stylesheet\" href=\"//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css\">
            <script src=\"https://code.jquery.com/jquery-1.12.4.js\"></script>
            <script src=\"https://code.jquery.com/ui/1.12.1/jquery-ui.js\"></script>                   
     
            <!-- bootstrap -->
            <link type=\"text/css\" 
            rel=\"stylesheet\" href=\"//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css\" />
            <script type=\"text/javascript\" src=\"//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js\">
</script>
     
            <!-- handlebars -->
            <script type=\"text/javascript\" src=\"//cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.7/handlebars.js\">
</script>
     
            <!-- alpaca -->
            <link type='text/css' href='//code.cloudcms.com/alpaca/1.5.23/bootstrap/alpaca.min.css' rel='stylesheet' />
            <script type='text/javascript' src='//code.cloudcms.com/alpaca/1.5.23/bootstrap/alpaca.min.js'></script>

            <script type='text/javascript' src='$pluginUrl/assets/js/json_form.js'></script>
            <script type='text/javascript' src='$pluginUrl/assets/js/upload_form.js'></script>
            
            <h1>Conditional Content</h1>
            
            <div id='tabs'>
                <ul>
                  <li><a href='#tabs-1'>Upload Configuration</a></li>
                  <li><a href='#tabs-2'>Configuration Editor</a></li>
                </ul>
                <div id='tabs-1'>
                    <div id='uploadTab'></div>
                </div>
                <div id='tabs-2'>
                    <div id='form'></div>       
                </div>
            </div>         
                
            <div style='display: none' id='conditional-content-jsonConfig'>{$json}</div>
            <div style='display: none' id='conditional-content-loggingLevel'>{$settings['loggingLevel']}</div>
            <div style='display: none' id='conditional-content-sidebars_id'>{$sidebarId}</div>
            <div style='display: none' id='conditional-content-sidebars_name'>{$sidebarName}</div>
            <!-- JSON FORM -->                  
        ";
    }
}
