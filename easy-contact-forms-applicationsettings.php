<?php

/**
 * @file
 *
 * 	EasyContactFormsApplicationSettings class definition
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
 * 	EasyContactFormsApplicationSettings
 *
 */
class EasyContactFormsApplicationSettings extends EasyContactFormsBase {

	/**
	 * 	EasyContactFormsApplicationSettings class constructor
	 *
	 * @param boolean $objdata
	 * 	TRUE if the object should be initialized with db data
	 * @param int $new_id
	 * 	object id. If id is not set or empty a new db record will be created
	 */
	function __construct($objdata = FALSE, $new_id = NULL) {

		$this->type = 'ApplicationSettings';

		$this->fieldmap = array(
				'id' => NULL,
				'Description' => '',
				'TinyMCEConfig' => '',
				'UseTinyMCE' => 0,
				'ApplicationWidth' => 0,
				'ApplicationWidth2' => 0,
				'DefaultStyle' => '',
				'DefaultStyle2' => '',
				'SecretWord' => '',
				'NotLoggenInText' => '',
				'SendFrom' => '',
			);

		if ($objdata) {
			$this->init($new_id);
		}

	}

	/**
	 * 	getDeleteStatements
	 *
	 * 	prepares delete statements to be executed to delete a
	 * 	applicationsetting record
	 *
	 * @param int $id
	 * 	object id
	 *
	 * @return array
	 * 	the array of statements
	 */
	function getDeleteStatements($id) {

		$stmts[] = "DELETE FROM #wp__easycontactforms_applicationsettings WHERE id=$id;";

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

		$request = EasyContactFormsUtils::parseRequest($request, 'UseTinyMCE', 'boolean');
		$request = EasyContactFormsUtils::parseRequest($request, 'ApplicationWidth', 'int');
		$request = EasyContactFormsUtils::parseRequest($request, 'ApplicationWidth2', 'int');

		parent::update($request, $id);

	}

	/**
	 * 	getInstance
	 *
	 * 	Returns a single EasyContactFormsApplicationSettings instance
	 *
	 *
	 * @return object
	 * 	the EasyContactFormsApplicationSettings instance
	 */
	function getInstance() {

		static $obj;
		if (!isset($obj)) {
			$obj = new EasyContactFormsApplicationSettings(TRUE, 1);
			if ($obj->get('SecretWord') == '') {
				$obj->set('SecretWord', md5('mt=' . microtime()));
				$obj->save();
			}
		}
		return $obj;

	}

	/**
	 * 	getEmailTemplate
	 *
	 * 	Makes a list of object fields available for adding into the email
	 * 	template
	 *
	 * @param string $type
	 * 	An object type
	 */
	function getEmailTemplate($type) {
	}

	/**
	 * 	getMainForm
	 *
	 * 	prepares the view data and finally passes it to the html template
	 *
	 * @param array $formmap
	 * 	request data
	 */
	function getMainForm($formmap) {

		$formmap['oid'] = '1';
		$query = "SELECT * FROM #wp__easycontactforms_applicationsettings WHERE id=:id";

		$obj = $this->formQueryInit($formmap, $query);

		$obj->UseTinyMCEChecked = $obj->get('UseTinyMCE') ? 'checked' : '';
		$obj->UseTinyMCE = $obj->get('UseTinyMCE') ? 'on' : 'off';

		$obj->set('TinyMCEConfig', htmlspecialchars($obj->get('TinyMCEConfig')));
		$obj->set('DefaultStyle', htmlspecialchars($obj->get('DefaultStyle'), ENT_QUOTES));
		$obj->set('SecretWord', htmlspecialchars($obj->get('SecretWord'), ENT_QUOTES));
		$obj->set('NotLoggenInText', htmlspecialchars($obj->get('NotLoggenInText')));
		$obj->set('SendFrom', htmlspecialchars($obj->get('SendFrom'), ENT_QUOTES));

		?>
		<input type='hidden' class='ufostddata' id='t' value='<?php echo $obj->type;?>'>
		<input type='hidden' class='ufostddata' id='oid' value='<?php echo $obj->getId();?>'>
		<?php

		require_once 'views/easy-contact-forms-applicationsettingsmainform.php';

	}

}
