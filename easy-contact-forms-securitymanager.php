<?php

/**
 * @file
 *
 * 	EasyContactFormsSecurityManager class definition
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
 * 	EasyContactFormsSecurityManager
 *
 * 	Security and access functions
 *
 */
class EasyContactFormsSecurityManager {

	/**
	 * 	getInstance
	 *
	 * 	Returns an instance of security manager
	 *
	 *
	 * @return object
	 * 	a security manager instance
	 */
	function getInstance() {

		static $sminstance;
		if (!isset($sminstance)) {
			$sminstance = new EasyContactFormsSecurityManager();
		}
		return $sminstance;

	}

	/**
	 * 	getGuest
	 *
	 * 	returns a guest role object
	 *
	 *
	 * @return object
	 * 	the guest role object
	 */
	function getGuest() {

		$role = (object) array();
		$role->id = 4;
		$role->Description = 'Guest';
		$user = (object) array();
		$user->id = 0;
		$user->role = $role;
		return $user;

	}

	/**
	 * 	getRights
	 *
	 * 	inits a 'easycontactusr' variable with the current user role
	 *
	 * @param array $_ssmap
	 * 	request data
	 */
	function getRights($_ssmap) {

		$_ssmap['easycontactusr'] = EasyContactFormsSecurityManager::getGuest();

		$foreignid = intval($_ssmap['frid']);
		if ($foreignid == 0) {
			return $_ssmap;
		}
		if (isset($_ssmap['m'])) {
			$m = $_ssmap['m'];
			$sm = addcslashes($m, "\\\'\"&;%<>");
			$sm = str_replace(' ' , '', $sm);
			if ($sm != $m) {
				unset($_ssmap['m']);
				$_ssmap['m']='&';
				return $_ssmap;
			}
		}

		$query = "SELECT
				Users.Role AS roleid,
				Users.id
			FROM
				#wp__easycontactforms_users AS Users
			WHERE
				Users.CMSId='$foreignid'";

		$usr = EasyContactFormsDB::getObjects($query);
		if (EasyContactFormsDB::err()) {
			return $_ssmap;
		}

		if (count($usr) == 0) {
			return $_ssmap;
		}

		$usr = $usr[0];

		$query = "SELECT * FROM #wp__easycontactforms_roles AS Roles WHERE Roles.id = '" . $usr->roleid . "'";
		$role = EasyContactFormsDB::getObjects($query);

		if (EasyContactFormsDB::err()) {
			return $_ssmap;
		}
		if (count($role) == 0) {
			return $_ssmap;
		}

		$usr->role = (object) array();
		$usr->role->Description = $role[0]->Description;
		$usr->role->id = $role[0]->id;
		unset($usr->roleid);

		$_ssmap['easycontactusr'] = $usr;
		return $_ssmap;

	}

	/**
	 * 	roleObjectCheck
	 *
	 * 	performs a simple check if users of current user role have access to
	 * 	a selected object type
	 *
	 * @param array $_cmmap
	 * 	request data
	 *
	 * @return boolean
	 * 	TRUE if they have, FALSE if they do not
	 */
	function roleObjectCheck($_cmmap) {

		$objecttype = $_cmmap['t'];
		$userrole = $_cmmap['easycontactusr']->role->Description;

		$query = "SELECT
				Count(id)
			FROM
				#wp__easycontactforms_acl
			WHERE
				objtype='$objecttype'
				AND role='$userrole'";

		$value = EasyContactFormsDB::getValue($query);
		return ($value > 0);

	}

	/**
	 * 	getYouAreNotLoggedInMessage
	 *
	 * 	Prints a 'not logged in message'
	 *
	 */
	function getYouAreNotLoggedInMessage() {

		require_once 'easy-contact-forms-ihtml.php';
		return EasyContactFormsIHTML::getNotLoggedInHTML();

	}

	/**
	 * 	getViewName
	 *
	 * 	Finds a name of an object view indended for a current user role
	 *
	 * @param array $_vnmap
	 * 	request data
	 *
	 * @return string
	 * 	view name
	 */
	function getViewName($_vnmap) {

		$objecttype = $_vnmap['t'];
		$vnmethod = $_vnmap['m'];

		if (empty($objecttype)) {
			return '';
		}
		if (empty($vnmethod)) {
			return '';
		}

		switch ($vnmethod) {
			case 'show':
			case 'new':
			case 'view':
			case 'viewDetailed':
				return EasyContactFormsSecurityManager::getObjectViewName($_vnmap);
				break;
			default:
				return EasyContactFormsSecurityManager::getObjectMethodViewName($_vnmap);
		}

	}

	/**
	 * 	getObjectMethodViewName
	 *
	 * 	returns a view name
	 *
	 * @param array $_cmmap
	 * 	Request data
	 *
	 * @return string
	 * 	the name
	 */
	function getObjectMethodViewName($_cmmap) {

		return EasyContactFormsSecurityManager::getObjectViewName($_cmmap);

	}

