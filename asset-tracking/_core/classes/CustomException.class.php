<?php
/**
 * File: CustomException.class.php
 * Created on: Sun Aug 1 23:55 CDT 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Handles custom exception types
 *
 * @package org.sugarfish.core
 * @name CustomException
 */

class CustomException extends Exception {

	/**
	 * Custom error message
	 * @access public
	 * @static
	 * @var string $strMessage
	 */
	public static $strMessage;

	/**
	 * Custom error number
	 * @access public
	 * @static
	 * @var integer $intErrorNumber
	 */
	public static $intErrorNumber;

	/**
	 * Console output
	 * @access public
	 * @static
	 * @var string $strConsoleOutput
	 */
	protected static $strConsoleOutput;

	/**
	 * Log output
	 * @access public
	 * @static
	 * @var string $strErrorLogOutput
	 */
	protected static $strErrorLogOutput;

	/**
	 * Class constructor
	 * @access public
	 * @param integer $intErrorNumber
	 * @param string $strMessage
	 * @return void
	 */
	public function __construct($intErrorNumber = 0, $strMessage) {
		self::$strMessage = $strMessage;
		self::$intErrorNumber = $intErrorNumber;
		$this->CheckForProgrammerError();
	}

	/**
	 * Checks for programmer error; outputs fancy stack trace
	 * @access private
	 * @return void
	 */
	private function CheckForProgrammerError() {
		// instantiate the Exception class
		parent::__construct(self::$strMessage, self::$intErrorNumber);

		self::$strErrorLogOutput = sprintf("%s; Stack Trace: %s, [%s]",
			$this->getMessage(),
			$this->getTraceAsString(),
			Application::$RemoteAddress
		);

		Application::Log(self::$strErrorLogOutput);

		$strOS = array_key_exists('OS', $_SERVER)?sprintf('<b>Operating System:</b> %s', $_SERVER['OS']):'';
		self::$strConsoleOutput = sprintf(
			'<div style="position:absolute;left:0;top:0;width:100%%;z-index:10000;font-family:Arial,Helvetica,sans-serif;font-size:10pt">
				<table border="0" cellspacing="0" width="100%%">
					<tr>
						<td nowrap="nowrap" style="background-color:#464;color:#fff;padding:10px 0 10px 10px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10px;font-weight:bold;width:70%%;vertical-align:top">SFc Development Framework %s<br /><span style="font-size:18px;color:#fff">Error Report</span></td>
						<td nowrap="nowrap" style="background-color:#464;color:#fff;padding:10px 10px 10px 0;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10px;width:30%%;vertical-align:top;text-align:right">
							<b>PHP Version:</b> %s;&nbsp;&nbsp;<b>Zend Engine Version:</b> %s;<br />
							%s&nbsp;&nbsp;<b>Application:</b> %s;&nbsp;&nbsp;<b>Server Name:</b> %s
						</td>
					</tr>
				</table>
				<div style="padding:5px;background-color:#fff;color:#f00">
					<p style="padding:0;font-family:Arial,Helvetica,sans-serif;font-size:12pt;color:#f00;border:0">%s</p>
					<p style="padding:0;font-family:Arial,Helvetica,sans-serif;font-size:10pt;color:#f00;font-weight:normal;border:0">
						Stack Trace:
						<pre style="background-color:pink;padding:3px 5px">%s</pre>
					</p>
				</div>
			</div>',
			__VERSION__,
			PHP_VERSION,
			zend_version(),
			$strOS,
			$_SERVER['SERVER_SOFTWARE'],
			$_SERVER['SERVER_NAME'],
			$this->getMessage(),
			$this->getTraceAsString()
		);
	}

	/**
	 * Override __toString
	 * @access public
	 * @return mixed
	 */
	public function __toString() {
		if (__SHOW_FRIENDLY_ERROR__) {
			ob_clean();
			ob_start();
			if (self::$intErrorNumber == Exceptions::ERROR) {
				require_once(__FRIENDLY_ERROR__);
			} else {
				require_once(__PROGRAM_ERROR__);
			}
			$strError = ob_get_contents();
			ob_end_clean();
			return $strError;
		} else {
			return self::$strConsoleOutput;
		}
	}
}
?>
