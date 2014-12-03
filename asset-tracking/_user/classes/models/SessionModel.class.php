<?php
class SessionModel extends ModelBase {

    private $objDb;

    public function __construct() {
        $this->objDb = ModelBase::getInstance();
    }

	public function GetUser($strUsername) {
		$objStmt = $this->objDb->prepare("
			SELECT
				a.user_id,
				a.name,
				a.password,
				a.type
			FROM
				admin AS a
			WHERE
				a.username = ?;
		");
		$objStmt->bind_param('s', $strUsername);
		$objStmt->execute();
		$objStmt->bind_result($intUserId, $strName, $strPassword, $intType);

		$arrResult = array();
		if ($objStmt->fetch()) {
			$arrResult['user_id'] = $intUserId;
			$arrResult['name'] = $strName;
			$arrResult['password'] = $strPassword;
			$arrResult['type'] = $intType;
		}

		return $arrResult;
	}

	public function GetUserFromCookie() {
		$objStmt = $this->objDb->prepare(
			sprintf("
				SELECT
					a.username
				FROM
					admin AS a
				WHERE
					a.login_cookie = ?;
			")
		);
		$objStmt->bind_param('s', $_COOKIE[Auth::COOKIE_KEY]);
		$objStmt->bind_result($strUsername);
		$objStmt->execute();

		if ($objStmt->fetch()) {
			$strUsername = $strUsername;
		}

		return $strUsername;
	}

	public function LogOut($intUserId) {
		$objStmt = $this->objDb->prepare("
			UPDATE
				admin AS a
			SET
				a.login_cookie = NULL
			WHERE
				a.user_id = ?;
		");
		$objStmt->bind_param('i', $intUserId);
		$objStmt->execute();

		return true;
	}

	public function UpdateCookie($intUserId, $strLoginCookie) {
		$objStmt = $this->objDb->prepare(
			sprintf("
				UPDATE
					admin AS a
				SET
					a.login_cookie = ?,
					a.last_login = ?,
					a.ip_address = ?
				WHERE
					a.user_id = ?;
			")
		);
		$objStmt->bind_param('sssi',
			$strLoginCookie,
			date(DateFormats::DATETIME),
			Application::$RemoteAddress,
			$intUserId
		);
		$objStmt->execute();

		return true;
	}
}
?>
