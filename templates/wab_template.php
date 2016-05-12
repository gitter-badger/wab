<?php
/**
* class file for the WAB log plugin.
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

if (!defined('e107_INIT')) {
    exit;
}

/**
* WabTemplate
*
* @package
* @author Barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class WabTemplate {
    /**
    * WabTemplate::__construct()
    */
    function __construct() {
    }
    function wabTemlateLogListHeader() {
        return $retval;
    }
    /**
    * WabTemplate::wabMenu()
    *
    * @return string containing icon menu for displaying at top of page
    */
    function wabTemplateMenuOpen() {
        $retval = "
<div id='wabFilterMenu'>
	<div class='wabTopMenu' >
		<div class='wabMenuContainer' >
			{WAB_MYLOGBOOKSICON}
			{WAB_USERLOGICON}
			{WAB_USERICON}
			{WAB_CHARTSICON}
			{WAB_BOOKSICON}
			{WAB_COUNTYICON}
			{WAB_ISLANDSICON}
			{WAB_TRIGICON}

			{WAB_COUNTRYICON}
			{WAB_COASTALICON}
			{WAB_IMPORTICON}
			{WAB_EXPORTICON}
			{WAB_SYNCICON}
			{WAB_SETTINGSICON}
			{WAB_NEWSICON}
			{WAB_EVENTSICON}";
        return $retval;
    }
    function wabTemplateMenuClose() {
        $retval = "
		</div>
	</div>
</div>";
        return $retval;
    }
    function wabMainHeader() {
        $retval = "
<div id='wabMainPage'>
		";
        return $retval;
    }
    function wabMainFooter() {
        $retval .= "
	<div id='wabFooterContainer' >
		<div id='wabFooterLeft' >&nbsp;</div><div id='wabFooterRight'  >Per Page<br />{WAB_PERPAGE}</div>
	</div> <!-- end footer container -->
</div>";
        return $retval;
    }
    function wabListFilter($wabObject) {
        $retval .= "
<table style='width:100%' class='wabListTableTable' >
	<thead>
	<tr class='wabFilterRow'>
    	<th style='width:24%;text-align:center;' class='wabFilterCell'>User<br />{WAB_USERSELECT}</th>
    	<th style='width:24%;text-align:center;' class='wabFilterCell'>Log<br />{WAB_LOGSELECT}</th>
    	<th style='text-align:center;' class='wabFilterCell'>Select<br />{WAB_LOGFILTER}</th>
    	<th style='width:24%;text-align:center;' class='wabFilterCell'>Search<br />{WAB_LOGSEARCH}</th>
    	<th style='width:40px;text-align:center;' class='wabFilterCell'>Filter<br />{WAB_LOGFILTERGO}</th>
	</tr>
	</thead>
</table>";
        return $retval;
    }

    function wabListHeader($wabObject) {
        $retval .= "
<div id='wabTableWrapper' >";
        $retval .= "
	<table class='wabListTable' >
	<thead>
		<tr>
    		<th class='wabdate'>" . LAN_WAB_DATE . "<br />" . $wabObject->iconSort('date', 'asc') . "</th>
    		<th class='wabtime'>" . LAN_WAB_TIMEON . "</th>
    		<th class='wabtime'>" . LAN_WAB_TIMEOFF . "</th>
    		<th class='wabcall' >" . LAN_WAB_CALL . "<br />" . $wabObject->iconSort('call', 'asc') . "</th>
    		<th class='wabarea' >" . LAN_WAB_SQUARE . "<br />" . $wabObject->iconSort('area', 'asc') . "</th>
    		<th class='wabconfirm'>" . LAN_WAB_CONFIRMED . "</th>
    		<th class='wabcountry'>" . LAN_WAB_COUNTRY . "<br />" . $wabObject->iconSort('country', 'asc') . "</th>
    		<th class='wabcoastal'>" . LAN_WAB_COASTAL . "</th>
			<th class='wabisland'>" . LAN_WAB_ISLAND . "</th>
    		<th class='wabfreq'>" . LAN_WAB_FREQ . "</th>
    		<th class='wabband'>" . LAN_WAB_BAND . "</th>
    		<th class='wabmode'>" . LAN_WAB_MODE . "</th>
    		<th class='wabrst'>RST<br />Sent</th>
    		<th class='wabrst'>RST<br />Rcvd</th>
    		<th class='wabname'>" . LAN_WAB_NAME . "</th>
    		<th class='wabpower'>" . LAN_WAB_POWER . "</th>
    		<th class='wabqsl'>QSL<br />Sent</th>
    		<th class='wabqsl'>QSL<br />Rcvd</th>
    		<th class='wabaction'>" . LAN_WAB_ACTION . "</th>
    	</tr>
    </thead>
    <tbody>";

        return $retval;
    }
    function wabListDetail($wabObject) {
        $retval .= "
    	<tr class='wabDetailRow'>
    		<td class='wabrdate'>{WAB_LOGSTARTDATE}</td>
    		<td class='wabrtime'>{WAB_LOGTIMEON}</td>
    		<td class='wabrtime'>{WAB_LOGTIMEOFF}</td>
    		<td class='wabrcall'>{WAB_LOGCALLSIGN}</td>
    		<td class='wabrarea'>{WAB_LOGAREA}</td>
    		<td class='wabrconfirm'>{WAB_LOGCONFIRM}</td>
    		<td class='wabrcountry'>{WAB_LOGFLAG}</td>
    		<td class='wabrcoastal'>{WAB_LOGCOASTAL}</td>
    		<td class='wabrisland'>{WAB_LOGISLAND}</td>
    		<td class='wabrfreq'>{WAB_LOGFREQ}</td>
    		<td class='wabrband'>{WAB_LOGBAND}</td>
    		<td class='wabrmode'>{WAB_LOGMODE}</td>
    		<td class='wabrrst'>{WAB_LOGRSTIN}</td>
    		<td class='wabrrst'>{WAB_LOGRSTOUT}</td>
    		<td class='wabrname' >{WAB_LOGNAME}</td>
    		<td class='wabrpower' >{WAB_LOGPOWER} W</td>
    		<td class='wabrqsl'>{WAB_LOGQSLOUT}</td>
    		<td class='wabrqsl'>{WAB_LOGQSLIN}</td>
    		<td class='wabraction'>{WAB_LOGEDIT}</td>
    	</tr>";

        return $retval;
    }
    function wabListNoDetail() {
        $retval .= "
		<tr id='wabDetailRow'>
    		<td colspan='19' ><div id='wabNoLogExist' >No Logbook selected</div><div id='wabNoLogAjax'>{WAB_AJAXLOADER=fountain}</div></td>
    	</tr>";

        return $retval;
    }
    function wabListFooter() {
        $retval .= "
	</tbody>
	</table>
</div>";
        return $retval;
    }

    function wabIslandFilter($wabObject) {
        $retval .= "
<table style='width:100%' class='wabListTableTable' >
	<thead>
	<tr class='wabFilterRow'>
    	<th style='text-align:center;' class='wabFilterCell'>
    		<div class='wabMenuContainer wabMenuExport'>
    			{WAB_ISLANDPDF} {WAB_ISLANDXLS} {WAB_ISLANDCSV}</div>
    		</th>
    	<th style='text-align:center;' class='wabFilterCell'>Select<br />{WAB_LOGFILTER}</th>
    	<th style='width:24%;text-align:center;' class='wabFilterCell'>Search<br />{WAB_LOGSEARCH}</th>
    	<th style='width:40px;text-align:center;' class='wabFilterCell'>Filter<br />{WAB_LOGFILTERGO}</th>
	</tr>
	</thead>
</table>";
        return $retval;
    }

    function wabIslandHeader($wabObject) {
        $retval .= "
<div id='wabTableWrapper' >";
        $retval .= "
	<table class='wabListTable' >
	<thead>
		<tr>
    		<th class='wabid'>" . LAN_WAB_ID . "<br />" . $wabObject->iconSort('id', 'asc') . "</th>
    		<th class='wabislandname'>Island<br />" . $wabObject->iconSort('date', 'asc') . "</th>
    		<th class='wabislandarea'>Area</th>
    		<th class='wabislandcountry'>Country</th>
    		    		<th class='wabislandcount' >Log<br />Count</th>
    		<th class='wabislandcomment' >Comment</th>

     	</tr>
    </thead>
    <tbody>";

        return $retval;
    }
    function wabIslandDetail($wabObject) {
        $retval .= "
    	<tr class='wabDetailRow'>
    		<td class='wabrid' >{WAB_ISLANDID}</td>
    		<td class='wabrislandname'>{WAB_ISLANDNAME}</td>
    		<td class='wabrislandarea'>{WAB_ISLANDAREA}</td>
    		<td class='wabrislandcountry'>{WAB_ISLANDFLAG}</td>
    		    		<td class='wabrislandcount'>{WAB_ISLANDCOUNT}</td>
    		<td class='wabrislandcomment'>{WAB_ISLANDCOMMENT}</td>

    	</tr>";

        return $retval;
    }
    function wabIslandNoDetail() {
        $retval .= "
		<tr id='wabDetailRow'>
    		<td colspan='6' ><div id='wabNoLogExist' >No islands matching this criteria</div><div id='wabNoLogAjax'>{WAB_AJAXLOADER=fountain}</div></td>
    	</tr>";

        return $retval;
    }
    function wabIslandFooter() {
        $retval .= "
	</tbody>
	</table>
</div>";
        return $retval;
    }

    function wabBooksFilter($wabObject) {
        $retval .= "
<table style='width:100%' class='wabListTableTable' >
	<thead>
	<tr class='wabFilterRow'>
    	<th style='text-align:center;' class='wabFilterCell'>
    		<div class='wabMenuContainer wabMenuExport'>
    			{WAB_BooksPDF} {WAB_BooksXLS} {WAB_BooksCSV}</div>
    		</th>
    	<th style='text-align:center;' class='wabFilterCell'>Select<br />{WAB_LOGFILTER}</th>
    	<th style='width:24%;text-align:center;' class='wabFilterCell'>Search<br />{WAB_LOGSEARCH}</th>
    	<th style='width:40px;text-align:center;' class='wabFilterCell'>Filter<br />{WAB_LOGFILTERGO}</th>
	</tr>
	</thead>
</table>";
        return $retval;
    }

    function wabBooksHeader($wabObject) {
        $retval .= "
<div id='wabTableWrapper' >";
        $retval .= "
	<table class='wabListTable wabBooksTable' >
	<thead>
		<tr>
    		<th class='wabBooksNumber'>Book No<br />" . $wabObject->iconSort('id', 'asc') . "</th>
    		<th class='wabBookscall'>Owner<br />" . $wabObject->iconSort('date', 'asc') . "</th>
    		<th class='wabBookseries'>Series</th>
    		<th class='wabBooksissued'>Issued</th>
    		<th class='wabBookstick' >Reissue</th>
    		<th class='wabBookstick' >NLV</th>
     	</tr>
    </thead>
    <tbody>";

        return $retval;
    }
    function wabBooksDetail() {
        $retval .= "
    	<tr class='wabDetailRow'>
    		<td class='wabBooksNumber' >{WAB_BOOKSNUMBER}</td>
    		<td class='wabBookscall'>{WAB_BOOKSCALL}</td>
    		<td class='wabBookseries'>{WAB_BOOKSSERIES}</td>
    		<td class='wabBooksissued'>{WAB_BOOKSISSUED}</td>
    		<td class='wabBookstick'>{WAB_REISSSUE}</td>
    		<td class='wabBookstick'>{WAB_BOOKSNLV}</td>

    	</tr>";

        return $retval;
    }
    function wabBooksNoDetail() {
        $retval .= "
		<tr id='wabDetailRow'>
    		<td colspan='6' ><div id='wabNoLogExist' >No Books matching this criteria</div><div id='wabNoLogAjax'>{WAB_AJAXLOADER=fountain}</div></td>
    	</tr>";

        return $retval;
    }
    function wabBooksFooter() {
        $retval .= "
	</tbody>
	</table>
</div>";
        return $retval;
    }
    // *
    // * Counties List
    // *
    function wabCountiesFilter($wabObject) {
        $retval .= "
<table style='width:100%' class='wabListTableTable' >
	<thead>
	<tr class='wabFilterRow'>
    	<th style='text-align:center;' class='wabFilterCell'>
    		<div class='wabMenuContainer wabMenuExport'>
    			{WAB_BooksPDF} {WAB_BooksXLS} {WAB_BooksCSV}</div>
    		</th>
    	<th style='text-align:center;' class='wabFilterCell'>Select<br />{WAB_LOGFILTER}</th>
    	<th style='width:24%;text-align:center;' class='wabFilterCell'>Search<br />{WAB_LOGSEARCH}</th>
    	<th style='width:40px;text-align:center;' class='wabFilterCell'>Filter<br />{WAB_LOGFILTERGO}</th>
	</tr>
	</thead>
</table>";
        return $retval;
    }

    function wabCountiesHeader($wabObject) {
        $retval .= "
<div id='wabTableWrapper' >";
        $retval .= "
	<table class='wabListTable wabCountyTable' >
	<thead>
		<tr>
    		<th class='wabCountyFlag'>Flag</th>
    		<th class='wabCountyName'>County<br />" . $wabObject->iconSort('countyname', 'asc') . "</th>
    		<th class='wabCountyFlag'>Flag</th>
    		<th class='wabCountyName'>Country<br />" . $wabObject->iconSort('country', 'asc') . "</th>
    		<th class='wabCountryNotes'>Notes</th>
     	</tr>
    </thead>
    <tbody>";

        return $retval;
    }
    function wabCountiesDetail() {
        $retval .= "
    	<tr class='wabDetailRow'>
    		<td class='wabCountyFlag'>{WAB_COUNTYFLAG}</td>
    		<td class='wabCountyName'>{WAB_COUNTYNAME}</td>
    		<td class='wabCountyFlag'>{WAB_COUNTRYFLAG}</td>
    		<td class='wabCountyName'>{WAB_COUNTRYNAME}</td>
    		<td class='wabCountryNotes'>{WAB_COUNTYNOTES}</td>
    	</tr>";

        return $retval;
    }
    function wabCountiesNoDetail() {
        $retval .= "
		<tr id='wabDetailRow'>
    		<td colspan='5' ><div id='wabNoCountyExist' >No counties matching this criteria</div><div id='wabNoLogAjax'>{WAB_AJAXLOADER=fountain}</div></td>
    	</tr>";

        return $retval;
    }
    function wabCountiesFooter() {
        $retval .= "
	</tbody>
	</table>
</div>";
        return $retval;
    }
    // *
    // * Countries templates
    // *
    function wabCountriesFilter($wabObject) {
        $retval .= "
<table style='width:100%' class='wabListTableTable' >
	<thead>
	<tr class='wabFilterRow'>
    	<th style='text-align:center;' class='wabFilterCell'>
    		<div class='wabMenuContainer wabMenuExport'>
    			{WAB_BooksPDF} {WAB_BooksXLS} {WAB_BooksCSV}</div>
    		</th>
    	<th style='text-align:center;' class='wabFilterCell'>Select<br />{WAB_LOGFILTER}</th>
    	<th style='width:24%;text-align:center;' class='wabFilterCell'>Search<br />{WAB_LOGSEARCH}</th>
    	<th style='width:40px;text-align:center;' class='wabFilterCell'>Filter<br />{WAB_LOGFILTERGO}</th>
	</tr>
	</thead>
</table>";
        return $retval;
    }

    function wabCountriesHeader($wabObject) {
        $retval .= "
<div id='wabTableWrapper' >";
        $retval .= "
	<table class='wabListTable wabCountryTable' >
	<thead>
		<tr>
			<th class='wabCountryFlag'>Flag</th>
    		<th class='wabCountryName'>Country<br />" . $wabObject->iconSort('date', 'asc') . "</th>
    		<th class='wabCountryNotes'>Notes</th>
    		<th class='wabCountryID'>ID<br />" . $wabObject->iconSort('id', 'asc') . "</th>
     	</tr>
    </thead>
    <tbody>";

        return $retval;
    }
    function wabCountriesDetail() {
        $retval .= "
    	<tr class='wabDetailRow'>
    		<td class='wabCountryFlag'>{WAB_COUNTRYFLAG}</td>
    		<td class='wabCountryName'>{WAB_COUNTRYNAME}</td>
    		<td class='wabCountryNotes'>{WAB_COUNTRYNOTES}</td>
    		<td class='wabCountryID' >{WAB_COUNTRYID}</td>
    	</tr>";

        return $retval;
    }
    function wabCountriesNoDetail() {
        $retval .= "
		<tr id='wabDetailRow'>
    		<td colspan='4' class='wabNoCountyExist' >No counties matching this criteria<br /><div id='wabNoLogAjax'>{WAB_AJAXLOADER=fountain}</div></td>
    	</tr>";

        return $retval;
    }
    function wabCountriesFooter() {
        $retval .= "
	</tbody>
	</table>
</div>";
        return $retval;
    }
    /**
    * personal settings
    */
