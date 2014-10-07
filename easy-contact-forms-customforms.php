<?php

/**
 * @file
 *
 * 	EasyContactFormsCustomForms class definition
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
 * 	EasyContactFormsCustomForms
 *
 */
class EasyContactFormsCustomForms extends EasyContactFormsBase {

	/**
	 * 	EasyContactFormsCustomForms class constructor
	 *
	 * @param boolean $objdata
	 * 	TRUE if the object should be initialized with db data
	 * @param int $new_id
	 * 	object id. If id is not set or empty a new db record will be created
	 */
	function __construct($objdata = FALSE, $new_id = NULL) {

		$this->type = 'CustomForms';

		$this->fieldmap = array(
				'id' => NULL,
				'Description' => '',
				'NotificationSubject' => '',
				'SendFrom' => '',
				'SendConfirmation' => 0,
				'ConfirmationSubject' => '',
				'ConfirmationText' => '',
				'Redirect' => 0,
				'RedirectURL' => '',
				'ShortCode' => '',
				'Template' => 0,
				'SubmissionSuccessText' => '',
				'StyleSheet' => '',
				'HTML' => '',
				'SendFromAddress' => '',
				'ShowSubmissionSuccess' => 0,
				'SuccessMessageClass' => '',
				'FailureMessageClass' => '',
				'Width' => 0,
				'WidthUnit' => '',
				'LineHeight' => 0,
				'LineHeightUnit' => '',
				'FormClass' => '',
				'FormStyle' => '',
				'Style' => '',
			);

		if ($objdata) {
			$this->init($new_id);
		}

	}

	/**
	 * 	getDeleteStatements
	 *
	 * 	prepares delete statements to be executed to delete a customform
	 * 	record
	 *
	 * @param int $id
	 * 	object id
	 *
	 * @return array
	 * 	the array of statements
	 */
	function getDeleteStatements($id) {

		$stmts[] = "DELETE FROM #wp__easycontactforms_customforms_mailinglists WHERE CustomForms=$id;";

		$query = "SELECT id FROM #wp__easycontactforms_customformfields WHERE CustomForms=$id;";
		EasyContactFormsDB::cDelete($query, 'CustomFormFields');

		$query = "SELECT id FROM #wp__easycontactforms_customformsentries WHERE CustomForms=$id;";
		EasyContactFormsDB::cDelete($query, 'CustomFormsEntries');

		$stmts[] = "DELETE FROM #wp__easycontactforms_customforms WHERE id=$id;";

		return $stmts;

	}

	/**
	 * 	getEmptyObject. Overrides EasyContactFormsBase::getEmptyObject()
	 *
	 * 	creates and initializes a new CustomForm
	 *
	 * @param array $map
	 * 	request data
	 * @param array $fields
	 * 	a field array
	 *
	 * @return object
	 * 	the initialized instance
	 */
	function getEmptyObject($map, $fields = NULL) {

		$fields = (object) array();
		$fields->ConfirmationText = '{submission}';
		$fields->Width = 0;
		$fields->WidthUnit = 'px';
		$fields->LineHeight = 10;
		$fields->LineHeightUnit = 'px';
		$fields->Style = 'formsstd';

		return parent::getEmptyObject($map, $fields);

	}

	/**
	 * 	add
	 *
	 * @param  $map
	 * 
	 *
	 * @return
	 * 
	 */
	function add($map) {

		$cf = 0;
		foreach ($map as $key => $value) {
			if (!EasyContactFormsUtils::beginsWith($key, 'hidden-')) {
				continue;
			}
			$cf = intval(EasyContactFormsUtils::cutPrefix($key, 'hidden-'));
			break;
		}
		if ($cf == 0) {
			return;
		}
		$form = EasyContactFormsClassLoader::getObject('CustomForms', true, $cf);
		if (!$form) {
			return;
		}

		$query = "SELECT
				CustomFormFields.id
			FROM
				#wp__easycontactforms_customformfields AS CustomFormFields
			WHERE
				CustomFormFields.CustomForms='$cf'";

		$flds = EasyContactFormsDB::getObjects($query);
		foreach ($flds as $fldid) {
			$fld = EasyContactFormsClassLoader::getObject('CustomFormFields', true, $fldid->id);
			$form->validate($fld, $map);
		}
		$response = (object) array();
		$response->formid = "ufo-form-id-$cf";
		$status = 1;
		if (isset($form->sendBack) && $form->sendBack == TRUE) {
			if (isset($form->errors) && count($form->errors) > 0) {
				$errors = array();
				foreach ($form->errors as $fld=>$err) {
					$errors[] = "<strong>{$fld}</strong><br />{$err}";
				}
				$errors = implode('</div><div>', $errors);
				$errors = "<div>$errors</div>";

				$submitfailureclass = $form->isEmpty('FailureMessageClass') ? 'ufo-form-submit-failure' : $form->get('FailureMessageClass');

				$response->className = $submitfailureclass;
				$response->text = $errors;
			}
			$response->status = 2;
		}
		else {
			$form->newEntry($map);
			$response->status = 0;
			if ($form->get('ShowSubmissionSuccess') && !$form->isEmpty(SubmissionSuccessText)) {

				$submitsuccessclass = $form->isEmpty('SuccessMessageClass') ? 'ufo-form-submit-success' : $form->get('SuccessMessageClass');

				$response->className = $submitsuccessclass;
				$response->text = $form->get('SubmissionSuccessText');
				$response->status = 1;
			}
			$url = $form->get('RedirectURL');
			if ($form->get('Redirect') && !empty($url)) {
				$response->status = 1;
				$response->url = $url;
			}
		}
		header('Content-Type: application/javascript');
		echo (json_encode($response));

	}

