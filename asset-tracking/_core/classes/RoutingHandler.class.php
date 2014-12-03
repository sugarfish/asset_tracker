<?php
/**
 * File: RoutingHandler.class.php
 * Created on: Thu Sep 21 15:21 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Handles custom routing
 *
 * @package org.sugarfish.core
 * @name RoutingHandler
 */

class RoutingHandler {

	/**
     * String that holds the current URL
     * @access private
     * @static
     * @var string $strUrl
     */
	private static $strUrl;

	/**
     * Array that holds path information
     * @access private
     * @static
     * @var array $arrPathInfo
     */
	private static $arrPathInfo = array();

	/**
     * Array that holds params
     * @access private
     * @static
     * @var array $arrParams
     */
	private static $arrParams = array();

	/**
     * String that holds the current module
     * @access private
     * @static
     * @var string $strModule
     */
	private static $strModule = null;

	/**
     * String that holds the current controller
     * @access private
     * @static
     * @var string $strController
     */
	private static $strController = null;

	/**
     * String that holds the current action
     * @access private
     * @static
     * @var string $strAction
     */
	private static $strAction = null;

	/**
     * Does the current URL match a custom route?
     * @access private
     * @static
     * @var boolean $blnCustomRoute
     */
	private static $blnCustomRoute = false;

	/**
     * Length of matching custom route
     * @access private
     * @static
     * @var integer $intLength
     */
	private static $intLength = 0;

	/**
	 * Determines module, contoller and action and starts processing
	 * @access public
	 * @static
	 * @return void
	 */
	public static function Run() {
		self::$strUrl = preg_replace('/\/\/+/', '/', Application::$RequestUri);

		self::ParseCustomRoutes();

		if (!self::$blnCustomRoute) {

			$arrParams = explode('?', self::$strUrl);
			$arrPathInfo = explode('/', $arrParams[0]);	// <-- we use this later on to get inline params
			self::$arrPathInfo = array_slice($arrPathInfo, 1, 3);

			/**
			 * URI components may contain dashes (-) for display
			 * purposes. We need to remove them so that the module,
			 * controller, and action match up.
			 */
			foreach (self::$arrPathInfo as $intKey => $arrPath) {
				self::$arrPathInfo[$intKey] = str_replace('-', '', $arrPath);
			}

			/**
			 * e.g., convert '/index/index/index[/]' to '/'
			 */
			if (self::$arrPathInfo[0] == __DEFAULT_MODULE__ && self::$arrPathInfo[1] == __DEFAULT_CONTROLLER__ && self::$arrPathInfo[2] == __DEFAULT_ACTION__) {
				Application::Redirect('/');
			}
		
			/**
			 * test for 'index' page
			 */
			if (count(self::$arrPathInfo) == 1 && empty(self::$arrPathInfo[0])) {
				self::$arrPathInfo = array('index', 'index', 'index');
			}

			/**
			 * test for invalid route;
			 * this usually means there aren't enough slash-delimited values to
			 * comprise a route
			 */
			try {
				if (count(self::$arrPathInfo) == 2) {
					throw new CustomException(Exceptions::ERROR, sprintf("Call to undefined route: '%s'", self::$strUrl));
				}
			} catch(CustomException $e) {
				header(HttpResponse::NOT_FOUND);
				print $e;
				exit;
			}

			self::$arrParams = array_slice($arrPathInfo, 4);

			//$arrOutput = array('path' => self::$arrPathInfo, 'params' => self::$arrParams);

			$blnFoundController = false;
			foreach (self::$arrPathInfo as $strNode) {
				if ($blnFoundController) {
					self::$strAction = sprintf('%sAction', $strNode);
					break;
				}
				if (!file_exists(sprintf('%s/%s/controllers/%sController.class.php', __DOCROOT__, self::$strModule, $strNode))) {
					// it's a module
					self::$strModule .= sprintf('%s', $strNode);
				} else {
					// it's a controller
					self::$strController = sprintf('%sController', $strNode);
					$blnFoundController = true;
				}
			}

			try {
				if (!$blnFoundController) {
					throw new CustomException(Exceptions::ERROR, sprintf("Call to undefined route: '%s'", self::$strUrl));
				}
			} catch(CustomException $e) {
				header(HttpResponse::NOT_FOUND);
				print $e;
				exit;
			}

		}

		require_once (sprintf('%s/%s/controllers/%s.class.php', __DOCROOT__, self::$strModule, self::$strController));

		self::SetParams();

		self::RunControllerAction();
	}

