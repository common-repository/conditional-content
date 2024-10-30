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

namespace com\extremeidea\wordpress\plugin\conditional\content\classes\core;

/**
 * Class Variables
 *
 * @package com\extremeidea\wordpress\plugin\conditional\content\classes\core
 */

class Variables {

    /**
     *  Array variables
     *
     * @var array $variables array with variables
     */
    protected $variables;

    /**
     * Variables constructor.
     *
     * @param string $pluginDir plugin directory
     * @param string $themeDir current theme directory
     * @param string $homeDir home directory
     * @param string $homeUrl home URL
     * @param string $pluginUrl plugin URL
     * @param string $themeUrl theme URL
     *
     * @return mixed
     */
    public function __construct($pluginDir, $themeDir, $homeDir, $homeUrl, $pluginUrl, $themeUrl) {
        $this->variables = array(
            '${plugin.dir}' => $pluginDir,
            '${theme.dir}' => $themeDir,
            '${home.dir}' => $homeDir,
            '${home.url}' => $homeUrl,
            '${plugin.url}' => $pluginUrl,
            '${theme.url}' => $themeUrl,
            '${contentCollection}' => null,
        );
    }

    /**
     * Get all variables
     *
     * @return array
     */
    public function getVariables() {
        return $this->variables;
    }

    /**
     * Parse variables in the string
     *
     * @param string $string to parse
     *
     * @return mixed
     */
    public function parse($string) {

        if (empty($string)) {
            return false;
        }

        $result = $string;

        foreach ($this->getVariables() as $name => $value) {
            $result = str_replace($name, $value, $result);
        }
        return $result;
    }

    /**
     * Set content for posts
     *
     * @param array $content content to display on posts
     *
     * @return mixed
     */
    public function setContent(array $content) {
        $this->variables['${contentCollection}'] = json_encode($content);
    }
}