	/**
	 * 	newEntry
	 *
	 * @param  $map
	 * 
	 *
	 * @return
	 * 
	 */
	function newEntry($map) {

		$sign = md5(EasyContactFormsSecurityManager::getServerPwd() . $_SERVER['REMOTE_ADDR']);
		if ($this->processSpam(!isset($map['ufo-sign']) || $map['ufo-sign'] != $sign, $map)) {
			return;
		}
		$fldvalues = array();
		foreach ($map as $key => $value) {
			if (!EasyContactFormsUtils::beginsWith($key, 'id-')) {
				continue;
			}
			$fldid = intval(EasyContactFormsUtils::cutPrefix($key, 'id-'));
			$fldvalues[$fldid] = htmlspecialchars($value, ENT_QUOTES);
		}
		if ($this->processSpam(count($fldvalues) == 0, $map)) {
			return;
		}
		$formid = $this->get('id');
		if ($this->processSpam(!isset($formid) || empty($formid), $map)) {
			return;
		}
		$s = implode(',', array_keys($fldvalues));

		$query = "SELECT
				CustomFormFields.id,
				CustomFormFields.Settings,
				CustomFormFieldTypes.id AS tid,
				CustomFormFieldTypes.Description,
				CustomFormFieldTypes.Processor,
				CustomFormFieldTypes.ValueField
			FROM
				#wp__easycontactforms_customformfields AS CustomFormFields
			INNER JOIN
				#wp__easycontactforms_customformfieldtypes AS CustomFormFieldTypes
					ON
						CustomFormFields.Type=CustomFormFieldTypes.id
			WHERE
				CustomFormFields.CustomForms='$formid'
				AND CustomFormFields.id IN ($s)
			ORDER BY
				CustomFormFields.ListPosition";

		$fields = EasyContactFormsDB::getObjects($query);
		if ($this->processSpam(count($fields) == 0, $map)) {
			return;
		}
		$text = new EasyContactFormsSimpleXML('<div/>');
		$clientid = 0;
		if (isset($map['easycontactusr']) && isset($map['easycontactusr']->id) && !empty($map['easycontactusr']->id)) {
			$clientid = intval($map['easycontactusr']->id);
			$text->addAttribute('userid', $clientid);
		}
		global $current_user;
		$siteuserid = $current_user->ID;
		if (!empty($siteuserid)) {
			$siteuserid = intval($siteuserid);
		}
		$clientemail = '';
		foreach($fields as $fld) {
			$value = isset($fldvalues[$fld->id]) ? $fldvalues[$fld->id] : null;
			if ($this->processSpam(is_null($value), $map)) {
				return;
			}
			$xml = simplexml_load_string($fld->Settings);
			$default = $this->getFieldValue($xml, true, 'DefaultValue', 'SetDefaultValue');
			$required = (string) $xml->Required;
			$required = $required == 'on';
			$spam = $required && $fld->tid == 14 && $value != $default;
			if ($this->processSpam($spam, $map)) {
				return;
			}
			$spam = (!$required) && $fld->tid == 14 && $value != '';
			if ($this->processSpam($spam, $map)) {
				return;
			}
			$isblank = isset($xml->IsBlankValue) && (string) $xml->IsBlankValue == 'on';
			if ($value == $default && $isblank) {
				continue;
			}
			if (!$fld->ValueField) {
				continue;
			}

			$validate = isset($xml->Validate) && (string) $xml->Validate == 'on';
			if ($clientemail == '' && $fld->tid == 5 && $validate) {
				$clientemail = $value;
			}
			$fldlabel = (string) $xml->Label;
			$fldlabel = $fldlabel == '' ? $fld->Description : $fldlabel;
			$displayvalue = $value;
			$fld = EasyContactFormsClassLoader::getObject('CustomFormFields', true, $fld->id);
			$phase = (object) array('index' => 6);
			include $fld->getTMPFileName('proc');
			$iddiv = $text->addChild('div');
			$iddiv->addAttribute('id', $fld->get('id'));
			$h1 = $iddiv->addChild('h1', $value);
			$h1->addAttribute('style', 'display:none');
			$ldiv = $iddiv->addChild('div');
			$label = $ldiv->addChild('label', $fldlabel);
			$label->addAttribute('class', 'ufo-cform-label');
			$iddiv->addChild('div', $displayvalue);
		}
		$cfe = EasyContactFormsClassLoader::getObject('CustomFormsEntries', true);
		$cfe->set('Date', date(DATE_ATOM));
		$cfe->set('Content', $text->asXML());
		$cfe->set('CustomForms', $formid);
		if (!empty($siteuserid)) {
			$cfe->set('SiteUser', $siteuserid);
		}
		$cfe->set('CustomForms', $formid);
		$cfe->save();

		unset($text->attributes()->userid);
		foreach ($text->children() as $child) {
			unset($child->attributes()->id);
			unset($child->h1);
		}
		$text->addAttribute('class', 'ufo-form-envelope');
		$this->doEmailing($text->asCHTML(), $clientemail, $clientid);

	}

	/**
	 * 	processSpam
	 *
	 * @param  $condition
	 * 
	 * @param  $map
	 * 
	 *
	 * @return
	 * 
	 */
	function processSpam($condition, $map) {

		return $condition;

	}

	/**
	 * 	check
	 *
	 * @param  $text
	 * 
	 * @param  $signature
	 * 
	 * @param  $pbk
	 * 
	 * @param  $b
	 * 
	 *
	 * @return
	 * 
	 */
	function check($text, $signature, $pbk, $b) {

				$set = split(' ', $signature);
				$hash = '';
				for($i=0; $i<count($set); $i++){
						$code = bcpowmod($set[$i], $pbk, $b);
						while(bccomp($code, '0') != 0){
								$ascii = bcmod($code, '256');
								$code = bcdiv($code, '256', 0);
								$hash .= chr($ascii);
						}
				}
				return ($hash == md5($text));

	}

	/**
	 * 	copy
	 *
	 * 	Copies the form
	 *
	 * @param none $map
	 * 	request data
	 */
	function copy($map) {

		$formid = intval($map['oid']);
		$form = EasyContactFormsClassLoader::getObject('CustomForms', true, $formid);
		if (!$form) {
			return '';
		}
		$newform = $form->sibling();
		$newformid = $newform->get('id');

		$query = "SELECT
				CustomFormFields.id
			FROM
				#wp__easycontactforms_customformfields AS CustomFormFields
			WHERE
				CustomFormFields.CustomForms='$formid'
				AND CustomFormFields.Type IN (1,
					2)
			ORDER BY
				ListPosition";

		$cids = EasyContactFormsDB::getObjects($query);
		foreach ($cids as $cid) {
			$oldcid = $cid->id;
			$cfield = EasyContactFormsClassLoader::getObject('CustomFormFields', true, $oldcid);
			$container = $cfield->sibling(array('CustomForms' => $newformid, 'Description' => $cfield->get('Description')));
			$cntid = $container->get('id');
			$container->set('FieldSet', $cntid);
			$container->save();

			$query = "SELECT
							CustomFormFields.id
						FROM
							#wp__easycontactforms_customformfields AS CustomFormFields
						WHERE
							CustomFormFields.CustomForms='$formid'
							AND CustomFormFields.Type NOT IN (1,
								2)
							AND CustomFormFields.FieldSet='$oldcid'
						ORDER BY
							ListPosition";

			$fldids = EasyContactFormsDB::getObjects($query);
			foreach ($fldids as $fldid) {
				$cfield = EasyContactFormsClassLoader::getObject('CustomFormFields', true, $fldid->id);

				$sibling = $cfield->sibling(array('CustomForms' => $newformid, 'FieldSet' => $cntid, 'Description' => $cfield->get('Description')));

				$sibling->updateTemplate();
			}
		}
		$map['oid'] = $newformid;
		$map['m'] = 'show';
		EasyContactFormsRoot::processEvent($map);

	}

