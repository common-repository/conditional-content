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
 * Class Settings
 * 
 * @package com\extremeidea\wordpress\plugin\conditional\content\classes\core
 */
class Settings {

    /**
     * DB option
     */
    const OPTION = 'Extreme-Idea-Conditional-Content';

    /**
     * Settings
     *
     * @var array $settings settings attribute
     */
    protected $settings;

    /**
     * Settings constructor.
     * 
     * @return void
     */
    public function __construct() {
        $this->settings = unserialize(get_option(self::OPTION, serialize($this->getDefaultSettings())));
    }

    /**
     * Default settings array
     *
     * @return mixed
     */
    public function getDefaultSettings() {
        return array(
            'loggingLevel' => 'error',
            'json' => ''
        );

    }

    /**
     * Get setting from array
     * 
     * @param string $key setting name
     * 
     * @return mixed|null
     */
    public function get($key) {
        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }

    /**
     * Get all settings
     * 
     * @return array
     */
    public function getAll() {
        return $this->settings;
    }

    /**
     * Get setting for logger login level
     * 
     * @return string
     */
    public function getLoggingLevel() {
        return $this->settings['loggingLevel'];
    }

    /**
     * Set new settings
     * 
     * @param array $settings array with settings
     * 
     * @return void
     */
    public function setArray(array $settings) {
        $settings['json'] = stripcslashes($settings['json']);
        $this->settings = $settings;
    }

    /**
     * Set value for key
     *
     * @param string $key setting key
     * @param string $value value to set
     *
     * @return void
     */
    public function set($key, $value) {
        $this->settings[$key] = $value;
    }

    /**
     * Save settings to WP DB
     *
     * @return void
     */
    public function save() {
        update_option(self::OPTION, serialize($this->settings));
    }
}
