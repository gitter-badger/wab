<?php
/*
   * e107 website system
   *
   * Copyright (C) 2008-2013 e107 Inc (e107.org)
   * Released under the terms and conditions of the
   * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
   *
   * e107 Blank Plugin
   *
*/
require_once("../../class2.php");
// print_a($_SESSION['wab']);
// unset($_SESSION['wab']);
e107::js('wab', 'js/wab.js', 'jquery'); // Load Plugin javascript and include jQuery framework
e107::css('wab', 'css/wab.css'); // load css file
e107::meta('keywords', 'some words'); // add meta data to <HEAD>
e107::lan('wab', 'front', true); // front language files
e107::lan('wab', 'global', true); // front language files

require_once('wab_class.php');
require_once('templates/wab_template.php');
require_once('wab_adifclass.php');

/*
   $p = new ADIF_Parser;
   $p->load_from_file("adifin/g4hdu.adi");
   $p->initialize();

   while($record = $p->get_record())
   {
   if(count($record) == 0)
   {
   break;
   };
   echo $record["call"]."<br>";
   //	print_a($record);
   };
*/
if (!is_object($wabAjax)) {
    $wabAjax = new wabAjax;
}
if ($_GET['wabExportAjax'] == 'checkrecs') {
    echo $wabAjax->getFilterNumrecs($_GET);
    exit();
}