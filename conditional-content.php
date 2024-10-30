<?php
/**
 * Plugin Name: Conditional Content
 * Plugin URI: http://www.extreme-idea.com/plugins/wordpress/conditional-content
 * Description: Plugin helps you to insert conditional content on the WordPress pages/posts/sidebars.
 * Version: 1.0.1
 * Author: EXTREME IDEA LLC
 * Author URI: http://www.extreme-idea.com/
 */

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

namespace com\extremeidea\wordpress\plugin\conditional\content;

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/custom_functions.php');

use com\extremeidea\wordpress\plugin\conditional\content\classes\core\JsonConfig;
use com\extremeidea\wordpress\plugin\conditional\content\classes\core\Logger;
use com\extremeidea\wordpress\plugin\conditional\content\classes\core\Settings;
use com\extremeidea\wordpress\plugin\conditional\content\classes\core\Variables;
use com\extremeidea\wordpress\plugin\conditional\content\classes\form\AdminSettingsForm;

register_activation_hook(__FILE__,
    array('com\extremeidea\wordpress\plugin\conditional\content\ConditionalContent', 'activation'));

register_uninstall_hook(__FILE__,
    array('com\extremeidea\wordpress\plugin\conditional\content\ConditionalContent', 'uninstall'));

add_action('plugins_loaded', array('com\extremeidea\wordpress\plugin\conditional\content\ConditionalContent', 'init'));

/**
 * Class ConditionalContent
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 *
 * @package com\extremeidea\wordpress\plugin\conditional\content
 */
class ConditionalContent {

    /**
     * Settings
     *
     * @var Settings $settings object
     */
    protected $settings;

    /**
     * Json config
     *
     * @var JsonConfig $json configuration
     */
    protected $json;

    /**
     * Plugin instance
     *
     * @var ConditionalContent $instance plugin instance
     */
    protected static $instance;

    /**
     * Logger
     *
     * @var Logger $logger instance
     */
    protected $logger;

    /**
     * Variables instance
     *
     * @var Variables $variables
     */
    protected $variables;

    /**
     * Sidebar data
     *
     * @var array $sidebars
     */
    protected $sidebars = array();

    /**
     * Initialize plugin function
     *
     * @return ConditionalContent
     */
    public static function init() {
        is_null(self::$instance) and self::$instance = new self;

        return self::$instance;
    }

    /**
     * ConditionalContent constructor.
     *
     * @return mixed
     */
    public function __construct() {
        $this->htaccessLogsProtection();

        $this->settings = new Settings();
        $this->logger = new Logger($this->settings);

        $this->variables = new Variables(dirname(__FILE__), get_stylesheet_directory(), rtrim(ABSPATH, '/'), home_url(),
            plugins_url("", __FILE__), get_stylesheet_directory_uri());

        $this->json = new JsonConfig($this->settings, $this->logger, $this->variables);

        $this->exportConfiguration();

        /* Register Activation Hook */
        register_activation_hook(__FILE__, array($this, 'activation'));

        /* Add admin settings*/
        add_action('admin_menu', array($this, 'adminMenu'));

        /* Add to page head */
        add_action('wp_head', array($this, 'addToHead'));

        add_action('widgets_init', array($this, 'registerWidget'));
        add_filter('conditional_content_widget_text',  array($this,'widgetContent'));
    }

