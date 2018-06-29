<?php
/**
 * AllLogTest file
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * AllLogTest class
 *
 * This test group will run log tests.
 *
 * @package       Cake.Test.Case
 */
class AllLogTest extends PHPUnit_Framework_TestSuite {

/**
 * suite method, defines tests for this suite.
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Logging related class tests');
		$suite->addTestDirectory(CORE_TEST_CASES . DS . 'Log');
		$suite->addTestDirectory(CORE_TEST_CASES . DS . 'Log' . DS . 'Engine');
		return $suite;
	}
}
