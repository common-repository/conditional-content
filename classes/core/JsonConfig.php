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
 * Class JsonConfig
 *
 * @package com\extremeidea\wordpress\plugin\conditional\content\classes\core
 */
class JsonConfig {

    /**
     * Settings
     *
     * @var Settings $settings object with settings
     */
    protected $settings;

    /**
     * Logger instance
     *
     * @var Logger $logger instance of logger
     */
    protected $logger;

    /**
     * Variables instance
     *
     * @var Variables $variables instance
     */
    protected $variables;

    /**
     * JsonConfig constructor.
     *
     * @param Settings  $settings  settings pbject
     * @param Logger    $logger    logger instance
     * @param Variables $variables variables instance
     *
     * @return mixed
     */
    public function __construct(Settings $settings, Logger $logger, Variables $variables) {
        $this->settings = $settings;
        $this->logger = $logger;
        $this->variables = $variables;
    }

    /**
     * Get JSON object
     *
     * @return mixed
     */
    public function getJsonInstance() {
        $json = $this->settings->get('json');
        $json = json_decode($json);

        return $json;
    }

    /**
     * Get conditions from json instance
     *
     * @param string $tag tag in json (head/body)
     *
     * @return array|mixed
     */
    public function getJsonConditions($tag) {
        $json = $this->getJsonInstance();
        if (!$json) {
            return array();
        }
        $conditions = array();
        /* For sections (example head) */
        foreach ($json as $key => $params) {
            /* For parameters condition, output */
            foreach ($params as $parameter) {
                $conditions[$key][][$parameter->condition->function] = $parameter->output;
            }
        }

        if (empty($conditions)) {
            return array();
        }

        /* ['head']['callable_function']['array_with_output'] */

        return isset($conditions[$tag]) ? $conditions[$tag] : [];
    }

    /**
     * Get Output stings
     *
     * @param array $data data to output
     *
     * @return string
     */
    public function getOutputContent($data) {

        $data = isset($data) ? $data : array();

        $outputData = '';

        foreach ($data as $from => $output) {

            if (empty($from)) {
                continue;
            }

            $output = $this->variables->parse($output);
            switch ($from) {
                case 'from_text' :
                    $outputData .= $output;
                    $this->logger->trace("From_text_string: $outputData");
                    break;
                case 'from_text_array' :
                    if (is_array($output)) {
                        foreach ($output as $string) {
                            $outputData .= $string;
                        }
                        $this->logger->trace("From_text_array: $outputData");
                        break;
                    }
                    $outputData .= $output;
                    $this->logger->trace("From_text_string: $outputData");
                    break;
                case 'from_file' :
                    $outputData .= $this->fromFile($output);
                    break;
                case 'from_php_func' :
                    $outputData .= $this->fromPhpFunc($output);
                    break;
                case 'from_php_inc':
                    $outputData .= $this->fromPhpInc($output);
                    break;

            }
        }

        return $outputData;
    }

    /**
     * Get content from file
     *
     * @param string $fileName file name
     *
     * @return string
     */
    protected function fromFile($fileName) {

        $data = '';
        $isUrl = filter_var($fileName, FILTER_VALIDATE_URL);

        if ($isUrl && !$this->validateUrl($fileName)) {
            return '';
        }

        if ($isFile = is_file($fileName) or $isUrl) {
            $data = file_get_contents($fileName);
        }

        $this->logger->trace("From_file: $fileName Exist:" . (int)$isFile . ", Data: $data, $isUrl");

        return $data;
    }

    /**
     * Function output from_php_inc
     *
     * @param string $path path to file
     *
     * @return string
     */
    protected function fromPhpInc($path) {

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $this->logger->error("From_php_inc: It is forbidden to use links to specify a file in this function! "
                . "File:$path");

            return '';
        }

        $fileExist = file_exists($path);

        if (pathinfo($path, PATHINFO_EXTENSION) != 'php') {
            $this->logger->error("From_php_inc: Only php files are allowed! File: $path");

            return '';
        }

        if (!$fileExist) {
            $this->logger->trace("From_php_inc: File not exists $path");

            return "";
        }

        ob_start();
        include($path);
        $content = ob_get_contents();
        ob_end_clean();
        $message = $fileExist ? "File exists" : "File not exists";

        $this->logger->trace("From_php_inc: $message: $path, Data: $content");

        return $content;
    }

    /**
     * Function output from_php_func
     *
     * @param string $func global function
     *
     * @return string
     */
    protected function fromPhpFunc($func) {
        $funcExist = function_exists($func);

        if (!$funcExist) {
            return "";
        }

        $data = call_user_func($func);
        $this->logger->trace("From_function '$func': Exist:" . (int)$funcExist . ", Data: $data");

        return $data;

    }

    /**
     * Get sidebars configuration
     *
     * @return array
     */
    public function getSidebars() {
        $json = $this->getJsonInstance();
        if (!$json) {
            return false;
        }
        $result = array();

        $sidebars = $json->sidebars;

        if (!$sidebars) {
            return false;
        }

        foreach ($json->sidebars as $sidebar) {
            $result[$sidebar->name][] = $sidebar;
        }

        return $result;
    }

    /**
     * Check for baa protection
     *
     * @param string $url url to file
     *
     * @return boolean
     */
    protected function validateUrl($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if ($code != 200) {
            $this->logger->error("http code: $code, Error: $error, File: $url");

            return false;
        }

        return true;
    }
}
