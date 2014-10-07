<?php

/**
 * @file
 *
 * 	EasyContactFormsStrings class definition
 */

/*  Copyright Georgiy Vasylyev, 2008-2012 | http://wp-pal.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 * Please read the entire license text in the license.txt file
 */

/**
 * 	EasyContactFormsStrings
 *
 * 	string operations
 *
 */
class EasyContactFormsStrings {

	/**
	 * 	getInstance
	 *
	 * 	returns a single instance of the EasyContactFormsStrings object
	 *
	 *
	 * @return object
	 * 	the instance
	 */
	function getInstance() {

		static $singinstance;
			if (!isset($singinstance)) {
				$singinstance = new EasyContactFormsT();
			}
		return $singinstance;

	}

	/**
	 * 	get
	 *
	 * @param string $id
	 * 
	 *
	 * @return
	 * 
	 */
	function get($id) {

		$inst = EasyContactFormsStrings::getInstance();
		return $inst->{$id};

	}

}