	/**
	 * 	Lists installed client side form styles
	 *
	 *
	 * @return
	 * 
	 */
	function getAvaliableStyles() {

		$ds = DIRECTORY_SEPARATOR;
		$styleroot = dirName(__FILE__) . $ds . 'forms' . $ds . 'styles';
		$dirs = array();
		$dir = dir($styleroot);
		$current = $this->get('Style');;
		while(($cdir = $dir->read()) !== false) {
			if($cdir != '.' && $cdir != '..' && is_dir($styleroot . $ds . $cdir)) {
				$selected = $cdir == $current ? ' selected' : '';
				$dirs[] = "<option{$selected}>{$cdir}</option>";
			}
		}
		$dir->close();
		return implode('', $dirs);

	}

	/**
	 * 	getForm
	 *
	 * @param  $map
	 * 
	 *
	 * @return
	 * 
	 */
	function getForm($map) {

		$fid = isset($map['fid']) ? intval($map['fid']) : 0;
		if ($fid == 0) {
			return '';
		}
		$cfid = isset($map['hidden-' . $fid]);
		$form = new EasyContactFormsCustomForms(true, $fid);
		if (!$form->isValid()) {
			return '';
		}
		$map = EasyContactFormsSecurityManager::getRights($map);
		$form->user = $map['easycontactusr'];
		if (!$cfid) {
			$html = $form->preprocess();
		}
		else {
			$html = $form->preprocess($map);
			if (!isset($form->sendBack) || $form->sendBack == FALSE) {
				$form->newEntry($map);
				$html = array();
				if (!$form->isEmpty('Style')) {
					ob_start();
					$ds = DIRECTORY_SEPARATOR;

					require_once dirName(__FILE__) . $ds . 'forms' . $ds . 'styles' . $ds . $form->get('Style') . $ds . 'easy-contact-forms-getstyle.php';

					$html[] = ob_get_contents();
					ob_end_clean();
				}
				if (!$form->isEmpty('StyleSheet')) {
					$html[] = '<style>' . $form->get('StyleSheet') . '</style>';
				}

				$submitsuccessclass = $form->isEmpty('SuccessMessageClass') ? 'ufo-form-submit-success' : $form->get('SuccessMessageClass');

				$submitsuccesstext = $form->isEmpty('SubmissionSuccessText') ? '' : $form->get('SubmissionSuccessText');
				$html[] = "<div class='$submitsuccessclass'>{$submitsuccesstext}</div>";
				$html = implode('', $html);
			}
		}

		return $html;

	}

	/**
	 * 	getStyle
	 *
	 *
	 * @return
	 * 
	 */
	function getStyle() {

		$id = $this->get('id');

		$query = "SELECT
				CustomFormFields.id,
				CustomFormFields.Settings
			FROM
				#wp__easycontactforms_customformfields AS CustomFormFields
			WHERE
				CustomFormFields.CustomForms='$id'
				AND CustomFormFields.Type='14';";

		$fields = EasyContactFormsDB::getObjects($query);
		$classes = array();
		foreach ($fields as $fld) {
			$xml = simplexml_load_string($fld->Settings);
			$classname = (string) $xml->RowCSSClass;
			if (empty($classname)) {
				$classes[]='.ufo-row-' . $fld->id . '{display:none;}';
			}
		}
		return $this->fields->StyleSheet . implode('', $classes);

	}

	/**
	 * 	preprocess
	 *
	 * @param  $pvarmap
	 * 
	 *
	 * @return
	 * 
	 */
	function preprocess($pvarmap = null) {
		$cf = $this->get('id');

		$query = "SELECT
				CustomFormFields.id
			FROM
				#wp__easycontactforms_customformfields AS CustomFormFields
			WHERE
				CustomFormFields.CustomForms='$cf'";

		$flds = EasyContactFormsDB::getObjects($query);
		$varmap = is_null($pvarmap) ? array() : $pvarmap;
		$currentuser = $this->user->id;
		foreach ($flds as $fldid) {
			$fld = EasyContactFormsClassLoader::getObject('CustomFormFields', true, $fldid->id);
			$varmap = $this->preprocessField($fld, $varmap, !is_null($pvarmap));
		}
		$html = $this->get('HTML');
		foreach ($varmap as $key=>$value){
			if (!is_string($value)) {
				continue;
			}
			$html = str_replace('{' . $key. '}', $value, $html);
		}

		$html = str_replace('{ufosignature}', md5(EasyContactFormsSecurityManager::getServerPwd() . $_SERVER['REMOTE_ADDR']), $html);

		$errors = '';
		if (isset($this->errors) && count($this->errors) > 0){
			$errors = array();
			foreach ($this->errors as $fld=>$err) {
				$errors[] = "<strong>{$fld}</strong><br />{$err}";
			}
			$errors = implode('</div><div>', $errors);
			$errors = "<div>$errors</div>";

			$submitfailureclass = $this->isEmpty('FailureMessageClass') ? 'ufo-form-submit-failure' : $this->get('FailureMessageClass');

			$errors = "<div class='$submitfailureclass'>$errors</div>";
		}

		return $errors.$html;

	}

