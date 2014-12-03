<?php
/**
 * File: routes.inc.php
 * Created on: Thu Mar 28 14:06 PDT 2013
 *
 * @author Ian
 *
 */

/**
 * yet another test
 */
$_ROUTER->AddRoute(
	'superduper',
	array(
		'route' => 'yat/test/',
		'module' => 'test',
		'controller' => 'test',
		'action' => 'anothertest'
	)
);

/**
 * testing again
 */
$_ROUTER->AddRoute(
	'groovy',
	array(
		'route' => 'development/test/',
		'module' => 'test',
		'controller' => 'test',
		'action' => 'test'
	)
);

/**
 * you may notice that none of this does anything useful
 */
$_ROUTER->AddRoute(
	'badoing',
	array(
		'route' => 'development/test/buzoingo',
		'module' => 'test',
		'controller' => 'test',
		'action' => 'test'
	)
);
?>