	/**
	 * 	isObjectOwner
	 *
	 * 	Check if a current user may play as object's owner
	 *
	 * @param string $objtype
	 * 	object type
	 * @param int $objid
	 * 	object id
	 * @param int $usrid
	 * 	user id
	 *
	 * @return boolean
	 * 	TRUE if he may, FALSE if he may not
	 */
	function isObjectOwner($objtype, $objid, $usrid) {

		$xml = EASYCONTACTFORMS__APPLICATION_DIR . DIRECTORY_SEPARATOR . 'easy-contact-forms-objects.xml';
		$xml = simplexml_load_file($xml);

		$nodes = $xml->xpath('//' . $objtype);
		$node = $nodes[0];
		$childname = strtolower($node->getName());

		while (TRUE) {
			$parents = $node->xpath('..');
			$parent = $parents[0];
			$parentname = $parent->getName();
			$noparents = ($parentname == 'objects');
			$obj = EasyContactFormsClassLoader::getObject($childname, TRUE, $objid);
			if (!$obj) {
				return FALSE;
			}
			if ($obj->get('ObjectOwner') == $usrid) {
				return TRUE;
			}
			if ($noparents) {
				break;
			}
			$objid = $obj->get($parentname);
			$node = $parent;
			$childname = strtolower($parentname);
		}
		return FALSE;

	}

	/**
	 * 	getOwnerRole
	 *
	 * 	Perform additional search in the roles.xml file to find
	 * 	if there are any exceptions to the general access rules
	 *
	 * @param string $roleid
	 * 	a current user role name
	 * @param string $objtype1
	 * 	an object name the user gets access to
	 * @param string $objtype2
	 * 	a subordinated object name the user gets access to
	 *
	 * @return string
	 * 	final role name
	 */
	function getOwnerRole($roleid, $objtype1, $objtype2) {

		$xml = EASYCONTACTFORMS__APPLICATION_DIR . DIRECTORY_SEPARATOR . 'easy-contact-forms-roles.xml';
		$xml = simplexml_load_file($xml);
		$roleid = $xml->xpath("$roleid/$objtype1/$objtype2");
		$roleid = $roleid ? $roleid[0] : 'Owner';

		return $roleid;

	}

	/**
	 * 	checkRole
	 *
	 * 	Performs additional role check
	 *
	 * @param array $_ofnmap
	 * 	request data
	 *
	 * @return string
	 * 	role name
	 */
	function checkRole($_ofnmap) {

		$usr = $_ofnmap['easycontactusr'];

		if ($usr->role->Description == 'SuperAdmin') {
			return $usr->role->Description;
		}
		if ($usr->role->Description == 'Guest') {
			return $usr->role->Description;
		}

		$objtype1 = @$_ofnmap['t'];
		$objtype2 = @$_ofnmap['t'];
		$method = @$_ofnmap['m'];
		$objid = @$_ofnmap['oid'];

		if (isset($_ofnmap['specialfilter'])) {
			$sf = json_decode(stripslashes($_ofnmap['specialfilter']));
			$objtype1 = $method == 'viewDetailed' ?
				$sf[0]->property :
				$_ofnmap['n'] ;
			$objid = $sf[0]->value->values[0];
		}

		if (isset($_ofnmap['a'])) {
			$a = json_decode(stripslashes($_ofnmap['a']));
			$mtm = isset($a->m) &&
				$a->m == 'mtmview';
			if ($mtm) {
				$objtype1 = $a->ca[0]->t;
				$objid = $a->ca[0]->oid;
			}
		}

		if (!isset($objid)) {
			return $usr->role->Description;
		}

		$obj = EasyContactFormsClassLoader::getObject($objtype1);
		$fieldlist = $obj->getFieldNames();
		if (!in_array('ObjectOwner', $fieldlist)) {
			return $usr->role->Description;
		}

		if (!EasyContactFormsSecurityManager::isObjectOwner($objtype1, $objid, $usr->id)) {
			return $usr->role->Description;
		}

		$usr->role->Description = EasyContactFormsSecurityManager::getOwnerRole(
			$usr->role->Description,
			$objtype1,
			$objtype2
			);

		return $usr->role->Description;

	}

	/**
	 * 	getObjectViewName
	 *
	 * 	Returns a view name
	 *
	 * @param array $_ovnmap
	 * 	request data
	 *
	 * @return string
	 * 	a view name
	 */
	function getObjectViewName($_ovnmap) {

		$ovnmethod = $_ovnmap["m"];
		$objecttype = $_ovnmap["t"];
		$roleid = EasyContactFormsSecurityManager::checkRole($_ovnmap);

		return EasyContactFormsSecurityManager::getACLViewName($roleid, $objecttype, $ovnmethod);

	}