	/**
	 * 	preprocessField
	 *
	 * @param  $fld
	 * 
	 * @param  $varmap
	 * 
	 * @param  $validate
	 * 
	 *
	 * @return
	 * 
	 */
	function preprocessField($fld, $varmap, $validate) {

		if ($validate) {
			$varmap = $this->validate($fld, $varmap);
		}

		$xml = simplexml_load_string($fld->get('Settings'));
		$currentuser = $this->user->id;
		$fldid = $fld->get('id');
		$varmap['display-' . $fldid] = '';

		if (empty($varmap['id-' . $fldid])) {
			$varmap['id-' . $fldid] = $this->getFieldValue($xml, true, 'DefaultValue', 'SetDefaultValue');
		}
		if (!$validate) {
			$phase = (object) array('index' => 3);
			include $fld->getTMPFileName('proc');
		}
		if (!isset($xml->SetContactOptions)) {
			return $varmap;
		}
		$ruo = (string) $xml->RegistredUsersOptions;

		$test1 = $this->getFieldValue($xml, false, 'LinkToAppField', 'SetContactOptions', $ruo != 'none', !empty($currentuser));

		if (!$test1) {
			return $varmap;
		}

		$link = (string) $xml->LinkToAppField;
		$link = explode('_', $link);
		if ($link[0] != 'Users') {
			return $varmap;
		}

		if (!isset($this->userobj)) {
			$this->userobj = EasyContactFormsClassLoader::getObject('Users', true, $currentuser);
		}

		$userval = $this->userobj->get($link[1]);
		if ($ruo == 'hidefilled' && !empty($userval)) {
			$varmap['display-' . $fldid] = "display:none;";
			$varmap['id-' . $fldid] =	$userval;
		}
		else {
			$varmap['id-' . $fldid] = $userval;
		}
		return $varmap;

	}

	/**
	 * 	getFieldValue
	 *
	 *
	 * @return
	 * 
	 */
	function getFieldValue() {

		$args = func_get_args();
		$node = $args[0];
		$sting = $args[1];

		$valueName = $args[2];
		$value = (string) $node->$valueName;

		if (empty($value)) {
			return $sting ? '' : FALSE;
		}

		for ($i = 3; $i < count($args); $i++) {
			$arg = $args[$i];
			if (is_string($arg)) {
				$flag = (string) $node->$args[$i];
				if ($flag != 'on') {
					return $sting ? '' : FALSE;
				}
			}
			else {
				if ($arg === FALSE) {
					return $sting ? '' : FALSE;
				}
			}
		}
		return $sting ? $value : TRUE;

	}

	/**
	 * 	validate
	 *
	 * @param  $fld
	 * 
	 * @param  $varmap
	 * 
	 *
	 * @return
	 * 
	 */
	function validate($fld, $varmap) {

		$txml = simplexml_load_string($fld->get('Template'));
		$validation = (string) $txml->Validation;
		if (empty($validation)) {
			$phase = (object) array('index' => 7);
			include $fld->getTMPFileName('proc');
			return $varmap;
		}
		list($first, $second) = explode('ufoForms.validate(', $validation, 2);
		list($validation, $second) = explode(');', $second, 2);
		$config = json_decode($validation);
		if (is_null($config)) {
			return $varmap;
		}
		$fldid = $fld->get('id');
		$fldvalue = $varmap['id-' . $fldid];
		$valid = NULL;
		$phase = (object) array('index' => 5);
		include $fld->getTMPFileName('proc');
		if (is_null($valid)) {
			foreach ($config->events as $key=>$handlers) {
				if ($key != 'blur') {
					continue;
				}
				foreach ($handlers as $handler) {
					$valid = $this->checkValid($fld, $fldvalue, $handler, $config);
					if (!$valid) {
						break;
					}
				}
			}
		}
		if (!$valid) {
			unset($varmap['id-' . $fldid]);
			$varmap['id-' . $fldid] = '';
		}
		return $varmap;

	}

	/**
	 * 	checkValid
	 *
	 * @param  $fld
	 * 
	 * @param  $fldvalue
	 * 
	 * @param  $handler
	 * 
	 * @param  $config
	 * 
	 *
	 * @return
	 * 
	 */
	function checkValid($fld, $fldvalue, $handler, $config) {

		if ($handler == 'default') {
			return TRUE;
		}

		if (!isset($config->Required) && $this->isEmptyValue($fldvalue, $config)) {
			return TRUE;
		}

		if ($handler == 'required' && $this->isEmptyValue($fldvalue, $config)) {
			$this->processInvalid($fld, 'required', $config);
			return FALSE;
		}

		if ($handler == 'minmax') {
			$min = isset($config->min) ? intval($config->min) : -1;
			$max = isset($config->max) ? intval($config->max) : PHP_INT_MAX;
			$invalid = strlen($fldvalue) < $min || strlen($fldvalue) > $max;
			if ($invalid) {
				$this->processInvalid($fld, 'minmax', $config);
				return FALSE;
			}
		}

		if ($handler == 'minmaxnumeric') {
			$min = isset($config->min) ? intval($config->min) : (~PHP_INT_MAX);
			$max = isset($config->max) ? intval($config->max) : PHP_INT_MAX;
			$invalid = $fldvalue < $min || $fldvalue > $max;
			if ($invalid) {
				$this->processInvalid($fld, 'minmaxnumeric', $config);
				return FALSE;
			}
		}

		$re = array();
		$re['email']='/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,6})+$/';
		$re['currency']='/^-?(?:0|[1-9]\d{0,2}(?:,?\d{3})*)(?:\.\d+)?$/';
		$re['integer']='/^\-?[0-9]+$/';
		$re['numeric']='/^[0-9]+$/';

		if (isset($re[$handler])) {
			$re = $re[$handler];
			$test = preg_match($re, $fldvalue) ? TRUE : FALSE;
			if (!$test) {
				$this->processInvalid($fld, $handler, $config);
				return FALSE;
			}
		}

		return TRUE;

	}

	/**
	 * 	processInvalid
	 *
	 * @param  $fld
	 * 
	 * @param  $handler
	 * 
	 * @param  $config
	 * 
	 *
	 * @return
	 * 
	 */
	function processInvalid($fld, $handler, $config) {

		$this->sendBack = TRUE;
		if (!isset($this->errors)) {
			$this->errors = array();
		}
		$this->errors[$fld->get('Description')] = $config->RequiredMessage;

	}

	/**
	 * 	isEmptyValue
	 *
	 * @param  $fldvalue
	 * 
	 * @param  $config
	 * 
	 *
	 * @return
	 * 
	 */
	function isEmptyValue($fldvalue, $config) {

		if ($config->isDefaultBlank && $config->defaultValue == $fldvalue) {
			return TRUE;
		}

		if (empty($fldvalue)) {
			return TRUE;
		}

		return FALSE;

	}

