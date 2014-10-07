<?php

/**
 * @file
 *
 * 	EasyContactFormsCustomFormFieldTypes class definition
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
 * 	EasyContactFormsCustomFormFieldTypes
 *
 */
class EasyContactFormsCustomFormFieldTypes extends EasyContactFormsBase {

	/**
	 * 	EasyContactFormsCustomFormFieldTypes class constructor
	 *
	 * @param boolean $objdata
	 * 	TRUE if the object should be initialized with db data
	 * @param int $new_id
	 * 	object id. If id is not set or empty a new db record will be created
	 */
	function __construct($objdata = FALSE, $new_id = NULL) {

		$this->type = 'CustomFormFieldTypes';

		$this->fieldmap = array(
				'id' => NULL,
				'Description' => '',
				'Form' => '',
				'Template' => '',
				'CssClass' => '',
				'Settings' => '',
				'Processor' => '',
				'Signature' => '',
				'ListPosition' => 0,
				'ValueField' => 0,
				'HelpLink' => '',
			);

		if ($objdata) {
			$this->init($new_id);
		}

	}

	/**
	 * 	getDeleteStatements
	 *
	 * 	prepares delete statements to be executed to delete a
	 * 	customformfieldtype record
	 *
	 * @param int $id
	 * 	object id
	 *
	 * @return array
	 * 	the array of statements
	 */
	function getDeleteStatements($id) {

		$stmts[] = "DELETE FROM #wp__easycontactforms_customformfieldtypes WHERE id=$id;";

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

		$request = EasyContactFormsUtils::parseRequest($request, 'ListPosition', 'int');
		$request = EasyContactFormsUtils::parseRequest($request, 'ValueField', 'boolean');

		parent::update($request, $id);

	}

}