function wabSettingsMain(){

	$retval .= "
<div id='wabTableWrapper' >";
	$retval .= "
	<table class='wabListTable wabSettingsTable' >
	<thead>
		<tr>
			<th colspan='6' class='wabSettingsTitle' >My Settings</th>
     	</tr>
    </thead>
    <tbody>
    	<tr class='wabDetailRow'>
    		<th colspan='3' class=''>My Name</th>
    		<th colspan='3' class=''>Callsign</th>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td colspan='3' class=''>{WAB_SETTINGSNAME}</td>
    		<td colspan='3' class=''>{WAB_SETTINGSCALL}</td>
    	</tr>
    	<tr class='wabDetailRow'>
    		<th class=''>Area</th>
    		<th colspan='2' class=''>County</th>
    		<th colspan='2' class=''>Country</th>
    		<th class=''>IARU</th>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td class=''>{WAB_SETTINGSAREA}</td>
    		<td colspan='2' class=''>{WAB_SETTINGSCOUNTY}</td>
    		<td colspan='2' class=''>{WAB_SETTINGSCOUNTRY}</td>
    		<td class=''>{WAB_SETTINGSIARU}</td>
    	</tr>
    	<tr class='wabDetailRow'>
    		<th colspan='3' class=''>QRZ Login</th>
    		<th colspan='3' class=''>QRZ Password</th>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td colspan='3' class=''>{WAB_SETTINGSQRZLOGIN}</td>
    		<td colspan='3' class=''>{WAB_SETTINGSQRZPASSWORD}</td>
    	</tr>
    	<tr class='wabDetailRow'>
    		<th class=''>Log</th>
    		<th class=''>Callsign</th>
    		<th class=''>Type</th>
    		<th class=''>Private</th>
    		<th class=''>Active</th>
    		<th class=''>Default</th>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td class=''>1</td>
    		<td class=''>{WAB_SETTINGSTITLE=1}</td>
    		<td class=''>{WAB_SETTINGSTYPE=1}</td>
    		<td class=''>{WAB_SETTINGSPRIVATE=1}</td>
    		<td class=''>{WAB_SETTINGSACTIVE=1}</td>
    		<td class=''>{WAB_SETTINGSDEFAULT=1}</td>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td class=''>2</td>
    		<td class=''>{WAB_SETTINGSTITLE=2}</td>
    		<td class=''>{WAB_SETTINGSTYPE=2}</td>
    		<td class=''>{WAB_SETTINGSPRIVATE=2}</td>
    		<td class=''>{WAB_SETTINGSACTIVE=2}</td>
    		<td class=''>{WAB_SETTINGSDEFAULT=2}</td>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td class=''>3</td>
    		<td class=''>{WAB_SETTINGSTITLE=3}</td>
    		<td class=''>{WAB_SETTINGSTYPE=3}</td>
    		<td class=''>{WAB_SETTINGSPRIVATE=3}</td>
    		<td class=''>{WAB_SETTINGSACTIVE=3}</td>
    		<td class=''>{WAB_SETTINGSDEFAULT=3}</td>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td class=''>4</td>
    		<td class=''>{WAB_SETTINGSTITLE=4}</td>
    		<td class=''>{WAB_SETTINGSTYPE=4}</td>
    		<td class=''>{WAB_SETTINGSPRIVATE=4}</td>
    		<td class=''>{WAB_SETTINGSACTIVE=4}</td>
    		<td class=''>{WAB_SETTINGSDEFAULT=4}</td>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td class=''>5</td>
    		<td class=''>{WAB_SETTINGSTITLE=5}</td>
    		<td class=''>{WAB_SETTINGSTYPE=5}</td>
    		<td class=''>{WAB_SETTINGSPRIVATE=5}</td>
    		<td class=''>{WAB_SETTINGSACTIVE=5}</td>
    		<td class=''>{WAB_SETTINGSDEFAULT=5}</td>
    	</tr>
    	<tr>
    		<td colspan='6' class='' >{WAB_SETTINGSSAVE}</td>
    	</tr>
	</tbody>
	</table>
</div>";


	return $retval;
}
	function wabSettingsNoAcc(){

		$retval .= "
<div id='wabTableWrapper' >";
		$retval .= "
		<table class='wabListTable wabSettingsTable' >
		<thead>
			<tr>
				<th class='wabSettingsTitle' >Create Account</th>
     	</tr>
    </thead>
    <tbody>
    	<tr class='wabDetailRow'>
    		<td  colspan='4' class=''>You do not currently have an log book account. If you wish to create a new account click on the button below. This will then create your account and 5 log books.</td>
    	</tr>
<tr class='wabDetailRow'>
    		<th colspan='2' class=''>My Name *</th>
    		<th colspan='2' class=''>Callsign *</th>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td colspan='2' class=''>{WAB_SETTINGSNAME}</td>
    		<td colspan='2' class=''>{WAB_SETTINGSCALL}</td>
    	</tr>
    	<tr class='wabDetailRow'>
    		<th class=''>Area</th>
    		<th colspan='1' class=''>County</th>
    		<th colspan='1' class=''>Country</th>
    		<th class=''>IARU</th>
    	</tr>
    	<tr class='wabDetailRow'>
    		<td class=''>{WAB_SETTINGSAREA}</td>
    		<td colspan='1' class=''>{WAB_SETTINGSCOUNTY}</td>
    		<td colspan='1' class=''>{WAB_SETTINGSCOUNTRY}</td>
    		<td class=''>{WAB_SETTINGSIARU}</td>
    	</tr>

    	<tr>
    		<td  colspan='4' class='' >{WAB_SETTINGSCREATE}</td>
    	</tr>
		</tbody>
		</table>
</div>";


		return $retval;
	}

