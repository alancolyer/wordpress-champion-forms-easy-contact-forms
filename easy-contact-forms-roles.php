<?php

/**
 * @file
 *
 * 	EasyContactFormsRoles class definition
 */

/*  Copyright Georgiy Vasylyev, 2008-2012 | http://wp-pal.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 * Please read the entire license text in the license.txt file
 */

require_once 'easy-contact-forms-baseclass.php';

/**
 * 	EasyContactFormsRoles
 *
 */
class EasyContactFormsRoles extends EasyContactFormsBase {

	/**
	 * 	EasyContactFormsRoles class constructor
	 *
	 * @param boolean $objdata
	 * 	TRUE if the object should be initialized with db data
	 * @param int $new_id
	 * 	object id. If id is not set or empty a new db record will be created
	 */
	function __construct($objdata = FALSE, $new_id = NULL) {

		$this->type = 'Roles';
		$this->fieldmap = array(
				'id' => NULL,
				'Description' => '',
				'Admin' => 0,
				'Employee' => 0,
			);

		if ($objdata) {
			$this->init($new_id);
		}

	}

	/**
	 * 	getDeleteStatements
	 *
	 * 	prepares delete statements to be executed to delete a role record
	 *
	 * @param int $id
	 * 	object id
	 *
	 * @return array
	 * 	the array of statements
	 */
	function getDeleteStatements($id) {

		$stmts[] = "DELETE FROM #wp__easycontactforms_roles WHERE id=$id;";

		return $stmts;

	}

	/**
	 * 	update. Overrides EasyContactFormsBase::update()
	 *
	 * 	updates an object with request data
	 *
	 * @param array $request
	 * 	request data
	 * @param int $id
	 * 	object id
	 */
	function update($request, $id) {

		$request = EasyContactFormsUtils::parseRequest($request, 'Admin', 'boolean');
		$request = EasyContactFormsUtils::parseRequest($request, 'Employee', 'boolean');

		parent::update($request, $id);

	}

}
