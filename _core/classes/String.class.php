<?php
/**
 * File: String.class.php
 * Created on: Thu Oct 16 00:54 CDT 2008
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

/**
 * An abstract utility class to handle string manipulation; all methods are statically available
 *
 * @package org.sugarfish.core
 * @name String
 */

abstract class String {

	/**
	 * Returns the first character of a given string, or null if the given string is null
	 * @access public
	 * @final
	 * @static
	 * @param string $strString 
	 * @return string
	 */
	public final static function FirstCharacter($strString) {
		if (strlen($strString) > 0) {
			return substr($strString, 0 , 1);
		} else {
			return null;
		}
	}

	/**
	 * Returns the last character of a given string, or null if the given string is null
	 * @access public
	 * @final
	 * @static
	 * @param string $strString 
	 * @return string
	 */
	public final static function LastCharacter($strString) {
		$intLength = strlen($strString);
		if ($intLength > 0) {
			return substr($strString, $intLength - 1);
		} else {
			return null;
		}
	}

	/**
	 * Truncates a string to a given length
	 * @access public
	 * @final
	 * @static
	 * @param string $strString
	 * @param integer $intLength
	 * @param boolean $blnRemovePunctuation
	 * @param string $strTail
	 */
	public final static function Truncate($strString, $intLength = 30, $blnRemovePunctuation = true, $strTail = "&nbsp;&hellip;") {
		$strString = trim($strString);
		$intTxtLen = strlen($strString);
		if ($intTxtLen > $intLength) {
			for($i=1;$strString[$intLength-$i]!=" ";$i++) {
				if ($i == $intLength) {
					return substr($strString, 0, $intLength) . $strTail;
				}
			}
			if ($blnRemovePunctuation) {
				for(;$strString[$intLength-$i]=="," || $strString[$intLength-$i]=="." || $strString[$intLength-$i]==" ";$i++) {;}
				$strString = substr($strString, 0, $intLength-$i+1) . $strTail;
			}
		}
		return $strString;
	}

	/**
	 * Escapes the string so that it can be safely used in as an Xml Node (basically, adding CDATA if needed)
	 * @access public
	 * @final
	 * @static
	 * @param string $strString string to escape
	 * @return string $strString
	 */
	public final static function XmlEscape($strString) {
		if ((strpos($strString, '<') !== false) || (strpos($strString, '&') !== false)) {
			$strString = str_replace(']]>', ']]]]><![CDATA[>', $strString);
			$strString = sprintf('<![CDATA[%s]]>', $strString);
		}
		return $strString;
	}

	/**
	 * Returns an array in an easy-to-read format
	 * @access public
	 * @final
	 * @static
	 * @param array $arrName name of input array
	 * @return string the formatted array as a string
	 */
	public final static function ArrayFormat($arrName) {
		return sprintf('<pre>%s</pre>', print_r($arrName, true));
	}

	/**
	 * Returns a delimiter separated string
	 * @access public
	 * @final
	 * @static
	 * @param array $arrValues values to be delimited
	 * @param string $strDelimiter optional delimiter, default ', '
	 * @return string $strOutput
	 */
	public final static function InsertDelimiters($arrValues, $strDelimiter = ', ') {
		$strOutput = null;
		$blnIsFirst = true;
		foreach ($arrValues as $strValue) {
			if (!is_null($strValue)) {
				if (!$blnIsFirst) {
					$strOutput .= sprintf('%s%s', $strDelimiter, $strValue);
				} else {
					$strOutput .= $strValue;
				}
				$blnIsFirst = false;
			}
		}

		return $strOutput;
	 }
}
?>
