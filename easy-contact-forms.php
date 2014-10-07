<?php
/*
Plugin Name: Easy Contact Forms
Plugin URI: http://easy-contact-forms.com 
Version: 1.1.8.12
Author: wppal
Author URI: http://wp-pal.com
Description: Easy Contact Forms. Easy to create. Easy to fill out. Easy to change. Easy to manage. Easy to protect	
*/

if (!class_exists('EasyContactForms')) {
	class EasyContactForms {
		static function install() {
			$plugin_prefix_root = plugin_dir_path( __FILE__ );
			$plugin_prefix_filename = "{$plugin_prefix_root}/easy-contact-forms.install.php";
			include_once $plugin_prefix_filename;	
			easycontactforms_install();
			easycontactforms_install_data();
		}	
		static function uninstall() {
			$plugin_prefix_root = plugin_dir_path( __FILE__ );
			$plugin_prefix_filename = "{$plugin_prefix_root}/easy-contact-forms.install.php";
			include_once $plugin_prefix_filename;	
			easycontactforms_uninstall();
		}	
	}
	$easycontact = new EasyContactForms(); 	
} 	

if ( isset($easycontact) && function_exists('register_activation_hook') ){

	register_activation_hook( __FILE__, array('EasyContactForms', 'install') );
	register_deactivation_hook( __FILE__, array('EasyContactForms', 'uninstall') );
	add_action( 'admin_menu', 'easycontactforms_main_page', 1 );
	add_action( 'wp_ajax_nopriv_easy-contact-forms-submit', 'easycontactforms_entrypoint' );
	add_action( 'wp_ajax_easy-contact-forms-submit', 'easycontactforms_entrypoint' );	
	add_shortcode( 'easy_contact_forms_frontend', 'easycontactforms_entrypoint' );
	add_shortcode( 'easy_contact_forms', 'easycontactforms_formentrypoint' );	
	add_action( 'plugins_loaded', 'easycontactforms_update_db_check');

} 
function easycontactforms_update_db_check() {
	$db_version = '1.1.8.12';
	if (get_site_option('easy-contact-forms_db_version') != $db_version) {
		EasyContactForms::install();
		require_once 'easy-contact-forms-utils.php';		
		require_once 'easy-contact-forms-securitymanager.php';		
		require_once 'easy-contact-forms-applicationsettings.php';		
		$dirname = plugin_dir_path( __FILE__ ) . '/forms/tmp';
		EasyContactFormsUtils::rrmdir($dirname);
		$dirname = plugin_dir_path( __FILE__ ) . '/forms/' . md5(EasyContactFormsSecurityManager::getServerPwd());
		EasyContactFormsUtils::rrmdir($dirname);
	}
}

function easycontactforms_main_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('Easy Contact Forms'), __('Easy Contact Forms'), 'manage_options', 'easy-contact-forms-main-page', 'easycontactforms_entrypoint');
}

function easycontactforms_tag() {
	easycontactforms_entrypoint();
}
/**
 * 	Easy Contact Forms entrypoint
 *
 */

