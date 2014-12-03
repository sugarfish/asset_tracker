<?php
/**
 * File: ModelBase.class.php
 * Created on: Mon Oct 11 23:19 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Base model class
 *
 * Connects to MySQL and returns the connection for re-use
 *
 * @package org.sugarfish.core
 * @name ModelBase
 */

class ModelBase extends MySQLi {

    /**
     * Object that holds the current database connection
     * @access private
     * @var object $objHeader
     */
	private static $objDb = null;

    /**
     * Private constructor prevents objects being instantiated directly; class is a Singleton
     * @access private
     * @param string $strServer
     * @param string $strUsername
     * @param string $strPassword
     * @param string $strDatabase
     * @return void
     */
    private function __construct($strServer, $strUsername, $strPassword, $strDatabase) {
        try {
            parent::__construct($strServer, $strUsername, $strPassword, $strDatabase);

            if (mysqli_connect_errno()) {
                throw new Exception(mysqli_connect_error(), mysqli_connect_errno());
            }
        } catch (CustomException $e) {
            print $e;
            exit;
        }
    }

    /**
     * Returns an instance of ModelBase
     * @access public
     * @param string $strServer
     * @param string $strUsername
     * @param string $strPassword
     * @param string $strDatabase
     * @return object $objDb
     */
    public static function GetInstance($strServer = __DB_SERVER__, $strUsername = __DB_USERNAME__, $strPassword = __DB_PASSWORD__, $strDatabase = __DB__) {
        if (!self::$objDb) {
            try {
            	self::$objDb = new ModelBase($strServer, $strUsername, $strPassword, $strDatabase);
            	if (!self::$objDb->set_charset("utf8")) {
            		throw new Exception(sprintf("Error loading character set UTF-8: %s\n", self::$objDb->error));
            	}
            } catch (CustomException $e) {
            	print $e;
            	exit;
        	}
        }

        return self::$objDb;
    }

    /**
     * Returns an array of stdClass objects, one for each row
     * @access public
     * @param string $objStmt
     * @return array $arrRows
     */
    public function BindRowsAsObjects($objStmt) {
        if (is_null($objStmt)) {
            return false;
        }

        $arrRows = array();

        $objMetaData = $objStmt->result_metadata();
        $objFields = $objMetaData->fetch_fields();
        foreach ($objFields as $objField) {
            $arrResult[$objField->name] = "";
            $arrResults[$objField->name] = &$arrResult[$objField->name];
        }

        call_user_func_array(array($objStmt, 'bind_result'), $arrResults);

        // create object of results and array of objects
        while ($objStmt->fetch()) {
            $objResult = new stdClass;

            foreach ($arrResults as $mixKey => $strValue) {
                $objResult->$mixKey = $strValue;
            }

            array_push($arrRows, $objResult);
        }

        return $arrRows;
    }
}
?>
