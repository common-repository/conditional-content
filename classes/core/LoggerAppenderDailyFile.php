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

use com\extremeidea\php\tools\log4php as log4php;

/**
 * Class LoggerAppenderDailyFile
 *
 * @package com\extremeidea\wordpress\plugin\conditional\content\classes\core
 */
class LoggerAppenderDailyFile extends log4php\LoggerAppenderDailyFile {

    /**
     * Set log file
     *
     * @param string $file filename
     *
     * @return void
     */
    public function setFile($file) {
        parent::setFile(ABSPATH . '/wp-content/uploads/logs/' . $file);
    }
}
