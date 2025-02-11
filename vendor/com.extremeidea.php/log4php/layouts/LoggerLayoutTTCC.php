<?php
namespace com\extremeidea\php\tools\log4php; 
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * TTCC layout format consists of <b>t</b>ime, <b>t</b>hread, <b>c</b>ategory and nested
 * diagnostic <b>c</b>ontext information, hence the name.
 * 
 * <p>Each of the four fields can be individually enabled or
 * disabled. The time format depends on the <b>DateFormat</b> used.</p>
 *
 * <p>If no dateFormat is specified it defaults to '%c'. 
 * See php {@link PHP_MANUAL#date} function for details.</p>
 *
 * Configurable parameters for this layout are:
 * - {@link $threadPrinting} (true|false) enable/disable pid reporting.
 * - {@link $categoryPrefixing} (true|false) enable/disable logger category reporting.
 * - {@link $contextPrinting} (true|false) enable/disable NDC reporting.
 * - {@link $microSecondsPrinting} (true|false) enable/disable micro seconds reporting in timestamp.
 * - {@link $dateFormat} (string) set date format. See php {@link PHP_MANUAL#date} function for details.
 *
 * An example how to use this layout:
 * 
 * {@example ../../examples/php/layout_ttcc.php 19}<br>
 * 
 * {@example ../../examples/resources/layout_ttcc.properties 18}<br>
 *
 * The above would print:<br>
 * <samp>02:28 [13714] INFO root - Hello World!</samp>
 *
 * @version $Revision: 170 $
 * @package log4php
 * @subpackage layouts
 * 
 * @deprecated LoggerLayout TTCC is deprecated and will be removed in a future release. Please use 
 *   LoggerLayoutPattern instead. 
 */
class LoggerLayoutTTCC extends LoggerLayout {

	// Internal representation of options
	protected $threadPrinting    = true;
	protected $categoryPrefixing = true;
	protected $contextPrinting   = true;
	protected $microSecondsPrinting = true;
	
	/**
	 * @var string date format. See {@link PHP_MANUAL#strftime} for details
	 */
	protected $dateFormat = '%c';

	/**
	 * Constructor
	 *
	 * @param string date format
	 * @see dateFormat
	 */
	public function __construct($dateFormat = '') {
		$this->warn("LoggerLayout TTCC is deprecated and will be removed in a future release. Please use LoggerLayoutPattern instead.");
		if (!empty($dateFormat)) {
			$this->dateFormat = $dateFormat;
		}
		return;
	}

	/**
	 * The <b>ThreadPrinting</b> option specifies whether the name of the
	 * current thread is part of log output or not. This is true by default.
	 */
	public function setThreadPrinting($threadPrinting) {
		$this->setBoolean('threadPrinting', $threadPrinting);
	}

	/**
	 * @return boolean Returns value of the <b>ThreadPrinting</b> option.
	 */
	public function getThreadPrinting() {
		return $this->threadPrinting;
	}

	/**
	 * The <b>CategoryPrefixing</b> option specifies whether {@link Category}
	 * name is part of log output or not. This is true by default.
	 */
	public function setCategoryPrefixing($categoryPrefixing) {
		$this->setBoolean('categoryPrefixing', $categoryPrefixing);
	}

	/**
	 * @return boolean Returns value of the <b>CategoryPrefixing</b> option.
	 */
	public function getCategoryPrefixing() {
		return $this->categoryPrefixing;
	}

	/**
	 * The <b>ContextPrinting</b> option specifies log output will include
	 * the nested context information belonging to the current thread.
	 * This is true by default.
	 */
	public function setContextPrinting($contextPrinting) {
		$this->setBoolean('contextPrinting', $contextPrinting);
	}

	/**
	 * @return boolean Returns value of the <b>ContextPrinting</b> option.
	 */
	public function getContextPrinting() {
		return $this->contextPrinting;
	}
	
	/**
	 * The <b>MicroSecondsPrinting</b> option specifies if microseconds infos
	 * should be printed at the end of timestamp.
	 * This is true by default.
	 */
	public function setMicroSecondsPrinting($microSecondsPrinting) {
		$this->setBoolean('microSecondsPrinting', $microSecondsPrinting);
	}

	/**
	 * @return boolean Returns value of the <b>MicroSecondsPrinting</b> option.
	 */
	public function getMicroSecondsPrinting() {
		return $this->microSecondsPrinting;
	}
	
	
	public function setDateFormat($dateFormat) {
		$this->setString('dateFormat', $dateFormat);
	}
	
	/**
	 * @return string
	 */
	public function getDateFormat() {
		return $this->dateFormat;
	}

	/**
	 * In addition to the level of the statement and message, the
	 * returned string includes time, thread, category.
	 * <p>Time, thread, category are printed depending on options.
	 *
	 * @param LoggerLoggingEvent $event
	 * @return string
	 */
	public function format(LoggerLoggingEvent $event) {
		$timeStamp = (float)$event->getTimeStamp();
		$format = strftime($this->dateFormat, (int)$timeStamp);
		
		if ($this->microSecondsPrinting) {
			$usecs = floor(($timeStamp - (int)$timeStamp) * 1000);
			$format .= sprintf(',%03d', $usecs);
		}
			
		$format .= ' ';
		
		if ($this->threadPrinting) {
			$format .= '['.getmypid().'] ';
		}
		
		$level = $event->getLevel();
		$format .= $level.' ';
		
		if($this->categoryPrefixing) {
			$format .= $event->getLoggerName().' ';
		}
	   
		if($this->contextPrinting) {
			$ndc = $event->getNDC();
			if($ndc != null) {
				$format .= $ndc.' ';
			}
		}
		
		$format .= '- '.$event->getRenderedMessage();
		$format .= PHP_EOL;
		
		return $format;
	}

	public function ignoresThrowable() {
		return true;
	}
}
