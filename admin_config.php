<?php
/**
 * admin config file for the administration interface for the WAB log plugin.
 *
 * @package WAB
 * @copyright 2008-2015 Barry Keal G4HDU
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Barry Keal G4HDU <www.g4hdu.co.uk>
 * @version 1.0.1  In development. Very unstable.
 */

/**
 * .
 */

require_once('../../class2.php');
e107::lan('wab', 'admin',true); // admin language files
e107::lan('wab', 'global',true); // front language files
if (!defined('ADMIN_WIDTH'))
{
	define('ADMIN_WIDTH', "width:100%;");
}
// Check valid admin if not send them to front page
if (!getperms('P')) {
    header('location:' . e_BASE . 'index.php');
    exit;
}
// get necessary handlers etc
require_once(e_ADMIN . 'auth.php');
require_once(e_HANDLER . 'userclass_class.php');
require_once(e_HANDLER . 'form_handler.php');
require_once('wab_class.php');
// creat wab object.  Everything happens in here
if (!is_object($wabAdmin)) {
	$wabAdmin=new wabAdmin;
}

require_once(e_ADMIN . 'footer.php');

function admin_config_adminmenu($action) {
    global $wab,$wabAdmin;
    if ($wabAdmin->getAction() == '') {
        $wabAdmin->setAction('prefs') ;
    }
    $var = $wabAdmin->get_options($wabAdmin->getAction());
    show_admin_menu(LAN_WAB_ADMIN_MENU0, $wabAdmin->getAction(), $var);
}

?>