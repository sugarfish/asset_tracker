<?php
class Session {
	private static $objSessionModel;
	private static $blnInstance;
	private static $strModule = Auth::MODULE;
	private static $strController = Auth::CONTROLLER;
	private static $strLoginPage = Auth::DEFAULT_LOGIN_PAGE;
	private static $strLandingPage = Auth::DEFAULT_LANDING_PAGE;

	public static $blnLoggedIn;
	public static $arrUser;
	public static $strError;

	/**
	 * This faux constructor method throws a caller exception.
	 * The DataModel object should never be instantiated!!!
	 *
	 * @return void
	 */
	public final function __construct() {
		try {
			$strMessage = "Session should never be instantiated. All methods and variables are publically statically accessible";
		throw new CustomException(Exceptions::STATIC_CLASS_INSTANTIATION, $strMessage);
		} catch (CustomException $e) {
			print $e;
			exit;
		}
	}

	public static function Initialize($strModule = null, $strController = null, $strLoginPage = null, $strLandingPage = null) {
		if (!self::$blnInstance) {
			if (!session_start()) {
				try {
					$strMessage = "Session was not started";
					throw new CustomException(Exceptions::SESSION_START, $strMessage);
				} catch (CustomException $e) {
					print $e;
					exit;
				}
			} else {
				self::$objSessionModel = new SessionModel;
				self::$strError = null;
				self::$blnLoggedIn = $_SESSION['blnLoggedIn'];
				self::$blnInstance = true;
                self::$arrUser = $_SESSION['arrUser'];

                if (!is_null($strModule) && !is_null($strController) && !is_null($strLoginPage) && !is_null($strLandingPage)) {
					self::$strModule = $strModule;
					self::$strController = $strController;
					self::$strLoginPage = $strLoginPage;
					self::$strLandingPage = $strLandingPage;
				}
			}
		} else {
			$strMessage = "Session already established.\n";
			throw new CustomException(Exceptions::MULTIPLE_SESSION_START, $strMessage);
		}
	}

	public static function LogIn($strUsername, $strPassword = null, $blnAutoLogin = false) {
		$arrResult = self::$objSessionModel->GetUser($strUsername);

		if ($arrResult) {
			$intUserId = $arrResult['user_id'];
			$strStoredPassword = $arrResult['password'];
			$strName= $arrResult['name'];
			$intType = $arrResult['type'];

			if ($strStoredPassword == md5($strPassword) || $blnAutoLogin == true) {
				self::$blnLoggedIn = true;
				$_SESSION['blnLoggedIn'] = self::$blnLoggedIn;
				self::$arrUser = array('user_id' => $intUserId, 'name' => $strName, 'username' => $strUsername, 'type' => $intType);
				$_SESSION['arrUser'] = self::$arrUser;
				session_write_close();

                $strLoginCookie = md5(uniqid(rand(), true));
				$objResult = self::$objSessionModel->UpdateCookie($intUserId, $strLoginCookie);

				if ($objResult) {
					setcookie(Auth::COOKIE_KEY, $strLoginCookie, time() + 60*60*24*7, '/', __COOKIE_DOMAIN__);
					// redirect
					if (!$blnAutoLogin) {
						self::ForwardTo(self::$strLandingPage);
					}
				} else {
					// database error
					self::$strError = 'failed';
					session_write_close();
				}

			} else {
				// password incorrect
				self::$strError = 'failed';
				session_write_close();
			}
		} else {
			// username incorrect
			self::$strError = 'failed';
			session_write_close();
		}
	}

	/**
	 * NULL the login_cookie column in the table, set the cookie timeout in the past
	 * Doing both of these ensures that the user is logged out
	 */
	public static function LogOut() {
		self::$objSessionModel->LogOut(self::$arrUser['user_id']);
		setcookie(Auth::COOKIE_KEY, "", time()-3600);
		if (self::$blnLoggedIn) {
			session_destroy();
		}
		self::ForwardTo(self::$strLoginPage);
	}

	public static function GetUser($blnSecure = false) {
		self::$blnLoggedIn = $_SESSION['blnLoggedIn'];
		if (self::$blnLoggedIn) {
			self::$arrUser = $_SESSION['arrUser'];
		} else {
			$blnValidCookie = false;
			$strLoginCookie = $_COOKIE[Auth::COOKIE_KEY];

			if (strlen($strLoginCookie) > 0) {
				$strUsername = self::$objSessionModel->GetUserFromCookie();
				if (strlen($strUsername) > 0) {
					self::LogIn($strUsername, null, true);
					$blnValidCookie = true;
				}
			}

			if ($blnValidCookie == false) {
				if ($blnSecure) {
					self::ForwardTo(self::$strLoginPage);
					exit;
				}
			}
		}
		return self::$arrUser;
	}

	private static function ForwardTo($strLocation) {
		Application::Redirect(
			UrlHandler::BuildUrl(
				array(
					'module'  	 => self::$strModule,
					'controller' => self::$strController,
					'action' 	 => $strLocation
				)
			)
		);
	}
}
?>