function wabExport(){
	$retval .= "
<div id='wabTableWrapper' >";
	$retval .= "
	<table class='wabExportTable' >
	    <tbody>
		<tr>
			<td colspan='2' class='wabExportHead' >Export Logbook</td>
     	</tr>
    	<tr >
    		<td  class='wabExportColh'>Logbook</td>
    		<td  class='wabExportColh'>WAB</td>
    	</tr>
    	<tr >
    		<td  class='wabExportCol'>{WAB_EXPORTLOG}</td>
    		<td  class='wabExportCol'>{WAB_EXPORTWAB}</td>
    	</tr>
    	<tr >
    		<td  class='wabExportColh'>Active</td>
    		<td  class='wabExportColh'>Private</td>
    	</tr>
    	<tr >
    		<td  class='wabExportCol'>{WAB_EXPORTACTIVE}</td>
    		<td  class='wabExportCol'>{WAB_EXPORTPRIVATE}</td>
    	</tr>
    	<tr >
    	    <td  class='wabExportColh'>Book Holders</td>
    	    <td  class='wabExportColh'>Coastal</td>
    	</tr>
    	<tr >
    	    <td  class='wabExportCol'>{WAB_EXPORTBOOKS}</td>
    	    <td  class='wabExportCol'>{WAB_EXPORTCOASTAL}</td>
    	</tr>
    	<tr >
    		<td  class='wabExportColh'>Islands</td>
    		<td  class='wabExportColh'>Country</td>
    	</tr>
    	<tr >
    		<td  class='wabExportCol'>{WAB_EXPORTISLANDS}</td>
    		<td  class='wabExportCol'>{WAB_EXPORTCOUNTRY}</td>
    	</tr>
    	<tr >
    		<td  class='wabExportColh'>Mode</td>
    		<td  class='wabExportColh'>Band</td>
    	</tr>

    	<tr >
    		<td  class='wabExportCol'>{WAB_EXPORTMODE}</td>
    		<td  class='wabExportCol'>{WAB_EXPORTBAND}</td>
    	</tr>
    	<tr >
    		<td  class='wabExportColh'>QSL Sent</td>
    		<td  class='wabExportColh'>QSL Rcvd</td>
    	</tr>
    	<tr >
    		<td  class='wabExportCol'>{WAB_EXPORTQSLSENT}</td>
    		<td  class='wabExportCol'>{WAB_EXPORTQSLRCVD}</td>
    	</tr>
    	<tr >
    		<td class='wabExportColh'>Continent</td>
    		<td class='wabExportColh'>Limit to</td>
    	</tr>
    	<tr >
    		<td class='wabExportCol' >{WAB_EXPORTCONTINENT}</td>
    		<td class='wabExportCol' >{WAB_EXPORTLIMIT}</td>
    	</tr>
    	<tr>
    		<td colspan='2' class='wabExportHead' ><span id='wabnumberofrecords'> </span> Records Selected</td>
    	</tr>
    	<tr>
    		<td colspan='2' class='wabExportHead' >Destination</td>
    	</tr>
        <tr >
    		<td  class='wabExportColh'>Order by</td>
    		<td  class='wabExportColh'>Format</td>
    	</tr>
    	<tr >
    		<td class='wabExportCol'>{WAB_EXPORTORDER}</td>
    		<td class='wabExportCol'>{WAB_EXPORTFORMAT}</td>
    	</tr>
    	<tr>
    		<td colspan='2' class='wabExportHead ' >&nbsp; </td>
    	</tr>
    	<tr>
    		<td colspan='2' class='  wabExportHead' >{WAB_EXPORTRESET}&nbsp;&nbsp;&nbsp;&nbsp;{WAB_EXPORTDO}</td>
    	</tr>
	</tbody>
	</table>
</div>";
return $retval;
}

    function wabCoastalFilter($wabObject) {
        $retval .= "
<table style='width:100%' class='wabListTableTable' >
	<thead>
	<tr class='wabFilterRow'>
    	<th style='text-align:center;' class='wabFilterCell'>
    		<div class='wabMenuContainer wabMenuExport'>
    			{WAB_BooksPDF} {WAB_BooksXLS} {WAB_BooksCSV}</div>
    		</th>
    	<th style='text-align:center;' class='wabFilterCell'>Select<br />{WAB_LOGFILTER}</th>
    	<th style='width:24%;text-align:center;' class='wabFilterCell'>Search<br />{WAB_LOGSEARCH}</th>
    	<th style='width:40px;text-align:center;' class='wabFilterCell'>Filter<br />{WAB_LOGFILTERGO}</th>
	</tr>
	</thead>
</table>";
        return $retval;
    }

    function wabCoastalHeader($wabObject) {
        $retval .= "
<div id='wabTableWrapper' >";
        $retval .= "
	<table class='wabListTable' >
	<thead>
		<tr>
    		<th class='wabBooksNumber'>Coastal ID<br />" . $wabObject->iconSort('id', 'asc') . "</th>
    		<th class='wabBookscall'>Area<br />" . $wabObject->iconSort('date', 'asc') . "</th>
    		<th class='wabBookseries'>Flag</th>
    		<th class='wabBooksissued'>Notes</th>
     	</tr>
    </thead>
    <tbody>";

        return $retval;
    }
    function wabCoastalDetail() {
        $retval .= "
    	<tr class='wabDetailRow'>
    		<td class='wabrBooksNumber' >{WAB_COASTALID}</td>
    		<td class='wabrBookscall'>{WAB_COASTALAREA}</td>
    		<td class='wabrBookseries'>{WAB_COASTALFLAG}</td>
    		<td class='wabrBooksissued'>{WAB_COASTALNOTES}</td>
    	</tr>";

        return $retval;
    }
    function wabCoastalNoDetail() {
        $retval .= "
		<tr id='wabDetailRow'>
    		<td colspan='4' ><div id='wabNoCountyExist' >No counties matching this criteria</div><div id='wabNoLogAjax'>{WAB_AJAXLOADER=fountain}</div></td>
    	</tr>";

        return $retval;
    }
    function wabCoastalFooter() {
        $retval .= "
	</tbody>
	</table>
</div>";
        return $retval;
    }
    // *
    // * Users List
    // *
    function wabUsersHeader($wabObject) {
        $retval .= "
<div id='wabTableWrapper' >";
        $retval .= "
	<table class='wabListTable wabUsersTable' >
	<thead>
		<tr>
    		<th class='wabUsersCall'>Callsign<br />" . $wabObject->iconSort('date', 'asc') . "</th>
     		<th class='wabrUsersName'>WAB Name</th>
     		<th class='wabrUsersName'>Name</th>
    		<th class='wabrUsersLogs'>Logs</th>
    		<th class='wabrUsersBooks'>Books</th>
    		<th class='wabrUsersArea'>Square</th>
    		<th class='wabUsersFlag'>County</th>
    		<th class='wabUsersFlag'>Country</th>
     	</tr>
    </thead>
    <tbody>";

        return $retval;
    }
    function wabUsersDetail() {
        $retval .= "
    	<tr class='wabDetailRow'>
    		<td class='wabUsersCall'>{WAB_USERCALL}</td>
    		<td class='wabrUsersName'>{WAB_USERWABNAME}</td>
    		<td class='wabrUsersName'>{WAB_USERNAME}</td>
    		<td class='wabrUsersLogs'>{WAB_USERLOGS}</td>
    		<td class='wabrUsersBooks'>{WAB_USERBOOKS}</td>
    		<td class='wabrUsersArea'>{WAB_USERAREA}</td>
    		<td class='wabUsersFlag'>{WAB_USERCOUNTY}</td>
    		<td class='wabUsersFlag'>{WAB_USERFLAG}</td>
    	</tr>";

        return $retval;
    }
    function wabUsersNoDetail() {
        $retval .= "
		<tr id='wabDetailRow'>
    		<td colspan='8' ><div id='wabNoCountyExist' >No users matching this criteria</div><div id='wabNoLogAjax'>{WAB_AJAXLOADER=fountain}</div></td>
    	</tr>";

        return $retval;
    }
    function wabUsersFooter() {
        $retval .= "
	</tbody>
	</table>
</div>";
        return $retval;
    }
    function wabImportADIHeader() {
        $retval .= "
<div id='wabTableWrapper' >";
        $retval .= "
	<table class='wabImportTable' >
	<thead>
		<tr>
			<th colspan='3'>Import ADI Files</th>
		</tr>
	</thead>
	    <tbody>
		<tr>
     		<td colspan='3' class=''>Upload a file to import</td>
     	</tr>
     	<tr>
    		<td class='wabBooksNumber'>Select file<br /><i>Uploaded files will be deleted after 7 days</i></td>
    		<td colspan='2' class='wabBookscall'>{WAB_IMPORTUPLOAD} {WAB_DOUPLOAD}</td>
     	</tr>
     	     	 <tr>
     		<td colspan='3' class=''>&nbsp;</td>
     	</tr>
     	 <tr>
     		<td colspan='3' class=''>File Type</td>
     	</tr>
    	<tr>
    		<td class='wabBooksNumber'>File Type</td>
    		<td colspan='2' class='wabBookscall'>{WAB_IMPORTTYPE}</td>
     	</tr>
     	<tr>
     		<td colspan='3' class=''>&nbsp;</td>
     	</tr>
		<tr>
    		<td class=''>My ADI files </td>
    		<td class=''>Import</td>
    		<td class=''>Delete</td>
     	</tr>";
        return $retval;
    }
    function wabImportADIFiles() {
        $retval .= "
		<tr>
    		<td class=''> {WAB_IMPORTFILES}</td>
    		<td class=''>{WAB_IMPORTSELECT}</td>
    		<td class=''>{WAB_IMPORTDELETE}</td>
     	</tr>";
        return $retval;
    }
    function wabImportADIFooter() {
        $retval .= "
		<tr>
    		<td  class=''> </td>
    		<td  class=''> Toggle Delete</td>
    		<td class=''>{WAB_IMPORTTOGGLE}</td>
     	</tr>
		<tr>
    		<td  class=''> </td>
    		<td  class=''></td>
    		<td class=''>{WAB_DOIMPORT}</td>
     	</tr>
	</tbody>
	</table>
</div>";
        return $retval;
    }
    function wabQuickMenu() {
        $retval .= "
<div id='wabQuickTable'>
	<div id='wabQuickMenuStatus'>Result:Success</div>
	<div id='wabQuickMyLog'>My Logbook<br />{WAB_QUICKMINE}</div>

	<table class='wabQuickTable'>
		<thead>
			<tr>
				<th class='wabQuickHField'>Field</th>
				<th class='wabQuickHValue'>Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='wabQuickField' >Date</td>
				<td class='form-inline' >{WAB_QUICKSTART}</td>
			</tr>
			<tr>
				<td class='wabQuickField' >Callsign</td>
				<td class='wabQuickValue' >{WAB_QUICKCALL}</td>
			</tr>
			<tr>
				<td class='wabQuickField' >Frequency</td>
				<td class='wabQuickValue' >{WAB_QUICKFREQ}</td>
			</tr>
			<tr>
				<td class='wabQuickField' >RS Rcvd</td>
				<td class='wabQuickValue' >{WAB_QUICKRRS}</td>
			</tr>
			<tr>
				<td class='wabQuickField' >RS Sent</td>
				<td class='wabQuickValue' >{WAB_QUICKSRS}</td>
			</tr>
			<tr>
				<td class='wabQuickField' >Mode</td>
				<td class='wabQuickValue' >{WAB_QUICKMODE}</td>
			</tr>
			<tr>
				<td class='wabQuickField' >Square</td>
				<td class='wabQuickValue' >{WAB_QUICKSQR}</td>
			</tr>
			<tr>
				<td class='wabQuickField' >My Area</td>
				<td class='wabQuickValue' >{WAB_QUICKAREA}</td>
			</tr>
			<tr>
				<td class='wabQuickSubmit' colspan='2'>{WAB_QUICKSUB}</td>
			</tr>
		</tbody>
	</table>
</div>";
        return $retval;
    }
}

?>