	/**
	 * 	getACLViewName
	 *
	 * 	Returns a view name based on a user role, object type and request
	 * 	method
	 *
	 * @param string $role
	 * 	a role name
	 * @param string $type
	 * 	an object type
	 * @param string $method
	 * 	a method name
	 *
	 * @return string
	 * 	a view name
	 */
	function getACLViewName($role, $type, $method) {

		$query = "SELECT
				name
			FROM
				#wp__easycontactforms_acl
			WHERE
				objtype='$type'
				AND role='$role'
				AND method='$method'";

		$result = EasyContactFormsDB::getValue($query);
		if (EasyContactFormsDB::err()) {
			return '';
		}
		return $result;

	}

	/**
	 * 	getServerPwd
	 *
	 * 	Returns the Appplicataion Settings SecretWord constant value
	 *
	 *
	 * @return string
	 * 	the value
	 */
	function getServerPwd() {

		return EasyContactFormsApplicationSettings::getInstance()->get('SecretWord');

	}

	/**
	 * 	getGetSessionValue
	 *
	 * @param  $map
	 * 
	 * @param  $key
	 * 
	 *
	 * @return
	 * 
	 */
	function getGetSessionValue($map, $key) {

		if (!isset($map['sid'])) {
			return NULL;
		}
		$sid = $map['sid'];
		$dirname = EasyContactFormsSecurityManager::getSessionDir();
		$filename = md5($sid . EasyContactFormsSecurityManager::getServerPwd());
		$filename = $dirname . DIRECTORY_SEPARATOR . $filename;
		if (!is_file($filename)) {
			return NULL;
		}
		$xml = simplexml_load_file($filename);
		return (string) $xml->$key;

	}

	/**
	 * 	setSessionValue
	 *
	 * @param  $key
	 * 
	 * @param  $value
	 * 
	 * @param  $sid
	 * 
	 *
	 * @return
	 * 
	 */
	function setSessionValue($key, $value, $sid = NULL) {

		$newsid = FALSE;
		if (is_null($sid)) {
			$newsid = TRUE;
			$sid = EasyContactFormsSecurityManager::getSid();
		}
		else if (is_array($sid) && isset($sid['sid'])) {
			$sid = $sid['sid'];
		}
		else {
			return NULL;
		}
		$dirname = EasyContactFormsSecurityManager::getSessionDir();
		$filename = md5($sid . EasyContactFormsSecurityManager::getServerPwd());
		$filename = $dirname . DIRECTORY_SEPARATOR . $filename;
		if (newsid) {
			$xml = simplexml_load_string('<data/>');
		}
		else if (is_file($filename)) {
			$xml = simplexml_load_file($filename);
		}
		else {
			return NULL;
		}
		$xml->$key = $value;
		$xml->asXML($filename);
		return $sid;

	}

	/**
	 * 	getSid
	 *
	 *
	 * @return
	 * 
	 */
	function getSid() {

		$pwd = EasyContactFormsSecurityManager::getServerPwd();
		$dirname = EasyContactFormsSecurityManager::getSessionDir();
		$flag = $dirname . DIRECTORY_SEPARATOR . 'index.html';
		if (!is_file($flag)) {
			$handle = fopen($flag, 'w');
			fwrite($handle, '<body></body>');
			fclose($handle);
		}
		$fhandle = fopen($flag, 'r');
		flock($fhandle, LOCK_EX);
		$filename = md5('counter' . $pwd);
		$filename = $dirname . DIRECTORY_SEPARATOR . $filename;
		if (!is_file($filename)) {
			$counter = rand(1, 10000000);
		}
		else {
			$handle = fopen($filename, 'r');
			$counter = fread($handle, filesize($filename));
			$counter = intval($counter);
			fclose($handle);
		}
		$counter++;
		$handle = fopen($filename, 'w');
		fwrite($handle, $counter);
		fclose($handle);
		fclose($fhandle);

		// hours * minutes * seconds
		$sesslifetime = 6 * 60 * 60;
		if (rand(1, 10) == 9) {
			$exceptions = array();
			$exceptions[] = 'index.html';
			$exceptions[] = $filename;
			$handle = opendir($dirname);
			while (FALSE !== ($file = readdir($handle))) {
				if ($file == '.' || $file == '..' || in_array($file, $exceptions)) {
					continue;
				}
				$filemtime = filemtime($dirname . DIRECTORY_SEPARATOR . $file);
				if (time() - $filemtime < $sesslifetime) {
					continue;
				}
				unlink($dirname . DIRECTORY_SEPARATOR . $file);
			}
			closedir($handle);
		}
		return md5($counter . $pwd);

	}

	/**
	 * 	getSessionDir
	 *
	 *
	 * @return
	 * 
	 */
	function getSessionDir() {

		$dirname = md5('sessions' . EasyContactFormsSecurityManager::getServerPwd());
		$dirname = EASYCONTACTFORMS__SESSION_DIR . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $dirname;
		if (!is_dir($dirname)) {
			EasyContactFormsUtils::createFolder($dirname);
		}
		return $dirname;

	}

}
