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
 * Class Widget
 *
 * @package com\extremeidea\wordpress\plugin\conditional\content\classes\core
 */
class Widget extends \WP_Widget {

    /**
     * Widget constructor.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('conditional_content_widget', 'Conditional Content');
    }

    /**
     * Widget function
     *
     * @param array $args args
     * @param  array $instance instance
     *
     * @return void
     */
    public function widget($args, $instance) {
        $instance['text'] = apply_filters('conditional_content_widget_text', $this->id);
        echo $args['before_widget'], wpautop($instance['text']), $args['after_widget'];
    }

    /**
     * Settings form on widget page
     *
     * @param array $instance instance with settings
     *
     * @return void
     */
    public function form($instance) {
        $text = isset($instance['text']) ? esc_textarea($instance['text']) : '';

        printf(
            '<textarea class="widefat" rows="7" cols="20" id="%1$s" name="%2$s">%3$s</textarea>',
            $this->get_field_id('text'),
            $this->get_field_name('text'),
            $text
        );
    }
}