	/**
	 * 	doEmailing
	 *
	 * @param  $submission
	 * 
	 * @param  $clientemail
	 * 
	 * @param  $clientid
	 * 
	 *
	 * @return
	 * 
	 */
	function doEmailing($submission, $clientemail, $clientid) {

		require_once 'easy-contact-forms-backoffice.php';
		$bo = new EasyContactFormsBackOffice();

		$submission = '<style>label.ufo-cform-label {font-weight:bold;padding:10px 0 3px 0;}</style>' . $submission;

		if ($this->get('SendConfirmation')) {
			if (empty($clientemail) && !empty($clientid)) {
				$userdata = $bo->getSenderData($clientid);
				$clientemail = $userdata->email;
			}
			if (!empty($clientemail)) {
				$message = (object) array();
				$message->subject = $this->get('ConfirmationSubject');
				$message->body = str_replace('{submission}', $submission, $this->get('ConfirmationText'));
				$message->ishtml = true;
				$sender = (object) array();
				$sender->name = $this->get('SendFrom');
				$email = $this->get('SendFromAddress');
				$list = array($clientemail);
				$bo->send($message, $list, $sender, $email);
			}
		}

		$recps = $bo->getListMemberEmails('CustomForms', $this->get('id'));
		if (count($recps) == 0) {
			return;
		}
		$message = (object) array();
		$message->subject = $this->get('NotificationSubject');
		$message->body = $submission;
		$message->ishtml = true;
		$sender = (object) array();
		$sender->name = $this->get('Description');
		$sender->email = EasyContactFormsApplicationSettings::getInstance()->get('SendFrom');;
		$bo->send($message, $recps, $sender);

	}

	/**
	 * 	preview
	 *
	 * @param  $map
	 * 
	 *
	 * @return
	 * 
	 */
	function preview($map) {
		$ds = DIRECTORY_SEPARATOR;

		$fid = intval($map['oid']);
		$map['ufo-skipoutput']=1;
		$formhtml = $this->refreshForm($map);
		$spec = $this->getfilespec($fid);

		$query = "SELECT
				CustomForms.id,
				CustomForms.Description
			FROM
				#wp__easycontactforms_customforms AS CustomForms";

		$availableforms = EasyContactFormsDB::getObjects($query);

		$links = array();
			$links[] = "<ul style='width:90%;float:right;' class='ufo-tab-header ufo-tab-left'>";
		foreach ($availableforms as $aform) {
			$links[] = "<li>";
			$active = $aform->id == $fid ? 'ufo-active' : '';

			$links[] = "<a href='javascript:;'  class='ufo-preview-list $active' onclick='ufoCf.refreshForm(this, {$aform->id})'><span>{$aform->Description}</span></a>";

			$links[] = "</li>";
		}
			$links[] = "</ul>";
		$links = implode('', $links);

		$index = "<html>";
		$js = "config = {};";
		$js .= "config.url='" . admin_url( 'admin-ajax.php' ) . "';";
		$js .= "config.initial = {t:'CustomForms', m:'preview'};";
		$js .= "config.bodyid = 'ufo-formpreview-wrapper';";
		$js .= "config.resources = {};";
		$js .= "var appManConfig = config;";

		$index .= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>";

		$index .= "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>";
		$index .= "<head>";
		$index .= "<script type='text/javascript'>$js</script>";

		$index .= "<script type='text/javascript' src='" . EASYCONTACTFORMS__engineWebAppDirectory . "/js/jquery.js'></script>";

		$index .= "<script type='text/javascript' src='" . EASYCONTACTFORMS__engineWebAppDirectory . "/js/as.js'></script>";

		$index .= "<script type='text/javascript' src='" . EASYCONTACTFORMS__engineWebAppDirectory . "/easy-contact-formshtml.js'></script>";

		$index .= "<style>";
		$index .= "*{margin:0;padding:0}";
		$index .= "html, body {height:100%;width:100%;overflow:hidden}";
		$index .= "table {height:100%;width:100%;table-layout:static;border-collapse:collapse}";
		$index .= "td{height:100%}";
		$index .= "iframe {height:100%;width:100%}";
		$index .= ".content {height:100%}";
		$index .= "</style>";
		$index .= "</head>";
		$index .= "<body>";
		$styleName = EasyContactFormsApplicationSettings::getInstance()->get('DefaultStyle2');
		ob_start();

		require_once _EASYCONTACTFORMS_PLUGIN_PATH . $ds . 'styles' . $ds . $styleName . $ds . 'easy-contact-forms-getstyle.php';

		$index .= ob_get_contents();
		ob_end_clean();
		$index .= "<table id='ufo-formpreview-wrapper'><tr>";
		$index .= "<td style='width:15%;vertical-align:top;padding:10px 0;background:#f5f5f5;border-right:1px solid #bbb'>";
		$index .= $links;
		$index .= "</td>";
		$index .= "<td style='width:85%;'>";

		$index .= "<iframe frameborder=0 src='{$spec->fileurl}' class='ufo-form-preview ufo-id-link' style='overflow:auto' id='ufo-form-preview'></iframe>";

		$index .= "</td>";
		$index .= "<tr></table></html>";

		EasyContactFormsUtils::createFolder($spec->dir);
		EasyContactFormsUtils::overwritefile($spec->dir . $ds . 'frame.html', $index);
		echo json_encode(array('url' => $spec->webfolder . '/frame.html'));

	}

	/**
	 * 	getfilespec
	 *
	 * @param  $fid
	 * 
	 *
	 * @return
	 * 
	 */
	function getfilespec($fid) {

		$ds = DIRECTORY_SEPARATOR;
		$pwd = EasyContactFormsSecurityManager::getServerPwd();
		$spec = (object) array();
		$spec->htmlfile = 'form-' . $fid . '.html';
		$spec->subpath = array();
		$spec->subpath[] = 'forms';
		$spec->subpath[] = 'tmp';
		$spec->subpath[] = md5($pwd);
		$spec->dir = dirName(__FILE__) . $ds . implode($ds, $spec->subpath);
		$spec->filepath = $spec->dir . $ds . $spec->htmlfile;
		$spec->webfolder = EASYCONTACTFORMS__engineWebAppDirectory . '/' . implode('/', $spec->subpath);
		$spec->fileurl = $spec->webfolder . '/' . $spec->htmlfile;
		return $spec;

	}

