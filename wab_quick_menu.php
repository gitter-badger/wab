<?php
/*
   * e107 website system
   *
   * Copyright (C) 2008-2013 e107 Inc (e107.org)
   * Released under the terms and conditions of the
   * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
   *
*/

if (!defined('e107_INIT'))  exit;

global $menu_pref;

$e107 = e107::getInstance();
$tp = e107::getParser();
$sql = e107::getDb();
$gen = new convert;
$pref = e107::getPref();

require_once(e_PLUGIN.'wab/wab_class.php');
require_once(e_PLUGIN.'wab/wabmain_class.php');

require_once(e_PLUGIN.'wab/wabquick_class.php');

require_once(e_PLUGIN.'wab/templates/wab_template.php');

e107::lan('wab','menu',true);  // English_menu.php or {LANGUAGE}_menu.php
$wabQuick=new wabQuickMenu();
$text=$wabQuick->showQuick();
e107::getRender()->tablerender('WAB Quick Log', $text, 'wabquick_menu');