function easycontactforms_entrypoint() {

	global $current_user;

	$l_locale = get_locale();

	$map = $_REQUEST;

	$base = get_bloginfo('wpurl');
	$base = rtrim($base, '/');

	// Http root
	DEFINE('EASYCONTACTFORMS__APPLICATION_ROOT', $base);
	// DIRECTORY_SEPARATOR
	DEFINE('WP_DS', DIRECTORY_SEPARATOR);
	// Plugin directory
	DEFINE('_EASYCONTACTFORMS_DIR', 'wp-content/plugins/easy-contact-forms');
	// Plugin url

	DEFINE('EASYCONTACTFORMS__engineWebAppDirectory', rtrim(EASYCONTACTFORMS__APPLICATION_ROOT, '/') . '/' . _EASYCONTACTFORMS_DIR);

	// An absolute plugin path
	DEFINE('_EASYCONTACTFORMS_PLUGIN_PATH', ABSPATH . _EASYCONTACTFORMS_DIR);

	$tag = strtolower(str_replace('_', '-', l_locale));
	$map['l'] = $tag;

	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-strings.php';
	if (!(@include_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-resources_' . $tag . '.php')) {
		require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-resources_en-gb.php';
		$map['l'] = 'en-gb';
	}

	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-utils.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-database.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-root.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-applicationsettings.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-appconfigdata.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-securitymanager.php';

	$userid = $current_user->ID;

	unset($map['frid']);
	$map['frid'] = $userid;

	if (isset($map['ac']) && ($map['ac'] == '1')) {
		EasyContactFormsRoot::ajaxCall($map);
		die();
	}

	$map = EasyContactFormsSecurityManager::getRights($map);

	if (isset($map['m']) && ($map['m'] == 'download')) {
		EasyContactFormsRoot::download($map);
		die();
	}

	if (!isset($map['m'])) {
		$map['m'] = 'view';
	}
	if (!isset($map['t'])) {
		$map['t'] = 'CustomForms';
	}

	wp_enqueue_script('jquery');

	wp_enqueue_script('easy-contact-formshtml', '/' . _EASYCONTACTFORMS_DIR . '/easy-contact-formshtml.js');
	wp_enqueue_script('jquery.ui.core', '/' . _EASYCONTACTFORMS_DIR . '/js/jqui/jquery.ui.core.js');
	wp_enqueue_script('jquery.ui.widget', '/' . _EASYCONTACTFORMS_DIR . '/js/jqui/jquery.ui.widget.js');
	wp_enqueue_script('jquery.ui.mouse', '/' . _EASYCONTACTFORMS_DIR . '/js/jqui/jquery.ui.mouse.js');
	wp_enqueue_script('jquery.ui.sortable', '/' . _EASYCONTACTFORMS_DIR . '/js/jqui/jquery.ui.sortable.js');
	wp_enqueue_script('scrollto', '/' . _EASYCONTACTFORMS_DIR . '/js/jqui/scrollto.js');
	wp_enqueue_script('as', '/' . _EASYCONTACTFORMS_DIR . '/js/as.js');
	wp_enqueue_script('calendar_stripped', '/' . _EASYCONTACTFORMS_DIR . '/js/calendar/calendar_stripped.js');
	wp_enqueue_script('calendar-setup_stripped', '/' . _EASYCONTACTFORMS_DIR . '/js/calendar/calendar-setup_stripped.js');
	wp_enqueue_script('calendar-en', '/' . _EASYCONTACTFORMS_DIR . '/js/calendar/lang/calendar-en.js');

	if (EasyContactFormsApplicationSettings::getInstance()->get('UseTinyMCE')) {
		wp_enqueue_script('tiny_mce', '/' . _EASYCONTACTFORMS_DIR . '/js/tinymce/tiny_mce.js');
	}

	$js = "config = {};";
	$js .= "config.url='" . admin_url( 'admin-ajax.php' ) . "';";
	$js .= "config.initial = {t:'" . $map['t'] . "', m:'" . $map['m'] . "'};";
	$js .= "config.bodyid = 'divEasyContactForms';";
	$js .= "config.resources = {};";

	$js .= "config.resources['EmailFormatIsExpected'] = " . json_encode(EasyContactFormsT::get('EmailFormatIsExpected')) . ";";

	$js .= "config.resources['ValueLengthShouldBeBetween'] = " . json_encode(EasyContactFormsT::get('ValueLengthShouldBeBetween')) . ";";

	$js .= "config.resources['ValueLengthShouldBeMoreThan'] = " . json_encode(EasyContactFormsT::get('ValueLengthShouldBeMoreThan')) . ";";

	$js .= "config.resources['ValueLengthShouldBeLessThan'] = " . json_encode(EasyContactFormsT::get('ValueLengthShouldBeLessThan')) . ";";

	$js .= "config.resources['ThisIsAnIntegerField'] = " . json_encode(EasyContactFormsT::get('ThisIsAnIntegerField')) . ";";

	$js .= "config.resources['ThisFieldIsRequired'] = " . json_encode(EasyContactFormsT::get('ThisFieldIsRequired')) . ";";

	$js .= "config.resources['ThisIsAFieldOfCurrencyFormat'] = " . json_encode(EasyContactFormsT::get('ThisIsAFieldOfCurrencyFormat')) . ";";

	$js .= "config.resources['ItwillDeleteRecordsAreYouSure'] = " . json_encode(EasyContactFormsT::get('ItwillDeleteRecordsAreYouSure')) . ";";

	$js .= "config.resources['NoRecordsSelected'] = " . json_encode(EasyContactFormsT::get('NoRecordsSelected')) . ";";
	$js .= "config.resources['CloseFilter'] = " . json_encode(EasyContactFormsT::get('CloseFilter')) . ";";
	$js .= "config.resources['Search'] = " . json_encode(EasyContactFormsT::get('Search')) . ";";
	$js .= "config.resources['NoResults'] = " . json_encode(EasyContactFormsT::get('NoResults')) . ";";
	$js .= "config.resources['CF_Pin'] = " . json_encode(EasyContactFormsT::get('CF_Pin')) . ";";
	$js .= "config.resources['CF_UnPin'] = " . json_encode(EasyContactFormsT::get('CF_UnPin')) . ";";
	$js .= "var appManConfig = config;";

	echo "<link href='" . EASYCONTACTFORMS__engineWebAppDirectory . '/js/calendar/css/calendar-system.css' . "' rel='stylesheet' type='text/css'/>";

	if (function_exists('is_admin')) {

		$paramName = is_admin() ? 'DefaultStyle2' : 'DefaultStyle';
		$styleName = EasyContactFormsApplicationSettings::getInstance()->get($paramName);

		$paramName = is_admin() ? 'ApplicationWidth2' : 'ApplicationWidth';
		$appWidth = EasyContactFormsApplicationSettings::getInstance()->get($paramName);

	}
	else {

		$styleName = EASYCONTACTFORMS__DEFAULT_STYLE;
		$appWidth = EasyContactFormsApplicationSettings::getInstance()->get('ApplicationWidth');

	}

	$wrStyle = 'style=\'width:' . $appWidth . 'px\'';

	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'styles' . WP_DS . $styleName . WP_DS . 'easy-contact-forms-getstyle.php';

	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-menu.php';
	echo "<div id='ufo-app-wrapper' $wrStyle>";
	EasyContactFormsMenu::getMenu($map);
	echo "<div id='divEasyContactForms'>";
	echo "<script>$js</script>";
	echo EasyContactFormsRoot::processRequest($map);
	echo "</div>";
	echo "</div>";

}

/**
 * 	Easy Contact Forms form entrypoint
 *
 * @param array $map
 * 
 *
 * @return string
 * 
 */

function easycontactforms_formentrypoint($map) {
	$base = get_bloginfo('wpurl');
	$base = rtrim($base, '/');

	// Http root
	DEFINE('EASYCONTACTFORMS__APPLICATION_ROOT', $base);
	// DIRECTORY_SEPARATOR
	DEFINE('WP_DS', DIRECTORY_SEPARATOR);
	// Plugin directory
	DEFINE('_EASYCONTACTFORMS_DIR', 'wp-content/plugins/easy-contact-forms');
	// Plugin url
	DEFINE('_EASYCONTACTFORMS_PLUGIN_PATH', ABSPATH . _EASYCONTACTFORMS_DIR);
	// Plugin url

	DEFINE('EASYCONTACTFORMS__engineWebAppDirectory', rtrim(EASYCONTACTFORMS__APPLICATION_ROOT, '/') . '/' . _EASYCONTACTFORMS_DIR);

	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-utils.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-database.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-root.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-applicationsettings.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-customforms.php';
	require_once _EASYCONTACTFORMS_PLUGIN_PATH . WP_DS . 'easy-contact-forms-appconfigdata.php';
	wp_enqueue_script('ufoforms', '/' . _EASYCONTACTFORMS_DIR . '/easy-contact-forms-forms.js');

	$map = array_merge($map, $_REQUEST);
	global $current_user;
	$userid = $current_user->ID;
	unset($map['frid']);
	$map['frid'] = $userid;
	echo EasyContactFormsCustomForms::getForm($map);

}