	/**
	 * 	refreshForm
	 *
	 * @param  $map
	 * 
	 *
	 * @return
	 * 
	 */
	function refreshForm($map) {

		$fid = intval($map['oid']);
		$form = new EasyContactFormsCustomForms(true, $fid);
		$spec = $this->getfilespec($fid);
		$admin = isset($map['admin']);
		if (!$admin == 0) {
			$form->user = EasyContactFormsSecurityManager::getGuest();
		}
		$html = $form->preprocess();
		$text = array();

		$text[] = "<script type='text/javascript' src='" . EASYCONTACTFORMS__engineWebAppDirectory . "/easy-contact-forms-forms.js'></script>";

		$text[] = "<table align=center style='height:100%'><tr>";
		$text[] = "<td style='padding-top:50px;vertical-align:top'>";
		$text[] = $html;
		$text[] = "</td>";
		$text[] = "</tr></table>";
		$text = implode('', $text);

		EasyContactFormsUtils::createFolder($spec->dir);
		EasyContactFormsUtils::overwritefile($spec->filepath, $text);
		if (!isset($map['ufo-skipoutput'])) {
			echo json_encode(array('url' => $spec->fileurl));
			exit;
		}

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

		$request = EasyContactFormsUtils::parseRequest($request, 'SendConfirmation', 'boolean');
		$request = EasyContactFormsUtils::parseRequest($request, 'Redirect', 'boolean');
		$request = EasyContactFormsUtils::parseRequest($request, 'Template', 'boolean');
		$request = EasyContactFormsUtils::parseRequest($request, 'ShowSubmissionSuccess', 'boolean');
		$request = EasyContactFormsUtils::parseRequest($request, 'Width', 'int');
		$request = EasyContactFormsUtils::parseRequest($request, 'LineHeight', 'int');

		parent::update($request, $id);

		$this->updateTemplate($id);

	}