    /**
     * Activation hook
     *
     * @return void
     */
    public static function activation() {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $plugin_data = get_plugin_data(__FILE__);
            $plugin_name = $plugin_data['Name'];
            echo '';
            _ex("Plugin: " . $plugin_name . "<br />", "Module's name - plugin");
            _ex("Fatal error: Current PHP version (" . PHP_VERSION . ") is below the required specification",
                "Current PHP version is below the required specification");
            exit();
        }
    }

    /**
     * Uninstall hook
     *
     * @return void
     */
    public static function uninstall() {
        delete_option(Settings::OPTION);
        delete_option('widget_conditional_content_widget');
    }

    /**
     * Admin menu hook
     *
     * @return void
     */
    public function adminMenu() {
        add_options_page('Conditional Content', 'Conditional Content', 'manage_options', 'ConditionalContent',
            array($this, 'settingsPage'));
    }

    /**
     * Add to head tag
     *
     * @return mixed
     */
    public function addToHead() {
        $this->sidebarContent();
        $this->logger->trace('************* Start head hook *************');

        $addToHead = '';

        $conditions = $this->json->getJsonConditions('head');

        if (!$conditions) {
            return false;
        }

        /* Add to head block*/
        foreach ($conditions as $key => $number) {
            foreach ($number as $condition => $output) {

                $funcExist = function_exists($condition);
                $func = function_exists($condition) && call_user_func($condition);
                $message =
                    "Condition $key: $condition, " . 'Function Exist:' . (int)$funcExist . ', Result:' . (int)$func;
                $this->logger->trace($message);
                if ($func) {
                    $addToHead .= $this->json->getOutputContent($output);
                }
            }
        }
        echo $this->variables->parse($addToHead);
    }

    /**
     * Display settings page
     *
     * @return void
     */
    public function settingsPage() {

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        $this->submitForm();

        $form = new AdminSettingsForm();
        $form->displayForm($this->settings->getAll());
    }

    /**
     * Submit form action
     *
     * @return mixed
     */
    protected function submitForm() {
        if (isset($_POST['resetJsonConfig'])) {
            $this->settings->set('json', '');
            $this->settings->save();
            $this->showMessage('notice notice-success', 'JSON configuration successfully cleared.');
        }

        if (isset($_POST['loggingLevel_save'])) {
            $this->settings->set('loggingLevel', $_POST['loggingLevel']);
            $this->settings->save();
            $this->showMessage('notice notice-success', 'Logging level successfully changed.');
        }

        if (isset($_POST['jsonConfig_save'])) {
            if ($this->validateJson($json = stripslashes($_POST['json']))) {
                $this->settings->set('json', $json);
                $this->settings->save();
                $this->showMessage('notice notice-success', 'JSON configuration successfully saved.');
            }
        }

        /* Upload configuration */
        if (isset($_POST['uploadConfigFile'])) {
            $file = $_FILES['jsonConfigComp'];
            $fileError = $file['error'];

            if ($fileError) {
                $this->logger->error("Upload configuration: No file uploaded! File error, code: $fileError;");
                $this->showMessage('error', 'No file uploaded!');

                return false;
            }

            if (!$fileError && $this->validExtension($file['name']) && $this->saveSettingsFromFile($file['tmp_name'])) {
                $this->showMessage('notice notice-success', 'Settings saved successfully');

                return true;
            }

        }
    }

    /**
     * Validate file extension
     *
     * @param string $file path to file
     *
     * @return bool
     */
    private function validExtension($file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if ($ext != 'json') {
            $message = "Wrong file extension! Extension '.$ext' is not supported!";
            $this->logger->error($message);
            $this->showMessage('error', $message, 'Supported only *.json files.');

            return false;
        }

        return true;
    }

    /**
     * Save settings from file configuration
     *
     * @param string $file file with json
     *
     * @return bool
     */
    protected function saveSettingsFromFile($file) {
        $json = file_get_contents($file);

        if ($this->validateJson($json)) {
            $this->settings->set('json', $json);
            $this->settings->save();

            return true;
        }

        return false;
    }

    /**
     * Validate form data
     *
     * @param string $newSettings settings
     *
     * @return boolean
     */
    protected function validateJson($newSettings) {
        $schema = json_decode(file_get_contents(dirname(__FILE__) . "/classes/core/schema.json"));

        $validator = new \League\JsonGuard\Validator(json_decode($newSettings), $schema);

        if ($validator->fails()) {
            foreach ($validator->errors() as $jsonError) {
                $this->showMessage('error', htmlspecialchars($jsonError['message']));
                $this->logger->error($jsonError['message']);
            }

            return false;
        }

        return true;
    }

    /**
     * Create .htaccess file for log path
     *
     * @return void
     */
    private function htaccessLogsProtection() {
        $path = ABSPATH . '/wp-content/uploads/logs/';
        $file = '.htaccess';
        if (file_exists($path) and !is_file($path . $file)) {
            file_put_contents($path . $file, 'Deny from all');
        }
    }

    /**
     * Widget sidebar hook
     *
     * @return mixed
     */
    public function registerWidget() {
        $this->logger->trace("************* Sidebar section *************");

        register_widget('com\extremeidea\wordpress\plugin\conditional\content\classes\core\Widget');

        $activeWidgets = get_option('sidebars_widgets');
        $sidebars = $this->json->getSidebars();

        $widgetNumber = 0;

        if (!$sidebars) {
            $this->logger->trace('Sidebars not defined.');

            return false;
        }

        foreach ($activeWidgets as $sidebar => $widgetArray) {
            foreach ((array)$widgetArray as $key => $value) {
                if (strpos($value, 'conditional_content_widget') !== false) {
                    unset($activeWidgets[$sidebar][$key]);
                }
            }
        }

        foreach ($sidebars as $sidebar => $arrayContent) {

            if (!isset($activeWidgets[$sidebar])) {
                $this->logger->trace("Sidebar: $sidebar not found! Check your json configuration");
                continue;
            }

            foreach ($arrayContent as $content) {
                $this->sidebars[$sidebar][] = $content;
                $widgetNumber++;
                $widgetName = 'conditional_content_widget-' . $widgetNumber;

                $position = $content->position;

                if (!in_array($widgetName, $activeWidgets[$sidebar])) {
                    array_splice($activeWidgets[$sidebar], $position, 0, $widgetName);

                    $this->logger->trace("Sidebar: $sidebar, Added widget: $widgetName, Position: $position");
                }

                $arrayIndex = array_search($widgetName, $activeWidgets[$sidebar]);
                if ($arrayIndex != $position) {
                    array_splice($activeWidgets[$sidebar], $position, 0, $widgetName);
                    unset($activeWidgets[$sidebar][$arrayIndex]);
                    $this->logger->trace("Sidebar: $sidebar, Changed widget position from $arrayIndex to $position");
                }
            }
        }
        update_option('sidebars_widgets', $activeWidgets);
    }

    /**
     * Set content to sidebars
     *
     * @return array
     */
    protected function sidebarContent() {
        $widgetNumber = 0;
        $widgetContent = [];
        $widgetEmptyContent = [];

        if (!is_array($this->sidebars)) {
            return false;
        }

        foreach ($this->sidebars as $name => $sidebars) {
            foreach ($sidebars as $sidebar) {
                $widgetNumber++;
                $widgetEmptyContent[$widgetNumber] = array('text' => '');

                $function = $sidebar->condition->function;

                if (!function_exists($function)) {
                    $this->logger->trace("Sidebar: $name, Functuon: $function, Not Exist");
                    continue;
                }

                if (!call_user_func($function)) {
                    $this->logger->trace("ERROR: Sidebar: $name, Functuon: $function, Return: false");
                    continue;
                }

                $widgetName = 'conditional_content_widget-' . $widgetNumber;
                $this->logger->trace("Sidebar: $name, Output:");
                $outputData = $this->json->getOutputContent($sidebar->output);
                $widgetContent[$widgetName] = $outputData;
            }
        }
        update_option('widget_conditional_content_widget', $widgetEmptyContent);
        return $widgetContent;
    }

    /**
     * Set widget content
     *
     * @param string $id widget id
     *
     * @return string
     */
    public function widgetContent($id) {
        $data = $this->sidebarContent();

        if (in_array($id, array_keys($data))) {
            return $data[$id];
        }
        return '';
    }

    /**
     * Show message to User
     *
     * @param string $type        class for <div> element
     * @param string $message     Main message
     * @param string $subMesssage sub message
     *
     * @return void
     */
    public function showMessage($type, $message, $subMesssage = '') {
        echo "
            <div class='$type'>
                <p>
                    <strong>$message</strong>
                </p>
                <p>
                    $subMesssage                
                </p>
            </div>
            ";
    }

    /**
     * Export json configuration
     *
     * @return void
     */
    protected function exportConfiguration() {
        if (isset($_POST['exportConfiguration'])) {
            $file = dirname(__FILE__) . '/json_configuration.json';
            file_put_contents($file, $this->settings->get('json'));
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="json_configuration.json"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit();
        }
    }
}