	/**
     * Gathers all parameters
     * @access private
     * @static
     * @return void
     */
	private static function SetParams() {
		if (self::$blnCustomRoute) {
			$strParams = substr(self::$strUrl, self::$intLength + 1);

			if (strlen($strParams) > 1) {
				self::$arrParams = array_slice(explode('/', $strParams), 1);
			}			
		}

		if (count(self::$arrParams) > 0) {
			$i = 1;
			foreach (self::$arrParams as $mixValue) {
				if ($i % 2 == 1) {
					$strName = $mixValue;
				} else {
					$_POST[$strName] = $mixValue;
				}
				$i++;
			}
		}
		foreach ($_GET as $strName => $mixValue) {
			$_POST[$strName] = $mixValue;
		}
		self::$arrParams = $_POST;
	}

	/**
     * Runs current controller action
     * @access private
     * @static
     * @return void
     */
	private static function RunControllerAction() {
		// run the controller
		$strController = self::$strController;
		$strAction = self::$strAction;

		/**
		 * prevent internal content being routed through here
		 */
		if (strpos($strAction, 'http') !== false) {
			return false;
		}

		//Application::Log(sprintf('Routing to: %s->%s()', $strController, $strAction)); 

		$_CONTROLLER = new $strController;

		try {
			if (method_exists($_CONTROLLER, $strAction)) {
				$_CONTROLLER->$strAction();
			} else {
				$_CONTROLLER->ActionErrorHandler($strController, $strAction);
			}
		} catch(CustomException $e) {
			header(HttpResponse::NOT_FOUND);
			print $e;
			exit;
		}

		/**
		 * if we're redirecting then we don't need to render anything
		 */
		if ($_CONTROLLER->Layout != Layout::None) {

			if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				ob_start('ob_gzhandler');
			} else {
				ob_start();
			}

			switch ($_CONTROLLER->Layout) {

				case Layout::NotSet:
					if (isset($_CONTROLLER->objHeader)) {
						$_CONTROLLER->objHeader->Render();
					}

					$_CONTROLLER->Render();

					if (isset($_CONTROLLER->objFooter)) {
						$_CONTROLLER->objFooter->Render();
					}
					break;

				case Layout::Plain:
					$_CONTROLLER->Render();

			}

			ob_end_flush();

		}
	}

	/**
     * Collects defined custom routes and determines whether the current route is custom or not
     * @access private
     * @static
     * @return void
     */
	private function ParseCustomRoutes() {
		/**
		 * Custom routing possibilities...
		 *
		 * This uses the routes.inc.php file (in /includes) to set up custom routing.
		 * It's still experimental in nature, but the development of this uncovered some
		 * interesting issues regarding the way the controller was parsing for the
		 * template file.
		 */

		$_ROUTER = new Router;

		// CUSTOM ROUTES
		/**
		 * load any custom routes that have been defined
		 */
		require_once "routes.inc.php";

		if (!count($_ROUTER->Routes)) {
			return;
		} else {
			foreach ($_ROUTER->Routes as $strKey => $arrRoute) {
				$strRoute = rtrim($arrRoute['route'], '/');
				
				if (strpos(self::$strUrl, $strRoute, 1) !== false && strpos(self::$strUrl, $strRoute, 1) === 1 && strlen($strRoute) > self::$intLength) {
					$arrMatchedRoute = $arrRoute;
					self::$intLength = strlen($strRoute);
				}
			}

			if (self::$intLength > 0) {
				self::$strModule = $arrMatchedRoute['module'];
				self::$strController = strpos($arrMatchedRoute['controller'], 'Controller') !== false?$arrMatchedRoute['controller']:sprintf('%sController', $arrMatchedRoute['controller']);
				self::$strAction = strpos($arrMatchedRoute['action'], 'Action') !== false?$arrMatchedRoute['action']:sprintf('%sAction', $arrMatchedRoute['action']);

				try {
					if (!file_exists(sprintf('%s/%s/controllers/%s.class.php', __DOCROOT__, self::$strModule, self::$strController))) {
						throw new CustomException(Exceptions::ERROR, sprintf("Call to undefined route: '%s', from custom route: %s", sprintf('/%s/%s/%s', self::$strModule, str_replace('Controller', '', self::$strController), str_replace('Action', '', self::$strAction)), self::$strUrl));
					}
				} catch(CustomException $e) {
					header(HttpResponse::NOT_FOUND);
					print $e;
					exit;
				}

				self::$blnCustomRoute = true;
			}
		}

		return;
	}

	/**
     * Getter function: returns current module
     * @access public
     * @return string $strModule
     */
	public function GetModule() {
		return self::$strModule;
	}

	/**
     * Getter function: returns current controller
     * @access public
     * @return string $strController
     */
	public function GetController() {
		return self::$strController;
	}

	/**
     * Getter function: returns current action
     * @access public
     * @return string $strAction
     */
	public function GetAction() {
		return self::$strAction;
	}

	/**
     * Getter function: returns current parameters
     * @access public
     * @return array $arrParams
     */
	public function GetParams() {
		return self::$arrParams;
	}
}
?>