	/**
	 * 	updateTemplate
	 *
	 * @param  $cf
	 * 
	 *
	 * @return
	 * 
	 */
	function updateTemplate($cf) {

		$query = "SELECT
				CustomFormFields.id,
				FieldSetListPosition.ListPosition AS FieldSetListPosition,
				IF(CustomFormFields.Type=1
					OR CustomFormFields.Type=2,
					1,
					0) AS Container
			FROM
				#wp__easycontactforms_customformfields AS CustomFormFields
			INNER JOIN
				#wp__easycontactforms_customformfieldtypes AS CustomFormFieldTypes
					ON
						CustomFormFields.Type=CustomFormFieldTypes.id
				LEFT JOIN(
				SELECT
					CustomFormFields.id,
					CustomFormFields.ListPosition
				FROM
					#wp__easycontactforms_customformfields AS CustomFormFields) AS FieldSetListPosition
					ON
						FieldSetListPosition.id=CustomFormFields.FieldSet
			WHERE
				CustomFormFields.CustomForms=$cf
			ORDER BY
				FieldSetListPosition,
				Container DESC,
				CustomFormFields.ListPosition";

		$fields = EasyContactFormsDB::getObjects($query);
		$form = EasyContactFormsClassLoader::getObject('CustomForms', true, $cf);

		$divmargin =  $form->isEmpty('LineHeight') ? '' : "margin-top:{$form->get('LineHeight')}{$form->get('LineHeightUnit')};";

		$rows = array();
		$containertag = '';
		$containerbottom = '';
		$containerbottominside = '';
		$iscontainer = false;

		$vjs = array();
		$items = array();
		foreach ($fields as $fld) {
		$fld = EasyContactFormsClassLoader::getObject('CustomFormFields', true, $fld->id);
		$phase = (object) array('index' => 8);
		include $fld->getTMPFileName('proc');
			$text = $fld->get('Template');
			$xml = simplexml_load_string($text);
			$entry = (object) array();
			$iscontainer = false;
			foreach($xml->children() as $child) {
				$name = $child->getName();
				if ($name == 'Container'){
					$iscontainer = true;
					if (!$containertag == '') {
		//						$rows[] = "</div>";
						if (!empty($containerbottominside)) {
							$rows[] = $containerbottominside;
						}
						$rows[] = "</$containertag>";
						if (!empty($containerbottom)) {
							$rows[] = $containerbottom;
						}
					}
					$containerbottom = '';
					$containerbottominside = '';
					$containertag = (string) $child->attributes()->containertag;
				}

				$iscenter = ($name == 'Container' || $name == 'Input' || $name == 'Validation');
				$positionname = $iscenter ? 'center' : $child->attributes()->position;
				$width = isset($child->attributes()->width) ? $child->attributes()->width : '';
				$rowclass = isset($child->attributes()->rowclass) ? $child->attributes()->rowclass : '';

				if (!isset($entry->$positionname)) {
					$entry->$positionname = (object) array();
					$entry->$positionname->list = array();
					$entry->$positionname->width = '';
					$entry->$positionname->rowclass = '';
				}

				array_push($entry->$positionname->list, (string) $child);
				$entry->$positionname->width = $width;
				$entry->$positionname->rowclass = $rowclass;
			}

			if ($iscontainer) {
				if (isset($entry->top)) {
					$rows[] = implode('', $entry->top->list);
				}
				if (isset($entry->bottom)) {
					$containerbottom = implode('', $entry->bottom->list);
				}
				if (isset($entry->{'bottom-inside'})) {
					$containerbottominside = implode('', $entry->{'bottom-inside'}->list);
				}
				$rows[] = implode('', $entry->center->list);

				if (isset($entry->{'top-inside'})) {
					$rows[] = implode('', $entry->{'top-inside'}->list);
				}
		//				$rows[] = '<div>';
			}
			else {

				$fldid = $fld->get('id');
				$rowclass = empty($entry->center->rowclass) ? '' : ' ' . $entry->center->rowclass;

				$rows[] = "<div class='ufo-customform-row ufo-row-{$fldid}{$rowclass}' style='{$divmargin}{display-{$fldid}}'>";

				if (isset($entry->top)) {
					$entry->top->width = $entry->center->width;

					$rows = EasyContactFormsCustomForms::addRow($rows, $entry->top, isset($entry->left), isset($entry->right), $fldid, 1);

				}

				$entryleft = isset($entry->left) ? $entry->left : false;
				$entryright = isset($entry->right) ? $entry->right : false;
				$rows = EasyContactFormsCustomForms::addRow($rows, $entry->center, $entryleft, $entryright, $fldid, 2);

				if (isset($entry->bottom)) {
					$entry->bottom->width = $entry->center->width;

					$rows = EasyContactFormsCustomForms::addRow($rows, $entry->bottom, isset($entry->left), isset($entry->right), $fldid, 3);

				}
				$rows[] = '</div>';
			}
		}
		if (!empty($containerbottominside)) {
			$rows[] = $containerbottominside;
		}
		$rows[] = "</$containertag>";
		if (!empty($containerbottom)) {
			$rows[] = $containerbottom;
		}
		$rows = implode('', $rows);
		$html = array();
		$html[] = "<script type='text/javascript'>";
		$html[] = "var ufobaseurl = '" . admin_url( 'admin-ajax.php' ) . "';";

		$html[] = "
if (typeof(ufoForms) == 'undefined') {
	ufoForms = new function() {
		this.addEvent = function(elem, evType, fn) {
			if (elem.addEventListener) {
				elem.addEventListener(evType, fn, false);
			}
			else if (elem.attachEvent) {
				elem.attachEvent('on' + evType, fn);
			}
			else {
				elem['on' + evType] = fn;
			}
		}
		this.docReady = function(func){
			this.addEvent(document, 'readystatechange', function(){
				if (document.readyState == 'complete'){
					func();
				}
			});
		}

		this.validate = function (config){
			this.docReady(function(){ufoForms.addValidation(config)});
		}

		this.submitButton = function (config){
			this.docReady(function(){ufoForms.addSubmit(config)});
		}

		this.resetButton = function (config){
			this.docReady(function(){ufoForms.addReset(config)});
		}
		this.addValidation = function (config){};
		this.addSubmit = function (config){};
		this.addReset = function (config){};

	}	
}";

		$html[] = "</script>";
		if (!$form->isEmpty('Style')) {
			$ds = DIRECTORY_SEPARATOR;
			ob_start();

			require_once dirName(__FILE__) . $ds . 'forms' . $ds . 'styles' . $ds . $form->get('Style') . $ds . 'easy-contact-forms-getstyle.php';

			$html[] = ob_get_contents();
			ob_end_clean();
		}
		$stylesheet = $form->getStyle();
		if (!empty($stylesheet)) {
			$html[] = "<style>{$stylesheet}</style>";
		}
		$formclass = $form->isEmpty('FormClass') ? '' : " class='{$form->get('FormClass')}'";
		$formstyle = array();
		if (!$form->isEmpty('FormStyle')) {
			$formstyle[] =  $form->get('FormStyle');
		}
		if (!$form->isEmpty('Width')) {
			$formstyle[] =  "width:{$form->get('Width')}{$form->get('WidthUnit')}";
		}
		if (sizeof($formstyle) != 0) {
			$formstyle = " style='" . implode(';', $formstyle) . "'";
		}
		else {
			$formstyle = '';
		}
		$html[] = "<div{$formclass}{$formstyle} id='ufo-form-id-$cf'>";
		$html[] = "<noscript><form method='POST'><input type='hidden' name='cf-no-script' value='1'></noscript>";
		$html[] = "<input type='hidden' value='ufo-form-id-$cf' name='hidden-$cf' id='ufo-form-hidden-$cf'>";
		$html[] = "<input type='hidden' value='{ufosignature}' name='ufo-sign' id='ufo-sign'>";
		$html[] = $rows;
		$html[] = "<div id='ufo-form-id-$cf-message'></div>";
		$html[] = "<noscript></form></noscript>";
		$html[] = "</div>";
		if (count($vjs) > 0) {
			$html[] = '<script>' . implode('', $vjs) . '</script>';
		}
		$txt = implode('', $html);
		$html = array('HTML' => $txt, 'ShortCode' => '[easy_contact_forms fid=' . $cf . ']');
		EasyContactFormsDB::update($html, 'CustomForms', $cf);

	}

	/**
	 * 	addRow
	 *
	 * @param  $rows
	 * 
	 * @param  $center
	 * 
	 * @param  $left
	 * 
	 * @param  $right
	 * 
	 * @param  $fldid
	 * 
	 * @param  $rowindex
	 * 
	 *
	 * @return
	 * 
	 */
	function addRow($rows, $center, $left, $right, $fldid, $rowindex) {

		$prefix = 'ufo-cell';
		$centerclass = $prefix.'-center';
		$leftclass = $prefix.'-left';
		$rightclass = $prefix.'-right';
		$cellspec = $prefix.'-'.$fldid.'-'.$rowindex;

		if (!isset($center)) {
			return $rows;
		}
		$width = $center->width != '' ? " style='width:{$center->width}'" : '';
		$center = implode('', $center->list);
		$center = "<span class='{$centerclass}'{$width} id='{$cellspec}-center'>{$center}</span>";
		if (!$left) {
			$left = '';
		}
		else {
			if (is_object($left)) {
				$left = implode('', $left->list);
			}
			else {
				$left = "<p style='display:none'></p>";
			}
			$left = "<span class='{$leftclass}' id='{$cellspec}-left'>{$left}</span>";
		}
		if (!$right) {
			$right = '';
		}
		else {
		if (is_object($right)) {
			$right = implode('', $right->list);
		}
		else {
			$right = "<p style='display:none'></p>";
		}
			$right = "<span class='{$rightclass}' id='{$cellspec}-right'>{$right}</span>";
		}

		$rows[] = "<div class='{$cellspec}-row' id='{$cellspec}'>" . $left . $center . $right . "</div>";
		return $rows;

	}

	/**
	 * 	val
	 *
	 * @param  $map
	 * 
	 *
	 * @return
	 * 
	 */
	function val($map) {

		foreach ($map as $key=>$value){
			if (!EasyContactFormsUtils::beginsWith($key, 'id-')) {
				continue;
			}
			$names = explode('-', $key);
			$fldid = intval($names[1]);
			if ($fldid == 0 ) {
				continue;
			}
			$fld = EasyContactFormsClassLoader::getObject('CustomFormFields', true, $fldid);
			if (!$fld) {
				continue;
			}
			$phase = (object) array('index' => 4);
			include $fld->getTMPFileName('proc');
		}

	}

	/**
	 * 	dispatch. Overrides EasyContactFormsBase::dispatch()
	 *
	 * 	invokes requested object methods
	 *
	 * @param array $dispmap
	 * 	request data
	 */
	function dispatch($dispmap) {

		$dispmap = parent::dispatch($dispmap);
		if ($dispmap == NULL) {
			return NULL;
		}

		$dispmethod = $dispmap["m"];
		switch ($dispmethod) {

			case 'add':
				$this->add($dispmap);
				return NULL;

			case 'copy':
				$this->copy($dispmap);
				return NULL;

			case 'preview':
				$this->preview($dispmap);
				return NULL;

			case 'refreshForm':
				$this->refreshForm($dispmap);
				return NULL;

			case 'val':
				$this->val($dispmap);
				return NULL;

			default : return $dispmap;
		}

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

		$fields = array();
		$fields[] = 'id';
		$fields[] = 'Description';
		$fields[] = 'NotificationSubject';
		$fields[] = 'ShortCode';
		$fields[] = 'SendConfirmation';
		$fields[] = 'SendFrom';
		$fields[] = 'SendFromAddress';
		$fields[] = 'ConfirmationSubject';
		$fields[] = 'ConfirmationText';
		$fields[] = 'Redirect';
		$fields[] = 'RedirectURL';
		$fields[] = 'ShowSubmissionSuccess';
		$fields[] = 'SubmissionSuccessText';
		$fields[] = 'StyleSheet';
		$fields[] = 'Width';
		$fields[] = 'LineHeight';
		$fields[] = 'Style';
		$fields[] = 'FormClass';
		$fields[] = 'FormStyle';
		$fields[] = 'SuccessMessageClass';
		$fields[] = 'FailureMessageClass';
		$fields[] = 'Width';
		$fields[] = 'WidthUnit';
		$fields[] = 'LineHeight';
		$fields[] = 'LineHeightUnit';

		$obj = $this->formInit($formmap, $fields);
		$obj->set('Description', htmlspecialchars($obj->get('Description'), ENT_QUOTES));
		$obj->set('NotificationSubject', htmlspecialchars($obj->get('NotificationSubject'), ENT_QUOTES));
		$obj->set('ShortCode', htmlspecialchars($obj->get('ShortCode'), ENT_QUOTES));

		$obj->SendConfirmationChecked
			= $obj->get('SendConfirmation') ? 'checked' : '';
		$obj->SendConfirmation = $obj->get('SendConfirmation') ? 'on' : 'off';

		$obj->set('SendFrom', htmlspecialchars($obj->get('SendFrom'), ENT_QUOTES));
		$obj->set('SendFromAddress', htmlspecialchars($obj->get('SendFromAddress'), ENT_QUOTES));
		$obj->set('ConfirmationSubject', htmlspecialchars($obj->get('ConfirmationSubject'), ENT_QUOTES));
		$obj->set('ConfirmationText', htmlspecialchars($obj->get('ConfirmationText')));

		$obj->RedirectChecked = $obj->get('Redirect') ? 'checked' : '';
		$obj->Redirect = $obj->get('Redirect') ? 'on' : 'off';

		$obj->set('RedirectURL', htmlspecialchars($obj->get('RedirectURL'), ENT_QUOTES));

		$obj->ShowSubmissionSuccessChecked
			= $obj->get('ShowSubmissionSuccess') ? 'checked' : '';
		$obj->ShowSubmissionSuccess
			= $obj->get('ShowSubmissionSuccess') ? 'on' : 'off';

		$obj->set('SubmissionSuccessText', htmlspecialchars($obj->get('SubmissionSuccessText')));
		$obj->set('StyleSheet', htmlspecialchars($obj->get('StyleSheet')));
		$obj->set('FormClass', htmlspecialchars($obj->get('FormClass'), ENT_QUOTES));
		$obj->set('FormStyle', htmlspecialchars($obj->get('FormStyle')));
		$obj->set('SuccessMessageClass', htmlspecialchars($obj->get('SuccessMessageClass'), ENT_QUOTES));
		$obj->set('FailureMessageClass', htmlspecialchars($obj->get('FailureMessageClass'), ENT_QUOTES));
		$obj->set('WidthUnit', htmlspecialchars($obj->get('WidthUnit'), ENT_QUOTES));
		$obj->set('LineHeightUnit', htmlspecialchars($obj->get('LineHeightUnit'), ENT_QUOTES));

		?>
		<input type='hidden' class='ufostddata' id='t' value='<?php echo $obj->type;?>'>
		<input type='hidden' class='ufostddata' id='oid' value='<?php echo $obj->getId();?>'>
		<?php

		require_once 'views/easy-contact-forms-customformsmainform.php';

	}

	/**
	 * 	getMainView
	 *
	 * 	prepares the view data and finally passes it to the html template
	 *
	 * @param array $viewmap
	 * 	request data
	 */
	function getMainView($viewmap) {

		$spar = $this->getOrder($viewmap);

		$orderby = EasyContactFormsDB::getOrderBy(array('id', 'Description', 'ShortCode', 'Style'), $spar, "CustomForms.Description");

		$rparams = $this->getFilter($viewmap);
		$viewfilters = array();
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'id', 'int');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'Description');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'NotificationSubject');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'SendFrom');

		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'SendConfirmation', 'boolean');

		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'ConfirmationSubject');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'ConfirmationText');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'Redirect', 'boolean');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'RedirectURL');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'ShortCode');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'Template', 'boolean');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'SubmissionSuccessText');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'StyleSheet');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'SendFromAddress');

		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'ShowSubmissionSuccess', 'boolean');

		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'SuccessMessageClass');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'FailureMessageClass');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'Width', 'int');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'LineHeight', 'int');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'WidthUnit');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'LineHeightUnit');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'FormClass');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'FormStyle');
		$viewfilters = EasyContactFormsDB::getSignFilter($viewfilters, $rparams, 'CustomForms.', 'Style');
		EasyContactFormsRoot::mDelete('CustomForms', $viewmap);

		$query = "SELECT
				CustomForms.id,
				CustomForms.Description,
				CustomForms.ShortCode,
				CustomForms.Style
			FROM
				#wp__easycontactforms_customforms AS CustomForms";

		$this->start = isset($viewmap['start']) ? intval($viewmap['start']) : 0;
		$this->limit = isset($viewmap['limit']) ? intval($viewmap['limit']) : 10;
		$this->rowCount = EasyContactFormsDB::getRowCount($query, $viewfilters);

		$resultset = EasyContactFormsDB::select($query, $viewfilters, $orderby, $this);

		$obj = $this;
		?><input type='hidden' name='t' id='t' value='CustomForms'><?php

		require_once 'views/easy-contact-forms-customformsmainview.php';

	}

}
